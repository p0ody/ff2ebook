<?php

require_once(__DIR__ ."/../conf/proxyConf.php");
require_once("class.dbHandler.php");

class ProxyManager
{
    private $pdo;
    private $dbH;
    private $lastGiven = false;

    function __construct()
    {
        $this->dbH = new dbHandler();
        try
        {
            $this->pdo = $this->dbH->connect();
        }
        catch (PDOException $e)
        {
            return false;
        }
    }

    public function updateList()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, PROXY_SOURCE);
        $proxyList = curl_exec($curl);
        curl_close($curl);

        if ($proxyList !== false)
        {
            $list = explode(PHP_EOL, $proxyList);
            $list = array_filter($list, function ($value) { return (strlen($value) > 0) ? true : false; }); // Remove empty lines
            $sql = "";
            $count = 0;
            foreach($list as $line)
            {
                $count++;
                    
                $sql .= "(?)";
                if ($count < count($list))
                    $sql .=", ";

            }
            $query = $this->pdo->prepare("REPLACE INTO `proxy_list` (`ip`) VALUES $sql;");
            $query->execute($list);
            
            return $list;
        }
        else
            return false;
    }

    public function updateWorkingState($ip, $working)
    {
        $query = $this->pdo->prepare("UPDATE `proxy_list` SET `working`=? WHERE `ip`=?;");
        $query->execute(Array($working ? 1 : 0, $ip));
    }

    public function updateLatency($ip, $latency)
    {
        $query = $this->pdo->prepare("UPDATE `proxy_list` SET `latency`=? WHERE `ip`=?;");
        $query->execute(Array($latency, $ip));
    }

    public function testProxy($ip)
    {
        // Test url with selected proxy, cancel if it takes longer than 10 seconds to respond.
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // Page source seems gzip compressed, so we tell cURL to accept all encodings, otherwise the output is garbage (2019-06-20)
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_URL, PROXY_TEST_URL);
        curl_setopt($curl, CURLOPT_PROXY, $ip);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        $query = $this->pdo->prepare(SQL_INSERT_PROXY);
        if ($result === false)
        {
            $query->execute(Array("ip" => $ip, "working" => 0, "latency" => 10000));
            return false;
        }

        $query->execute(Array("ip" => $ip, "working" => 1, "latency" => $info['total_time'] * 1000));
        return true;
    }

    public function cleanNotWorking()
    {
        $query = $this->pdo->prepare(SQL_CLEAN_PROXY);
        $query->execute();
    }

    // Return an array containing ["ip"], ["latency"] & ["working"] for each proxy ordered by better latency
    public function getProxyList()
    {
        $query = $this->pdo->prepare(SQL_SELECT_PROXY_ALL);
        $query->execute();

        if ($query->rowCount() > 0) {
            $results = $query->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        }
    }

    // Return ip of proxy with best latency when last tested
    public function getBestProxy($lastTried = false) // lastTried is for getting the next in line if the best does not work
    {
        $list = $this->getProxyList();
        if (!$lastTried && !$this->lastGiven)
        {
            $this->lastGiven = $list[0]["ip"];
            return $list[0]["ip"];
        }
        else
            $lastTried = $this->lastGiven;

        $current = array_search($lastTried, $list);

        if (!$current) // If $lastTried is not found in array, return a random proxy.
        {   
            $rand = array_rand($list);
            $this->lastGiven = $rand;
            return $rand;
        }

        $this->lastGiven = $list[$current++]["ip"];
        return $list[$current++]["ip"];
    }

    public function getLastGiven() { return $this->lastGiven; }

}
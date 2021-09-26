<?php

require_once(__DIR__ ."/../conf/config.php");
require_once("class.dbHandler.php");
require_once("class.Proxy.php");

class ProxyManager
{
    private $pdo;
    private $dbH;

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

    /**
     * @return array<Proxy>|false Return and array of object Proxy or false on error
     */
    public function updateList()
    {
        $this->updateAuthedList();
        
        $proxyList = "";

        foreach(Config::PROXY_SOURCE as $url) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_URL, $url);
            $proxyList .= curl_exec($curl);
            curl_close($curl);
        }

        if ($proxyList !== false)
        {
            preg_match_all("/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}:[0-9]{1,5})/si", $proxyList, $matches, PREG_PATTERN_ORDER);
            $proxies = [];
            $blacklist = $this->getBlacklist();
            foreach($matches[0] as $ip) {
                // Remove blacklisted ips
                if ($blacklist && in_array($ip, $blacklist)) {
                    continue;
                }
                array_push($proxies, new Proxy(trim($ip)));
            }
            array_push($proxies, new Proxy("")); // Adding empty line to also test without proxy

            // Add ips from DB
            $query = $this->pdo->prepare(SQL_SELECT_PROXY_ALL);
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                array_push($proxies, new Proxy($row));
            }

            $proxies = array_unique($proxies); // Remove duplicates
            $proxies = array_values($proxies); // reindex values to avoid error with $query->execute

            $sql = "";
            
            foreach($proxies as $proxy)
            {
                $sql .= "(". $this->pdo->quote($proxy->getIP()) .", ". Config::PROXY_TEST_MAX_TIME_MS .", 0, ". $this->pdo->quote($proxy->getAuth()) .")";
                if ($proxy != end($proxies)) {
                    $sql .=", ";
                }
            }

            $query = $this->pdo->prepare("INSERT IGNORE INTO `proxy_list` (`ip`, `latency`, `working`, `auth`) VALUES $sql;");
            $query->execute();
            
            return $proxies;
        }
        else
            return false;
            
    }

    public function updateAuthedList() {
        $proxyList = "";

        foreach(Config::PROXY_AUTH_SOURCE as $url) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_URL, $url);
            $proxyList .= curl_exec($curl);
            curl_close($curl);
        }

        if ($proxyList !== false)
        {
            $proxies = [];
            preg_match_all("/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}:[0-9]{1,5}):(.+?:.+)/i", $proxyList, $matches, PREG_SET_ORDER);
            $blacklist = $this->getBlacklist();
            foreach($matches as $proxy) {
                // If ip is blacklisted, skip
                if ($blacklist && in_array($proxy[1], $blacklist)) {
                    continue;
                }
                array_push($proxies, new Proxy(trim($proxy[1]), trim($proxy[2])));
            }

            $sql = "";
            foreach($proxies as $proxy)
            {
                $sql .= "(". $this->pdo->quote($proxy->getIP()) .", ". Config::PROXY_TEST_MAX_TIME_MS .", 0, ". $this->pdo->quote($proxy->getAuth()) .")";
                if ($proxy != end($proxies)) {
                    $sql .=", ";
                }
            }
   
            $query = $this->pdo->prepare("INSERT IGNORE INTO `proxy_list` (`ip`, `latency`, `working`, `auth`) VALUES $sql;");
            $query->execute();
            
            return $proxies;
        }
        else {
            return false;
        }
    }

    /**
     * @param Proxy $proxy
     * @param bool $working
     * @return void
     */
    public function updateWorkingState(Proxy $proxy, $working)
    {
        $this->updateUptime($proxy->getIP(), $working);
        $query = $this->pdo->prepare("UPDATE `proxy_list` SET `working`=? WHERE `ip`=?;");
        $query->execute(Array($working ? 1 : 0, $proxy->getIP()));
    }

    /**
     * @param Proxy $proxy
     * @param int $latency
     * @return void
     */
    public function updateLatency(Proxy $proxy, $latency)
    {
        $query = $this->pdo->prepare("UPDATE `proxy_list` SET `latency`=? WHERE `ip`=?;");
        $query->execute(Array($latency, $proxy->getIP()));
    }

    public function testProxy(Proxy $proxy)
    {
        if ($this->isBlacklisted($proxy->getIP())) {
            return false;
        }

        // Test url with selected proxy, cancel if it takes longer than 10 seconds to respond.
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_URL, Config::PROXY_TEST_URL);
        curl_setopt($curl, CURLOPT_PROXY, $proxy->getIP());
        if ($proxy->isAuthed()) {
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy->getAuth());
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, Config::PROXY_TEST_MAX_TIME_SEC);
        curl_setopt($curl, CURLOPT_CONNECT_ONLY, true);
        
        curl_exec($curl);
        $totalTime = curl_getinfo($curl, CURLINFO_TOTAL_TIME);
        $error = curl_errno($curl);
        curl_close($curl);
        
        $query = $this->pdo->prepare(SQL_INSERT_PROXY);
        if ($error)
        {
            $query->execute(Array("ip" => $proxy->getIP(), "working" => 0, "latency" => Config::PROXY_TEST_MAX_TIME_MS, "auth" => $proxy->getAuth()));
            $this->updateUptime($proxy->getIP(), false);
            return false;
        }

        $query->execute(Array("ip" => $proxy->getIP(), "working" => 1, "latency" => $totalTime * 1000, "auth" => $proxy->getAuth()));
        $this->updateUptime($proxy->getIP(), true);
        return true;
    }

    public function cleanNotWorking()
    {
        $query = $this->pdo->prepare(SQL_CLEAN_PROXY);
        $query->execute();
    }

    /**
     * @param int $count Number of row that you want to get
     * @return array<Proxy>|false Return an array of Proxy for each proxy ordered by better latency
     */
    public function getProxyList(int $count = 1000)
    {
        $query = $this->pdo->prepare(SQL_SELECT_PROXY_LIMIT);
        $query->bindValue(":limitCount", $count, PDO::PARAM_INT);
        $query->execute();
        $results = [];
        if ($query->rowCount() > 0) {
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                array_push($results, new Proxy($row));
            }

            return $results;
        }
        return false;
    }

    /**
     * @return Proxy
     */
    public function getBestProxy() // lastTried is for getting the next in line if the best does not work
    {
        $best = $this->getProxyList(1);
        return $best[0];
    }

    /**
     * @return Proxy Return one random Proxy from the top 10 proxies
     */
    public function getRandomProxy() 
    {
        $list = $this->getProxyList(10);

        if (!$list) {
            return false;
        }
        $rand = array_rand($list);
        return $list[$rand];
    }


    public function addToBlacklist(Proxy $proxy)
    {
        $query = $this->pdo->prepare(SQL_PROXY_ADD_BLACKLIST);
        $query->execute(Array("ip" => $proxy->getIP()));
    }

    /**
     * @return array|false Return an array of IPs or false on error
     */
    public function getBlacklist()
    {
        try {
            $query = $this->pdo->prepare(SQL_PROXY_GET_BLACKLIST);
            $query->execute();

            if ($query->rowCount() > 0) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $ipList = [];
                foreach ($result as $row) {
                    array_push($ipList, $row["ip"]);
                }
                return $ipList;
            }
            
            return false;
        }
        catch (Exception $e) {
            return false;
        }
    }

    public function isBlacklisted($ip) {
        try {
            $query = $this->pdo->prepare(SQL_PROXY_IS_BLACKLISTED);
            $query->execute(["ip" => $ip]);

            if ($query->rowCount() > 0) {
                return true;
            }
            
            return false;
        }
        catch (Exception $e) {
            return false;
        }

    }

    public function updateUptime(string $ip, bool $isWorking) {
        $query = $this->pdo->prepare(SQL_PROXY_UPDATE_UPTIME);
        $query->execute(["isWorking" => $isWorking ? 0 : 1, "ip" => $ip]);
    }
}
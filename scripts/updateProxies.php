<?php
require_once(__DIR__ ."/../class/class.ProxyManager.php");

$proxy = new ProxyManager();

$proxy->cleanNotWorking();
$proxy->updateList();

$list = $proxy->getProxyList();

if (!$list)
    die("Error getting proxy list");

$curlList = [];
$count = 0;
$arrayKey = 0;
foreach($list as $proxy)
{
    $url = "http://". Config::DOMAIN_PATH_SCRIPT ."/testProxy.php";
    $url .= "?ip=". $proxy->getIP();
    if ($proxy->isAuthed()) {
        $url .= "&auth=". $proxy->getAuth();
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    
    if (!isset($curlList[$arrayKey])) {
        $curlList[$arrayKey] = [];
    }
    array_push($curlList[$arrayKey], $curl); 
    $count++;

    if ($count >= Config::CURL_MAX_ASYNC) {
        $count = 0;
        $arrayKey++;
    }
}
 
// Need to use curl multi to avoid getting php timeout on long sequential request, but its faster that way anyway lol,
$mh = curl_multi_init();

$curlProcessed = 0;
foreach($curlList as $list) {
    foreach ($list as $handle)
    {
        curl_multi_add_handle($mh, $handle);
        $curlProcessed++;
    }

    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

    foreach ($list as $handle)
    {
        curl_multi_remove_handle($mh, $handle);
    }
}

curl_multi_close($mh);

echo "[". date("Y-m-d H:i:s") ."] ". $curlProcessed ." proxies tested." .PHP_EOL;
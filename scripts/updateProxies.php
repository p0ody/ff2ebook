<?php
require_once(__DIR__ ."/../class/class.ProxyManager.php");

$proxy = new ProxyManager();

$proxy->cleanNotWorking();
$proxy->updateList();

$list = $proxy->getProxyList();

if (!$list)
    die("Error getting proxy list");

$domain = $_SERVER['HTTP_HOST'];
$prefix = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$path = substr($_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["REQUEST_URI"], "/")); // Strip last section or URL (updateProxies.php)

$curlList = [];
foreach($list as $proxy)
{
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, $prefix.$domain.$path."/testProxy.php?ip=". $proxy["ip"]);

    array_push($curlList, $curl);  
}
 
// Need to use curl multi to avoid getting php timeout on long sequential request, but its faster that way anyway lol,
$mh = curl_multi_init();
curl_multi_setopt($mh, CURLMOPT_MAX_TOTAL_CONNECTIONS, 50);

foreach ($curlList as $handle)
{
    curl_multi_add_handle($mh, $handle);
}

do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
} while ($running > 0);

curl_multi_close($mh);

echo count($curlList) ." proxies tested.";
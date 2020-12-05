<?php
require_once(__DIR__ ."/../class/class.ProxyManager.php");

if (!isset($_GET["ip"]))
    die ("No ip specified");


$proxy = new ProxyManager();

if ($proxy->testProxy($_GET["ip"]))
    echo "Success!";
else
    echo "Failed!";
<?php
require_once(__DIR__ ."/../class/class.ProxyManager.php");

if (!isset($_GET["ip"]))
    die ("No ip specified");

$auth = "";
if (isset($_GET["auth"]))
    $auth = $_GET["auth"];


$proxy = new ProxyManager();

if ($proxy->testProxy(new Proxy($_GET["ip"], $auth)))
    echo "Success!";
else
    echo "Failed!";
<?php
require_once __DIR__."/../conf/config.php";

$url = "";
$proxy = false;
$auth = false;

if (!isset($_POST["url"])) {
	die(http_response_code(404));
}

$url = $_POST["url"];

if (isset($_POST["proxy"])) {
	$proxy = $_POST["proxy"];
}
if (isset($_POST["auth"])) {
	$auth = $_POST["auth"];
}


$path = __DIR__."/./scraper.py";
$exec = Config::PYTHON_EXECUTABLE ." $path -u ". $url;

if ($proxy) {
	if ($proxy !== "") { // If proxy is set to "", it is local ip, so skip setting up a proxy
		$exec .= " -p ". $proxy;
		if ($auth) {
			$exec .= " -a ". $auth;
		}
	}  
}
putenv("LC_CTYPE=en_US.UTF-8"); 
$source = shell_exec($exec);

echo $source;
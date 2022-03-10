<?php
require_once __DIR__."/../class/class.dbHandler.php";
require_once __DIR__."/../class/class.SourceHandler.php";
require_once __DIR__."/../conf/config.php";

const MAX_TRY = 3;
const TEST_URL = "https://www.fanfiction.net/s/11254763/1/The-Butterfly-Effect";


$count = 0;
try {
	$db = new dbHandler();
	$db->connect();
}
catch (Exception $e) {
	die($e->getMessage());
}

foreach(Config::FF2EBOOKSCRAPER_URL as $scraper) {
	echo "Testing $scraper... ";
	$working = false;
	if (testScraper($scraper)) {
		echo("Working.". PHP_EOL);
		$working = true;
	}
	else {
		echo("Not working.". PHP_EOL);
	}
	try {
		$query = $db->handler()->prepare("INSERT INTO `scraper` (`url`, `lastUpdated`, `isWorking`, `priority`) VALUES (:url, :lastUpdated, :isWorking, :priority) ON DUPLICATE KEY UPDATE `isWorking`=:isWorking, `lastUpdated`=:lastUpdated, `priority`=:priority;");
		$query->execute(Array("url" => $scraper, "lastUpdated" => time(), "isWorking" => $working, "priority" => $count));
	}
	catch (Exception $e) {
		die($e->getMessage());
	}
	$count++;
}


function testScraper(string $scraper) {
	for ($i = 0; $i <= MAX_TRY; $i++) {
		if (!SourceHandler::useFf2ebookScraper(TEST_URL, false, $scraper)) { // If null is returned, retry
			continue;
		}
		else {
			return true;
		}
	}
	return false; // If we exit the retry loop, it mean the scraper doesn't work
}

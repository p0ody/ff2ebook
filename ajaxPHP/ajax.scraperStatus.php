<?php

require_once __DIR__."/../class/class.dbHandler.php";

try {
	$db = new dbHandler();
	$db->connect();

	$query = $db->handler()->prepare("SELECT * FROM `scraper` ORDER BY `priority` ASC;");
	$query->execute();

	$results = $query->fetchAll(PDO::FETCH_ASSOC);
	$list = "";
	$count = 1;
	foreach($results as $row) {
		$icon = null;
		$color = null;
		if ($row["isWorking"]) {
			$icon = "glyphicon-circle-arrow-up";
			$color = "#0dff00";
		}
		else {
			$icon = "glyphicon-circle-arrow-down";
			$color = "#FF0000";
		}

		$timeDiff = getTimeDiff($row["lastUpdated"]);
		$status = '<span class="glyphicon '. $icon .'" style="color: '. $color .'"></span> Server #'. $count .': Last tested '. $timeDiff .' ago.<br />';
		$list .= $status;
		$count++;
	}
	echo $list;
}
catch (Exception $e) {
	die ("Error fetching scrapers status");
}


function getTimeDiff($time) {
	$timeDiff = time() - $time;
	if ($timeDiff > 60) {
		$minutes = intval($timeDiff/60) ;
		return $minutes ." ". ($minutes > 1 ? "minutes" : "minute");
	}
	return $timeDiff . " seconds";

}

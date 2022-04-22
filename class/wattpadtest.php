<?php
require_once __DIR__."/sites.wattpad.php";
require_once __DIR__."/class.ErrorHandler.php";

$error = new ErrorHandler();
$test = new WATTPAD("https://www.wattpad.com/story/194058114-alpha-moretti", $error, false);
test($test);

echo "<br />-----------------<br />";

$test2 = new WATTPAD("https://www.wattpad.com/story/304770112-if-me-and-my-friends-met-the-genshin-characterz", $error, false);
test($test2);

echo "<br />-----------------<br />";

$test3 = new WATTPAD("https://www.wattpad.com/1156687790-%F0%9D%91%BB%F0%9D%91%AC%F0%9D%91%A8%F0%9D%91%AA%F0%9D%91%AF%F0%9D%91%AC%F0%9D%91%B9%F0%9D%91%BA-%F0%9D%91%B7%F0%9D%91%AC%F0%9D%91%BB-%E2%9C%8E-%E2%80%A2-prolouge-%E2%80%A2", $error, false);
test($test3);

echo "<br />-----------------<br />";

$test4 = new WATTPAD("https://www.wattpad.com/story/237394218-the-boy-who-didn%27t-love", $error, false);
test($test4);

echo "<br />-----------------<br />";

$test5 = new WATTPAD("https://www.wattpad.com/story/307134081-seed", $error, false);
test($test5);





function test(WATTPAD $test) {
	echo $test->getURL();
	echo "<br />";
	echo $test->getFicId();
	echo "<br />";
	echo $test->getTitle();
	echo "<br />";
	echo $test->getAuthor();
	echo "<br />";
	echo $test->getAuthorProfile();
	echo "<br />";
	echo $test->getFicType();
	echo "<br />";
	echo $test->getSummary();
	echo "<br />";
	echo $test->getUpdatedDate();
	echo "<br />";
	echo $test->getPublishedDate();
	echo "<br />";
	echo $test->getChapCount();
	echo "<br />";
	echo $test->getCompleted();
}

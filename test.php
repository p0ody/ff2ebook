<?php
require_once __DIR__."/class/class.hpff.php";

$error = new ErrorHandler();

$ff = new HPFF("http://www.harrypotterfanfiction.com/viewstory.php?psid=328215", $error);

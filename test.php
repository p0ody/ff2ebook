<?php
require_once __DIR__."/class/class.SourceHandler.php";
require_once __DIR__."/class/class.ProxyManager.php";

$source = SourceHandler::useSelenium("https://m.fanfiction.net/s/2651376/1/Voldemort-s-Last-Spell");

echo $source;
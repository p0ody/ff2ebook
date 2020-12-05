<?php
define("PROXY_SOURCE", "https://api.proxyscrape.com/v2/?request=getproxies&protocol=http&timeout=2000&country=all&ssl=yes&anonymity=all&simplified=true");
define("PROXY_OUTPUT_FILE", __DIR__ ."/proxy/list.txt");
define("PROXY_OUTPUT_WORKING_JSON", __DIR__ ."/proxy/working-list.json");
define("PROXY_TEST_URL", "https://www.fanfiction.net");
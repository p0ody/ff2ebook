<?php

abstract class Config {
    const SELENIUM_MAX_TRY          = 3;
    const CURL_MAX_ASYNC            = 50;
    const PYTHON_EXECUTABLE         = "C:/Users/Maxime/AppData/Local/Programs/Python/Python39/python";
    const DOMAIN_PATH_SCRIPT        = "localhost/ff2ebook/scripts"; 
    // MySQL
    const SQL_HOST                  = "localhost";
    const SQL_USER                  = "root";
    const SQL_PASSWD                = "password";
    const SQL_DB                    = "php";
    // Proxy
    const PROXY_SOURCE              = ["https://api.proxyscrape.com/v2/?request=getproxies&protocol=http&timeout=2000&country=all&ssl=yes&anonymity=all&simplified=true",
                                      "https://spys.one/en/free-proxy-list/",
                                      "https://raw.githubusercontent.com/clarketm/proxy-list/master/proxy-list-raw.txt",
                                      "file://".__DIR__."/../proxy/proxy_list"];

    const PROXY_AUTH_SOURCE         = ["https://proxy.webshare.io/proxy/list/download/kglgxwzmfjcdolxpywkctnlbxaiprsgilcslnqps/-/http/username/direct/"];
                                      
    const PROXY_TEST_URL            = "https://www.fanfiction.net";
    const PROXY_TEST_MAX_TIME_SEC   = 10;
    const PROXY_TEST_MAX_TIME_MS    = Config::PROXY_TEST_MAX_TIME_SEC * 1000;
}

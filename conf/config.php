<?php

abstract class Config {
    const DIRECT_MAX_TRY            = 3;
    const CURL_MAX_ASYNC            = 50;
    const PYTHON_EXECUTABLE         = "";
    const DOMAIN_PATH               = ""; 
    const TIME_MAX_LAST_CHECKED     = 24*60*60; // 24 hours * 60 minutes * 60 seconds
    const REMOTE_CLOUDSCRAPER_URL   = "";
    const FF2EBOOKSCRAPER_URL       = "";
    // MySQL
    const SQL_HOST                  = "";
    const SQL_USER                  = "";
    const SQL_PASSWD                = "";
    const SQL_DB                    = "";
    // Proxy
    const PROXY_SOURCE              = [];

    const PROXY_AUTH_SOURCE         = [];
                                      
    const PROXY_TEST_URL            = "https://www.fanfiction.net";
    const PROXY_TEST_MAX_TIME_SEC   = 10;
    const PROXY_TEST_MAX_TIME_MS    = Config::PROXY_TEST_MAX_TIME_SEC * 1000;
    const PROXY_MAX_COUNT_TEST      = 50;
    // Email 
    const EMAIL_SMTP_SERVER         = "";
    const EMAIL_SMTP_PORT           = 465;
    const EMAIL_SMTP_EMAIL          = "";
    const EMAIL_SMTP_PASSWORD       = "";

}

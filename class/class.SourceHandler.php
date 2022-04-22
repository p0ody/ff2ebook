<?php
require_once __DIR__."/../conf/config.php";
require_once __DIR__."/class.ProxyManager.php";
require_once __DIR__."/class.CurlHandler.php";
require_once __DIR__."/class.dbHandler.php";

class SourceHandler {
    public static function useCurl(string $url, bool $useProxy = false): string|false {
        $proxyM = false;
        $proxy = NULL;
        if ($useProxy) {
            $proxyM = new ProxyManager();
            $proxy = $proxyM->getBestProxy();
        }
        $res = CurlHandler::get($url, $proxy);
        $source = $res["response"];
        
        if (preg_match("#<title>Attention Required! | Cloudflare</title>#si", $source) === 1) {
            if ($useProxy) {
                $proxyM->addToBlacklist($proxy);
            }
            return false;
        }

        if ($useProxy) {
            
            $info = $res["curlInfo"];
            $proxyM->updateLatency($proxy, $info['total_time'] * 1000);

            if ($source === false) {
                $proxyM->updateWorkingState($proxy, false); // Set selected proxy to not working so we dont reuse it again for next try
            }
            else {
                $proxyM->updateWorkingState($proxy, true);
            }
        }



        return $source;
    }

    public static function useCloudscraper(string $url, bool $useProxy = false, bool $useRemotely = false) {
        $proxyM = false;
        $proxy = "";
        $path = __DIR__."/../python/scraper.py";
        $exec = Config::PYTHON_EXECUTABLE ." $path -u ". $url;
        
        if ($useProxy) {
            $proxyM = new ProxyManager();
            $proxy = $proxyM->getRandomProxy();
            if ($proxy->getIP() !== "") { // If proxy is set to "", it is local ip, so skip setting up a proxy
                $exec .= " -p ". $proxy->getIP();
                if ($proxy->isAuthed()) {
                    $exec .= " -a ". $proxy->getAuth();
                }
            }  
        }
        putenv("LC_CTYPE=en_US.UTF-8");
        if ($useRemotely) { // Try remotely if true
            $source = SourceHandler::cloudscraperRemote($url, $useProxy ? $proxyM->getRandomProxy() : false);
            if (!$source) { // If an error occured, use selenium as backup
                $source = SourceHandler::useFf2ebookScraper($url, true);
            }
        }
        else {
            $source = shell_exec($exec);
        }

        if ($useProxy) {
            if (!$source) {
                $proxyM->updateWorkingState($proxy, false); // Set selected proxy to not working so we dont reuse it again for next try
                return false;
            }

            $elapsed = Config::PROXY_TEST_MAX_TIME_MS;
            if (preg_match("/<duration:([0-9]+?)>/si", $source, $match) === 1) {
                $elapsed = $match[1];
            }

            $proxyM->updateLatency($proxy, $elapsed);
            $proxyM->updateWorkingState($proxy, true);
            // Blacklist proxies that trigger Cloudflare anti bot page
            if (preg_match("#<title>Attention Required! | Cloudflare</title>#si", $source) === 1) {
                $proxyM->addToBlacklist($proxy);
                return false;
            }
        }

        // Fucking encoding....
        $goodEncoding = ["ASCII", "UTF-8"];
        $encoding = mb_detect_encoding($source, ["ASCII", "UTF-8", "Windows-1252", "ISO-8859-1"]);
        if (!in_array($encoding, $goodEncoding)) { 
            // If encoding is now ASCII or UTF-&, assume it is fucking windows-1252.  Mostly used for local testing....
            $source = mb_convert_encoding($source, "UTF-8", "Windows-1252");
        }
        
        return $source;
    }

    /**
     * @param string $url
     * @param Proxy|false $proxy
     */
    private static function cloudscraperRemote(string $url, $proxy = false) {
        $source = false;
        if ($proxy) {
            return CurlHandler::sendPost(Config::REMOTE_CLOUDSCRAPER_URL, ["url" => $url, "proxy" => $proxy->getIP(), "auth" => $proxy->getAuth()], 2000);
        }
        return CurlHandler::sendPost(Config::REMOTE_CLOUDSCRAPER_URL, ["url" => $url], 2000);
    }

    public static function useFf2ebookScraper(string $url, bool $useProxy = false, string $scraper = null) {
        $proxyM = false;
        $proxy = "";
        if (!$scraper) {
            $scraper = SourceHandler::getBestScraper();

            if (!$scraper) {
                $scraper = Config::FF2EBOOKSCRAPER_URL[0];
            }
        }
        $scraperUrl = $scraper ."/?url=". $url;
        
        if ($useProxy) {
            $proxyM = new ProxyManager();
            $proxy = $proxyM->getRandomProxy();
            if ($proxy->getIP() !== "") { // If proxy is set to "", it is local ip, so skip setting up a proxy
                $scraperUrl .= "&proxy=http://". $proxy;
            }  
        }

        return SourceHandler::useCurl($scraperUrl, false);
    }

    public static function getBestScraper() {
        try {
            $db = new dbHandler();
            $db->connect();

            $query = $db->handler()->prepare("SELECT * FROM `scraper` WHERE `isWorking`=1 ORDER BY `priority` ASC LIMIT 1;");
            $query->execute();

            if ($query->rowCount() < 1) {
                return null;
            }

            $result = $query->fetch(PDO::FETCH_ASSOC);

            return $result["url"];
        }
        catch (Exception $e) {
            return null;
        }
    }
}

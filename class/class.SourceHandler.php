<?php
require_once __DIR__."/../conf/config.php";
require_once __DIR__."/class.ProxyManager.php";

class SourceHandler {
    public static function useCurl(string $url, bool $useProxy = false) {
        $proxyM = false;
        $curl = curl_init();
        if ($useProxy) {
            $proxyM = new ProxyManager();
            $proxy = $proxyM->getBestProxy();
            curl_setopt($curl, CURLOPT_PROXY, $proxy->getIP());
            if ($proxy->isAuthed()) {
                curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy->getAuth());
            }
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);

        $source = curl_exec($curl);

        if ($useProxy) {
            if (preg_match("#<title>Attention Required! | Cloudflare</title>#si", $source) === 1) {
                $proxyM->addToBlacklist($proxy);
            }
            $info = curl_getinfo($curl);
            $proxyM->updateLatency($proxy, $info['total_time'] * 1000);
        }

        if ($source === false) {
            $proxyM->updateWorkingState($proxy, false); // Set selected proxy to not working so we dont reuse it again for next try
        }

        curl_close($curl);
        
        return $source;
    }

    public static function useSelenium(string $url, bool $useProxy = false) {
        $proxyM = false;
        $proxy = "";
        $path = __DIR__."/../python/getUrl.py";
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
        $source = shell_exec($exec);
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
}
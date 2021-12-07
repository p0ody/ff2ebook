<?php
require_once __DIR__."/class.Proxy.php";

class CurlHandler {
    /**
     * @param string $toUrl Full URL.
     * @param array $data Send and array with field to send as follow "fieldName" => value
     * @return string|bool
     */
    public static function sendPost($toUrl, $data, $timeoutMS = 20000) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_URL, $toUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, $timeoutMS);
        $cookie = "./cookie.txt";
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie); 
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie); 
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    public static function get(string $url, Proxy $proxy = NULL, $timeoutSec = 21) {
        $curl = curl_init();
        if ($proxy) {
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
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeoutSec);

        $result = curl_exec($curl);
        $return = ["response" => $result, "curlInfo" => curl_getinfo($curl)];
        curl_close($curl);
        return $return;
    }
}

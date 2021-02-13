<?php
require_once __DIR__."/class.ff.net.php";
require_once __DIR__."/class.hpff.php";
require_once __DIR__."/class.fpcom.php";
require_once __DIR__."/class.hpffa.com.php";
require_once __DIR__."/class.wattpad.com.php";
require_once __DIR__."/class.ficwad.com.php";
require_once __DIR__."/class.ErrorHandler.php";


function bypass_cf($url="null"){ //added function to pass requests to python.
    if ($url == "null"){
        return;
    }
    $command = '/bin/bash -c \'cd ../class/py/;python3 cf_curl.py '.$url." 2>&1;'";
    $source = shell_exec($command);
    return $source;
}


abstract class FanFictionSite
{
    const ERROR     = -1;
    const FFnet     = 0;
    const HPFF      = 1;
    const FPCOM     = 2;
    const HPFFA     = 3;
    const WattPad   = 4;
    const FicWad    = 5;
}


class FanFiction
{
    /** @var ErrorHandler $error */
    private $url, $ficSite, $handler, $error, $source;

    // When adding a new site, dont forget to add it in class.Utils and class.base.handler getRealURL().
    public function __construct($url, $errorHandler)
    {
        $this->error = $errorHandler;
        $this->setURL($url);
        $this->ficSite = $this->parseURL();
        $this->source = false;

        switch($this->ficSite)
        {
            case FanFictionSite::WattPad:
                $this->handler = new WattPad($this->getURL(), $this->error);
                $this->source = "wattpad";
                break;

            case FanFictionSite::FicWad:
                $this->handler = new FicWad($this->getURL(), $this->error);
                $this->source = "ficwad";
                break;

            case FanFictionSite::FFnet:
                $this->handler = new FFnet($this->getURL(), $this->error);
                $this->source = "ffnet";
                break;

            case FanFictionSite::HPFF:
                $this->handler = new HPFF($this->getURL(), $this->error);
                $this->source = "hpff";
                break;

            case FanFictionSite::FPCOM:
                $this->handler = new FPCOM($this->getURL(), $this->error);
                $this->source = "fpcom";
                break;

            case FanFictionSite::HPFFA:
                $this->handler = new HPFFA($this->getURL(), $this->error);
                $this->source = "hpffa";
                break;

            case FanFictionSite::ERROR:
                $this->error->addNew(ErrorCode::ERROR_CRITICAL, "Invalid URL");
                break;

        }
    }

    public function errorHandler() { return $this->error; }
    public function ficHandler() { return $this->handler; }

    public function getURL() { return $this->url; }
    private function setURL($url) { $this->url = $url; }


    private function parseURL()
    {
        if (strlen($this->getURL()) === 0)
            return FanFictionSite::ERROR;

        if (strpos($this->url, "fanfiction.net") !== false)
            return FanFictionSite::FFnet;

        if (strpos($this->url, "harrypotterfanfiction.com") !== false)
            return FanFictionSite::HPFF;

        if (strpos($this->url, "fictionpress.com") !== false)
            return FanFictionSite::FPCOM;

        if (strpos($this->url, "hpfanficarchive.com") !== false)
            return FanFictionSite::HPFFA;

        if (strpos($this->url, "wattpad.com") !== false)
            return FanFictionSite::WattPad;

        if (strpos($this->url, "ficwad.com") !== false)
            return FanFictionSite::FicWad;


        return FanFictionSite::ERROR;

    }

    public function getChapter($chapNum,$info=null)
    {   
        if ($this->ficHandler()->getSite() == "wattpad"){
            return $this->ficHandler()->getChapter($chapNum);
        }else{
            
            return $this->ficHandler()->getChapter($chapNum);
        }
    }

    public function getSource() { return $this->source; }

}
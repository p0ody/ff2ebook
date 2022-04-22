<?php
require_once __DIR__."/sites.ff.net.php";
require_once __DIR__."/sites.hpff.php";
require_once __DIR__."/sites.fpcom.php";
require_once __DIR__."/sites.hpffa.com.php";
require_once __DIR__."/sites.fh.com.php";
require_once __DIR__."/sites.wattpad.php";
require_once __DIR__."/class.ErrorHandler.php";

enum FanFictionSite {
    case ERROR;
    case FFNET; // fanfiction.net
    case HPFF; // harrypotterfanfiction.com/
    case FPCOM; // fictionpress.com
    case HPFFA; // hpfanficarchive.com
    case FHCOM; // fictionhunt.com
    case WATTPAD; // wattpad.com

}

class FanFiction
{
    /** @var ErrorHandler $error */
    private $url, $ficSite, $handler, $error, $source;

    // When adding a new site, dont forget to add it in class.Utils and class.base.handler getRealURL().
    public function __construct($url, $errorHandler, $waitToPopulate)
    {
        $this->error = $errorHandler;
        $this->setURL($url);
        $this->ficSite = $this->parseURL();
        $this->source = false;

        switch($this->ficSite)
        {
            case FanFictionSite::FFNET:
                $this->handler = new FFNET($this->getURL(), $this->error, $waitToPopulate);
                $this->source = "ffnet";
                break;

            case FanFictionSite::HPFF:
                $this->handler = new HPFF($this->getURL(), $this->error);
                $this->source = "hpff";
                break;

            case FanFictionSite::FPCOM:
                $this->handler = new FPCOM($this->getURL(), $this->error, $waitToPopulate);
                $this->source = "fpcom";
                break;

            case FanFictionSite::HPFFA:
                $this->handler = new HPFFA($this->getURL(), $this->error);
                $this->source = "hpffa";
                break;

            case FanFictionSite::FHCOM: // Fictionhunt is now unsupported, need to change everything since a remake of the website (No idea since when)
                return $this->error->addNew(ErrorCode::ERROR_CRITICAL, "Unsupported site.");
                /* $this->handler = new FHCOM($this->getURL(), $this->error);
                $this->source = "fhcom";
                break; */

            case FanFictionSite::WATTPAD:
                $this->handler = new WATTPAD($this->getURL(), $this->error);
                $this->source = "wattpad";
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


    private function parseURL(): FanFictionSite
    {
        if (strlen($this->getURL()) === 0)
            return FanFictionSite::ERROR;

        if (strpos($this->url, "fanfiction.net") !== false)
            return FanFictionSite::FFNET;

        if (strpos($this->url, "harrypotterfanfiction.com") !== false)
            return FanFictionSite::HPFF;

        if (strpos($this->url, "fictionpress.com") !== false)
            return FanFictionSite::FPCOM;

        if (strpos($this->url, "hpfanficarchive.com") !== false)
            return FanFictionSite::HPFFA;

        if (strpos($this->url, "fictionhunt.com") !== false)
            return FanFictionSite::FHCOM;

        if (strpos($this->url, "wattpad.com") !== false)
            return FanFictionSite::WATTPAD;


        return FanFictionSite::ERROR;

    }

    public function getChapter($chapNum): Chapter
    {
        return $this->ficHandler()->getChapter($chapNum);
    }

    public function getSource(): string { return $this->source; }

}

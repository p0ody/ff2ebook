<?php
require_once __DIR__."/class.ff.net.php";
require_once __DIR__."/class.hpff.php";
require_once __DIR__."/class.ErrorHandler.php";


abstract class FanFictionSite
{
    const ERROR     = -1;
    const FFnet     = 0;
    const HPFF      = 1;
}


class FanFiction
{
    /** @var ErrorHandler $error */
    private $url, $ficSite, $handler, $error, $source;

    public function __construct($url, $errorHandler)
    {
        $this->error = $errorHandler;
        $this->setURL($url);
        $this->ficSite = $this->parseURL();
        $this->source = false;

        switch($this->ficSite)
        {
            case FanFictionSite::FFnet:
                $this->handler = new FFnet($this->getURL(), $this->error);
                $this->source = "ffnet";
                break;

            case FanFictionSite::HPFF:
                $this->handler = new HPFF($this->getURL(), $this->error);
                $this->source = "hpff";
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


        return FanFictionSite::ERROR;

    }

    public function getChapter($chapNum)
    {
        return $this->ficHandler()->getChapter($chapNum);
    }

    public function getSource() { return $this->source; }

}
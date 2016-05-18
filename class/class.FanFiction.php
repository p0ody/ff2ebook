<?php
include_once("class.ff.net.php");
include_once("class.ErrorHandler.php");


abstract class FanFictionSite
{
    const ERROR     = -1;
    const FFnet     = 0;
}


class FanFiction
{
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


        return FanFictionSite::ERROR;

    }

    public function getChapter($chapNum)
    {
        return $this->ficHandler()->getChapter($chapNum);
    }

    public function getSource() { return $this->source; }

}
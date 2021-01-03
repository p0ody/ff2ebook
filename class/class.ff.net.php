<?php
require_once("class.base.handler.php");
require_once("class.ErrorHandler.php");
require_once("../class/class.Chapter.php");


class FFnet extends BaseHandler
{   
    function populate()
    {
        $this->setFicId($this->popFicId());

        $infosSource = $this->getPageSource(1, false);
        $this->setTitle($this->popTitle($infosSource));

        $this->setAuthor($this->popAuthor($infosSource));
        $this->setFicType($this->popFicType($infosSource));
        $this->setSummary($this->popSummary($infosSource));
        $this->setPublishedDate($this->popPublished($infosSource));
        $this->setUpdatedDate($this->popUpdated($infosSource));
        $this->setWordsCount($this->popWordsCount($infosSource));
        $this->setPairing($this->popPairing($infosSource));
        $this->setChapCount($this->popChapterCount($infosSource));
        $this->setFandom($this->popFandom($infosSource));
        $this->setCompleted($this->popCompleted($infosSource));
    }


    public function getChapter($number)
    {

        $source = $this->getPageSource($number);

        $text = false;
        $title = false;

        if (preg_match("#Chapter [0-9]+?: (.+?)<br></div><div role='main' aria-label='story content' style='font-size:1.1em;'>#si", $source, $matches) === 1)
            $title = $matches[1];
        else
            $title = "Chapter $number";



        if (preg_match("#<div style='padding:5px 10px 5px 10px;' class='storycontent nocopy' id='storycontent'>(.+?)</div>#si", $source, $matches) === 1)
            $text = $matches[1];
        else
        {   
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find chapter($number) text.");
            return false;
        }

        return new Chapter($number, $title, $text);

    }

    protected function getPageSource($chapter = 1, $mobile = true) // $mobile is weither or not we use mobile version of site. (Mobile version is faster to load)
    {
        $url = "https://". ($mobile ? "m" : "www") .".fanfiction.net/s/". $this->getFicId() ."/". $chapter;

        /*$curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // Page source seems gzip compressed, so we tell cURL to accept all encodings, otherwise the output is garbage (2019-06-20)
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_PROXY, $proxy);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);*/
        
        $source = bypass_cf($url);
        //$info = curl_getinfo($curl);
        //$proxyM->updateLatency($proxy, $info['total_time'] * 1000);

        //curl_close($curl);
        if ($source === false)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source for chapter $chapter.");

        return $source;
    }

    private function popFicId()
    {
        if (preg_match("#fanfiction.net/s/([0-9]+)#", $this->getURL(), $matches) === 1)
            return $matches[1];
        else
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find Fic ID.");
    }

    private function popTitle($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#Follow/Fav</button><b class='xcontrast_txt'>(.+?)</b>#si", $source, $matches) === 1)
            return $matches[1];
        else {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find title.");
            return "Untitled";
        }

    }

    private function popAuthor($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#By:</span> <a class='xcontrast_txt' href='/u/([0-9]+?)/.*?'>(.+?)</a>#si", $source, $matches) === 1)
        {
            $this->setAuthorProfile("https://www.fanfiction.net/u/". $matches[1]);
            return $matches[2];
        }
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find author.");
            return "No Author.";
        }
    }

    private function popFicType($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#<a class=xcontrast_txt href='.*?'>(.+?)</a><span class='xcontrast_txt icon-chevron-right xicon-section-arrow'></span><a class=xcontrast_txt href=.*?>(.+?)</a>#si", $source, $matches) === 1)
            return $matches[1] ."/". $matches[2];
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find fic type.");
            return false;
        }

    }

    private function popFandom($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#<title>.+?, a (.+?) fanfic | FanFiction</title>#si", $source, $matches) === 1)
            return $matches[1];
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find fic fandom.");
            return false;
        }

    }

    private function popSummary($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#<div style='margin-top:2px' class='xcontrast_txt'>(.+?)</div>#si", $source, $matches) === 1)
            return $matches[1];
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find summary.");
            return false;
        }

    }

    private function popPublished($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#Published: <span data-xutime='([0-9]+?)'>.*?</span>#si", $source, $matches) === 1)
            return $matches[1];
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find published date.");
            return false;
        }

    }

    private function popUpdated($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#Updated: <span data-xutime='([0-9]+?)'>.*?</span>#si", $source, $matches) === 1)
            return $matches[1];
        else
        {
            return $this->getPublishedDate();
        }
    }

    private function popWordsCount($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#- Words: (.+?) -#si", $source, $matches) === 1)
            return $matches[1];
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find words count.");
            return false;
        }
    }

    private function popChapterCount($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");


        if (preg_match_all("#<option value=.+?>#si", $source, $matches) < 1)
            return 1;
        else
            return count($matches[0]) / 2;


        // Cleaner way but sometimes ffnet doesnt show the right number of chapter...
        /*if (preg_match("#- Chapters: ([0-9]+)#si", $source, $matches) === 1)
            return $matches[1];
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find chapter count (One shot fic?).");
            return 1;
        }*/
    }

    private function popPairing($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#target='rating'>.+?</a> - .*?-  (.+?) -#si", $source, $matches) === 1)
            return $matches[1];
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find pairing (No pairing?).");
            return false;
        }
    }

    private function popCompleted($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (strpos($source, "- Status: Complete -") !== false)
            return "Completed";


        return false;
    }
}




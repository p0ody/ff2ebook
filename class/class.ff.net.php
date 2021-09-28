<?php
require_once("class.base.handler.php");
require_once("class.ErrorHandler.php");
require_once("class.Chapter.php");
require_once("class.SourceHandler.php");


class FFnet extends BaseHandler
{
    function populate($waitToPopulate = false)
    {
        $this->setFicId($this->popFicId());

        if ($waitToPopulate) {
            return;
        }

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

        if (Utils::regexOnSource("#Chapter [0-9]+?: (.+?)<br></div><div role='main' aria-label='story content' style='font-size:1.1em;'>#si", $source, $matches) === 1)
            $title = $matches[1];
        else
            $title = "Chapter $number";

        // Updated to match the recent change in page source (2019-06-20)
        if (Utils::regexOnSource("#<div.+?id='storycontent'>(.+?)</div>#si", $source, $matches) === 1)
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
        $try = 0;
        $source = false;
        // Retry 3 times before giving up
        while (!$source && $try < Config::SELENIUM_MAX_TRY) {
            $source = SourceHandler::useSelenium($url, true);
            $try++;
        }

        if (!$source) {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't get source for chapter $chapter.");
        }

        $source = preg_replace("/(<script>.+?<\/script>)/si", "", $source); // Remove javascript from source

        return $source;
    }

    private function popFicId()
    {
        if (Utils::regexOnSource("#fanfiction.net/s/([0-9]+)#", $this->getURL(), $matches) === 1)
            return $matches[1];
        else
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find Fic ID.");
    }

    private function popTitle($source)
    {
        if (strlen($source) === 0) {
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");
        }


        if (Utils::regexOnSource("#Follow/Fav</button><b class='xcontrast_txt'>(.+?)</b>#si", $source, $matches) === 1)
            return $matches[1];
        else {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find title.");
            echo $source;
            return "Untitled";
        }

    }

    private function popAuthor($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (Utils::regexOnSource("#By:</span> <a class='xcontrast_txt' href='/u/([0-9]+?)/.*?'>(.+?)</a>#si", $source, $matches) === 1)
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

        if (Utils::regexOnSource("#<a class=xcontrast_txt href='.*?'>(.+?)</a><span class='xcontrast_txt icon-chevron-right xicon-section-arrow'></span><a class=xcontrast_txt href=.*?>(.+?)</a>#si", $source, $matches) === 1)
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

        if (Utils::regexOnSource("#<title>.+?, a (.+?) fanfic | FanFiction</title>#si", $source, $matches) === 1)
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

        if (Utils::regexOnSource("#<div style='margin-top:2px' class='xcontrast_txt'>(.+?)</div>#si", $source, $matches) === 1)
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

        if (Utils::regexOnSource("#Published: <span data-xutime='([0-9]+?)'>.*?</span>#si", $source, $matches) === 1)
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

        if (Utils::regexOnSource("#Updated: <span data-xutime='([0-9]+?)'>.*?</span>#si", $source, $matches) === 1)
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

        if (Utils::regexOnSource("#- Words: (.+?) -#si", $source, $matches) === 1)
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

        $matches = 0;
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

        if (Utils::regexOnSource("#target='rating'>.+?</a> - .*? - .*? - (.+?) -#si", $source, $matches) === 1)
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




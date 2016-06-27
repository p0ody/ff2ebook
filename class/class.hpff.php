<?php
require_once __DIR__."/class.base.handler.php";
require_once __DIR__."/class.ErrorHandler.php";
require_once __DIR__."/class.Chapter.php";
require_once __DIR__."/class.Utils.php";


class HPFF extends BaseHandler
{
    private $chaptersIDs;

    function populate()
    {
        $this->setFicId($this->popFicId());

        $infosSource = $this->getPageSource();
        $this->getChaptersIDs($infosSource);
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
        $this->setCompleted(false);
    }


    public function getChapter($number)
    {

        $source = $this->getPageSource($number);

        $text = false;
        $title = false;

        if (preg_match("#<option value=\"\?chapterid=[0-9]+?\" selected >[0-9]+?\. (.+?)</option>#si", $source, $matches) === 1)
            $title = $matches[1];
        else
            $title = "Chapter $number";



        if (preg_match("#<div id='fluidtext'>(.+?)</div><br><hr>#si", $source, $matches) === 1)
        {
            $text = Utils::cleanText($matches[1]);

            if (strlen($text) === 0)
            {
                $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find chapter($number) text.");
                return false;
            }
        }
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find chapter($number) text.");
            return false;
        }

        return new Chapter($number, $title, $text);

    }

    protected function getPageSource($chapter = 0)
    {
        $url = "";
        if ($chapter > 0)
            $url = "http://www.harrypotterfanfiction.com/viewstory.php?chapterid=". $this->chaptersIDs[$chapter];
        else
            $url = "http://www.harrypotterfanfiction.com/viewstory.php?psid=". $this->getFicId();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $source = curl_exec($curl);
        curl_close($curl);

        if ($source === false)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source for chapter $chapter.");

        return $source;
    }

    private function popFicId()
    {
        if (preg_match("#harrypotterfanfiction.com/viewstory.php\?psid=([0-9]+)#si", $this->getURL(), $matches) === 1)
            return $matches[1];
        else
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find Fic ID.");
    }

    private function popTitle($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");


        if (preg_match("#(?:<a href=\"javascript:.+?psid=[0-9]+?'\">|<a href=\"\?psid=[0-9]+?\">)(.+?)</a> by.*?<a href=\"viewuser\.php\?showuid=[0-9]+?\">(.+?)</a>#si", $source, $matches) === 1)
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

        if (preg_match("#<a href=\"viewuser\.php\?showuid=([0-9]+?)\">(.+?)</a>#si", $source, $matches) === 1)
        {
            $this->setAuthorProfile("http://www.harrypotterfanfiction.com/viewuser.php?showuid=". $matches[1]);
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

        if (preg_match("#<b>Genre\(s\):</b>(.+?)<br>#si", $source, $matches) === 1)
            return $matches[1];
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find fic type.");
            return false;
        }

    }

    private function popFandom($source)
    {
        return "Harry Potter";
    }

    private function popSummary($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#class='storysummary' width=700>(.+?)</table>#si", $source, $matches) === 1)
        {
            $return = Utils::cleanText($matches[1]);
            return strip_tags($return);
        }


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

        if (preg_match("#<b>First Published:</b> (.+?)<br>#si", $source, $matches) === 1)
        {
            $date = DateTime::createFromFormat("Y.m.d", $matches[1]);
            return intval($date->format("U"));
        }
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

        if (preg_match("#<b>Last Updated:</b> (.+?)<br />#si", $source, $matches) === 1)
        {
            $date = DateTime::createFromFormat("Y.m.d", $matches[1]);
            return intval($date->format("U"));
        }
        else
        {
            return $this->getPublishedDate();
        }
    }

    private function popWordsCount($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#<b>Words:</b> ([0-9]+?)<br>#si", $source, $matches) === 1)
            return $matches[1];
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find words count.");
            return false;
        }
    }

    private function popChapterCount($source)
    {
        return count($this->chaptersIDs);
    }

    private function popPairing($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#<b>Characters:</b> (.+?) <br>#si", $source, $matches) === 1)
            return $matches[1];
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find pairing (No pairing?).");
            return false;
        }
    }

    private function getChaptersIDs($source)
    {
        if (preg_match_all("#<option value=\"\?chapterid=([0-9]+?)\">.+?</option>#si", $source, $matches) !== false)
        {
            $this->chaptersIDs = Array();
            foreach($matches[1] as $key => $value)
            {
                $this->chaptersIDs[$key + 1] = $value;
            }
        }
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find chapters IDs.");
            return false;

        }
    }
}




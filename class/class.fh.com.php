<?php
require_once("class.base.handler.php");
require_once("class.ErrorHandler.php");
require_once("../class/class.Chapter.php");


class FHCOM extends BaseHandler
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
        $title = $title = "Chapter $number";;


        if (preg_match("#<div class=\"text \">(.+?)</div>#si", $source, $matches) === 1)
            $text = $matches[1];
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find chapter($number) text.");
            return false;
        }

        return new Chapter($number, $title, $text);

    }

    protected function getPageSource($chapter = 1)
    {
        $url = "http://www.fictionhunt.com/read/". $this->getFicId() ."/". $chapter;

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
        if (preg_match("#fictionhunt.com/read/([0-9]+)#", $this->getURL(), $matches) === 1)
            return $matches[1];
        else
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find Fic ID.");
    }

    private function popTitle($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#<div class=\"title\">(.*?)</div>#si", $source, $matches) === 1)
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

        if (preg_match("#by <a href=\"(.+?)\">(.+?)</a>#si", $source, $matches) === 1)
        {
            $this->setAuthorProfile($matches[1]);
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

        if (preg_match("#- English - (.*?) - Chapters:#si", $source, $matches) === 1)
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
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, "https://www.fanfiction.net/s/". $this->getFicId());
        $ffnet = curl_exec($curl);
        curl_close($curl);



        if (strlen($ffnet) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#<div style='margin-top:2px' class='xcontrast_txt'>(.+?)</div>#si", $ffnet, $matches) === 1)
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

        if (preg_match("#- Published: (.+?) -#si", $source, $matches) === 1)
        {
            $date = DateTime::createFromFormat("d-m-Y H:i:s", $matches[1] ." 00:00:00");
            if (!$date)
                return false;

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

        if (preg_match("#- Updated: (.+?) -#si", $source, $matches) === 1)
        {
            $date = DateTime::createFromFormat("d-m-Y H:i:s", $matches[1] ." 00:00:00");
            if (!$date)
                return false;

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


        if (preg_match("#- Chapters: ([0-9]+?) -#si", $source, $matches) === 1)
            return $matches[1];
        else
            return 1;
    }

    private function popPairing($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#<div class=\"details\">(.+?) - Words#si", $source, $matches) === 1)
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

        if (strpos($source, "- Complete -") !== false)
            return "Completed";


        return false;
    }
}




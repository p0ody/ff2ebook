<?php
require_once __DIR__."/class.base.handler.php";
require_once __DIR__."/class.ErrorHandler.php";
require_once __DIR__."/class.Chapter.php";
require_once __DIR__."/class.Utils.php";


class HPFFA extends BaseHandler
{
    function populate()
    {
        $this->setFicId($this->popFicId());

        $infosSource = $this->getPageSource();
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
        $this->setCompleted($this->popStatus($infosSource));
    }


    public function getChapter($number)
    {

        $source = $this->getPageSource($number);

        $text = false;
        $title = false;

        if (preg_match("#<option value='[0-9]' selected>[0-9]+?\. (.+?)</option>#si", $source, $matches) === 1)
            $title = $matches[1];
        else
            $title = "Chapter $number";



        if (preg_match("#<!-- STORY START -->(.+?)<!-- STORY END -->#si", $source, $matches) === 1)
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
            $url = "http://www.hpfanficarchive.com/stories/viewstory.php?sid=". $this->getFicId() ."&chapter=". $chapter;
        else
            $url = "http://www.hpfanficarchive.com/stories/viewstory.php?sid=". $this->getFicId();

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
        if (preg_match("#hpfanficarchive\.com/stories/viewstory.php\?sid=([0-9]+)#si", $this->getURL(), $matches) === 1)
            return $matches[1];
        else
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find Fic ID.");
    }

    private function popTitle($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");


        if (preg_match("#<!-- TITLE START --><a href=\"viewstory\.php\?sid=[0-9]+\">(.+?)</a><!-- TITLE END -->#si", $source, $matches) === 1)
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

        if (preg_match("#<!-- AUTHOR START --><a href=\"viewuser\.php\?uid=([0-9]+?)\">(.+?)</a><!-- AUTHOR END -->#si", $source, $matches) === 1)
        {
            $this->setAuthorProfile("http://www.hpfanficarchive.com/stories/viewuser.php?uid=". $matches[1]);
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

        if (preg_match("#Genres: (.+?)<span class='label'>#si", $source, $matches) === 1)
            return strip_tags($matches[1]);
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

        if (preg_match("#<!-- SUMMARY START -->(.+?)<!-- SUMMARY END -->#si", $source, $matches) === 1)
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

        if (preg_match("#<!-- PUBLISHED START -->(.+?)<!-- PUBLISHED END -->#si", $source, $matches) === 1)
        {
            $date = DateTime::createFromFormat("F d, Y H:i:s", $matches[1] ." 00:00:00");
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

        if (preg_match("#<!-- UPDATED START -->(.+?)<!-- UPDATED END -->#si", $source, $matches) === 1)
        {
            $date = DateTime::createFromFormat("F d, Y H:i:s", $matches[1] ." 00:00:00");
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

        if (preg_match("#<!-- WORDCOUNT START -->(.+?)<!-- WORDCOUNT END -->#si", $source, $matches) === 1)
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

        if (preg_match("#Chapters: </span> ([0-9]+?) <span class=\"label\">#si", $source, $matches) === 1)
            return $matches[1];
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find chapter count.");
            return false;
        }
    }

    private function popPairing($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#Pairings: (.+?)<span class='label'>#si", $source, $matches) === 1)
            return strip_tags($matches[1]);
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find pairing (No pairing?).");
            return false;
        }
    }

    private function popStatus($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#Status: </span> (.+?)<span class='label'>#si", $source, $matches) === 1)
            return strip_tags($matches[1]);
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find story status.");
            return false;
        }

    }
}




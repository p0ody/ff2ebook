<?php
require_once __DIR__."/class.Chapter.php";
require_once __DIR__."/class.ErrorHandler.php";

abstract class BaseHandler
{
    private $url;
    /** @var  ErrorHandler $error */
    private $error;
    private $ficId;
    private $title;
    private $author;
    private $ficType;
    private $summary;
    private $published;
    private $updated;
    private $wordsCount;
    private $chapCount;
    private $chapSource;
    private $outputDir;
    private $filename;
    private $fandom;
    private $authorProfile;
    private $completed;
    private $addInfos;


    /** @return Chapter */
    abstract public function getChapter($number);
    abstract protected function getPageSource($chapter);
    abstract protected function populate($waitToPopulate = false);

    public function __construct($url, $errorHandler, $waitToPopulate = false)
    {
        $this->url = $url;
        $this->error = $errorHandler;

        $this->populate($waitToPopulate);

        $this->chapSource = Array();

        $this->filename = false;

    }

    // Getters
    public function errorHandler()              { return $this->error; }
    public function getURL()                    { return $this->url; }
    public function getFicId()                  { return $this->ficId; }
    public function getTitle()                  { return $this->title; }
    public function getAuthor()                 { return $this->author; }
    public function getFicType()                { return $this->ficType; }
    public function getSummary()                { return $this->summary; }
    public function getPublishedDate()          { return $this->published; }
    public function getUpdatedDate()            { return $this->updated; }
    public function getWordsCount()             { return $this->wordsCount; }
    public function getChapCount()              { return $this->chapCount; }
    public function getOutputDir()              { return $this->outputDir; }
    public function getFilename()               { return $this->filename; }
    public function getFandom()                 { return $this->fandom; }
    public function getAuthorProfile()          { return $this->authorProfile; }
    public function getCompleted()              { return $this->completed; }
    public function getAddInfos()               { return $this->addInfos; }

    // Setters
    public function setFicId($id)               { $this->ficId = $id; }
    public function setTitle($title)            { $this->title = trim($title); }
    public function setAuthor($author)          { $this->author = trim($author); }
    public function setFicType($ficType)        { $this->ficType = trim($ficType); }
    public function setSummary($summary)        { $this->summary = trim($summary); }
    public function setPublishedDate($pub)      { $this->published = $pub; }
    public function setUpdatedDate($updated)    { $this->updated = $updated; }
    public function setWordsCount($words)       { $this->wordsCount = $words; }
    public function setChapCount($chapCount)    { $this->chapCount = $chapCount; }
    public function setOutputDir($dir)          { $this->outputDir = $dir; }
    public function setFileName($name)          { $this->filename = $name; }
    public function setFandom($fandom)          { $this->fandom = trim($fandom); }
    public function setAuthorProfile($profile)  { $this->authorProfile = $profile; }
    public function setCompleted($complete)     { $this->completed = $complete; }
    public function setAddInfos($infos)         { $this->addInfos = trim($infos); }

    public function getRealURL()
    {
        if (strpos($this->url, "fanfiction.net") !== false)
            return "https://www.fanfiction.net/s/". $this->getFicId();

        if (strpos($this->url, "harrypotterfanfiction.com") !== false)
            return "http://www.harrypotterfanfiction.com/viewstory.php?psid=". $this->getFicId();

        if (strpos($this->url, "fictionpress.com") !== false)
            return "https://www.fictionpress.com/s/". $this->getFicId();

        if (strpos($this->url, "hpfanficarchive.com") !== false)
            return "http://www.hpfanficarchive.com/stories/viewstory.php?sid=". $this->getFicId();

        if (strpos($this->url, "fictionhunt.com") !== false)
            return "http://fictionhunt.com/read/". $this->getFicId();
    }

}

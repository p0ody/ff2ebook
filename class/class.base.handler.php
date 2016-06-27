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
    private $pairing;
    private $chapCount;
    private $chapSource;
    private $outputDir;
    private $filename;
    private $fandom;
    private $authorProfile;
    private $completed;


    /** @return Chapter */
    abstract public function getChapter($number);
    abstract protected function getPageSource($chapter);
    abstract protected function populate();

    public function __construct($url, $errorHandler)
    {
        $this->url = $url;
        $this->error = $errorHandler;

        $this->populate();

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
    public function getPairing()                { return $this->pairing; }
    public function getChapCount()              { return $this->chapCount; }
    public function getOutputDir()              { return $this->outputDir; }
    public function getFilename()               { return $this->filename; }
    public function getFandom()                 { return $this->fandom; }
    public function getAuthorProfile()          { return $this->authorProfile; }
    public function getCompleted()              { return $this->completed; }

    // Setters
    public function setFicId($id)               { $this->ficId = $id; }
    public function setTitle($title)            { $this->title = $title; }
    public function setAuthor($author)          { $this->author = $author; }
    public function setFicType($ficType)        { $this->ficType = $ficType; }
    public function setSummary($summary)        { $this->summary = $summary; }
    public function setPublishedDate($pub)      { $this->published = $pub; }
    public function setUpdatedDate($updated)    { $this->updated = $updated; }
    public function setWordsCount($words)       { $this->wordsCount = $words; }
    public function setPairing($pairing)        { $this->pairing = $pairing; }
    public function setChapCount($chapCount)    { $this->chapCount = $chapCount; }
    public function setOutputDir($dir)          { $this->outputDir = $dir; }
    public function setFileName($name)          { $this->filename = $name; }
    public function setFandom($fandom)          { $this->fandom = $fandom; }
    public function setAuthorProfile($profile)  { $this->authorProfile = $profile; }
    public function setCompleted($complete)     { $this->completed = $complete; }

    public function getRealURL()
    {
        if (strpos($this->url, "fanfiction.net") !== false)
            return "https://www.fanfiction.net/s/". $this->getFicId();

        if (strpos($this->url, "harrypotterfanfiction.com") !== false)
            return "http://www.harrypotterfanfiction.com/viewstory.php?psid=". $this->getFicId();

        if (strpos($this->url, "fictionpress.com") !== false)
            return "https://www.fictionpress.com/s/". $this->getFicId();
    }

}
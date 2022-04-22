<?php
require_once __DIR__."/class.Chapter.php";
require_once __DIR__."/class.ErrorHandler.php";

abstract class BaseHandler
{
	private string $url;
	private ErrorHandler $error;
	private int|string|null $ficId;
	private ?string $title;
	private ?string $author;
	private ?string $ficType;
	private ?string $summary;
	private ?int $published;
	private ?int $updated;
	private ?int $wordsCount = 0;
	private ?int $chapCount;
	private ?array $chapSource;
	private ?string $outputDir;
	private ?string $filename;
	private ?string $fandom;
	private ?string $authorProfile;
	private ?string $completed;
	private ?string $addInfos;


	abstract public function getChapter(int $number): ?Chapter;
	abstract protected function getPageSource(int $chapter): ?string;
	abstract protected function populate(bool $waitToPopulate = false): void;

	public function __construct(string $url, ErrorHandler $errorHandler, bool $waitToPopulate = false)
	{
		$this->setUrl($url);
		$this->error = $errorHandler;

		$this->populate($waitToPopulate);

		$this->chapSource = Array();

		$this->filename = null;

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
	public function setFicId(int $id)                   { $this->ficId = $id; }
	public function setUrl(string $url)                 { $this->url = $url; }
	public function setTitle(string $title)             { $this->title = trim($title); }
	public function setAuthor(string $author)           { $this->author = trim($author); }
	public function setFicType(string $ficType)         { $this->ficType = trim($ficType); }
	public function setSummary(string $summary)         { $this->summary = trim($summary); }
	public function setPublishedDate(int $pub)          { $this->published = $pub; }
	public function setUpdatedDate(int $updated)        { $this->updated = $updated; }
	public function setWordsCount(int $words)           { $this->wordsCount = $words; }
	public function setChapCount(int $chapCount)        { $this->chapCount = $chapCount; }
	public function setOutputDir(string $dir)           { $this->outputDir = $dir; }
	public function setFileName(string $name)           { $this->filename = $name; }
	public function setFandom(?string $fandom)          { $this->fandom = $fandom ? trim($fandom) : null; }
	public function setAuthorProfile(string $profile)   { $this->authorProfile = $profile; }
	public function setCompleted(?string $complete)     { $this->completed = $complete; }
	public function setAddInfos(?string $infos)         { $this->addInfos = $infos ? trim($infos) : null; }

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

		if (strpos($this->url, "wattpad.com") !== false)
			return "https://www.wattpad.com/story/". $this->getFicId();

	}

}

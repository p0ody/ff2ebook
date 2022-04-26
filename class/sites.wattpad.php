<?php
require_once __DIR__."/class.base.handler.php";
require_once __DIR__."/class.ErrorHandler.php";
require_once __DIR__."/class.Chapter.php";
require_once __DIR__."/class.SourceHandler.php";
require_once __DIR__."/class.Utils.php";
require_once __DIR__."/class.FanFiction.php";

// wattpad.com

class WATTPAD extends BaseHandler
{
	private const USEPROXY = false;
	private ?array $chaptersInfo = null; // Will be getting chapter IDs and title while getting fic infos because wattpad use weird url for chapters 
	function populate(bool $waitToPopulate = false): void
	{
		$this->setFicId($this->popFicId());

		if ($waitToPopulate) {
			return;
		}

    	$infosSource = $this->getPageSource(); 
		$this->setTitle($this->popTitle($infosSource));

 		$this->setAuthor($this->popAuthor($infosSource));
		$this->setFicType($this->popFicType($infosSource));
		$this->setSummary($this->popSummary($infosSource));
		$this->setPublishedDate($this->popPublished($infosSource));
		$this->setUpdatedDate($this->popUpdated($infosSource));
		//$this->setWordsCount($this->popWordsCount($infosSource)); // Will count words on each chapter lookup
		$this->setChapCount($this->popChapterCount($infosSource));
		$this->setFandom($this->popFandom($infosSource));
		$this->setCompleted($this->popCompleted($infosSource));
		$this->setAddInfos($this->popAddInfos($infosSource)); 
	}
	
	public function getChapter(int $number): ?Chapter
	{

		$source = $this->getPageSource($number);
		if (Utils::regexOnSource("#<title>(.+?)</title><chapText>(.+?)</chapText>#si", $source, $matches) === 1) {
			$this->setWordsCount($this->getWordsCount() + str_word_count($matches[2]));
			return new Chapter($number, $matches[1], $matches[2]);
		}


		$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find chapter($number) text.");
		return null;
	}

	protected function getPageSource(int $chapter = 0): ?string
	{        
		$source = null;

		if ($chapter == 0) {
			$url = $this->getURL();
			$source = SourceHandler::useCurl($url, $this::USEPROXY);
		} else {
			// I found out i can use m.wattpad.com to get text from chapters instead of having to scroll the whole page to get each part to load.
			// It is still in parts, but at least i can use curl.
			$source = "<title>". $this->chaptersInfo[$chapter]->title ."</title><chapText>". $this->getChapterParts($chapter) ."</chapText>";
		}
		
		
		if (!$source) {
			$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source for chapter $chapter.");
			return null;
		}

		$source = preg_replace("/(<script>.+?<\/script>)/si", "", $source); // Remove javascript from source

		return $source;
	}

	private function getChapterParts(int $chapNum): ?string {
		if (!$this->chaptersInfo[$chapNum]) {
			return null;
		}

		$baseURL = "https://m.wattpad.com/" . $this->chaptersInfo[$chapNum]->urlID . "?m=";
		$parts = [];
		$numPart = 1;
		$chapter = "";
		
		$part1 = SourceHandler::useCurl($baseURL ."1", $this::USEPROXY);
		if (!$part1) {
			return null;
		}
		if (preg_match("#<title>.*?\(p.[0-9]+? of ([0-9]+?)\).*?</title>#si", $part1, $matches) === 1) {
			$numPart = intval($matches[1]);
		}

		$chapter .= $this->getChapterPartsText($part1);

		for ($i = 2; $i <= $numPart; $i++) {
			$partTemp = SourceHandler::useCurl($baseURL . $i, $this::USEPROXY);
			
			if (!$partTemp) {
				return null;
			}

			$chapter .= $this->getChapterPartsText($partTemp);
		}

		return $chapter;
	}

	/** Extract text from HTML of chapter parts */
	private function getChapterPartsText(string $html): ?string {
		if (preg_match("#<p class=\"t\">.*?</p><p class=\"t\">(.+?)</p>#si", $html, $matches) === 1) {
			return $matches[1];
		}

		return null;
	}

	private function popFicId(): ?int
	{
		$this->popChaptersInfo($this->getURL());

		if (preg_match("#/story/([0-9]+)#si", $this->getURL(), $matches) === 1) {
			return $matches[1];
		}

		$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Coudln't find fic ID.");
		return null;
	}

     private function popTitle($source): string
	{
		if (strlen($source) === 0) {
			$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");
		}


		if (Utils::regexOnSource("#<div class=\"story-info\"><span class=\"sr-only\">(.+?)</span>#si", $source, $matches) === 1) {
			return Utils::removeEmoji($matches[1]);
		}
		else {
			$this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find title.");
			return "Untitled";
		}

	}

	private function popAuthor(?string $source): string
	{
		if (!$source)
			$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

		if (Utils::regexOnSource("#<a href=\"/user/(.+?)\" class=\"on-navigate\" aria-label=\".*?\">(.+?)</a>#si", $source, $matches) === 1)
		{
			$this->setAuthorProfile("https://www.wattpad.com/user/". $matches[1]);
			return $matches[2];
		}
		else
		{
			$this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find author.");
			return "No Author.";
		}
	}

	private function popFicType(?string $source): ?string
	{
		if (!$source)
			$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

		if (Utils::regexAllOnSource("#<a class=\"tag-pill__3Z7X_\".*?href=\"/stories/.+?\">(.+?)</a>#si", $source, $matches) >= 1) {
			$str = "";
			foreach ($matches[1] as $tags) {
				$str .= "( $tags ) ";
			}

			return $str;
		}
		else
		{
			$this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find fic type.");
			return false;
		}

	}

	private function popFandom(?string $source): string
	{
		return "Unknown";
	}

	private function popSummary(?string $source): string
	{
		if (!$source)
			$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

		if (Utils::regexOnSource("#<pre class=\"description-text\">(.*?)</pre>#si", $source, $matches) === 1)
			return html_entity_decode($matches[1]);
		else
		{
			$this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find summary.");
			return "";
		}

	}

	private function popPublished(?string $source): int
	{

		if (strlen($source) === 0)
			$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

		if (Utils::regexOnSource("#<span class=\"sr-only\">.*?First published (.+?) ([0-9]+?)(?:, ([0-9]+?))*?</span>#si", $source, $matches) === 1) {
			if (!isset($matches[3])) { // If the year is not found, assume that the year is the current year
				$matches[3] = (new DateTime())->format("Y");
			}
			$date = DateTime::createFromFormat("d M Y", $matches[2] ." ". $matches[1] ." ". $matches[3]);
			return $date->getTimestamp();
		}


		$this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find published date.");
		return 0;
	}

	private function popUpdated(?string $source)
	{
		if (!$source)
			$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

		// Look for date like: Aug 18, 2020 or Aug 18
		if (Utils::regexOnSource("#<span class=\"table-of-contents__last-updated\">Last updated <strong>(.+?) ([0-9]+?)(?:, ([0-9]+?))*?</strong>#si", $source, $matches) === 1) {
			if (!isset($matches[3])) { // If the year is not found, assume that the year is the current year
				$matches[3] = (new DateTime())->format("Y");
			}
			$date = DateTime::createFromFormat("d M Y", $matches[2] . " " . $matches[1] . " " . $matches[3]);
			return $date->getTimestamp();
		}

		// Look for date like 7 days ago
		if (Utils::regexOnSource("#<span class=\"table-of-contents__last-updated\">Last updated <strong>([0-9]+?|an) days ago</strong>#si", $source, $matches) === 1) {
			$now = new DateTime();
			$now->sub(new DateInterval("P" . $matches[1] . "D")); // Substract the number of days for current date
			return $now->getTimestamp();
		}

		// Look for date like 2 hours ago or an hour ago
		if (Utils::regexOnSource("#<span class=\"table-of-contents__last-updated\">Last updated <strong>([0-9]+?|an) hours*? ago</strong>#si", $source, $matches) === 1) {
			if ($matches[1] == "an") {
				$matches[1] = 1;
			}

			$now = new DateTime();
			$now->sub(new DateInterval("PT". $matches[1] ."H")); // Substract the number of hours for current date
			return $now->getTimestamp();
		}

		return $this->getPublishedDate();
	}

	private function popWordsCount(?string $source): int
	{
		if (!$source)
			$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

		if (Utils::regexOnSource("#- Words: (.+?) -#si", $source, $matches) === 1)
			return $matches[1];
		else
		{
			$this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find words count.");
			return false;
		}
	}

	private function popChapterCount(?string $source): int
	{
		return count($this->chaptersInfo);
	}

	private function popAddInfos(?string $source): ?string
	{
		// No additional infos for now
		return null;
	}

	private function popCompleted(?string $source): ?string
	{
		if (!$source)
			$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

		if (Utils::regexOnSource("#Complete, First published#si", $source, $matches) === 1) {
			return "Completed";
		}
			
		return null;
	}

	private function popChaptersInfo(string $url, ?string $source = null): void {
		if ($this->chaptersInfo !== null) { // If chapter list is already populated, do nothing
			return;
		}

		if (!$source) {
			$source = SourceHandler::useCurl($url, $this::USEPROXY);
			if (!$source) {
				$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't fetch source.");
			}
		}

		if ($this->isTitlePage($url)) {
			if (!preg_match_all("#<li><a href=\"/([0-9]+).*?\" class=\"story-parts__part\"><div class=\"part__label\">(.+?)</div></a></li>#si", $source, $matches, PREG_SET_ORDER)) {
				$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't fetch source.");
				return;
			}

			foreach($matches as $key => $match) {
				$chapterList[$key + 1] = new ChapterInfo($match[1], $match[2]);
			}
			
			// Split the array in two because the abore regex also get the link in mobile menu.
			$halfedArray = array_slice($chapterList, 0, count($chapterList)/2, true);
			
			$this->chaptersInfo = $halfedArray;
		}
		else {
			if (Utils::regexOnSource("#<h6><a class=\"on-navigate\" href=\"/story\/([0-9]+)#si", $source, $matches) === 1)
			{
				$this->setFicId($matches[1]);
				$this->setUrl($this->getRealURL());
				$this->popChaptersInfo($this->getRealURL());
				return;
			}

		}
	}

	private function isTitlePage(string $url): bool {
		return str_contains($url, "/story/");
	}
}


class ChapterInfo {
	public int $urlID = 0;
	public string $title = "";

	function __construct(int $urlId, string $title) {
		$this->urlID = $urlId;
		$this->title = $title;
	}
}

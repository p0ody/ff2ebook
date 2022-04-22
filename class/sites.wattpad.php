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
	private ?array $chapterUrls = null; // Get All chapter url list ASAP because wattpad use weird url for their chapters
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

		$text = false;
		$title = false;

		if (Utils::regexOnSource("#<h1 class=\"h2\">(.+?)</h1>#si", $source, $matches) === 1)
			$title = $matches[1];
		else
			$title = "Chapter $number";

		if (Utils::regexOnSource("#<pre>(.+?)</pre>#si", $source, $matches) === 1) {
			$text = $matches[1];
			$this->setWordsCount($this->getWordsCount() + str_word_count($text)); // Add chapter words count to total words count
		}
		else 
		{
			$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find chapter($number) text.");
			return null;
		}

		return new Chapter($number, $title, $text);

	}

	protected function getPageSource(int $chapter = 0): ?string
	{        
		$url = $this->chapterUrls[$chapter];
		$source = SourceHandler::useCurl($url, false);

		if (!$source) {
			$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source for chapter $chapter.");
			return null;
		}

		$source = preg_replace("/(<script>.+?<\/script>)/si", "", $source); // Remove javascript from source

		return $source;
	}

	private function popFicId(): ?int
	{
		$this->popChapterUrl($this->getURL());

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
			return $matches[1];
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
			return $matches[1];
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
		return count($this->chapterUrls) - 1; // Substract one because the title page is added at pos. 0 in array.
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

	private function popChapterUrl(string $url, ?string $source = null): void {
		if ($this->chapterUrls !== null) { // If chapter list is already populated, do nothing
			return;
		}

		if (!$source) {
			$source = SourceHandler::useCurl($url);
			if (!$source) {
				$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't fetch source.");
			}
		}

		if ($this->isTitlePage($url)) {
			Utils::regexAllOnSource("#<li><a href=\"\/(.+?)\" class=\"story-parts__part\">#si", $source, $matches);
			
			// Split the array in two because the abore regex also get the link in mobile menu.
			$halfedArray = array_slice($matches[1], 0, count($matches[1])/2, true);
			$urlList = [];
			foreach ($halfedArray as $key => $value) {
				$urlList[$key] = Utils::webSourceURL("wattpad") ."/". $value;
			}
			$this->chapterUrls = [$url, ...$urlList];
		}
		else {
			if (Utils::regexOnSource("#<h6><a class=\"on-navigate\" href=\"/story\/([0-9]+)#si", $source, $matches) === 1)
			{
				$this->setFicId($matches[1]);
				$this->setUrl($this->getRealURL());
				$this->popChapterUrl($this->getRealURL());
				return;
			}

		}
	}

	private function isTitlePage(string $url): bool {
		return str_contains($url, "/story/");
	}
}

<?php
require_once __DIR__."/class.base.handler.php";
require_once __DIR__."/class.ErrorHandler.php";
require_once __DIR__."/class.Chapter.php";
require_once __DIR__."/class.Utils.php";


class FicWad extends BaseHandler
{
    function populate()
    {

        
        $this->setFicId($this->popFicId());

        $infosSource = $this->getPageSource();
        $infos = $this->getInfos($this->popFicId());
        

        $this->setTitle($this->popTitle($infosSource));
        $this->setAuthor($this->popAuthor($infosSource));
        $this->setFicType($this->popFicType($infosSource));
        $this->setSummary($this->popSummary($infosSource));
        $this->setPublishedDate($this->popPublished($infosSource));
        $this->setUpdatedDate($this->popUpdated($infosSource));
        $this->setWordsCount($this->popWordsCount($infosSource));
        $this->setPairing($this->popPairing($infosSource));
        if ($infos["mode"] == "multi"){
            $this->setChapCount($this->popChapterCount($infosSource));
        }else{
            $this->setChapCount(1);
        }
        $this->setFandom($this->popFandom($infosSource));
        $this->setCompleted($this->popStatus($infosSource));
        $_SESSION["infos"] = $infos;
    }

    public function getSite(){
        return "ficwad";
    }

    public function getChapter($number)
    {

        
        $infos = $_SESSION["infos"];

        if ($infos["mode"] == "multi"){
            $id = $infos["chapters"][$number-1];
            $source = $this->getPageSource($id);
        }else{
            $source = $this->getPageSource($this->popFicId());
            $id = $this->popFicId();
        }

        $text = false;
        $title = false;




        #<a href="/story/%link%">%name%</a>
        if (preg_match("#<a href=\"/story/.*?\">(.+?)</a>#si", $source, $matches) === 1)
            $title = $matches[1];
        else
            $title = "Chapter $number";



        if (preg_match("#</div><div id=\"storytext\" class=\"pure-u-1\">(.+?)</div><div class=\"pure-u-1 story-chapters\">#si", $source, $matches) === 1)
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


    protected function getInfos($storyid=0){

        if ($storyid != 0){
            $source = $this->getPageSource($storyid);
            $source = preg_replace(array('/ {2,}/','/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'),array(' ',''),$source);
            $ficinfo = [];

            if (strpos($source, 'id="chapters"') !== false) {
                $ficinfo["mode"] = "multi";
                $ficinfo["chapters"] = [];

                if (preg_match("#<\/span> - Chapters:&nbsp;([0-9]+?) - Published#si", $source, $matches) === 1){
                    $total = $matches[1];

                }

                if (preg_match_all("#<h4><a href=\"\/story\/(.+?)\">.*?<\/a><\/h4>#si", $source, $matches2)){
                    $chapters = $matches2[1];
                }
                else
                {
                    $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find fandom.");
                    return false;
                }


                for ($i=0; $i < $total; $i++) { 
                    $ficinfo["chapters"][$i] = $chapters[$i+1];
                }

            }else{
                $ficinfo["mode"] = "single";
            }

        return $ficinfo;

        }else{
                $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get Fic ID.");
                return false;
        }

    }

    protected function getPageSource($storyid = 0)
    {
        $url = "";
        if ($storyid > 0){

            $url = "https://ficwad.com/story/". $storyid;
            $id = $storyid;
        }
        else{
            $url = "https://ficwad.com/story/". $this->getFicId();
            $id = $this->getFicId();
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $source = curl_exec($curl);
        curl_close($curl);

        if ($source === false)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source for ".$id);

        return $source;
    }

    private function popFicId()
    {
        #https://ficwad.com/story/178350
        if (preg_match("#ficwad\.com/story/([0-9]+)#si", $this->getURL(), $matches) === 1)
            return $matches[1];
        else
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find Fic ID FicWad. ".$this->getURL());
    }

    private function popTitle($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        $source = preg_replace(array('/ {2,}/','/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'),array(' ',''),$source);


        $regexp = "#<h4><a href=\"/story/.*?\">(.+?)</a></h4>#si";

        if (preg_match($regexp, $source, $matches)){
            return $matches[1];
        }
        else {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find title.");
            return "Untitled";
        }

    }

    private function popAuthor($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");


        #<a href="/a/Sassy">Sassy</a>
        $source = preg_replace(array('/ {2,}/','/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'),array(' ',''),$source);
        $regexp = "#<a href=\"\/a\/.*?\">(.+?)</a>#si";

        if (preg_match($regexp, $source, $matches)){
            return $matches[1];
        }
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find author.");
            return "No Author.";
        }
    }

    private function popFicType($source)
    {

        #<a href="/category/.*?">(.+?)</a>
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        $source = preg_replace(array('/ {2,}/','/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'),array(' ',''),$source);
        $regexp = "#<a href=\"\/category\/.*?\">(.+?)</a>#si";

        if (preg_match($regexp, $source, $matches)){
            return $matches[1]." FanFiction";
        }
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find fic type.");
            return false;
        }

    }

    private function popFandom($source)
    {
        
        #<a href="/category/.*?">(.+?)</a>
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        $source = preg_replace(array('/ {2,}/','/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'),array(' ',''),$source);
        $regexp = "#<a href=\"\/category\/.*?\">(.+?)</a>#si";

        if (preg_match_all($regexp, $source, $matches)){
            return $matches[1][2];
        }
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find fandom.");
            return false;
        }
    }

    private function popSummary($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source.");

        if (preg_match("#<blockquote class=\"summary\"><p>(.+?)</p></blockquote>#si", $source, $matches) === 1)
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

        if (preg_match("#Published:&nbsp;<span data-ts=\"(.+?)\" title=\"#si", $source, $matches) === 1)
        {
            return $matches[1];
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

        if (preg_match("#</span> - Updated:&nbsp;<span data-ts=\"(.+?)\" title=#si", $source, $matches) === 1)
        {
            return $matches[1];
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

        if (preg_match("#Updated:&nbsp;.*? - (.+?)&nbsp;words</p>#si", $source, $matches) === 1)
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

        if (preg_match("#</span> - Chapters:&nbsp;([0-9]+?) - Published#si", $source, $matches) === 1)
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

        if (preg_match("#class=\"story-characters\">Characters:&nbsp;(.+?)</span>#si", $source, $matches) === 1)
            return strip_tags($matches[1]);
        else
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find pairing (No pairing?).");
            return false;
        }
    }

    private function popStatus($source)
    {
        return false;

    }
}



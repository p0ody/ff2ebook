<?php
require_once __DIR__."/class.base.handler.php";
require_once __DIR__."/class.ErrorHandler.php";
require_once __DIR__."/class.Chapter.php";
require_once __DIR__."/class.Utils.php";


class WattPad extends BaseHandler
{
    private $chaptersIDs;

    function populate()
    {
        if (strpos($this->getURL(), '/story/') == false) {
            $this->url = $this->retrieve_url($this->getURL());
        }else{
            $this->url = $this->getURL();
        }

        $url = $this->url;

        $id=$this->popFicId();
        $this->setFicId($id);

        $info = $this->infoapi($id);

        //echo $info;

        $infosSource = $this->getPageSource($this->url);

        //echo $info;
        //die();

        $title = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, str_replace("&#x27;","'",htmlspecialchars_decode(html_entity_decode($this->popTitle($infosSource), ENT_COMPAT, "UTF-8"))));

        $author = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $this->popAuthor($info));
        
        $summary = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $this->popSummary($info));

        

        $this->getChaptersIDs($infosSource);
        $this->setTitle($title);
        $this->setAuthor($author);
        $this->setFicType($this->popFicType($info));
        $this->setSummary($summary);
        $this->setPublishedDate($this->popPublished($info));
        $this->setUpdatedDate($this->popUpdated($info));
        $this->setWordsCount($this->popWordsCount($info));
        //$this->setPairing($this->popPairing($infosSource));
        $this->setPairing(False);
        $this->setChapCount($this->popChapterCount($info));
        $this->setFandom($this->popFandom($info));
        $this->setCompleted(false);
        $_SESSION["wattpad_info"] = $info;#
        //setcookie("wattpad_info", $info, time() + (86400 * 30), "/");

        $decoded = json_decode($info,true);
        $_SESSION["cover"] = $decoded["cover"];

    }

    public function getSite(){
    	return "wattpad";
    }

    public function retrieve_url($url){

    
    	$curl = curl_init();

        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0');
        curl_setopt($curl, CURLOPT_REFERER, 'https://www.wattpad.com/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'identity');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $source = utf8_decode(curl_exec($curl));
        curl_close($curl);


        $new="";
        if (preg_match_all('#<h6><a class="on-navigate" href="(.*?)">.*?</a></h6>#im', $source, $matches)){
            $new = 'https://www.wattpad.com'.$matches[1][0];
        }
        else {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find story url.");
            echo $source;
            die();
        }

        return $new; 
    }

    public function getChapter($number)
    {
        $infos = $_SESSION["wattpad_info"];
        /*try{
    	    $infos = $_SESSION["wattpad_info"];
        } catch (Exception $e) {
            $infos = $_COOKIE["wattpad_info"];
        }
        
        if (!isset($infos) or strlen($infos) === 0){
            $infos = $_COOKIE["wattpad_info"];
        }
        
        if (!isset($infos) or strlen($infos) === 0){
            $infos = $_SESSION["wattpad_info"];
        }*/
        
    	if (strlen($infos) === 0){
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get Api Info.");
            return False;
        }

        $infos = json_decode($infos,true);

        $chapters = $infos["group"];

        $text = false;
        $title = false;

        $title = $chapters[$number-1]["TITLE"];

        $id = $chapters[$number-1]["ID"];

        $url = "https://www.wattpad.com/apiv2/storytext?id=".$id;



 		$curl = curl_init();

        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0');
        curl_setopt($curl, CURLOPT_REFERER, 'https://www.wattpad.com/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'identity');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $text = utf8_decode(curl_exec($curl));
        curl_close($curl);


        //$text = Utils::cleanText($text);
        if (strlen($text) === 0)
        {
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find chapter($number) text.");
            return false;
        }

        return new Chapter($number, $title, $text);

    }

    protected function getPageSource($url)
    {

    	if (strlen($url) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get url for page source.");

        //echo $url;

        $curl = curl_init();
                    $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';

        curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']);
        curl_setopt($curl, CURLOPT_REFERER, 'https://www.domain.com/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $source = utf8_decode(curl_exec($curl));
        curl_close($curl);

        if ($source === false)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get story page source.");

        return $source;
    }

    private function infoapi($id){
	    $url = "https://www.wattpad.com/apiv2/info?id=".$id;
	    $curl = curl_init();
                    $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';

        curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']);
        curl_setopt($curl, CURLOPT_REFERER, 'https://www.domain.com/');
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_ENCODING, '');
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	            
	    $source = utf8_decode(curl_exec($curl));
	    $info = curl_getinfo($curl);

	    curl_close($curl);
	    return $source;
	}

    private function popFicId()
    {
    	$url = $this->url;

		if (strpos($url, "http") !== false){}else{
			if (strpos($url, "www") !== false){}else{
				$url = "www.".$url;
			}
			$url = "https://".$url;
		}

		if (strpos($url, "/story/") !== false){
			$storyurl = True;

			$curl = curl_init();
            $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';

            curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']);
            curl_setopt($curl, CURLOPT_REFERER, 'https://www.domain.com/');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_ENCODING, '');
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);        
			$source = utf8_decode(curl_exec($curl));
			$info = curl_getinfo($curl);
			curl_close($curl);
			//$source = preg_replace(array('/ {2,}/','/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'),array(' ',''),$source); 

			$id="";
		    //$regexp='/^.*data-part-id="([0-9]{8})".*?$/m';
            $regexp='#"story-parts"><ul><li><a href="\/(.*?)-.*?" class="story-parts__part"><div class="part__label">#im';
		    if (strlen($source) === 0){
				$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source one.");
            }
		    if (preg_match_all($regexp, $source, $matches)){
		        $id = $matches[1][0];
	        }
		    else {
		    	$this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find Fic ID.");
                //echo $source;
                die();
		    }

		}else{

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_ENCODING, '');
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);        
            $source = utf8_decode(curl_exec($curl));
            $info = curl_getinfo($curl);
            curl_close($curl);
            $source = preg_replace(array('/ {2,}/','/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'),array(' ',''),$source); 

			$storyurl = True;

		    $id="";

            $regexp = '#href="https:\/\/embed\.wattpad\.com/story/(.*?)" data-share-channel#si';
            //<link rel="canonical" href="https://www.wattpad.com/story/91597086-dragon-boy"/>
            if (strlen($source) === 0)
                $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source two. with: $url");

            if (preg_match($regexp, $source, $matches)){
                $id = $matches[1];
            }
            else {
                $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find Fic ID.");
            }


            $url = "https://wattpad.com/story/".$id;


            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_ENCODING, '');
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);        
            $source = utf8_decode(curl_exec($curl));
            $info = curl_getinfo($curl);
            curl_close($curl);
            $source = preg_replace(array('/ {2,}/','/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'),array(' ',''),$source); 


            $id="";
            //$regexp='/^.*data-part-id="([0-9]{8})".*?$/m';
            $regexp='/^.*data-part-id="(.*?)".*?$/m';
            if (strlen($source) === 0)
                    $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source three.");
            if (preg_match_all($regexp, $source, $matches)){
                $id = $matches[1][0];
            }
            else {
                $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find Fic ID.");
            }









		}

	    return $id;
	    //$this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find Fic ID.");
    }

    private function popTitle($source)
    {
        
	$result = "";
    $regexp='#<div aria-hidden="true" class="story-info__title">(.+?)</div>#im';
    //$source = preg_replace(array('/ {2,}/','/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'),array(' ',''),$source);
    //echo $source;
	if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source for title.");

        if (preg_match($regexp, $source, $matches)){
        	$result = $matches[1];
        }
        else {
            $this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find title.");
            //echo $source;
            die();
            //return "Untitled";
        }

	return html_entity_decode($result);



    }

    private function popAuthor($info)
    {
        if (strlen($info) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get Api Info.");

        $infos = json_decode($info,true);

        return $infos["author"];

    }

    private function popFicType($info)
    {
        if (strlen($info) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get Api Info.");

        $infos = json_decode($info,true);

        return $infos["tags"];

    }

    private function popFandom($info)
    {
        if (strlen($info) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get Api Info.");

        $infos = json_decode($info,true);

        return $infos["tags"];
    }

    private function popSummary($info)
    {
        if (strlen($info) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get Api Info.");

        $infos = json_decode($info,true);

        return $infos["description"];

    }

    private function popPublished($info)
    {
        if (strlen($info) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get Api Info.");

        $infos = json_decode($info,true);

        return strtotime($infos["date"]);

    }

    private function popUpdated($info)
    {
    	if (strlen($info) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get Api Info.");

        $infos = json_decode($info,true);

        return strtotime($infos["modifyDate"]);
    }

    private function popWordsCount($info)
    {
        if (strlen($info) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get Api Infos.");


        $infos = json_decode($info,true);

        return $infos["length"];

    }

    private function popChapterCount($info)
    {

    	if (strlen($info) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get Api Infos.");

        //echo $info;

        $infos = json_decode($info,true);

        $chapters = $infos["group"];
        //print_r($infos);
        return count($chapters);
    }

    private function popPairing($source)
    {
        if (strlen($source) === 0)
            $this->errorHandler()->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't get source four.");

        if (preg_match("#<b>Characters:</b> (.+?) <br>#si", $source, $matches) === 1)
            return $matches[1];
        else
        {
            //$this->errorHandler()->addNew(ErrorCode::ERROR_WARNING, "Couldn't find pairing (No pairing?).");
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



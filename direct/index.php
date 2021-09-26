<?php
ob_start();
require_once __DIR__."/class/UrlParser.php";
require_once __DIR__."/../class/CurlHandler.php";
require_once __DIR__."/../conf/config.php";
require_once __DIR__."/../class/class.dbHandler.php";
require_once __DIR__."/../class/class.Utils.php";


// Example URL: http://domain.com/direct/?id=xxxxxx&source=ffnet&filetype=epub
// Example URL: http://domain.com/direct/?id=xxxxxx&source=ffnet

try {
	$getData = new UrlParser($_GET);
    $url = Utils::getWebURL($getData->getId(), $getData->getSource());
    
    $ficInfos = CurlHandler::sendPost(Config::DOMAIN_PATH ."/ajaxPHP/ajax.getFicInfos.php", ["url" => $url, "force" => false]);
    var_dump($ficInfos);
    if (!$ficInfos) {
        throw new Exception("Error while fetching fic infos.");
    }

    $ficInfos = json_decode($ficInfos, true);

    if (!$ficInfos) {
        throw new Exception("Error while decoding infos.");
    }

    if ($ficInfos["exist"]) { // If file already exist
        if ($getData->getFileType() == FileType::EPUB) { // and filetype is epub (no need to convert)
            header("Location: ../download.php?source=". $getData->getSource() ."&id=". $getData->getId() ."&filetype=". $getData->getFileType()); // Redirect to download link
            return;
        }
        else { // If filetype is not epub, convert to specified filetype.
            if (CurlHandler::sendPost(Config::DOMAIN_PATH ."/ajaxPHP/ajax.convert.php", ["filetype" => $getData->getFileType()])) {
                header("Location: ../download.php?source=". $getData->getSource() ."&id=". $getData->getId() ."&filetype=". $getData->getFileType()); // Redirect to download link
                return;
            }
        }
    }
}
catch(Exception $e)
{
	die($e->getMessage());
}

ob_end_flush();
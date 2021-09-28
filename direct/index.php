<?php
ob_start();
require_once __DIR__."/class/UrlParser.php";
require_once __DIR__."/../class/CurlHandler.php";
require_once __DIR__."/../conf/config.php";
require_once __DIR__."/../class/class.dbHandler.php";
require_once __DIR__."/../class/class.Utils.php";
require_once __DIR__."/../sqlSession.php";
session_start();

// Example URL: http://domain.com/direct/?id=xxxxxx&source=ffnet&filetype=epub
// Example URL: http://domain.com/direct/?id=xxxxxx&source=ffnet

try {
	$getData = new UrlParser($_GET);
    $url = Utils::getWebURL($getData->getId(), $getData->getSource());
    
    $ficInfos = CurlHandler::sendPost(Config::DOMAIN_PATH ."/ajaxPHP/ajax.getFicInfos.php", ["url" => $url, "force" => false]);
    if (!$ficInfos) {
        throw new Exception("Error while fetching fic infos. Try again.");
    } 
    var_dump($ficInfos);
    $ficInfos = json_decode($ficInfos, true);

    if (!$ficInfos) {
        throw new Exception("Error while decoding infos. Try again.");
    }


    if (isset($ficInfos["exist"]) && $ficInfos["exist"]) { // If file already exist
        return getDownload($getData->getId(), $getData->getSource(), $getData->getFileType());
    }
    else {
        
        // If fic is not in DB, we need to fetch chapter and create file
        $chapters = [];
        for ($i = 1 ; $i <= $ficInfos["chapCount"] ; $i++) {
            $chapters[$i] = CurlHandler::sendPost(Config::DOMAIN_PATH ."/ajaxPHP/ajax.getChapter.php", ["chapNum" => $i]);
            echo $chapters[$i];
        }

        for ($i = 1 ; $i <= $ficInfos["chapCount"] ; $i++) {
            if (!$chapters[$i]) {
                throw new Exception("Error while getting chapters data. Try again.");
            }
        }
        if (CurlHandler::sendPost(Config::DOMAIN_PATH ."/ajaxPHP/ajax.createFile.php", [])) { // Create epub
            return getDownload($getData->getId(), $getData->getSource(), $getData->getFileType());
        }
        else {
            throw new Exception("Error creating file.");
        }

    }
}
catch(Exception $e)
{
	die($e->getMessage());
}

function getDownload($id, $source, $filetype) {
    if ($filetype == FileType::EPUB) { // and filetype is epub (no need to convert)
        header("Location: ../download.php?source=". $source ."&id=". $id ."&filetype=". $filetype); // Redirect to download link
        return;
    }
    else { // If filetype is not epub, convert to specified filetype.
        if (CurlHandler::sendPost(Config::DOMAIN_PATH ."/ajaxPHP/ajax.convert.php", ["filetype" => $filetype])) {
            header("Location: ../download.php?source=". $source ."&id=". $id ."&filetype=". $filetype); // Redirect to download link
            return;
        }
        throw new Exception("Error while getting file.");
    }

}
ob_end_flush();
session_write_close();
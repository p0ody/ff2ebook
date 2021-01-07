<?php
error_reporting(E_ERROR | E_PARSE);
session_start();
require_once("../sqlSession.php");
require_once("../class/class.ErrorHandler.php");
require_once("../class/class.ZipManager.php");
require_once("../class/class.FanFiction.php");
require_once("../class/class.FileManager.php");
require_once("../class/class.Download.php");


header('Content-type: application/json');


$error = new ErrorHandler();

if (!isset($_POST["filetype"]))
    $error->addNew(ErrorCode::ERROR_CRITICAL, "Didn't receive filetype for conversion.");

$filetype = $_POST["filetype"];

if ($filetype != "epub" && $filetype != "mobi" & $filetype != "pdf")
    $error->addNew(ErrorCode::ERROR_CRITICAL, "Invalid filetype.");

if (!isset($_SESSION["encoded_fic"]))
    $error->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find serialized fic infos.");

/** @var FanFiction $fic */
$fic = unserialize($_SESSION["encoded_fic"]);


if ($fic === false)
    $error->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't unserialize fic informations.");

/** @var BaseHandler $ficH */
$ficH = $fic->ficHandler();

$dl = new Download($fic->getSource(), $ficH->getFicId());

switch($filetype)
{
    case "epub":
        $dl->asEpub();
        break;

    case "mobi":
        $file = $dl->asMobi();
        break;

    default:
        break;
}


$return["error"] = $error->getAllAsJSONReady();
echo json_encode($return);


<?php
require_once("../sqlSession.php");
require_once("../class/class.FanFiction.php");
require_once("../class/class.ErrorHandler.php");
require_once("../class/class.FileManager.php");
require_once("../class/class.Chapter.php");

session_start();
header('Content-type: application/json');


$error = new ErrorHandler();

if (!isset($_REQUEST["chapNum"]) && is_numeric($_REQUEST["chapNum"]))
    $error->addNew(ErrorCode::ERROR_CRITICAL, "Didn't receive chapter number.");


if (!isset($_SESSION["encoded_fic"]))
    $error->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find serialized fic infos.");

/** @var FanFiction $fic */
$fic = unserialize($_SESSION["encoded_fic"]);

if ($fic === false)
    $error->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't unserialize fic informations.");

$chapter = $fic->ficHandler()->getChapter($_REQUEST["chapNum"]);


$return["ficId"] = $fic->ficHandler()->getFicId();
$return["chapNum"] = $_REQUEST["chapNum"];

$fm = new FileManager();
$file = $fm->createChapterFile($fic->ficHandler()->getOutputDir() ."/OEBPS/Content", $chapter);

$return["error"] = $error->getAllAsJSONReady();
echo json_encode($return);

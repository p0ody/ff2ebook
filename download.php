<?php
ob_start();
require_once("conf/config.php");
require_once("class/class.dbHandler.php");
require_once("class/class.Download.php");


if (!isset($_GET["source"]) || !isset($_GET["id"]))
    die("Invalid URL.");


$source = $_GET["source"];
$id = $_GET["id"];
$filetype = isset($_GET["filetype"]) ? $_GET["filetype"] : "epub";

if ($filetype != "epub" && $filetype != "mobi" && $filetype != "pdf")
    die("Invalid filetype.");

if ($filetype == "kindle")
    $filetype = "mobi";

$file = glob("archive/". $source ."_". $id ."*");

if (count($file) > 1 && !empty($file[count($file) - 1]))
    $file = $file[count($file) - 1];

elseif (count($file) === 1)
    $file = $file[0];

if (!is_string($file) || !is_file($file))
    die("File not found on server,");

$title = false;
$author = false;

$minetype;
/** @var DownloadFile */
$fileInfos = false;

$dl = new Download($source, $id);

switch($filetype)
{
    case "epub":
        $mimetype = "application/epub+zip";
        $fileInfos = $dl->asEpub();
        break;

    case "mobi":
        $mimetype = "application/x-mobipocket-ebook";
        $fileInfos = $dl->asMobi();
        break;

    default:
        die("Dafuq happened ?");

}


if (!is_object($fileInfos))
    die("An error has occured.");


if (!file_exists("archive/". $fileInfos->getFilename()))
    die("File not found on server,");


header("Content-Type: ". $mimetype);
header("Content-Length: " . filesize("archive/". $fileInfos->getFilename()));
header('Content-Disposition: attachment; filename="'. normalizer_normalize($fileInfos->getTitle() .' - '. $fileInfos->getAuthor()) .'.'. $filetype .'"');
header("Content-Transfer-Encoding: binary");
readfile("archive/". $fileInfos->getFilename());

if (PORTABLE_MODE) {
    unlink("archive/" . $fileInfos->getFilename());
}

ob_end_flush();
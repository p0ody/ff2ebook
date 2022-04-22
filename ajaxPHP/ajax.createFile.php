<?php
require_once("../sqlSession.php");
require_once("../class/class.ErrorHandler.php");
require_once("../class/class.ZipManager.php");
require_once("../class/class.FanFiction.php");
require_once("../class/class.FileManager.php");
require_once("../class/class.Epub.php");
require_once("../class/class.dbHandler.php");

session_start();
header('Content-type: application/json');

$error = new ErrorHandler();

if (!isset($_SESSION["encoded_fic"]))
    $error->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't find serialized fic infos.");

/** @var FanFiction $fic */
$fic = unserialize($_SESSION["encoded_fic"]);

if ($fic === false)
    $error->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't unserialize fic informations.");

if ($fic->getSource() === false)
    $error->addNew(ErrorCode::ERROR_CRITICAL, "An error occured, please try again later.");

/** @var BaseHandler $ficH */
$ficH = $fic->ficHandler();
$fm = new FileManager();
$ficH->setOutputDir($fm->createOutputDir());
$fm->createTitlePage($fic->ficHandler()->getOutputDir() . "/OEBPS/Content", $fic->ficHandler());

$path = "../archive/". $ficH->getFilename() .".epub";

$epub = new Epub("../output/". $ficH->getOutputDir(), $path);
if (!$epub->create())
    $error->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't create epub file.");

$fm->deleteFolderWithFile("../output/". $ficH->getOutputDir());


try
{
    $dbH = new dbHandler();
    $pdo = $dbH->connect();

    $query = $pdo->prepare(SQL_SELECT_FIC);
    $query->execute(Array(
        "id"    => $ficH->getFicId(),
        "site"  => $fic->getSource()
    ));

    if ($query->rowCount() === 1)
    {
        $result = $query->fetch();

        if ($ficH->getUpdatedDate() < $result["updated"])
            $fm->deleteFile("../archive/". $result["filename"]);
    }

    $query = $pdo->prepare(SQL_INSERT_FIC);
    $query->execute(Array(
        "site"      => $fic->getSource(),
        "id"        => $ficH->getFicId(),
        "title"     => $ficH->getTitle(),
        "author"    => $ficH->getAuthor(),
        "updated"   => $ficH->getUpdatedDate(),
        "filename"  => $fic->getSource() ."_". $ficH->getFicId() ."_". $ficH->getUpdatedDate() .".epub",
        "lastChecked" => time()
    ));
}
catch (PDOException $e)
{
    $error->addNew(ErrorCode::ERROR_WARNING, $dbH->userFriendlyError($e->getCode()));
}


$return["error"] = $error->getAllAsJSONReady();
echo json_encode($return);

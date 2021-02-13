<?php
require_once("../conf/config.php");
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

$path = "../archive/". $ficH->getFilename() .".epub";

$epub = new Epub("../output/". $ficH->getOutputDir(), $path);
if (!$epub->create())
    $error->addNew(ErrorCode::ERROR_CRITICAL, "Couldn't create epub file.");


$fm = new FileManager();
$fm->deleteFolderWithFile("../output/". $ficH->getOutputDir());

try
{
    if (PORTABLE_MODE)
    {
        $_SESSION[$fic->getSource() . "_" . $ficH->getFicId() . "_site"]= $fic->getSource();
        $_SESSION[$fic->getSource() . "_" . $ficH->getFicId() . "_id"]= $ficH->getFicId();
        $_SESSION[$fic->getSource() . "_" . $ficH->getFicId() . "_title"]= $ficH->getTitle();
        $_SESSION[$fic->getSource() . "_" . $ficH->getFicId() . "_author"]= $ficH->getAuthor();
        $_SESSION[$fic->getSource() . "_" . $ficH->getFicId() . "_updated"]= $ficH->getUpdatedDate();
        $_SESSION[$fic->getSource() . "_" . $ficH->getFicId() . "_filename"]=$fic->getSource() ."_". $ficH->getFicId() ."_". $ficH->getUpdatedDate() .".epub";
    }
    else
    {
        $dbH = new dbHandler();
        $pdo = $dbH->connect();

        $query1 = $pdo->prepare(SQL_SELECT_FIC);
        $query1->execute(Array(
            "id"    => $ficH->getFicId(),
            "site"  => $fic->getSource()
        ));

        if ($query1->rowCount() === 1)
        {
            $result = $query1->fetch();

            if ($ficH->getUpdatedDate() < $result["updated"])
                $fm->deleteFile("../archive/". $result["filename"]);
        }

        $query2 = $pdo->prepare("REPLACE INTO `fic_archive` (`site`, `id`, `title`, `author`, `updated`, `filename`) VALUES (:site, :id, :title, :author, :updated, :filename);");
        $query2->execute(Array(
            "site"      => $fic->getSource(),
            "id"        => $ficH->getFicId(),
            "title"     => $ficH->getTitle(),
            "author"    => $ficH->getAuthor(),
            "updated"   => $ficH->getUpdatedDate(),
            "filename"  => $fic->getSource() ."_". $ficH->getFicId() ."_". $ficH->getUpdatedDate() .".epub"
        ));
    }
}
catch (PDOException $e)
{
    $error->addNew(ErrorCode::ERROR_WARNING, $dbH->userFriendlyError($e->getCode()));
}


$return["error"] = $error->getAllAsJSONReady();
echo json_encode($return);


<?php
require_once("../sqlSession.php");
require_once("../class/class.FanFiction.php");
require_once("../class/class.ErrorHandler.php");
require_once("../class/class.FileManager.php");
require_once("../class/class.dbHandler.php");

session_start();
header('Content-type: application/json');


$error = new ErrorHandler();

if (!isset($_POST["url"]))
    $error->addNew(ErrorCode::ERROR_CRITICAL, "No URL entered.");


//$fic = new FanFiction("https://www.fanfiction.net/s/5069455/1/Follow-the-Phoenix", $error);
//$fic = new FanFiction("https://www.fanfiction.net/s/6706530", $error); // One shot
$fic = new FanFiction($_POST["url"], $error);

$return = Array();
$exist = false;

if (!isset($_POST["force"]) || $_POST["force"] === "false")
{
    try
    {
        $dbH = new dbHandler();
        $pdo = $dbH->connect();

        $query = $pdo->prepare(SQL_SELECT_FIC);
        $query->execute(Array(
            "id" => $fic->ficHandler()->getFicId(),
            "site" => $fic->getSource()
        ));

        if ($query->rowCount() === 1) // If this fic is already in archive and up to date
        {
            $result = $query->fetch();

            if ($fic->ficHandler()->getUpdatedDate() <= $result["updated"] && is_file("../archive/" . $result["filename"])) {
                $exist = true;
                $return["exist"] = true;
            }
        }
    }
    catch (PDOException $e)
    {
        $error->addNew(ErrorCode::ERROR_WARNING, $dbH->userFriendlyError($e->getCode()));
    }
}


$return["id"] = $fic->ficHandler()->getFicId();
$return["site"] = $fic->getSource();
$return["title"] = $fic->ficHandler()->getTitle();
$return["author"] = $fic->ficHandler()->getAuthor();
$return["chapCount"] = $fic->ficHandler()->getChapCount();
$return["updated"] = $fic->ficHandler()->getUpdatedDate();
$return["error"] = $error->getAllAsJSONReady();


if (!$exist)
{
    $fm = new FileManager();
    $ficH = $fic->ficHandler();
    $ficH->setOutputDir($fm->createOutputDir());
    $ficH->setFileName($fic->getSource() . "_" . $ficH->getFicId() . "_" . $ficH->getUpdatedDate());

    $fm->createTitlePage($fic->ficHandler()->getOutputDir() . "/OEBPS/Content", $fic->ficHandler());
}

$_SESSION["encoded_fic"] = serialize($fic);

echo json_encode($return);
session_write_close();




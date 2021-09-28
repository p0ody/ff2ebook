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


$fic = new FanFiction($_POST["url"], $error, true);
$return = Array();
$exist = false;

// if force update is not checked, check in DB to see if fic already exist.
if (!isset($_POST["force"]) || $_POST["force"] === "false" || !$_POST["force"])
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
            $result = $query->fetch(PDO::FETCH_ASSOC);

            // If more than 24 hours since last time checked if fic is up to date, check if our version is still up to date.
            if (time() - $result["lastChecked"] > Config::TIME_MAX_LAST_CHECKED) {
                $fic->ficHandler()->populate();
                if ($fic->ficHandler()->getUpdatedDate() <= $result["updated"] && is_file("../archive/" . $result["filename"])) {
                    $exist = true;
                    $return["exist"] = true;
                }

                $return["title"] = $fic->ficHandler()->getTitle();
                $return["author"] = $fic->ficHandler()->getAuthor();
                $return["chapCount"] = $fic->ficHandler()->getChapCount();
                $return["updated"] = $fic->ficHandler()->getUpdatedDate();
                $return["error"] = $error->getAllAsJSONReady();
            }
            else { // Otherwise  just return the existing file.\
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




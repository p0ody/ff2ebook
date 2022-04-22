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

$dbH = new dbHandler();

// Check blacklisted author and ficId
if (isFicBlacklisted($dbH, $fic->getSource(), $fic->ficHandler()->getFicId())) { 
    $error->addNew(ErrorCode::ERROR_BLACKLISTED, "Fic is blacklisted as per author request.");
    die(json_encode(Array("error"=> $error->getAllAsJSONReady())));
}

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
            $return["author"] = $result["author"];

            // If more than 24 hours since last time checked if fic is up to date, check if our version is still up to date.
            if (time() - $result["lastChecked"] > Config::TIME_MAX_LAST_CHECKED) {
                $fic->ficHandler()->populate();
                if ($fic->ficHandler()->getUpdatedDate() <= $result["updated"] && is_file("../archive/" . $result["filename"])) {
                    $exist = true;
                    $return["exist"] = true;
                }

                $query = $pdo->prepare(SQL_UPDATE_LASTCHECKED);
                $query->execute([   "id" => $fic->ficHandler()->getFicId(),
                                    "site" => $fic->getSource()]);
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
    $fic->ficHandler()->populate();
    $return["title"] = $fic->ficHandler()->getTitle();
    $return["author"] = $fic->ficHandler()->getAuthor();
    $return["chapCount"] = $fic->ficHandler()->getChapCount();
    $return["updated"] = $fic->ficHandler()->getUpdatedDate();

    // Check blacklisted author and ficId
    if (isFicBlacklisted($dbH, $fic->getSource(), $fic->ficHandler()->getFicId(), $fic->ficHandler()->getAuthor())) { 
        $error->addNew(ErrorCode::ERROR_BLACKLISTED, "Fic is blacklisted as per author request.");
    }

    if ($error->hasErrors()) {
        $return["error"] = $error->getAllAsJSONReady();
    }

    $fm = new FileManager();
    $ficH = $fic->ficHandler();
    $ficH->setOutputDir($fm->createOutputDir());
    $ficH->setFileName($fic->getSource() . "_" . $ficH->getFicId() . "_" . $ficH->getUpdatedDate());

    //$fm->createTitlePage($fic->ficHandler()->getOutputDir() . "/OEBPS/Content", $fic->ficHandler());
}

$_SESSION["encoded_fic"] = serialize($fic);
echo json_encode($return);



function isFicBlacklisted(DBHandler $dbHandler, $site, $id = null, $author = null) {
    try {
        $pdo = $dbHandler->connect();
        if ($id) {
            $query = $pdo->prepare("SELECT * FROM `author_blacklist` WHERE `site`=:site AND `ficId`=:id;");
            $query->execute(Array("site" => $site, "id" => $id));
            if ($query->rowCount() > 0) {
                return true;
            }
        }

        if ($author) {
            $query = $pdo->prepare("SELECT * FROM `author_blacklist` WHERE `site`=:site AND `author`=:author;");
            $query->execute(Array("site" => $site, "author" => $author));
            if ($query->rowCount() > 0) {
                return true;
            }
        }
        
        return false;
    }
    catch (Exception $e) {
        throw $e;
    }
}

session_write_close();

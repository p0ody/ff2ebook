<?php
require_once("../class/class.dbHandler.php");
require_once("../class/class.ErrorHandler.php");
require_once("../class/class.Utils.php");

header('Content-type: application/json');
$error = new ErrorHandler();

if (!isset($_POST["search"]))
    $error->addNew(ErrorCode::ERROR_CRITICAL, "No search input.");

$search = $_POST["search"];
$return = Array();
$return["result"] = "";

try
{
    $db = new dbHandler();
    $pdo = $db->connect();

    $sqlSearch = "%". str_replace(" ", "%", $search) ."%";

    $query = $pdo->prepare("SELECT * FROM `fic_archive` WHERE `id` LIKE :search OR `title` LIKE :search or `author` LIKE :search;");
    $query->execute(Array("search" => $sqlSearch));
    // @TODO Make things pretty on mobile...
    $return["result"] = "<table class=\"table table-hover table-responsive\">";
    $return["result"] .= "<thead><tr><th>Web Source</th><th>Title - Author</th><th>Updated Date</th><th>Download</th></tr></thead>";
    $return["result"] .= "<tbody>";


    while ($row = $query->fetch())
    {
        $epub = "download.php?source=". $row["site"] ."&id=". $row["id"] ."&filetype=epub";
        $mobi = "download.php?source=". $row["site"] ."&id=". $row["id"] ."&filetype=mobi";
        $return["result"] .= "<tr><td>". Utils::webSourceReadable($row["site"]) ."</td><td><a href=\"". Utils::getWebURL($row["id"], $row["site"])  ."\">". $row["title"] ." - ". $row["author"] ."</a></td><td>". date("Y-m-d", intval($row["updated"])) ."</td><td><a href=\"". $epub ."\">EPUB</a> <a href=\"". $mobi ."\">MOBI</a></td></tr>";
    }

    $return["result"] .= "</tbody>";
    $return["result"] .= "</table>";

    $return["count"] = $query->rowCount();
}
catch(PDOException $e)
{
    $error->addNew(ErrorCode::ERROR_CRITICAL, $e->getMessage());

}



$return["error"] = $error->getAllAsJSONReady();
echo json_encode($return);

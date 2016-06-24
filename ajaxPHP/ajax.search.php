<?php

require_once __DIR__."/../conf/config.php";
require_once __DIR__."/../class/class.Search.php";
require_once __DIR__."/../class/class.ErrorHandler.php";
require_once __DIR__."/../class/class.Pagination.php";

define("MAX_RESULTS_PER_PAGE", 50);

header('Content-type: application/json');

$error = new ErrorHandler();

if (!isset($_POST["searchInput"]) || strlen($_POST["searchInput"]) === 0)
    $error->addNew(ErrorCode::ERROR_CRITICAL, "No valid search input.");

$page = 1;
if (isset($_POST["page"]))
    $page = $_POST["page"];

$searchFor = $_POST["searchInput"];

$search = new Search($searchFor, $page, MAX_RESULTS_PER_PAGE);

$pagination = new Pagination("archive.php?search=". $searchFor ."&page=", $page, $search->getTotalCount(), MAX_RESULTS_PER_PAGE);

$return = Array(
    "pageResults" => $search->getFormaattedResults(),
    "count" => $search->getTotalCount(),
    "pagination" => (string)$pagination,
    "error" => $error->getAllAsJSONReady()
);

if (PORTABLE_MODE)
{
    $error->addNew(ErrorCode::ERROR_WARNING, "No search available - This server runs in portable mode");

    $return = Array(
        "count" => $search->getTotalCount(),
        "pagination" => (string)$pagination,
        "error" => $error->getAllAsJSONReady()
    );
}

echo json_encode($return);

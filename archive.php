<?php
require_once __DIR__."/class/class.Pagination.php";
require_once __DIR__."/class/class.Search.php";

$search = false;
if (isset($_GET["search"]))
    $search = $_GET["search"];

$page = 1;
if (isset($_GET["page"]))
    $page = $_GET["page"];

if ($page < 1)
    $page = 1;


function hasSearch($search) { return $search !== false; }



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FF2EBOOK :: Archive</title>
    <script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.11.3.min.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    <script src="js/errorHandler.js"></script>
    <script src="js/archive.js"></script>
    <link rel="stylesheet" type="text/css" href="default.css">
</head>
<body>
    <div class="container-fluid">
        <?php include("menu.html") ?>
        <!-- Input -->
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <form class="form-group top-spacing" method="get" action="archive.php" id="archive-search">
                    <div class="input-group">
                        <input type="text" id="archive-search-input" name="search" class="form-control" placeholder="Fic title or author or ID" value="<?php echo hasSearch($search) ? $search : ""; ?>" />
                        <span class="input-group-btn">
                            <button class="btn btn-default btn-block" >Search</button>
                        </span>
                    </div>
                </form>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="text-zone-bg text-left <?php echo hasSearch($search) ? "" : "hidden"; ?>" id="search-result-bg">
                    <div class="text-zone-header">Seach results: <span class="results-count"></span></div>
                    <div class="text-zone-content" id="search-result"></div>
                    <div class="pagi"></div>
                </div>
            </div>
        </div>


    </div>
</body>
</html>
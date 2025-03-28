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
    
    <?php include("html/header.html") ?>
    <script src="js/archive.js"></script>
</head>
<body>
    <div class="container-fluid">
        <?php include("html/menu.html") ?>
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
                    <select class="custom-select custom-select-lg" name="sort">
                        <option selected value="title">Title</option>
                        <option value="author">Author</option>
                        <option value="updated">Update date</option>
                        <option value="site">Source Website</option>
                    </select>
                </form>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="text-zone-bg text-left <?php echo hasSearch($search) ? "" : "hidden"; ?>" id="search-result-bg">
                    <div class="text-zone-header">Seach results: <span class="results-count"></span></div>
                    <div class="text-zone-content center" id="search-result"><img src="images/loader.gif"></div>
                    <div class="pagi center"></div>
                </div>
            </div>
        </div>




    </div>
</body>
</html>

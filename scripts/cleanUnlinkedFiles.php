<?php
require_once("../class/class.FileManager.php");
require_once("../class/class.dbHandler.php");

// Delete files with no DB link.
$fileList = scandir("../archive");

if (!$fileList)
    die("Error retrieving files list.");

$db = new dbHandler();
$pdo = $db->connect();
$query = $pdo->prepare("SELECT `id` FROM `fic_archive`;");
$query->execute();

$results = $query->fetchAll(PDO::FETCH_UNIQUE);

$fm = new FileManager();

$counter = 0;
foreach ($fileList as $file)
{
    if (preg_match("#[a-zA-Z]+_([0-9]+)_[0-9]+#", $file, $matches) === 1)
    {
        if (!array_key_exists($matches[1], $results))
        {
            $fm->deleteFile("../archive/". $file);
            echo $file ." deleted. <br />";
            $counter++;
        }
    }
}

echo $counter ." files deleted.";
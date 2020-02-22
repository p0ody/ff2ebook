<?php
require_once(__DIR__ ."/../class/class.FileManager.php");
require_once(__DIR__ ."/../class/class.dbHandler.php");

// Delete files with no DB link.
/*$fileList = scandir("../archive");

if (!$fileList)
    die("Error retrieving files list.");
*/

$db = new dbHandler();
$pdo = $db->connect();
$query = $pdo->prepare("SELECT `id`,`site` FROM `fic_archive`;");
$query->execute();

$results = $query->fetchAll(PDO::FETCH_KEY_PAIR);

$fm = new FileManager();

$counter = 0;
foreach (new DirectoryIterator("../archive") as $file)
{
    if ($file->getExtension() != "epub" && $file->getExtension() != "mobi")
        continue;

    $name = $file->getFilename();
    if (preg_match("#([a-zA-Z]+)_([0-9]+)_[0-9]+#", $name, $matches) === 1)
    {
        if (!array_key_exists($matches[2], $results))
        {
            $fm->deleteFile($file->getPathname());
            echo $name ." deleted. <br />";
            $counter++;
        }
        else
        {
            if ($results[$matches[2]] == $matches[1])
                continue;

            $fm->deleteFile($file->getPathname());
            echo $name ." deleted. <br />";
            $counter++;
        }
    }
}
/*
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
*/
echo $counter ." files deleted.";
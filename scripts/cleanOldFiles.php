<?php

require_once("../class/class.dbHandler.php");
require_once("../class/class.FileManager.php");

try
{
    $db = new dbHandler();
    $pdo = $db->connect();

    $sixMonths = 6/*Months*/ * 30 /*Days*/ * 24 /*Hours*/ * 60 /*Minutes*/ * 60 /*Seconds*/;
    $query = $pdo->prepare("DELETE FROM `fic_archive` WHERE `lastDL` < ". (time() - $sixMonths) .";");
    $query->execute();

    echo $query->rowCount() ." rows deleted in database. <br />";

    // Delete files with no DB link.
    $fileList = scandir("../archive");

    if (!$fileList)
        die("Error retrieving files list.");
       
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
    

}
catch(PDOException $e)
{
    die("An error has occured.");
    
}
<?php

require_once("../class/class.dbHandler.php");

try
{
    $db = new dbHandler();
    $pdo = $db->connect();

    $sixMonths = 6/*Months*/ * 30 /*Days*/ * 24 /*Hours*/ * 60 /*Minutes*/ * 60 /*Seconds*/;
    $query = $pdo->prepare("DELETE FROM `fic_archive` WHERE `lastDL` < ". (time() - $sixMonths) .";");
    $query->execute();

    echo $query->rowCount() ." rows deleted in database. <br />";

    
    

}
catch(PDOException $e)
{
    die("An error has occured.");
    
}
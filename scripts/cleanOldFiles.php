<?php

require_once("../class/class.dbHandler.php");

try
{
    $db = new dbHandler();
    $pdo = $db->connect();

    $sixMonths = 6/*Months*/ * 30 /*Days*/ * 24 /*Hours*/ * 60 /*Minutes*/ * 60 /*Seconds*/;
    $query = $pdo->prepare("DELETE FROM `fic_archive` WHERE `lastDL` < ". (time() - $sixMonths) .";");
    $query->execute();

    die($query->rowCount() ." rows deleted.");
}
catch(PDOException $e)
{
    die("An error has occured.");
    

}
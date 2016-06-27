<?php

require_once __DIR__ ."/../class/class.FileManager.php";

$fm = new FileManager();

$fm->deleteFolderWithFile(__DIR__."/../output");

echo date("o-m-d G:i:s") .": Output folder cleaned";
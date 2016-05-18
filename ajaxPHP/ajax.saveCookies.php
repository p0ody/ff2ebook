<?php

define("SEC_TIME_MONTH", 2592000);

if (isset($_POST["isSplit"]))
    setcookie("isSplit", $_POST["isSplit"], time()+SEC_TIME_MONTH);

if (isset($_POST["font"]))
    setcookie("font", $_POST["font"], time()+SEC_TIME_MONTH);

if (isset($_POST["filetype"]))
    setcookie("filetype", $_POST["filetype"], time()+SEC_TIME_MONTH);

if (isset($_POST["email"]))
{
    if (empty($_POST["email"]))
        setcookie("email", "", time() - 3600);

    setcookie("email", $_POST["email"], time() + SEC_TIME_MONTH);
}

if (isset($_POST["autodl"]))
    setcookie("autodl", $_POST["autodl"], time()+SEC_TIME_MONTH);
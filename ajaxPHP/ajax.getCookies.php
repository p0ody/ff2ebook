<?php
header('Content-type: application/json');

$data = Array();

if (isset($_COOKIE["isSplit"]))
    $data["isSplit"] = $_COOKIE["isSplit"];

if (isset($_COOKIE["font"]))
    $data["font"] = $_COOKIE["font"];

if (isset($_COOKIE["filetype"]))
    $data["filetype"] = $_COOKIE["filetype"];

if (isset($_COOKIE["email"]))
    $data["email"] = $_COOKIE["email"];

if (isset($_COOKIE["autodl"]))
    $data["autodl"] = $_COOKIE["autodl"];

echo json_encode($data);
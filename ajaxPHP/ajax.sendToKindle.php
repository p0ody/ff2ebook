<?php
require_once("../lib/PHPMailer/PHPMailerAutoload.php");

if (!isset($_POST["file"]) || !isset($_POST["filetype"]))
    die("No file specified.");

$filename = $_POST["file"];
$filetype = $_POST["filetype"];


if (!@file_exists("../archive/". $filename))
    die("Could not find file on server.");


if (!isset($_POST["email"]))
    die("No email address");

$email = $_POST["email"];

if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
    die("Invalid email.");

$subject = "Converterd Fanfiction";
if (isset($_POST["title"]) && isset($_POST["author"]))
    $subject = $_POST["title"] ." - ". $_POST["author"];

$att = $_POST["title"] ." - ". $_POST["author"] .".". $filetype;

$fromEmail = "ebook-sender@ff2ebook.com";

$mail = new PHPMailer();
$mail->setFrom($fromEmail, "FF2EBOOK");
$mail->addAddress($email);
$mail->Subject = $subject;
$mail->addAttachment("../archive/". $filename, $att);
$mail->Body = "Enjoy!";

if (!$mail->send())
{
    $msg = "Mailer Error: " . $mail->ErrorInfo;
}
else
{
    $msg = "Message sent!";
}

echo $msg;


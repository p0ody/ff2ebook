<?php
require_once("../lib/PHPMailer/PHPMailerAutoload.php");
require_once(__DIR__."/../conf/config.php");

if (!isset($_POST["file"]) || !isset($_POST["filetype"]))
    die("No file specified.");

$filename = $_POST["file"];
$filetype = $_POST["filetype"];


if (!@file_exists(__DIR__."/../archive/". $filename))
    die("Could not find file on server.");


if (!isset($_POST["email"]))
    die("No email address");

$email = $_POST["email"];

if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
    die("Invalid email.");

$subject = "Converted Fanfiction";
if (isset($_POST["title"]) && isset($_POST["author"]))
    $subject = $_POST["title"] ." - ". $_POST["author"];

$att = $_POST["title"] ." - ". $_POST["author"] .".". $filetype;

date_default_timezone_set('Etc/UTC');
$fromEmail = "ebook-sender@ff2ebook.com";
try {
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = Config::EMAIL_SMTP_SERVER;
    $mail->Port = Config::EMAIL_SMTP_PORT;
    $mail->Username = Config::EMAIL_SMTP_EMAIL;
    $mail->Password = Config::EMAIL_SMTP_PASSWORD;
    $mail->SMTPSecure = "ssl";

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
}
catch (Exception $e) {
    $msg = "Mailer Error: " . $e->getMessage();
}

echo $msg;


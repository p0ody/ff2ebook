<?php
require_once("./lib/PHPMailer/PHPMailerAutoload.php");
require_once(__DIR__."/conf/config.php");

$fromEmail = $_POST["email-address"];
$name = $_POST["name"];

if (filter_var($fromEmail, FILTER_VALIDATE_EMAIL) === false)
    die("Invalid email.");

$subject = "ff2ebook message from ". $fromEmail;

date_default_timezone_set('Etc/UTC');

try {
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = Config::EMAIL_SMTP_SERVER;
    $mail->Port = Config::EMAIL_SMTP_PORT;
    $mail->Username = Config::EMAIL_SMTP_EMAIL;
    $mail->Password = Config::EMAIL_SMTP_PASSWORD;
    $mail->SMTPSecure = "ssl";

    $mail->setFrom(Config::EMAIL_CONTACT, "ff2ebook: ". $name);
    $mail->addReplyTo($fromEmail, $name);
    $mail->addAddress(Config::EMAIL_CONTACT);
    $mail->Subject = $subject;
    $mail->Body = $_POST["email-body"];

    if (!$mail->send())
    {
        $msg = "Error while sending email";
    }
    else
    {
        $msg = "Message sent!";
    }
}
catch (Exception $e) {
    $msg = "Error while sending email";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FF2EBOOK :: Contact</title>
    
    <?php include("html/header.html") ?>
</head>
<body>
    <div class="container-fluid">
        <?php include("html/menu.html") ?>
        <!-- Input -->
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
				<?php echo $msg; ?>
            </div>
        </div>

    </div>
</body>
</html>

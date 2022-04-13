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
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-131272468-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-131272468-1');
    </script>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
        crossorigin="anonymous">
    </script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="default.css">
</head>
<body>
    <div class="container-fluid">
        <?php include("menu.html") ?>
        <!-- Input -->
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
				<?php echo $msg; ?>
            </div>
        </div>

    </div>
</body>
</html>

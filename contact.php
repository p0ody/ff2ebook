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
				<div id="contact">
					You can contact me via <a href="mailto: contact@ff2ebook.com" class="text-highlight bold">contact@ff2ebook.com</a> or with the form below.
				</div>
                <form class="form-group top-spacing" method="post" action="sendEmail.php" id="contact-form">
					<div>
						<label for="name" class="form-label align-left default-text">Name:</label>
						<input type="text" name="name" class="form-control" id="name" placeholder="John Doe" required>
					</div>

					<div>
						<label for="email-address" class="form-label align-left default-text">Email address:</label>
						<input type="email" name="email-address" class="form-control" id="email-address" placeholder="name@example.com" required>
					</div>

					<div>
						<label for="email-body" class="form-label default-text">Text to send:</label>
						<textarea class="form-control" name="email-body" id="email-body" rows="10" required></textarea>
					</div>

						<button class="btn btn-success float-left" type="submit">Send</button>
                </form>
            </div>
        </div>

    </div>
</body>
</html>

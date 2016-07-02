<?php require_once __DIR__."/conf/config.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FF2EBOOK :: Download your favorites FanFictions as eBooks.</title>
    <meta name="description" content="Convert fanfictions to ebooks.  Supports fanfiction.net, fictionpress.com, HarryPotterFanFiction.com, & HPFanFicArchive.com.  Convert to ePub and MOBI">
    <META NAME="ROBOTS" CONTENT="INDEX, NOFOLLOW">
    <script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.11.3.min.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    <script src="js/errorHandler.js"></script>
    <script src="js/functions.js"></script>
    <script src="js/ajax.functions.js"></script>
    <script src="js/main.js"></script>
    <link rel="stylesheet" type="text/css" href="default.css">
</head>
<body>
    <div class="container-fluid">
        <?php include("menu.html") ?>

        <!-- Progress bar -->
        <div class="row">
            <div class="col-xs-12 convert-progress">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                        <span class="sr-only"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 news col-md-offset-3 col-md-6">
                <ul class="news">
                    <li>If you are experiencing issues, you can still use <a href="./old">old version</a></li>
                    <li><a href="http://www.previewmywebsitenow.com/app/browse.php?u=http%3A%2F%2Fff2ebook.com%2Farchive%2Fold%2Fff_to_ebook_archive_2016-06-03.zip&b=4">Old archive available for download.</a></li>
                    <li>2016-07-02: Added support for hpfanficarchive.com</li>
                    <li>2016-06-26: Added support for FictionPress.com</li>
                </ul>
            </div>

        </div>

        <div id="status-area" class="row">
            <div class="col-xs-12">
                <span id="status-text"></span>
            </div>
        </div>

        <!-- Input -->
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <form class="form-group" id="fic-input-form">
                    <div class="input-group">
                        <input type="text" id="fic-url" class="form-control" placeholder="URL" value="" />
                        <span class="input-group-btn">
                            <button id="fic-input-submit" class="btn btn-default btn-block" >Go!</button>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Options -->
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="collapse-bg text-left" id="options-collapse" data-collapse-hidden="true">
                    <div class="collapse-header">Options:<span class="float-right">(Click to toggle)</span></div>
                    <div class="collapse-content">
                        <div class="form-group convert-options">
                            <!--<label for="fonttype">Font:</label>
                            <select class="form-control options" id="fonttype">
                                <option value="arial">Arial</option>
                            </select>-->

                            <?php if (! PORTABLE_MODE): ?>
                                <div class="checkbox">
                                    <label><input id="force-update" type="checkbox">Force update</label>
                                </div>
                            <?php endif; ?>

                            <div class="checkbox">
                                <label><input id="auto-dl" type="checkbox" checked="checked">Automatic Download</label>
                            </div>

                            <label for="filetype">File Type:</label>
                            <select class="form-control" id="filetype">
                                <option value="epub">ePub</option>
                                <option value="mobi">Mobi</option>
                                <option value="pdf" disabled>PDF</option>
                            </select>
                            <label for="kindle-email">eMail:</label><input type="email" id="kindle-email" class="form-control" placeholder="Send via eMail (Optional)" />
                            <br />Add ebook-sender@ff2ebook.com to your spam filter or Kindle Approved Personal Document E-mail List
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Infos -->
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="collapse-bg text-left"  data-collapse-hidden="true">
                    <div class="collapse-header">Infos:<span class="float-right">(Click to toggle)</span></div>
                    <div class="collapse-content">
                        Convert from your favorite sites<span class="text-highlight">:</span>
                        <ul>
                            <li>FanFiction<span class="text-highlight">.</span>net</li>
                            <li>FictionPress<span class="text-highlight">.</span>com</li>
                            <li><span class="text-highlight">TODO:</span> patronuscharm<span class="text-highlight">.</span>net (Maybe ?)</li>
                            <li>HarryPotterFanFiction<span class="text-highlight">.</span>com (Use URL finishing with <span class="text-highlight bold italic">/viewstory.php?psid=######</span>)</li>
                            <li>HPFanFicArchive<span class="text-highlight">.</span>com</li>
                            <li><span class="text-highlight">TODO:</span>ficwad<span class="text-highlight">.</span>com</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Output -->
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="collapse-bg text-left" data-collapse-hidden="true">
                    <div class="collapse-header">Output: <span id="warning-icon" class="glyphicon glyphicon-warning-sign text-warning" style="display: none"></span> <span id="critical-icon" class="glyphicon glyphicon-remove-circle text-critical"  style="display: none"></span><span class="float-right">(Click to toggle)</span></div>
                    <div class="collapse-content" id="output"></div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
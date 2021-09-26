<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FF2EBOOK :: Download your favorites FanFictions as eBooks.</title>
    <meta name="description" content="Convert fanfictions to ebooks.  Supports fanfiction.net, fictionpress.com, HarryPotterFanFiction.com, & HPFanFicArchive.com.  Convert to ePub and MOBI">
    <META NAME="ROBOTS" CONTENT="INDEX, NOFOLLOW">
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

    <script src="js/errorHandler.js"></script>
    <script src="js/functions.js"></script>
    <script src="js/ajax.functions20210926.js"></script>
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
                    <li>2021-09-17: After changing web host, i was able to reupload old fanfic from my archive backup and rebuilt the database.  So every fanfic in the torrent is now available in the archive search for download. (Over 400k)</li>
                    <li>2021-01-31: An old archive backup is available to download via a torrent <a href="magnet:?xt=urn:btih:462cb95d51a738bf242375bc4c2f98d79e4ca113&dn=ff2ebook-archive-2019-11-23-fix.7z&tr=udp%3A%2F%2Fpublic.popcorn-tracker.org%3A6969%2Fannounce&tr=http%3A%2F%2F104.28.1.30%3A8080%2Fannounce&tr=http%3A%2F%2F104.28.16.69%2Fannounce&tr=http%3A%2F%2F107.150.14.110%3A6969%2Fannounce&tr=http%3A%2F%2F109.121.134.121%3A1337%2Fannounce&tr=http%3A%2F%2F114.55.113.60%3A6969%2Fannounce&tr=http%3A%2F%2F125.227.35.196%3A6969%2Fannounce&tr=http%3A%2F%2F128.199.70.66%3A5944%2Fannounce&tr=http%3A%2F%2F157.7.202.64%3A8080%2Fannounce&tr=http%3A%2F%2F158.69.146.212%3A7777%2Fannounce&tr=http%3A%2F%2F173.254.204.71%3A1096%2Fannounce&tr=http%3A%2F%2F178.175.143.27%2Fannounce&tr=http%3A%2F%2F178.33.73.26%3A2710%2Fannounce&tr=http%3A%2F%2F182.176.139.129%3A6969%2Fannounce&tr=http%3A%2F%2F185.5.97.139%3A8089%2Fannounce&tr=http%3A%2F%2F188.165.253.109%3A1337%2Fannounce&tr=http%3A%2F%2F194.106.216.222%2Fannounce&tr=http%3A%2F%2F195.123.209.37%3A1337%2Fannounce&tr=http%3A%2F%2F210.244.71.25%3A6969%2Fannounce&tr=http%3A%2F%2F210.244.71.26%3A6969%2Fannounce&tr=http%3A%2F%2F213.159.215.198%3A6970%2Fannounce&tr=http%3A%2F%2F213.163.67.56%3A1337%2Fannounce&tr=http%3A%2F%2F37.19.5.139%3A6969%2Fannounce&tr=http%3A%2F%2F37.19.5.155%3A6881%2Fannounce&tr=http%3A%2F%2F46.4.109.148%3A6969%2Fannounce&tr=http%3A%2F%2F5.79.249.77%3A6969%2Fannounce&tr=http%3A%2F%2F5.79.83.193%3A2710%2Fannounce&tr=http%3A%2F%2F51.254.244.161%3A6969%2Fannounce&tr=http%3A%2F%2F59.36.96.77%3A6969%2Fannounce&tr=http%3A%2F%2F74.82.52.209%3A6969%2Fannounce&tr=http%3A%2F%2F80.246.243.18%3A6969%2Fannounce&tr=http%3A%2F%2F81.200.2.231%2Fannounce&tr=http%3A%2F%2F85.17.19.180%2Fannounce&tr=http%3A%2F%2F87.248.186.252%3A8080%2Fannounce&tr=http%3A%2F%2F87.253.152.137%2Fannounce&tr=http%3A%2F%2F91.216.110.47%2Fannounce&tr=http%3A%2F%2F91.217.91.21%3A3218%2Fannounce&tr=http%3A%2F%2F91.218.230.81%3A6969%2Fannounce&tr=http%3A%2F%2F93.92.64.5%2Fannounce&tr=http%3A%2F%2Fatrack.pow7.com%2Fannounce&tr=http%3A%2F%2Fbt.henbt.com%3A2710%2Fannounce&tr=http%3A%2F%2Fbt.pusacg.org%3A8080%2Fannounce&tr=http%3A%2F%2Fbt2.careland.com.cn%3A6969%2Fannounce&tr=http%3A%2F%2Fexplodie.org%3A6969%2Fannounce&tr=http%3A%2F%2Fmgtracker.org%3A2710%2Fannounce&tr=http%3A%2F%2Fmgtracker.org%3A6969%2Fannounce&tr=http%3A%2F%2Fopen.acgtracker.com%3A1096%2Fannounce&tr=http%3A%2F%2Fopen.lolicon.eu%3A7777%2Fannounce&tr=http%3A%2F%2Fopen.touki.ru%2Fannounce.php&tr=http%3A%2F%2Fp4p.arenabg.ch%3A1337%2Fannounce&tr=http%3A%2F%2Fp4p.arenabg.com%3A1337%2Fannounce&tr=http%3A%2F%2Fpow7.com%3A80%2Fannounce&tr=http%3A%2F%2Fretracker.gorcomnet.ru%2Fannounce&tr=http%3A%2F%2Fretracker.krs-ix.ru%2Fannounce&tr=http%3A%2F%2Fretracker.krs-ix.ru%3A80%2Fannounce&tr=http%3A%2F%2Fsecure.pow7.com%2Fannounce&tr=http%3A%2F%2Ft1.pow7.com%2Fannounce&tr=http%3A%2F%2Ft2.pow7.com%2Fannounce&tr=http%3A%2F%2Fthetracker.org%3A80%2Fannounce&tr=http%3A%2F%2Ftorrent.gresille.org%2Fannounce&tr=http%3A%2F%2Ftorrentsmd.com%3A8080%2Fannounce&tr=http%3A%2F%2Ftracker.aletorrenty.pl%3A2710%2Fannounce&tr=http%3A%2F%2Ftracker.baravik.org%3A6970%2Fannounce&tr=http%3A%2F%2Ftracker.bittor.pw%3A1337%2Fannounce&tr=http%3A%2F%2Ftracker.bittorrent.am%2Fannounce&tr=http%3A%2F%2Ftracker.calculate.ru%3A6969%2Fannounce&tr=http%3A%2F%2Ftracker.dler.org%3A6969%2Fannounce&tr=http%3A%2F%2Ftracker.dutchtracking.com%2Fannounce&tr=http%3A%2F%2Ftracker.dutchtracking.com%3A80%2Fannounce&tr=http%3A%2F%2Ftracker.dutchtracking.nl%2Fannounce&tr=http%3A%2F%2Ftracker.dutchtracking.nl%3A80%2Fannounce&tr=http%3A%2F%2Ftracker.edoardocolombo.eu%3A6969%2Fannounce&tr=http%3A%2F%2Ftracker.ex.ua%2Fannounce&tr=http%3A%2F%2Ftracker.ex.ua%3A80%2Fannounce&tr=http%3A%2F%2Ftracker.filetracker.pl%3A8089%2Fannounce&tr=http%3A%2F%2Ftracker.flashtorrents.org%3A6969%2Fannounce&tr=http%3A%2F%2Ftracker.grepler.com%3A6969%2Fannounce&tr=http%3A%2F%2Ftracker.internetwarriors.net%3A1337%2Fannounce&tr=http%3A%2F%2Ftracker.kicks-ass.net%2Fannounce&tr=http%3A%2F%2Ftracker.kicks-ass.net%3A80%2Fannounce&tr=http%3A%2F%2Ftracker.kuroy.me%3A5944%2Fannounce&tr=http%3A%2F%2Ftracker.mg64.net%3A6881%2Fannounce&tr=http%3A%2F%2Ftracker.opentrackr.org%3A1337%2Fannounce&tr=http%3A%2F%2Ftracker.skyts.net%3A6969%2Fannounce&tr=http%3A%2F%2Ftracker.tfile.me%2Fannounce&tr=http%3A%2F%2Ftracker.tiny-vps.com%3A6969%2Fannounce&tr=http%3A%2F%2Ftracker.tvunderground.org.ru%3A3218%2Fannounce&tr=http%3A%2F%2Ftracker.yoshi210.com%3A6969%2Fannounce&tr=http%3A%2F%2Ftracker1.wasabii.com.tw%3A6969%2Fannounce&tr=http%3A%2F%2Ftracker2.itzmx.com%3A6961%2Fannounce&tr=http%3A%2F%2Ftracker2.wasabii.com.tw%3A6969%2Fannounce&tr=http%3A%2F%2Fwww.wareztorrent.com%2Fannounce&tr=http%3A%2F%2Fwww.wareztorrent.com%3A80%2Fannounce&tr=https%3A%2F%2F104.28.17.69%2Fannounce&tr=https%3A%2F%2Fwww.wareztorrent.com%2Fannounce&tr=udp%3A%2F%2F107.150.14.110%3A6969%2Fannounce&tr=udp%3A%2F%2F109.121.134.121%3A1337%2Fannounce&tr=udp%3A%2F%2F114.55.113.60%3A6969%2Fannounce&tr=udp%3A%2F%2F128.199.70.66%3A5944%2Fannounce&tr=udp%3A%2F%2F151.80.120.114%3A2710%2Fannounce&tr=udp%3A%2F%2F168.235.67.63%3A6969%2Fannounce&tr=udp%3A%2F%2F178.33.73.26%3A2710%2Fannounce&tr=udp%3A%2F%2F182.176.139.129%3A6969%2Fannounce&tr=udp%3A%2F%2F185.5.97.139%3A8089%2Fannounce&tr=udp%3A%2F%2F185.86.149.205%3A1337%2Fannounce&tr=udp%3A%2F%2F188.165.253.109%3A1337%2Fannounce&tr=udp%3A%2F%2F191.101.229.236%3A1337%2Fannounce&tr=udp%3A%2F%2F194.106.216.222%3A80%2Fannounce&tr=udp%3A%2F%2F195.123.209.37%3A1337%2Fannounce&tr=udp%3A%2F%2F195.123.209.40%3A80%2Fannounce&tr=udp%3A%2F%2F208.67.16.113%3A8000%2Fannounce&tr=udp%3A%2F%2F213.163.67.56%3A1337%2Fannounce&tr=udp%3A%2F%2F37.19.5.155%3A2710%2Fannounce&tr=udp%3A%2F%2F46.4.109.148%3A6969%2Fannounce&tr=udp%3A%2F%2F5.79.249.77%3A6969%2Fannounce&tr=udp%3A%2F%2F5.79.83.193%3A6969%2Fannounce&tr=udp%3A%2F%2F51.254.244.161%3A6969%2Fannounce&tr=udp%3A%2F%2F62.138.0.158%3A6969%2Fannounce&tr=udp%3A%2F%2F62.212.85.66%3A2710%2Fannounce&tr=udp%3A%2F%2F74.82.52.209%3A6969%2Fannounce&tr=udp%3A%2F%2F85.17.19.180%3A80%2Fannounce&tr=udp%3A%2F%2F89.234.156.205%3A80%2Fannounce&tr=udp%3A%2F%2F9.rarbg.com%3A2710%2Fannounce&tr=udp%3A%2F%2F9.rarbg.me%3A2780%2Fannounce&tr=udp%3A%2F%2F9.rarbg.to%3A2730%2Fannounce&tr=udp%3A%2F%2F91.218.230.81%3A6969%2Fannounce&tr=udp%3A%2F%2F94.23.183.33%3A6969%2Fannounce&tr=udp%3A%2F%2Fbt.xxx-tracker.com%3A2710%2Fannounce&tr=udp%3A%2F%2Feddie4.nl%3A6969%2Fannounce&tr=udp%3A%2F%2Fexplodie.org%3A6969%2Fannounce&tr=udp%3A%2F%2Fmgtracker.org%3A2710%2Fannounce&tr=udp%3A%2F%2Fopen.stealth.si%3A80%2Fannounce&tr=udp%3A%2F%2Fp4p.arenabg.com%3A1337%2Fannounce&tr=udp%3A%2F%2Fshadowshq.eddie4.nl%3A6969%2Fannounce&tr=udp%3A%2F%2Fshadowshq.yi.org%3A6969%2Fannounce&tr=udp%3A%2F%2Ftorrent.gresille.org%3A80%2Fannounce&tr=udp%3A%2F%2Ftracker.aletorrenty.pl%3A2710%2Fannounce&tr=udp%3A%2F%2Ftracker.bittor.pw%3A1337%2Fannounce&tr=udp%3A%2F%2Ftracker.coppersurfer.tk%3A6969%2Fannounce&tr=udp%3A%2F%2Ftracker.eddie4.nl%3A6969%2Fannounce&tr=udp%3A%2F%2Ftracker.ex.ua%3A80%2Fannounce&tr=udp%3A%2F%2Ftracker.filetracker.pl%3A8089%2Fannounce&tr=udp%3A%2F%2Ftracker.flashtorrents.org%3A6969%2Fannounce&tr=udp%3A%2F%2Ftracker.grepler.com%3A6969%2Fannounce&tr=udp%3A%2F%2Ftracker.ilibr.org%3A80%2Fannounce&tr=udp%3A%2F%2Ftracker.internetwarriors.net%3A1337%2Fannounce&tr=udp%3A%2F%2Ftracker.kicks-ass.net%3A80%2Fannounce&tr=udp%3A%2F%2Ftracker.kuroy.me%3A5944%2Fannounce&tr=udp%3A%2F%2Ftracker.leechers-paradise.org%3A6969%2Fannounce&tr=udp%3A%2F%2Ftracker.mg64.net%3A2710%2Fannounce&tr=udp%3A%2F%2Ftracker.mg64.net%3A6969%2Fannounce&tr=udp%3A%2F%2Ftracker.opentrackr.org%3A1337%2Fannounce&tr=udp%3A%2F%2Ftracker.piratepublic.com%3A1337%2Fannounce&tr=udp%3A%2F%2Ftracker.sktorrent.net%3A6969%2Fannounce&tr=udp%3A%2F%2Ftracker.skyts.net%3A6969%2Fannounce&tr=udp%3A%2F%2Ftracker.tiny-vps.com%3A6969%2Fannounce&tr=udp%3A%2F%2Ftracker.yoshi210.com%3A6969%2Fannounce&tr=udp%3A%2F%2Ftracker2.indowebster.com%3A6969%2Fannounce&tr=udp%3A%2F%2Ftracker4.piratux.com%3A6969%2Fannounce&tr=udp%3A%2F%2Fzer0day.ch%3A1337%2Fannounce&tr=udp%3A%2F%2Fzer0day.to%3A1337%2Fannounce">Magnet link</a> or <a href="http://ff2ebook.com/torrent/ff2ebook-archive-2019-11-23-fix.torrent">Torrent file</a></li>
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

                            <div class="checkbox">
                                <label><input id="force-update" type="checkbox">Force update</label>
                            </div>

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
                            <li>FictionHunt<span class="text-highlight">.</span>com</li>
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
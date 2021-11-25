/**
 * Created by p0ody on 2015-06-26.
 */


function ajax_saveCookies()
{
    var /*vSplit, vFont, */vFiletype, vEmail, vAutoDL;

    /*vSplit = $("#is-split").prop("checked");
    vFont = $("#fonttype").find(":selected").val();*/
    vFiletype = $("#filetype").find(":selected").val();
    vEmail = $("#kindle-email").val();
    vAutoDL = hasAutoDownload();

    $.ajax
    ({
        url: "ajaxPHP/ajax.saveCookies.php",
        method: "POST",
        data: { /*isSplit: vSplit, font: vFont,*/ filetype: vFiletype, email: vEmail, autodl: vAutoDL },
        dataType: "json"
    });

}

function ajax_loadOptionsCookies()
{
    $.ajax
    ({
        url: "ajaxPHP/ajax.getCookies.php",
        dataType: "json"
    }).done(function(data)
    {
        if (data.length <= 0)
        {
            $("#options-collapse").children(".collapse-content").show();
            return;
        }

        /*if (data.isSplit)
            $("#is-split").prop("checked", data.isSplit ? true : false);*/

        /*if (data.font)
            $("#fonttype").val(data.font);*/

        if (data.filetype)
        {
            $("#filetype").val(data.filetype);
            $("#filetype").trigger("change");
        }

        if (data.email)
            $("#kindle-email").val(data.email);

        if (data.autodl)
            $("#auto-dl").prop('checked', data.autodl == "true");
    });
}

function ajax_getFicInfos()
{
    var url = $("#fic-url").val();
    var force = $("#force-update").is(':checked');

    _ajaxFicInfosTry = _ajaxFicInfosTry + 1;
    if (_ajaxFicInfosTry > AJAX_MAX_TRY)
        newError(ERROR_CRITICAL, "Failed to get Fic Infos, please try again later.");
    else if (_ajaxFicInfosTry > 1)
        newError(ERROR_WARNING, "Failed to get Fic Infos (Attempt "+ _ajaxFicInfosTry +"/"+ AJAX_MAX_TRY +"), attempting again.");

    $.ajax
    ({
        url: "ajaxPHP/ajax.getFicInfos.php", // Need URL
        method: "POST",
        data: { url: url, force: force },
        dataType: "json",
    }).done(function(data)
    {
        var failed = false;
        if (data.error != undefined || data.error != "undefined") {
            var errors = data.error
            
            $.each(errors, function (key, error) {
                if (error.code == ERROR_CRITICAL) {
                    failed = true;
                }
                else {
                    newError(error.code, error.message);
                }
            });
        }

        if (failed) {
            ajax_getFicInfos();
        }
        else {
            _ficData = data;

            if (data["exist"] == true)
            {
                if (data["site"] === "undefined" && data["id"] === "undefined")
                    return;

                statusOutput("<span class='text-ok'>File already on server...</span>");

                changeState(STATE_CONVERSION);
            }
            else
            {
                addPct(PCT_FIC_INFOS);
                nextState();
            }
        }
    }).fail(function()
    {
        ajax_getFicInfos();
    });
}

function ajax_getChapter(num)
{

    if (_ajaxChapterTry[num] === undefined)
        _ajaxChapterTry[num] = 0;

    _ajaxChapterTry[num] = _ajaxChapterTry[num] + 1;

    if (_ajaxChapterTry[num] > AJAX_MAX_TRY)
        newError(ERROR_CRITICAL, "Failed to get chapter #"+ num +", please try again later.");
    else if (_ajaxChapterTry[num] > 1)
        newError(ERROR_WARNING, "Failed to get chapter #"+ num +" (Attempt "+ _ajaxChapterTry[num] +"/"+ AJAX_MAX_TRY +"), attempting again.");

    _ajaxQueue.push(function()
    {
        $.ajax
        ({
            url: "ajaxPHP/ajax.getChapter.php",
            method: "POST",
            data: {chapNum: num},
            chapNum: num,
            dataType: "json"
        }).done(function (data) {
            var failed = false;
            if (data.error != undefined || data.error != "undefined") {
                var errors = data.error
                
                $.each(errors, function (key, error) {
                    if (error.code == ERROR_CRITICAL) {
                        failed = true;
                    }
                    else {
                        newError(error.code, error.message);
                    }
                });
            }

            if (failed) {
                ajax_getChapter(this.chapNum);
            }
            else {
                statusOutput("<span class='text-ok'>Received chapter #" + this.chapNum + " data.</span>");

                if (typeof _ficData.chapReady === "undefined")
                    _ficData.chapReady = [];

                _ficData.chapReady.push(this.chapNum);

                setStatusText("Chapters: " + _ficData.chapReady.length + "/" + _ficData.chapCount);
                addPct(PCT_CHAPTERS / parseFloat(_ficData.chapCount));

                if (_ficData.chapReady.length === parseInt(_ficData.chapCount))
                    nextState();
            }
        }).fail(function ()
        {
            ajax_getChapter(this.chapNum);
        }).always(function()
        {
            substractCurrentCalls();
        });
    });
}

function ajax_createFile()
{
    setStatusText("Creating eBook...");
    $.ajax
    ({
        url: "ajaxPHP/ajax.createFile.php",
        method: "POST",
        dataType: "json"
    }).done(function(data)
    {
        var errors = checkForError(data);

        $.each(errors, function(index, error)
        {
            newError(error.code, error.message)
        });

        if (_ficData["site"] === "undefined" || _ficData["id"] === "undefined")
            changeState(STATE_ERROR);


        addPct(PCT_FILE_CREATION);
        nextState();

    });
}


function ajax_sendToKindle()
{
    statusOutput("<span class='text-ok'>Sending email to "+ $("#kindle-email").val() +"...</span>");

    $.ajax
    ({
        url: "ajaxPHP/ajax.sendToKindle.php",
        method: "POST",
        data: {
            email: $("#kindle-email").val(),
            title: _ficData["title"],
            author: _ficData["author"],
            file: _ficData["site"] + "_" + _ficData["id"] + "_" + _ficData["updated"] + "." + $("#filetype").val(),
            filetype: $("#filetype").val()
        },
        dataType: "json"
    });
}

function ajax_convert()
{
    addPct(PCT_CONVERSION);

    if ($("#filetype").val() == "epub")
    {
        setPct(100);
        changeState(STATE_READY);
        downloadReady(_ficData["site"], _ficData["id"]);
        return;
    }

    $.ajax
    ({
        url: "ajaxPHP/ajax.convert.php",
        method: "POST",
        data: {
            filetype: $("#filetype").val()
        },
        dataType: "json"
    }).done(function (data)
    {
        var errors = checkForError(data);
        $.each(errors, function (index, error) {
            newError(error.code, error.message)
        });
        setPct(100);
        changeState(STATE_READY);
        downloadReady(_ficData["site"], _ficData["id"]);
    });
}

function ajaxQueueHandler()
{
    if (_ajaxQueue.length > 0) 
        setTimeout(ajaxQueueHandler, AJAX_CALLS_DELAY_MS);
    else
        return;

    if (_ajaxCurrentCalls < AJAX_MAX_CALLS)
    {
        var spliceLen = AJAX_MAX_CALLS - _ajaxCurrentCalls;
        if (spliceLen < 1) {
            spliceLen = 1;
        }

        var newCalls = _ajaxQueue.splice(0, spliceLen);

        $.each(newCalls, function(index, value)
        {
            value();
            addCurentCalls();
        });
    }
}

function substractCurrentCalls()
{
    if (_ajaxCurrentCalls > 0)
        _ajaxCurrentCalls--;

    //ajaxQueueHandler();

}

function addCurentCalls()
{
    _ajaxCurrentCalls++;
    //ajaxQueueHandler();
}
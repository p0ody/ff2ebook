function changeState(newState)
{
    switch(newState)
    {
        case STATE_READY:
            _state = newState;
            disableForm(false);
            //resetOutput();
            _ajaxFicInfosTry = 0;
            _ajaxChapterTry = [];
            setStatusText("Ready!");
            break;

        case STATE_INFOS:
            _state = newState;
            setPct(PCT_MIN);
            resetOutput();
            disableForm(true);
            statusOutput("<span class='text-ok'>Collecting Fiction infos...</span>");
            ajax_getFicInfos();
            setStatusText("Collecting infos...");
            break;

        case STATE_CHAPTERS:
            _state = newState;
            disableForm(true);
            statusOutput("<span class='text-ok'>Collecting chapters data...</span>");
            setStatusText("Collecting chapters data...");

            if (typeof _ficData.chapCount === "undefined")
                newError(ERROR_CRITICAL, "Couldn't find chapter count.");

            for (i = 1 ; i <= _ficData.chapCount ; i++)
            {
                ajax_getChapter(i);
            }
            ajaxQueueHandler();

            break;

        case STATE_ERROR:
            _state = newState;
            //newError(ERROR_CRITICAL, "An error has occured, please try again.")
            changeState(STATE_READY);
            throw new Error("An error has occured and the script has been stopped.");
            break;

        case STATE_FILE_CREATION:
            _state = newState;
            disableForm(true);
            statusOutput("<span class='text-ok'>Creating archive file....</span>");
            ajax_createFile();
            break;

        case STATE_CONVERSION:
            _state = newState;
            disableForm(true);
            statusOutput("<span class='text-ok'>Converting to desired file format...</span>");
            ajax_convert();
            break;

        default:
            changeState(STATE_READY);
            break;
    }
}


function nextState()
{
    if (_state < STATE_MAX_STATE)
        changeState(_state + 1);
    else
        changeState(STATE_READY);
}

function disableForm(bool)
{
    $("#fic-input-submit").attr("disabled", bool);
    $("#filetype").attr("disabled", bool);
    $("#auto-dl").attr("disabled", bool);
    $("#force-update").attr("disabled", bool);
    $("#kindle-email").attr("disabled", bool);
}

function statusOutput(text)
{
    $("#output").append(text +"<br />");
}

function resetOutput()
{
    $("#output").html("");
    $("#warning-icon").hide();
    $("#critical-icon").hide();
}

function setStatusText(text)
{
    $("#status-text").html(text);

}

function addPct(pct)
{
    pct = parseFloat(pct);
    if (!isNumber(pct))
        return;

    var old = parseFloat($(".convert-progress .progress .progress-bar").attr("aria-valuenow"));

    setPct(old + pct);
}

function setPct(pct)
{
    pct = parseFloat(pct);
    if (!isNumber(pct))
        return;

    var progress = $(".convert-progress .progress .progress-bar");

    if (progress.attr("aria-valuenow") > 100)
        progress.attr("aria-valuenow", 100);

    if (progress.attr("aria-valuenow") < 0)
        progress.attr("aria-valuenow", 0);

    progress.attr("aria-valuenow", pct);

    progress.css({width: pct + "%" });
}

function isNumber(obj)
{
    return !isNaN(parseFloat(obj));
}

function hasAutoDownload()
{
    return $("#auto-dl").is(':checked');
}

function downloadLink(source, id, linkText)
{
    return "<a id=\"download-link\" href=\"download.php?source="+ source +"&id="+ id +"&filetype="+ $("#filetype").val() +"\">"+ linkText +"</a>";
}

function downloadReady(source, id)
{
    setStatusText(downloadLink(source, id, "Download here!"));

    if (hasAutoDownload())
    {
        statusOutput("<span class='text-ok'>Starting download...</span>");
        $("#download-link")[0].click();
    }

    if ($("#kindle-email").val().length > 0)
    {
        ajax_sendToKindle();
    }
}
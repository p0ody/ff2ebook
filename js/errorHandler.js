// Same as PHP
const ERROR_CRITICAL    = 0;
const ERROR_WARNING     = 1;
const ERROR_BLACKLISTED = 2;


function getErrorType(code)
{
    switch(code)
    {
        case ERROR_CRITICAL:
            return "Critical Error";

        case ERROR_WARNING:
            return "Warning";

        case ERROR_BLACKLISTED:
            return "Cancelled";

        default:
            return "";
    }
}

function getErrorColorClass(code)
{
    switch(parseInt(code))
    {
        case ERROR_CRITICAL:
        case ERROR_BLACKLISTED:
            return "text-critical";

        case ERROR_WARNING:
            return "text-warning";

        default:
            return "";
    }
}

function newError(code, message)
{
    printError(code, message);
    if (code == ERROR_CRITICAL || code == ERROR_BLACKLISTED) {
        changeState(STATE_ERROR);
    }
}

function checkForError(data)
{
    var errors = [];
    if (data.error != undefined)
    {
        $.each(data.error, function(key, value) {
            if (key.substr(0, key.indexOf("_")) == "code")
            {
                temp = { code: value, message: data.error["message_"+number] };
                errors[number] = temp;
            }
        });
    }

    return errors;
}

function printError(code, message)
{
    $("#output").append("<div class=\""+ getErrorColorClass(code) +"\">"+  getErrorType(code) +": "+ message +"</div>");

    if (code == ERROR_WARNING)
        $("#warning-icon").show();

    if (code == ERROR_CRITICAL || code == ERROR_BLACKLISTED)
        $("#critical-icon").show();
}

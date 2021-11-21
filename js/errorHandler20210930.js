// Same as PHP
var ERROR_CRITICAL   = 0;
var ERROR_WARNING   = 1;


function getErrorType(code)
{
    switch(code)
    {
        case ERROR_CRITICAL:
            return "Critical Error";

        case ERROR_WARNING:
            return "Warning";

        default:
            return "";
    }
}

function getErrorColorClass(code)
{
    switch(parseInt(code))
    {
        case ERROR_CRITICAL:
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
    if (code == ERROR_CRITICAL)
        changeState(STATE_ERROR);
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

    if (code == ERROR_CRITICAL)
        $("#critical-icon").show();
}
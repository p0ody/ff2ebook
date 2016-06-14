/**
 * Created by p0ody on 2015-05-08.
 */

var STATE_ERROR         = -1;
var STATE_READY         = 0;
var STATE_INFOS         = 1;
var STATE_CHAPTERS      = 2;
var STATE_FILE_CREATION = 3;
var STATE_CONVERSION    = 4;
var STATE_MAX_STATE     = 5;
var AJAX_MAX_TRY        = 3;
var AJAX_MAX_CALLS      = 10;
var PCT_FIC_INFOS       = 20;
var PCT_CHAPTERS        = 65;
var PCT_FILE_CREATION   = 5;
var PCT_CONVERSION      = 5;
var PCT_MAX             = 100;
var PCT_MIN             = 0;

var _ficData = null;
var _state = STATE_READY;
var _ajaxFicInfosTry = 0;
var _ajaxChapterTry = [];
var _ajaxCurrentCalls = 0;
var _ajaxQueue = [];

$(document).ready(
    function() {
        // Functions to run right after document is ready
        ajax_loadOptionsCookies();
        changeState(STATE_READY);


        // Fix the top right menu focus shadow
        $(".menu-button").focus(function (event) // Remove button focus outline
        {
            event.target.blur();
        });

        // handler for collapse box
        $(".collapse-header").click(toggleCollapse);

        /*// Hide collapse box when has collapse-hidden = true
        $(".collapse-bg[data-collapse-hidden='true']").children(".collapse-content").hide();*/


        $("#fic-input-form").submit(function (e) {
            e.preventDefault();
            ajax_saveCookies();

            if ($("#fic-url").val().length === 0)
                return;

            if (_state != STATE_READY)
                return;

            changeState(STATE_INFOS);
        });

    }
);

function toggleCollapse(event)
{
    $(this).next(".collapse-content").toggle("slow");
}

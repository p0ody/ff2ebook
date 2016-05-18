$(document).ready(
    function()
    {
        // Fix the top right menu focus shadow
        $(".menu-button").focus(function (event) // Remove button focus outline
        {
            event.target.blur();
        });


        $("#archive-search").submit(function (e)
        {
            if ($("#archive-search-input").val().length === 0)
                e.preventDefault();

        });

        if (getURLVars()["search"] !== "undefined" && getURLVars()["search"].length > 0)
            ajax_sendSearch();

    }
);


function ajax_sendSearch()
{

    $.ajax
    ({
        url: "ajaxPHP/ajax.archive.php",
        method: "POST",
        data: { search: $("#archive-search-input").val() },
        dataType: "json"
    }).done(function(data)
    {
        var errors = checkForError(data);

        $.each(errors, function(index, error)
        {
            newError(error.code, error.message)
        });

        if (data["count"] > 0)
            $("#search-result").html(data["result"]);
        else
            $("#search-result").html("No result found.");


    });
}


function getURLVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}
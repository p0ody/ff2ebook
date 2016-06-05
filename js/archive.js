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

        if (getURLVars()["search"] != undefined && getURLVars()["search"].length > 0)
            ajax_sendSearch();
    }
);


function ajax_sendSearch()
{
    var page = getURLVars()["page"];
    if (page == undefined)
    page = 1;

    $.ajax
    ({
        url: "ajaxPHP/ajax.search.php",
        method: "POST",
        data: {
            searchInput: $("#archive-search-input").val(),
            page: page
        },
        dataType: "json"
    }).done(function(data)
    {
        var errors = checkForError(data);

        $.each(errors, function(index, error)
        {

            $("#search-result").html(error.message);
        });

        $("#search-result").html(data.pageResults);
        $(".pagi").html(data.pagination);
        $(".results-count").html(data.count + " found");

    });

}

function getURLVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

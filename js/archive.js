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

        //$(document).on("click", "tr", createPopover);
    }
);


function ajax_sendSearch()
{
    var page = getURLVars()["page"];
    var sort = getURLVars()["sort"];
    if (page == undefined)
        page = 1;

    if (sort == undefined)
        sort = "NAME";

    $.ajax
    ({
        url: "ajaxPHP/ajax.search.php",
        method: "POST",
        data: {
            searchInput: $("#archive-search-input").val(),
            page: page,
            sort: sort
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

        $(".table-mobile > tbody > tr").each(createPopover);

    });

}

function getURLVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

function createPopover()
{
    var source = $(this).data("source");
    var sourceURL = $(this).data("source-url");
    var ficURL = $(this).data("fic-url");
    var title = $(this).data("title");
    var author = $(this).data("author");
    var updated = $(this).data("updated");
    var epub = $(this).data("download-epub");
    var mobi = $(this).data("download-mobi");

    var content =
        "Title: <a href=\""+ ficURL +"\">"+ title +"</a><br />"+
        "Author: "+ author +"<br />"+
        "Source: <a href=\""+ sourceURL +"\">"+ source +"</a><br />"+
        "Last Updated: "+ updated +"<br />"+
        "<a href=\""+ epub +"\">Download EPUB</a>  <a href=\""+ mobi +"\">Download MOBI</a>";

    $(this).popover(
        {
            content: content,
            title: title,
            html: true,
            placement: "bottom",
            trigger: "focus"
        }
    );
}
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
    }
);

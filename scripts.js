jQuery(function ($) {
    $(document).ready( function() {
        $(".toggle_filters_button").on("click", () => {
            // Text ändern
            $("#th_filters").slideToggle(300);
            $(".toggle_filters_icon").toggleClass("rotate");
        })
    });
});
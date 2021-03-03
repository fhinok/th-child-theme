jQuery(function ($) {
    $.fn.extend({
        toggleText: function(a, b){
            return this.text(this.text() == b ? a : b);
        }
    });

    $(document).ready( function() {
        $(".toggle_filters_button").on("click", () => {
            $("#th_filters").slideToggle(300);
            $(".toggle_filters_action").toggleText("ausblenden", "einblenden");
            $(".toggle_filters_icon").toggleClass("rotate");
        })
    });
});
jQuery(function ($) {
    $.fn.extend({
        toggleText: function(a, b){
            return this.text(this.text() == b ? a : b);
        },
        toggleHTML: function(a, b){
            return this.html(this.html() == b ? a : b);
        }
    });

    $(document).ready( function() {
        $(".toggle_filters_button").on("click", () => {
            $("#th_filters").slideToggle(300);
            $(".toggle_filters_action").toggleText("ausblenden", "einblenden");
            $(".toggle_filters_icon").toggleClass("rotate");
        })

        // Hack um Hauptkategoire auszublenden
        function remove_cat_from_filter(cats) {
            var el = "";
            try {
                $.each(cats, (key, cat) => {
                    el = $('input[value=' + cat +']').parent()
                    el.css('display', 'none');
                });
            }
            catch(e) {
                return;
            }
            return
        }
        
        cats = ['frischprodukte', 'backwaren', 'teigwaren'];
        remove_cat_from_filter(cats);
        
        $(document).on('berocket_ajax_products_loaded', function() {
            remove_cat_from_filter(cats);
        });
    });

    $(document).ready( function() {
        $('.woocommerce-product-attributes-item__value p').html( function() {
            var text = $(this).text();
            allergene.forEach(allergen => {
                var regex = new RegExp('\\b(\\p{L}*)?(' + allergen + ')(\\p{L}+)?\\b', 'giu');
                var match = text.match(regex);
                if (match) match.forEach(value => {
                    text = text.replace(value, '<strong class="allergen">' + value + '</strong>');
                })
                return text;
            });
            $(this).html(text);
        });
    });

    $(document).ready( function() {
		$('.contact_map-toggle').on('click', function() {
            $(this).toggleHTML('<span class="dashicons dashicons-location"></span>', '<span class="dashicons dashicons-email"></span>');
			$(this).toggleClass('open');
		});
	});
});
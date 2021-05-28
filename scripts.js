jQuery(function ($) {
    // add functions to toggle text and html to jquery
    $.fn.extend({
        toggleText: function(a, b){
            return this.text(this.text() == b ? a : b);
        },
        toggleHTML: function(a, b){
            return this.html(this.html() == b ? a : b);
        }
    });

    // Show/Hide filters on mobile
    $(document).ready( function() {
        $(".toggle_filters_button").on("click", () => {
            $("#th_filters").slideToggle(300);
            $(".toggle_filters_action").toggleText("ausblenden", "einblenden");
            $(".toggle_filters_icon").toggleClass("rotate");
        })

        // Hack to remove main categories from ajax filter
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

    // automatically bold all allergenes in product attributes
    $(document).ready( function() {
        $('.woocommerce-product-attributes-item__value p').html( function() {
            var text = $(this).text();
            allergene.forEach(allergen => {
                var regex = new RegExp('\\b(?!kakaobutter)(\\p{L}*)?(' + allergen + ')(\\p{L}+)?\\b', 'giu');
                text = text.replace(regex, '<strong class="allergen">$1$2$3</strong>');
                return text;
            });

            text = text.replace('Allergien', '<br><br>Allergien')
            console.log(text);
            $(this).html(text);
        });
    });

    // function to toggle between map and contact form
    $(document).ready( function() {
		$('.contact_map-toggle').on('click', function() {
            var htmlMap = '<span class="dashicons dashicons-location"></span><span class="hidden-mobile">Karte anzeigen</span>';
            var htmlContact = '<span class="dashicons dashicons-email"></span><span class="hidden-mobile">Kontakt anzeigen</span>';
            $(this).toggleHTML(htmlMap, htmlContact);
			$(this).toggleClass('open');
		});
	});

    // Datepicker on checkout settings
    $(document).ready( function() {

        // check if next days are on weekend and set to monday
        var tomorrow = new Date();
        var tomorrow_allowed = () => {
            tomorrow.setDate(tomorrow.getDate() + 1);
            if (tomorrow.getDay() === 6) {
                tomorrow.setDate(tomorrow.getDate() + 2);
            }

            if (tomorrow.getDay() === 0) {
                tomorrow.setDate(tomorrow.getDate() + 1);
            }

            return tomorrow;
        }

        // init datepicker
        $('#shipping_date').datepicker({
            offset: -22,
            language: 'de-DE',
            format: 'dd.mm.YYYY',
            date: tomorrow_allowed(tomorrow),
            startDate: new Date(),
            weekStart: 1,
            autoPick: true,
            filter: (date, view) => {
                if(date.getDay() === 0 && view === 'day' || date.getDay() === 6 && view === 'day') {
                    return false;
                }
            }
        });

        // if customer picks today, show message
        $('#shipping_date').on('pick.datepicker', function (e) {
            var today = new Date();
            today.setHours(0,0,0,0);
            if (e.date.valueOf() === today.valueOf()) {
                $('#shipping_date').closest('.form-row').append("<p class='sameday'>Für eine Lieferung heute kontaktieren Sie bitte das Töpferhaus!</p>");
            } else {
                $('.sameday').remove();
            }
          });
    });
});
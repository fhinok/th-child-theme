var ULTRAADDONS_ADDONS_DATA = {
    ajaxurl: "http:\/\/shop.local\/wp-admin\/admin-ajax.php",
    ajax_url:
      "http:\/\/shop.local\/wp-admin\/admin-ajax.php",
    site_url: "http:\/\/shop.local",
    checkout_url: "http:\/\/shop.local\/checkout\/",
    cart_url: "http:\/\/shop.local\/cart\/",
  };

jQuery(function (a) {
    "use strict",
    a(document).ready(function () {
      b();
      function b() {
        a(".quantity_cart_plus_minus").val(0),
          a.ajax({
            type: "POST",
            url: ULTRAADDONS_ADDONS_DATA.ajax_url,
            data: { action: "ultraaddons_cart_count_info" },
            complete: function () {},
            success: function (c) {
              var d = c.fragments,
                b;
              try {
                (b = d.ultraaddons_cart),
                  b &&
                    a.each(b, function (b, c) {
                      "string" == typeof b &&
                        a(
                          "#product_id_" + b + " input.input-text.qty.text"
                        ).val(c);
                    });
              } catch (a) {}
            },
            error: function () {},
          });
      }
      a("body").on("change", ".quantity_cart_plus_minus", function () {
        var b = a(this).val(),
          c = a(this).closest("tr").data("product_id");
        console.log(ULTRAADDONS_ADDONS_DATA.ajax_url),
          a.ajax({
            type: "POST",
            url: ULTRAADDONS_ADDONS_DATA.ajax_url,
            data: {
              action: "ultraaddons_addons_qty_ajax_update",
              qty_val: b,
              product_id: c,
            },
            complete: function () {
              a(document.body).trigger("updated_cart_totals"),
                a(document.body).trigger("wc_fragments_refreshed"),
                a(document.body).trigger("wc_fragments_refresh"),
                a(document.body).trigger("wc_fragment_refresh");
            },
            success: function (b) {
              a(".saiful_click_wrapper").html(b), console.log(b);
              var c = b.fragments;
              try {
                c &&
                  a.each(c, function (b, c) {
                    "string" == typeof b &&
                      typeof a(b) == "object" &&
                      a(b).replaceWith(c);
                  });
              } catch (a) {}
            },
            error: function () {},
          });
      });
    });
});

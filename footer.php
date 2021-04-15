<?php
/**
 * The template for displaying the footer.
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

$back_to_top_class = mfn_opts_get('back-top-top');

if ($back_to_top_class == 'hide') {
	$back_to_top_position = false;
} elseif (strpos($back_to_top_class, 'sticky') !== false) {
	$back_to_top_position = 'body';
} elseif (mfn_opts_get('footer-hide') == 1) {
	$back_to_top_position = 'footer';
} else {
	$back_to_top_position = 'copyright';
}
?>

<?php do_action('mfn_hook_content_after'); ?>

<?php if ('hide' != mfn_opts_get('footer-style')): ?>

	<footer id="Footer" class="clearfix">

		<?php if ($footer_call_to_action = mfn_opts_get('footer-call-to-action')): ?>
		<div class="footer_action">
			<div class="container">
				<div class="column one column_column">
					<?php echo do_shortcode($footer_call_to_action); ?>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<?php
			$sidebars_count = 0;
			for ($i = 1; $i <= 5; $i++) {
				if (is_active_sidebar('footer-area-'. $i)) {
					$sidebars_count++;
				}
			}

			if ($sidebars_count > 0) {

				$align = mfn_opts_get('footer-align');

				echo '<div class="widgets_wrapper '. $align .'">';
				
				echo '<div class="container">';

						if ($footer_layout = mfn_opts_get('footer-layout')) {

							// Theme Options

							$footer_layout 	= explode(';', $footer_layout);
							$footer_cols = $footer_layout[0];

							for ($i = 1; $i <= $footer_cols; $i++) {
								if (is_active_sidebar('footer-area-'. $i)) {
									echo '<div class="column '. esc_attr($footer_layout[$i]) .'">';
										dynamic_sidebar('footer-area-'. $i);
									echo '</div>';
								}
							}

						} else {

							// default with equal width

							$sidebar_class = '';
							switch ($sidebars_count) {
								case 2: $sidebar_class = 'one-second'; break;
								case 3: $sidebar_class = 'one-third'; break;
								case 4: $sidebar_class = 'one-fourth'; break;
								case 5: $sidebar_class = 'one-fifth'; break;
								default: $sidebar_class = 'one';
							}

							for ($i = 1; $i <= 5; $i++) {
								if (is_active_sidebar('footer-area-'. $i)) {
									echo '<div class="column '. esc_attr($sidebar_class) .'">';
										dynamic_sidebar('footer-area-'. $i);
									echo '</div>';
								}
							}

						}
					echo '</div>';
				echo '</div>';
			}
		?>

		<?php if (mfn_opts_get('footer-hide') != 1): ?>

			<div class="footer_copy">
				<div class="container">
					<div class="column one">

						<?php
							if ($back_to_top_position == 'copyright') {
								echo '<a id="back_to_top" class="footer_button" href=""><i class="icon-up-open-big"></i></a>';
							}
						?>

						<div class="copyright">
							<?php
								if (mfn_opts_get('footer-copy')) {
									echo do_shortcode(mfn_opts_get('footer-copy'));
								} else {
									echo '&copy; '. esc_html(date('Y')) .' <a href="https://toepferhaus.ch">'. esc_html(get_bloginfo('name')) .'</a>. All Rights Reserved. ';
								}
							?>
						</div>

                        <div class="links">
							<a href="/impressum">&nbsp;Impressum</a>. <a href="/datenschutz">Datenschutzerklärung</a>. <a href="/agb">AGB</a>
                        </div>

						<?php
							if (has_nav_menu('social-menu-bottom')) {
								mfn_wp_social_menu_bottom();
							} else {
								get_template_part('includes/include', 'social');
							}
						?>

					</div>
				</div>
			</div>

		<?php endif; ?>

		<?php
			if ($back_to_top_position == 'footer') {
				echo '<a id="back_to_top" class="footer_button in_footer" href=""><i class="icon-up-open-big"></i></a>';
			}
		?>

	</footer>
<?php endif; ?>

</div>

<?php
	// side slide menu
	if (mfn_opts_get('responsive-mobile-menu')) {
		get_template_part('includes/header', 'side-slide');
	}
?>

<?php
	if ($back_to_top_position == 'body') {
		echo '<a id="back_to_top" class="footer_button '. esc_attr($back_to_top_class) .'" href=""><i class="icon-up-open-big"></i></a>';
	}
?>

<?php if (mfn_opts_get('popup-contact-form')): ?>
	<div id="popup_contact">
		<a class="footer_button" href="#"><i class="<?php echo esc_attr(mfn_opts_get('popup-contact-form-icon', 'icon-mail-line')); ?>"></i></a>
		<div class="popup_contact_wrapper">
			<?php echo do_shortcode(mfn_opts_get('popup-contact-form')); ?>
			<span class="arrow"></span>
		</div>
	</div>
<?php endif; ?>

<?php do_action('mfn_hook_bottom'); ?>
<script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCipobJfajxCJblhNpQj9pS2ahW4P2pSaw&callback=initMap&libraries=&v=weekly"
      async
    ></script>

<script>
	let map;
	let markers = [];
	function initMap() {
		const center_location = { lat: 47.38083812629795, lng: 8.06602044797135 };
		const atelier_location = { lat: 47.39060611657107, lng: 8.053645363362653 };
		const bachstrasse_location = { lat: 47.38636673097061, lng: 8.057152546717441 };
		const suhr_location = { lat: 47.3671549277468, lng: 8.08368075893751 };

		let info = (titel, angebot) => {
			var text = `
			<div id="content">
				<h1 id="firstHeading" class="firstHeading">${ titel }</h1>
				<div id="bodyContent">
					<p>Hier können Sie ${ angebot } abholen</p>
				</div>
			</div>`
			return text;
		}

		map = new google.maps.Map(document.getElementById('map'), {
			center: center_location,
			zoom: 13,
			disableDefaultUI: true,
		})

		const marker_atelier = addMarker(atelier_location);
		const marker_bachstrasse = addMarker(bachstrasse_location);
		const marker_suhr = addMarker(suhr_location);

		const atelier_button = document.getElementById("atelier");
		const bachstrasse_button = document.getElementById("bachstrasse");
		const suhr_button = document.getElementById("suhr");

		google.maps.event.addDomListener(atelier_button, 'click', (e) => {
			e.preventDefault();
			zoomLocation(atelier_location);
			info_atelier.open(map, marker_atelier);
			// remove moouseover events
		});

		google.maps.event.addDomListener(bachstrasse_button, 'click', (e) => {
			e.preventDefault();
			zoomLocation(bachstrasse_location);
			info_bachstrasse.open(map, marker_bachstrasse);
		});

		google.maps.event.addDomListener(suhr_button, 'click', (e) => {
			e.preventDefault();
			zoomLocation(suhr_location);
			info_suhr.open(map, marker_suhr);
		});

		const info_atelier = new google.maps.InfoWindow({
			content: info('Tagesstätte', 'Karten und pataBee'),
		});

		const info_bachstrasse = new google.maps.InfoWindow({
			content: info('Bachstrasse', 'Frischprodukte'),
		});

		const info_suhr = new google.maps.InfoWindow({
			content: info('Suhr (ab Sommer 2021)', 'Teigwaren und Backwaren'),
		});

		marker_atelier.addListener('click', () => {
			info_bachstrasse.close();
			info_suhr.close();
			info_atelier.open(map, marker_atelier);
		});

		marker_bachstrasse.addListener('click', () => {
			info_atelier.close();
			info_suhr.close();
			info_bachstrasse.open(map, marker_bachstrasse);
		});

		marker_suhr.addListener('click', () => {
			info_atelier.close();
			info_bachstrasse.close();
			info_suhr.open(map, marker_suhr);
		});
	}

	function addMarker(location) {
		const marker = new google.maps.Marker({
			position: location,
			map,
		});
		return marker;
	}

	function zoomLocation(location) {
		var el = document.querySelector( '.contact_map-toggle' );
		el.classList.add('open');
		el.innerHTML = '<span class="dashicons dashicons-email"></span><span class="hidden-mobile">Kontakt anzeigen</span>';
		map.panTo(location);
		map.setZoom(16);
	}

</script>
<?php wp_footer(); ?>
</body>
</html>

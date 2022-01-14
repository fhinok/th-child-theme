<?php
/**
 * Betheme Child Theme
 *
 * @package Betheme Child Theme
 * @author Muffin group
 * @link https://muffingroup.com
 */

/**
 * Child Theme constants
 * You can change below constants
 */

// white label

define('WHITE_LABEL', false);

/**
 * Enqueue Styles
 */

function mfnch_enqueue_styles()
{
	// enqueue the parent stylesheet
	// however we do not need this if it is empty
	// wp_enqueue_style('parent-style', get_template_directory_uri() .'/style.css');

	// enqueue the parent RTL stylesheet

	if (is_rtl()) {
		wp_enqueue_style('mfn-rtl', get_template_directory_uri() . '/rtl.css');
	}

	// enqueue the child stylesheet

	wp_dequeue_style('style');
	wp_enqueue_style('style', get_stylesheet_directory_uri() .'/style.css');
}
add_action('wp_enqueue_scripts', 'mfnch_enqueue_styles', 101);

/**
 * Load Textdomain
 */

function mfnch_textdomain()
{
	load_child_theme_textdomain('betheme', get_stylesheet_directory() . '/languages');
	load_child_theme_textdomain('mfn-opts', get_stylesheet_directory() . '/languages');
}
add_action('after_setup_theme', 'mfnch_textdomain');

add_filter('body_class', function($classes) {
    global $current_user;
    
    foreach ($current_user->roles as $user_role) {
        $classes[] = 'role-'. $user_role;
    }

    return $classes;
});


/**
 * enque all scripts and styles
 */
function th_theme_enqueue () {
	wp_enqueue_script( 'allergene', get_stylesheet_directory_uri() . '/allergene.js', array( 'jquery', 'jquery-ui-core', 'jquery-effects-slide' ),'',true );
	wp_enqueue_script( 'theme-js', get_stylesheet_directory_uri() . '/scripts.js', array( 'jquery', 'jquery-ui-core', 'jquery-effects-slide', 'allergene' ),'',true );
	wp_enqueue_script( 'deliverydate', get_stylesheet_directory_uri() . '/deliverydate.js', array( 'jquery', 'jquery-ui-core', 'jquery-effects-slide', 'allergene' ),'',true );
	wp_enqueue_script( 'datepicker-js', get_stylesheet_directory_uri() . '/plugins/datepicker.min.js', array( 'jquery' ),'',true );
	wp_enqueue_script( 'datepicker-de', get_stylesheet_directory_uri() . '/plugins/datepicker.de-DE.js', array( 'jquery', 'datepicker-js' ),'',true );
	wp_enqueue_style( 'datepicker-css', get_stylesheet_directory_uri() . '/plugins/datepicker.min.css');
	wp_enqueue_style('dashicons');
}
add_action( 'wp_enqueue_scripts', 'th_theme_enqueue' );


######################### TÖPFERHAUS FUNKTIONEN #########################
/**
 * Author: 	Samuel Will
 * Mail:	mail@willsam.ch
 * Link:	https://github.com/fhinok/th-child-theme
 */

/**
 * Split comma separated options to array
 */
function th_return_option( $name ) {
	$option = preg_split( '/(\s*,*\s*)*,+(\s*,*\s*)*/', get_option( $name ));
	return $option;
}

/**
 * Add more filtering methods to woocommerce orderby select
 */
add_filter( 'woocommerce_catalog_orderby', 'th_rename_default_sorting_options' );
function th_rename_default_sorting_options( $options ){

	unset( $options[ 'date' ] );

	$options = array(
		'menu_order' => 'Sortierung: Standard',
		'popularity' => 'Beliebteste',
		'title' => 'A - Z',
		'title-desc' => 'Z - A',
		// 'in-stock' => 'Verfügbarkeit',
		'price' => 'Günstigste',
		'price-desc' => 'Teuerste'
	);
 
	return $options;
}

/**
 * sort products loop by title or stock status
 */
add_filter( 'woocommerce_get_catalog_ordering_args', 'th_custom_wc_sorting_args' );
function th_custom_wc_sorting_args( $args ){

	if ( isset( $_GET['orderby'] ) && 'title' === $_GET['orderby'] ) {
		$args['orderby'] = 'title';
		$args['order'] = 'asc';
	}

	if ( isset( $_GET['orderby'] ) && 'title-desc' === $_GET['orderby'] ) {
		$args['orderby'] = 'title';
		$args['order'] = 'desc';
	}

	if( isset( $_GET['orderby'] ) && 'in-stock' === $_GET['orderby'] ) {
		$args['meta_key'] = '_stock_status';
		$args['orderby'] = array( 'meta_value' => 'ASC' );
	}
 
	return $args;
}

/**
 * hide product counter for categories
 */
add_filter( 'woocommerce_subcategory_count_html', '__return_null' );

/**
 * if user is not b2b customer
 * add customer number 530 to order meta.
 * used for internal processsing in erp
 */
if( !isb2b() ) {
	add_action( 'woocommerce_checkout_update_order_meta', 'th_save_custom_checkout_fields_notb2b' );
	function th_save_custom_checkout_fields_notb2b( $order_id ) {
		$customer_number = '530';
		update_post_meta($order_id, 'th-customer-number', $customer_number);
	}
}

/**
 * Add custom fields to checkout page if user is b2b customer
 */
if( isb2b() ){

	// Show fields on checkout page
	add_filter( 'woocommerce_after_order_notes', 'th_custom_checkout_fields' );
	function th_custom_checkout_fields( $fields ) {
		woocommerce_form_field( 'boxes', array(
			'type'	=> 'checkbox',
			'class'	=> array('form-row-wide'),
			'label'	=> 'Gebinde zurücknehmen?',
		), $fields->get_value( 'boxes' ) );

		woocommerce_form_field( 'shipping_date', array(
			'type'	=> 'text',
			'class'	=> array('form-row-wide'),
			'label'	=> 'Gewünschtes Lieferdatum (Bestellungen nach 15:00 Uhr können nicht für den Folgetag getätigt werden.)',
			'custom_attributes' => array(  'readonly' => 'readonly' ),
			'required' => true,
		), $fields->get_value( 'shipping_date' ) );

	}

	// save custom checkout fields
	add_action( 'woocommerce_checkout_update_order_meta', 'th_save_custom_checkout_fields' );
	function th_save_custom_checkout_fields( $order_id ) {
		$customer_number = get_the_author_meta('customer_number', wp_get_current_user()->ID );
		if( empty( $customer_number ) ) {
			$customer_number = '530';
		}
		update_post_meta($order_id, 'th-customer-number', $customer_number);

		if( !empty( $_POST['boxes'] ) && $_POST['boxes'] == 1 )
			update_post_meta( $order_id, 'boxes', 1 );

		if( !empty( $_POST['shipping_date'] ) )
			update_post_meta( $order_id, 'shipping_date', $_POST['shipping_date'] );
	}

	add_filter( 'woocommerce_checkout_fields', 'th_change_shipping_notes', 50 );
	function th_change_shipping_notes( $fields ) {
		$fields['order']['order_comments']['placeholder'] = "Anmerkungen zu Ihrer Bestellung.";
		return $fields;
	}
}

/**
 * show contact info on profile page for b2b customers
 */
if( isb2b() ) {
	add_action('woocommerce_account_dashboard', 'th_show_crm_contact');

	function th_show_crm_contact( ) {
		$crm_contact = get_the_author_meta('crm_contact', wp_get_current_user()->ID );
		$customer_number = get_the_author_meta('customer_number', wp_get_current_user()->ID);

		echo "<br><h3>Ihr Kontakt im Töpferhaus</h3>";
		echo "<p>Ihre Kundennummer: <strong>".$customer_number."</strong></p>";

		if( $crm_contact ) {
			echo "<address style='white-space: pre-line;'>". $crm_contact ."</address>";
		} else {
			echo "<a href='mailto:bestellung@toepferhaus.ch'>bestellung@toepferhaus.ch</a><p>062 837 61 84</p>";

		}
	}
}

/**
 * check if logged in user has a b2b role set 
 */
function isb2b( ) {
	$user = wp_get_current_user();
	$roles = ( array ) $user->roles;
	$b2b_roles = th_return_option( 'b2b_roles' );

	if( count(array_intersect( $b2b_roles, $roles ) ) ){
		return true;
	}

	return false;
}

/**
 * hide meta in loop
 */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

/**
 * hide given categories everywhere
 */
add_filter( 'get_terms', 'th_get_subcategory_terms', 10, 3 );
function th_get_subcategory_terms( $terms, $taxonomies, $args ) {
	$new_terms = array();
	if( empty($taxonomies) ) {
		$taxonomies = array();
	}

	if ( in_array( 'product_cat', $taxonomies ) && ! is_admin() &&is_shop() ) {
		foreach( $terms as $key => $term ) {
			if ( !in_array( $term->slug, array( 'unkategorisiert', 'box_saucen', 'box_pasta', 'box_pasta-gross', 'box_verpackungen', 'box_products' ) ) ) { 
				$new_terms[] = $term;
			}
		}
		$terms = $new_terms;
	}
	return $terms;
}

/**
 * show noscript notification
 */
add_filter( 'wp_head', 'th_noscript', 20 );
function th_noscript() {
	?>
	<noscript>
		<div class="noscript">Sie haben JavaScript in ihrem Browser deaktiviert! Bitte beachten Sie, dass einige Funktionen in unserem Webshop nur mit aktiviertem JavaScript richtig funktionieren.</div>
	</noscript>
	<?php
}

/**
 * remove <p> and <br> tags from contactform7
 */
add_filter( 'wpcf7_autop_or_not', '__return_false' );

/**
 * change thumbnail size in card product loop
 */
add_filter( 'woocommerce_get_image_size_thumbnail', 'th_change_thumbnail' );
function th_change_thumbnail($args) {
	global $wp;
	$current_slug = add_query_arg( array(), $wp->request );
	if( $current_slug === "karten" ) {
		$args = [
			'width' => 900,
			'height' => '',
			'crop' => 0
		];
	}
	return $args;
}

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );


/** 
 * hide or show shipping methods based on customer role.
 * if b2b customer has fixed shipping method
 * remove all other ones
 */
add_filter( 'woocommerce_package_rates', 'th_shippings' );
function th_shippings( $shipping_methods ) {
	if( is_checkout() || is_page( 'warenkorb' ) ){
		// get all settings
		$remove_methods = get_option( 'hide_shipping_methods' );
		$remove_methods_guest = get_option( 'hide_shipping_methods_guest' );
		$customer_shipping = get_the_author_meta('customer_shipping', wp_get_current_user()->ID);

		// Hide Lieferung durch Töpferhaus nach Distanz for not b2b users
		if( !$remove_methods_guest ) { $remove_methods_guest = array(); } // create array if not existing
		array_push($remove_methods_guest, 'wcsdm:78', 'wcsdm:66', 'wcsdm:69');
		
		// switch preg statement based on fixed shipping method
		switch ($customer_shipping) {
			case 0:
				$statement = '/ /';
				break;
			case 1:
				$statement = '/lieferung/i';
				break;
			case 2:
				$statement = '/abholung/i';
				break;
			case 3:
				$statement = '/postversand/i';
				break;
		}

		// get the ids of methods
		foreach( $shipping_methods as $shipping_method_key => $shipping_method ) {
			$shipping_id = $shipping_method->get_id();
			
			if( isb2b() ) {
				// remove method from checkout, if shipping id is in array of methods to remove
				if( $remove_methods && in_array($shipping_id, $remove_methods) ) {
					unset( $shipping_methods[$shipping_method_key] );
				}

				// remove all methods not matching fixed shipping method
				if ( !preg_match($statement, $shipping_method->label) ) {
					unset( $shipping_methods[$shipping_method_key] );
				}

			} else {
				// remove method from checkout, if shipping id is in array of methods to remove
				if( $remove_methods_guest && in_array($shipping_id, $remove_methods_guest) ) {
					unset( $shipping_methods[$shipping_method_key] );
				}
			}
		}

	}
	return $shipping_methods;
}

/**
 * different menues for different customers
 */
add_filter( 'wp_nav_menu_args', 'th_nav_menu_args' );
function th_nav_menu_args( $args = '' ) {
	if( is_user_logged_in() ) { 
		$args['menu'] = 'logged-in';
		
		$user_categories = get_the_author_meta('can_buy_categories', get_current_user_id());
		if( $user_categories ) {
			if (in_array('karten', $user_categories)) {
				$args['menu'] = "logged-in-card";
			}
		}
	} else { 
		$args['menu'] = 'logged-out';
	} 
	return $args;
}

/**
 * sort out payment gateways for b2b customers
 */
add_filter('woocommerce_available_payment_gateways','th_change_payment', 9999, 1 );
function th_change_payment( $allowed_gateways ) {
	$allowed_gateways = array();
	$all_gateways = WC()->payment_gateways->payment_gateways();
	foreach( $all_gateways as $gateway) {
		if( $gateway->enabled === 'yes' && !isb2b() ) {
			if( $gateway->id === 'offline_gateway' ) { continue; } // don't allow b2b gateway for other customers
			$allowed_gateways[$gateway->id] = $gateway;
		}
	}

	if( isb2b() ) {
		$allowed_gateways['offline_gateway'] = $all_gateways['offline_gateway'];
	}
	
	return $allowed_gateways;
}

/**
 * filter <br> tag in product page
 */

 add_filter( 'the_title', 'filter_br_tag', 10, 2 );
 function filter_br_tag( $title, $post_id ) {
	 $post_type = get_post_field( 'post_type', $post_id, true );

	 if( is_product() && in_array( $post_type, array( 'product', 'product_variation' ) ) ) {
		 $title = str_replace( '<br>', ' ', $title );
	 }
	 return $title;
 }

	
/**
 * remove sorting (gets added in sidebar again)
 * and the countig on top of the loop
 */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/**
 * include changed woocommerce functions
 */
include_once('includes/wc-template-functions.php');
include_once('includes/wc-account-functions.php');

/**
 * 21.12.2021 - remove related products
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );


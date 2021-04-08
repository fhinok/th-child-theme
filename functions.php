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

function th_theme_enqueue () {
	wp_enqueue_script( 'theme-js', get_stylesheet_directory_uri() . '/scripts.js', array( 'jquery', 'jquery-ui-core', 'jquery-effects-slide' ),'',true );
	wp_enqueue_style('dashicons');
}
add_action( 'wp_enqueue_scripts', 'th_theme_enqueue' );


######################### TÖPFERHAUS FUNKTIONEN #########################
/**
 * Author: 	Samuel Will
 * Mail:	saemiwill@gmail.com
 * Link:	https://git.willsam.ch/fhinok/th-child-theme
 */

function th_return_option( $name ) {
	$option = preg_split( '/(\s*,*\s*)*,+(\s*,*\s*)*/', get_option( $name ));
	return $option;
}

// Passt das Filtermenü an
add_filter( 'woocommerce_catalog_orderby', 'th_rename_default_sorting_options' );
 
function th_rename_default_sorting_options( $options ){

	unset( $options[ 'date' ] );

	$options = array(
		'menu_order' => 'Sortierung: Standard',
		'popularity' => 'Beliebteste',
		'title' => 'A - Z',
		'title-desc' => 'Z - A',
		'in-stock' => 'Verfügbarkeit',
		'price' => 'Günstigste',
		'price-desc' => 'Teuerste'
	);
 
	return $options;
}

// Ermöglicht die Sortierung nach Titel oder Verfügbarkeit
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

// Remove the category count for WooCommerce categories
add_filter( 'woocommerce_subcategory_count_html', '__return_null' );

####### B2B #######
if( isb2b() ){

	add_filter( 'woocommerce_after_order_notes', 'th_custom_checkout_fields' );
	function th_custom_checkout_fields( $fields ) {
		$custom_shipping_note = get_the_author_meta('customer_shipping_desc', wp_get_current_user()->ID );		

		woocommerce_form_field( 'shipping_notes', array(
			'type' => 'text',
			'class' => array('form-row-wide'),
			'label' => 'Anmerkungen zum Versand',
		),  $custom_shipping_note );

		woocommerce_form_field( 'boxes', array(
			'type'	=> 'checkbox',
			'class'	=> array('form-row-wide'),
			'label'	=> 'Gebinde zurücknehmen?',
		), $fields->get_value( 'boxes' ) );

	}

	add_action( 'woocommerce_checkout_update_order_meta', 'th_save_custom_checkout_fields' );
	function th_save_custom_checkout_fields( $order_id ) {
		$customer_number = get_the_author_meta('customer_number', wp_get_current_user()->ID );
		update_post_meta($order_id, 'th-customer-number', $customer_number);

		if( !empty( $_POST['boxes'] ) && $_POST['boxes'] == 1 )
			update_post_meta( $order_id, 'boxes', 1 );

		if( !empty( $_POST['shipping_notes'] ) )
			update_post_meta( $order_id, 'shipping_notes', $_POST['shipping_notes'] );
	}

	add_filter( 'woocommerce_checkout_fields', 'th_change_shipping_notes', 50 );
	function th_change_shipping_notes( $fields ) {
		$fields['order']['order_comments']['placeholder'] = "Anmerkungen zu Ihrer Bestellung.";
		return $fields;
	}

	add_action('woocommerce_account_dashboard', 'th_show_crm_contact');

	function th_show_crm_contact( ) {
		$crm_contact = get_the_author_meta('crm_contact', wp_get_current_user()->ID );
		$customer_number = get_the_author_meta('customer_number', wp_get_current_user()->ID);

		echo "<h3>Ihre Kontaktperson im Töpferhaus</h3>";
		echo "<p>Kundennummer: <strong>".$customer_number."</strong></p>";
		echo "<address style='white-space: pre-line;'>". $crm_contact ."</address>";
	}
}

##### END B2B #####

function isb2b( ) {
	$user = wp_get_current_user();
	$roles = ( array ) $user->roles;
	$b2b_roles = th_return_option( 'b2b_roles' );

	if( count(array_intersect( $b2b_roles, $roles ) ) ){
		return true;
	}

	return false;
}

// Verstecke Meta auf Produktseite
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

// Verstecke Gewisse Kategorien auf den Shopseiten und in den Filtern
add_filter( 'get_terms', 'th_get_subcategory_terms', 10, 3 );
function th_get_subcategory_terms( $terms, $taxonomies, $args ) {
	$new_terms = array();
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

// Noscript Banner
add_filter( 'wp_head', 'th_noscript', 20 );
function th_noscript() {
	?>
	<noscript>
		<div class="noscript">Sie haben JavaScript in ihrem Browser deaktiviert! Bitte beachten Sie, dass einige Funktionen in unserem Webshop nur mit aktiviertem JavaScript richtig funktionieren.</div>
	</noscript>
	<?php
}

add_filter( 'wpcf7_autop_or_not', '__return_false' );

// Seitenauswahl vor und nach Produkten
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
// add_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 20 );

// füge abgeänderte Funktionen ein
include_once('includes/wc-template-functions.php');
include_once('includes/wc-account-functions.php');

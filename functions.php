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


######################### TÖPFERHAUS FUNKTIONEN #########################
/**
 * Author: 	Samuel Will
 * Mail:	saemiwill@gmail.com
 * Link:	https://git.willsam.ch/fhinok/th-child-theme
 */


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

// Deaktiviere die Verkaufsfunktion für bestimmte Kategorien
add_filter( 'woocommerce_is_purchasable', 'th_hide_add_to_cart', 30, 2 );
function th_hide_add_to_cart( $return_val, $product ) {
	// Alle Kategorien, die (noch) nicht zum Verkauf stehen
	$deactivate_categories = th_return_option( 'categories_disabled' );
	$b2b_roles = th_return_option( 'b2b_roles' );

	$user = wp_get_current_user();
	$roles = ( array ) $user->roles;

	// Falls der Kunde ein Stammkunde ist, aktiviere die Verkaufsfunktion
	if( count(array_intersect( $b2b_roles, $roles ) ) ) {
		return $return_val;
	}

	// Ist das Produkt in einer ausgeschlossenen Kategorie, wird der Verkauf deaktiviert
	if( has_term( $deactivate_categories, 'product_cat', $product->id ) ) {
		return false;
	} else {
		return $return_val;
	}
}

// füge abgeänderte Funktionen ein
include_once('includes/wc-template-functions.php');
include_once('includes/wc-account-functions.php');

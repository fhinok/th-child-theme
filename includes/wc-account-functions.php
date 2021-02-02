<?php
// Zeige die Telefonnummer und E-Mail in der formatierten Adresse
add_filter( 'woocommerce_my_account_my_address_formatted_address', function( $args, $customer_id, $name ){
    // the phone is saved as billing_phone and shipping_phone
    $args['phone'] = get_user_meta( $customer_id, $name . '_phone', true );
    $args['mail'] = get_user_meta( $customer_id, $name . '_email', true );
    return $args;
}, 10, 3 ); 

// modify the address formats
add_filter( 'woocommerce_localisation_address_formats', function( $formats ){
    foreach ( $formats as $key => &$format ) {
        // put a break and then the phone after each format.
        $format .= "\n{phone}";
        $format .= "\n{mail}";
    }
    return $formats;
} );


// add the replacement value
add_filter( 'woocommerce_formatted_address_replacements', function( $replacements, $args ){
    // we want to replace {phone} in the format with the data we populated
    $replacements['{phone}'] = $args['phone'];
    $replacements['{mail}'] = $args['mail'];
    return $replacements;
}, 10, 2 );

?>
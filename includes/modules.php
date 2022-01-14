<?php
/**
 *
 * Frontend modules
 *
 * @package CommerceKit
 * @subpackage Shoptimizer
 */

/**
 * Display module output
 *
 * @param  string $display_text of module output.
 */
function commercekit_module_output( $display_text ) {
	$args = array(
		'span'     => array(
			'data-product-id' => array(),
			'data-type'       => array(),
			'data-wpage'      => array(),
			'class'           => array(),
			'aria-label'      => array(),
		),
		'h2'       => array(
			'class' => array(),
		),
		'del'      => array(),
		'ins'      => array(),
		'strong'   => array(),
		'em'       => array(
			'class' => array(),
		),
		'b'        => array(),
		'i'        => array(
			'class' => array(),
		),
		'img'      => array(
			'href'        => array(),
			'alt'         => array(),
			'class'       => array(),
			'scale'       => array(),
			'width'       => array(),
			'height'      => array(),
			'src'         => array(),
			'srcset'      => array(),
			'sizes'       => array(),
			'data-src'    => array(),
			'data-srcset' => array(),
		),
		'br'       => array(),
		'p'        => array(),
		'a'        => array(
			'href'            => array(),
			'data-product-id' => array(),
			'data-type'       => array(),
			'data-wpage'      => array(),
			'class'           => array(),
			'aria-label'      => array(),
			'target'          => array(),
			'title'           => array(),
		),
		'div'      => array(
			'data-product-id' => array(),
			'data-type'       => array(),
			'data-wpage'      => array(),
			'class'           => array(),
			'aria-label'      => array(),
		),
		'noscript' => array(),
	);

	echo wp_kses( $display_text, $args );
}

/**
 * Kses allowed protocols
 *
 * @param  string $protocols protocols.
 */
function commercekit_kses_allowed_protocols( $protocols ) {
	$protocols[] = 'data';
	return $protocols;
}
add_filter( 'kses_allowed_protocols', 'commercekit_kses_allowed_protocols' );

/**
 * Get default settings
 *
 * @param  string $key default settings key.
 * @param  string $options default settings.
 */
function commercekit_get_default_settings( $key = '', $options = array() ) {
	$defaults = array(

		'ajax_search'            => '0',
		'ajs_placeholder'        => esc_html__( 'Search products...', 'commercegurus-commercekit' ),
		'ajs_display'            => 'all',
		'ajs_tabbed'             => '0',
		'ajs_pre_tab'            => '0',
		'ajs_other_text'         => esc_html__( 'Other results', 'commercegurus-commercekit' ),
		'ajs_no_text'            => esc_html__( 'No results', 'commercegurus-commercekit' ),
		'ajs_all_text'           => esc_html__( 'View all results', 'commercegurus-commercekit' ),
		'ajs_outofstock'         => '0',
		'ajs_hidevar'            => '0',

		'countdown_timer'        => '0',
		'order_bump'             => '0',

		'pdp_gallery'            => '0',
		'pdp_lightbox'           => '1',
		'pdp_video_autoplay'     => '1',
		'pdp_gallery_layout'     => 'horizontal',

		'pdp_attributes_gallery' => '0',
		'attribute_swatches'     => '0',
		'attribute_swatches_plp' => '0',
		'as_activate_atc'        => '0',
		'as_quickadd_txt'        => esc_html__( 'Quick add', 'commercegurus-commercekit' ),
		'as_more_opt_txt'        => esc_html__( 'More options', 'commercegurus-commercekit' ),
		'as_button_style'        => '0',

		'inventory_display'      => '0',
		'inventory_text'         => esc_html__( 'Only %s items left in stock!', 'commercegurus-commercekit' ), // phpcs:ignore
		'inventory_text_31'      => esc_html__( 'Less than %s items left!', 'commercegurus-commercekit' ), // phpcs:ignore
		'inventory_text_100'     => esc_html__( 'This item is selling fast!', 'commercegurus-commercekit' ),

		'waitlist'               => '0',
		'wtl_intro'              => esc_html__( 'Notify me when the item is back in stock.', 'commercegurus-commercekit' ),
		'wtl_email_text'         => esc_html__( 'Enter your email address...', 'commercegurus-commercekit' ),
		'wtl_button_text'        => esc_html__( 'Join waiting list', 'commercegurus-commercekit' ),
		'wtl_consent_text'       => esc_html__( 'I consent to being contacted by the store owner', 'commercegurus-commercekit' ),
		'wtl_success_text'       => esc_html__( 'You have been added to the waiting list for this product!', 'commercegurus-commercekit' ),
		'wtl_readmore_text'      => esc_html__( 'Get notified', 'commercegurus-commercekit' ),
		'wtl_from_email'         => get_option( 'admin_email' ),
		'wtl_from_name'          => get_option( 'blogname' ),
		'wtl_recipient'          => get_option( 'admin_email' ),
		'waitlist_auto_mail'     => '1',
		'wtl_auto_subject'       => esc_html__( 'A product you are waiting for is back in stock!', 'commercegurus-commercekit' ),
		'wtl_auto_content'       => esc_html__( "Hi,\r\n{product_title} is now back in stock on {site_name}.\r\nYou have been sent this email because your email address was registered in a waiting list for this product.\r\nIf you would like to purchase {product_title}, please visit the following link:\r\n{product_link}", 'commercegurus-commercekit' ),
		'waitlist_admin_mail'    => '1',
		'wtl_admin_subject'      => esc_html__( 'You have a new waiting list request on {site_name}', 'commercegurus-commercekit' ),
		'wtl_admin_content'      => esc_html__( "Hi,\r\nYou got a waiting list request from {site_name} ({site_url}) for the following:\r\nCustomer email: {customer_email}\r\nProduct Name: {product_title}, SKU: {product_sku}\r\nProduct link: {product_link}", 'commercegurus-commercekit' ),
		'waitlist_user_mail'     => '1',
		'wtl_user_subject'       => esc_html__( 'We have received your waiting list request', 'commercegurus-commercekit' ),
		'wtl_user_content'       => esc_html__( "Hi,\r\nWe have received your waiting list request from {site_name} for the following:\r\nProduct Name: {product_title}, SKU: {product_sku}\r\nProduct link: {product_link}\r\n\r\nWe will send you an email once this item is back in stock.", 'commercegurus-commercekit' ),

		'wishlist'               => '0',
		'wsl_adtext'             => esc_html__( 'Add to wishlist', 'commercegurus-commercekit' ),
		'wsl_pdtext'             => esc_html__( 'Product added', 'commercegurus-commercekit' ),
		'wsl_brtext'             => esc_html__( 'Browse wishlist', 'commercegurus-commercekit' ),
		'wsl_page'               => '0',
	);

	if ( '' !== $key ) {
		if ( isset( $defaults[ $key ] ) ) {
			return $defaults[ $key ];
		} else {
			return '';
		}
	}

	foreach ( $defaults as $dkey => $dvalue ) {
		if ( isset( $options[ $dkey ] ) ) {
			continue;
		} else {
			$options[ $dkey ] = $dvalue;
		}
	}

	return $options;
}

$commercekit_options    = get_option( 'commercekit', array() );
$enable_inventory_bar   = isset( $commercekit_options['inventory_display'] ) && 1 === (int) $commercekit_options['inventory_display'] ? 1 : 0;
$enable_countdown_timer = isset( $commercekit_options['countdown_timer'] ) && 1 === (int) $commercekit_options['countdown_timer'] ? 1 : 0;
$enable_ajax_search     = isset( $commercekit_options['ajax_search'] ) && 1 === (int) $commercekit_options['ajax_search'] ? 1 : 0;
$enable_waitlist        = isset( $commercekit_options['waitlist'] ) && 1 === (int) $commercekit_options['waitlist'] ? 1 : 0;
$enable_order_bump      = isset( $commercekit_options['order_bump'] ) && 1 === (int) $commercekit_options['order_bump'] ? 1 : 0;
$enable_wishlist        = isset( $commercekit_options['wishlist'] ) && 1 === (int) $commercekit_options['wishlist'] ? 1 : 0;
$enable_pdp_triggers    = isset( $commercekit_options['pdp_triggers'] ) && 1 === (int) $commercekit_options['pdp_triggers'] ? 1 : 0;
$enable_attr_swatches   = isset( $commercekit_options['attribute_swatches'] ) && 1 === (int) $commercekit_options['attribute_swatches'] ? 1 : 0;

if ( $enable_inventory_bar ) {
	require_once dirname( __FILE__ ) . '/module-inventory-bar.php';
}
if ( $enable_countdown_timer ) {
	require_once dirname( __FILE__ ) . '/module-countdown-timer.php';
}
if ( $enable_ajax_search ) {
	require_once dirname( __FILE__ ) . '/module-ajax-search.php';
}
if ( $enable_waitlist ) {
	require_once dirname( __FILE__ ) . '/module-waitlist.php';
}
if ( $enable_order_bump ) {
	require_once dirname( __FILE__ ) . '/module-order-bump.php';
}
if ( $enable_wishlist ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( 'yith-woocommerce-wishlist/init.php' ) ) {
		global $commerce_gurus_commercekit, $pagenow;
		include_once ABSPATH . 'wp-includes/pluggable.php';
		$nonce = wp_verify_nonce( 'commercekit_nonce', 'commercekit_settings' );
		$cpage = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		if ( 'admin.php' === $pagenow && 'commercekit' === $cpage ) {
			$commerce_gurus_commercekit->add_admin_notice( 'bad_wishlist', 'error', esc_html__( 'You will need to first disable the YITH Wishlist plugin in order to use the CommerceKit Wishlist feature.', 'commercegurus-commercekit' ) );
		}
	} else {
		require_once dirname( __FILE__ ) . '/module-wishlist.php';
	}
}
if ( $enable_pdp_triggers ) {
	require_once dirname( __FILE__ ) . '/module-pdp-triggers.php';
}
if ( $enable_attr_swatches ) {
	require_once dirname( __FILE__ ) . '/admin-attribute-swatches.php';
	require_once dirname( __FILE__ ) . '/module-attribute-swatches.php';
}

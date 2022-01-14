<?php
/**
 * Admin Attributes Swatches
 *
 * @author   CommerceGurus
 * @package  Attributes_Swatches
 * @since    1.0.0
 */

/**
 * Get product attributes swatches admin tab.
 *
 * @param string $tabs admin product tabs.
 */
function commercegurus_get_attribute_swatches_tab( $tabs ) {
	$tabs['commercekit_swatches'] = array(
		'label'    => esc_html__( 'Attribute Swatches', 'commercegurus-commercekit' ),
		'target'   => 'cgkit_attr_swatches',
		'class'    => array( 'commercekit-attributes-swatches', 'show_if_variable' ),
		'priority' => 62,
	);
	return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'commercegurus_get_attribute_swatches_tab' );

/**
 * Get product attributes swatches admin panel.
 */
function commercegurus_get_attribute_swatches_panel() {
	global $post;
	$product_id = $post->ID;
	$product_id = intval( $product_id );
	$product    = wc_get_product_object( 'variable', $product_id );
	$attributes = commercegurus_attribute_swatches_load_attributes( $product );

	$attribute_swatches = get_post_meta( $product_id, 'cgkit_attribute_swatches', true );
	require_once dirname( __FILE__ ) . '/templates/admin-attribute-swatches.php';
}
add_filter( 'woocommerce_product_data_panels', 'commercegurus_get_attribute_swatches_panel' );

/**
 * Add admin CSS and JS scripts
 */
function commercegurus_attribute_swatches_admin_scripts() {
	$screen = get_current_screen();
	if ( 'product' === $screen->post_type && 'post' === $screen->base ) {
		wp_enqueue_style( 'commercekit-admin-attribute-swatches-style', CKIT_URI . 'assets/css/admin-attribute-swatches.css', array(), CGKIT_CSS_JS_VER );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'commercekit-admin-attribute-swatches-script', CKIT_URI . 'assets/js/admin-attribute-swatches.js', array( 'wp-color-picker' ), CGKIT_CSS_JS_VER, true );
	}
}

add_action( 'admin_enqueue_scripts', 'commercegurus_attribute_swatches_admin_scripts' );

/**
 * Load selected attributes
 *
 * @param string $product admin product.
 */
function commercegurus_attribute_swatches_load_attributes( $product ) {
	$attributes = array();

	if ( $product ) {
		foreach ( $product->get_attributes( 'edit' ) as $attribute ) {
			if ( ! $attribute->get_variation() ) {
				continue;
			}
			$attr_slug = sanitize_title( $attribute->get_name() );
			if ( $attr_slug ) {
				if ( $attribute->is_taxonomy() ) {
					$tax = get_taxonomy( $attr_slug );

					$attributes[ $attr_slug ] = array(
						'slug'  => $attr_slug,
						'name'  => ucwords( $tax->label ),
						'terms' => $attribute->get_terms(),
					);
				} else {
					$_options  = $attribute->get_options();
					$tax_terms = array();
					if ( count( $_options ) ) {
						foreach ( $_options as $_option ) {
							$tax_terms[] = (object) array(
								'name'    => $_option,
								'slug'    => sanitize_title( $_option ),
								'term_id' => 0,
							);
						}
					}
					$attributes[ $attr_slug ] = array(
						'slug'  => $attr_slug,
						'name'  => $attribute->get_name(),
						'terms' => $tax_terms,
					);
				}
			}
		}
	}

	return $attributes;
}

/**
 * Save product attributes gallery
 *
 * @param string $post_id post ID.
 * @param string $post post.
 */
function commercegurus_save_product_attribute_swatches( $post_id, $post ) {
	if ( 'product' !== $post->post_type ) {
		return;
	}
	$cgkit_swatches_nonce = isset( $_POST['cgkit_swatches_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['cgkit_swatches_nonce'] ) ) : '';
	if ( $cgkit_swatches_nonce && wp_verify_nonce( $cgkit_swatches_nonce, 'cgkit_swatches_nonce' ) ) {
		if ( $post_id ) {
			$attribute_swatches = isset( $_POST['cgkit_attribute_swatches'] ) ? map_deep( wp_unslash( $_POST['cgkit_attribute_swatches'] ), 'sanitize_textarea_field' ) : array();
			if ( ! isset( $attribute_swatches['enable_loop'] ) ) {
				$attribute_swatches['enable_loop'] = 0;
			}
			update_post_meta( $post_id, 'cgkit_attribute_swatches', $attribute_swatches );
		}
	}
}
add_action( 'woocommerce_process_product_meta', 'commercegurus_save_product_attribute_swatches', 10, 2 );

/**
 * Get ajax product gallery
 */
function commercegurus_get_ajax_attribute_swatches() {
	$ajax           = array();
	$ajax['status'] = 0;
	$ajax['html']   = '';

	$product_id = isset( $_GET['product_id'] ) ? (int) $_GET['product_id'] : 0; // phpcs:ignore
	if ( $product_id ) {
		ob_start();
		$product_id   = intval( $product_id );
		$product      = wc_get_product_object( 'variable', $product_id );
		$attributes   = commercegurus_attribute_swatches_load_attributes( $product );
		$without_wrap = true;

		$attribute_swatches = get_post_meta( $product_id, 'cgkit_attribute_swatches', true );
		require_once dirname( __FILE__ ) . '/templates/admin-attribute-swatches.php';

		$ajax['status'] = 1;
		$ajax['html']   = ob_get_contents();
		ob_clean();
	}

	wp_send_json( $ajax );
}

add_action( 'wp_ajax_get_ajax_attribute_swatches', 'commercegurus_get_ajax_attribute_swatches' );

/**
 * Update ajax product gallery
 */
function commercegurus_update_ajax_attribute_swatches() {
	$ajax           = array();
	$ajax['status'] = 0;
	$ajax['html']   = '';

	$product_id = isset( $_GET['product_id'] ) ? (int) $_GET['product_id'] : 0; // phpcs:ignore
	if ( $product_id ) {
		$post = get_post( $product_id );
		commercegurus_save_product_attribute_swatches( $product_id, $post );
		$ajax['status'] = 1;
	}

	wp_send_json( $ajax );
}

add_action( 'wp_ajax_update_ajax_attribute_swatches', 'commercegurus_update_ajax_attribute_swatches' );

/**
 * Build product swatch cache
 *
 * @param string $product product object.
 * @param string $return return HTML.
 */
function commercekit_as_build_product_swatch_cache( $product, $return = false ) {
	$product_id = $product ? $product->get_id() : 0;
	$cache_key  = 'cgkit_swatch_loop_form_' . $product_id;
	$cache_key2 = 'cgkit_swatch_loop_form_data_' . $product_id;

	if ( isset( $GLOBALS[ $cache_key ] ) && $GLOBALS[ $cache_key ] ) {
		return;
	}
	$commercekit_options  = get_option( 'commercekit', array() );
	$available_variations = $product->get_available_variations();
	$cgkit_images         = array();
	$images_data          = array();
	if ( is_array( $available_variations ) && count( $available_variations ) ) {
		foreach ( $available_variations as $variation ) {
			if ( isset( $variation['attributes'] ) && count( $variation['attributes'] ) ) {
				$variation_img_id = get_post_thumbnail_id( $variation['variation_id'] );
				if ( $variation_img_id ) {
					$cgkit_images[] = $variation_img_id;
				}
			}
		}
	}
	$as_quickadd_txt     = isset( $commercekit_options['as_quickadd_txt'] ) && ! empty( $commercekit_options['as_quickadd_txt'] ) ? commercekit_get_multilingual_string( stripslashes_deep( $commercekit_options['as_quickadd_txt'] ) ) : commercekit_get_default_settings( 'as_quickadd_txt' );
	$cgkit_image_gallery = get_post_meta( $product_id, 'commercekit_image_gallery', true );
	if ( is_array( $cgkit_image_gallery ) ) {
		$cgkit_image_gallery = array_filter( $cgkit_image_gallery );
	}
	$attribute_swatches = get_post_meta( $product_id, 'cgkit_attribute_swatches', true );
	if ( is_array( $cgkit_image_gallery ) && count( $cgkit_image_gallery ) ) {
		foreach ( $cgkit_image_gallery as $slug => $image_gallery ) {
			if ( 'global_gallery' === $slug ) {
				continue;
			}
			$images = explode( ',', trim( $image_gallery ) );
			if ( isset( $images[0] ) && ! empty( $images[0] ) ) {
				$cgkit_images[] = $images[0];
			}
		}
	}
	$cgkit_images = array_unique( $cgkit_images );
	if ( count( $cgkit_images ) ) {
		foreach ( $cgkit_images as $image_id ) {
			$image_data = commercekit_as_get_loop_swatch_image( $image_id );
			if ( $image_data ) {
				$images_data[ 'img_' . $image_id ] = $image_data;
			}
		}
	}

	$attributes          = $product->get_variation_attributes();
	$selected_attributes = $product->get_default_attributes();
	$attribute_keys      = array_keys( $attributes );
	$for_json_data       = array(
		'variations' => $available_variations,
		'images'     => $images_data,
	);
	$variations_json     = wp_json_encode( $for_json_data );
	$variations_attr     = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );
	set_transient( $cache_key2, $variations_json, 2 * DAY_IN_SECONDS );
	ob_start();
	require dirname( __FILE__ ) . '/templates/product-attribute-swatches.php';
	$swatches_html = ob_get_clean();
	if ( $swatches_html ) {
		set_transient( $cache_key, $swatches_html, 2 * DAY_IN_SECONDS );
	}
	$GLOBALS[ $cache_key ] = true;
	if ( $return ) {
		return $swatches_html;
	}
}

/**
 * Prepare action scheduler for attribute swatches all cache
 */
function commercekit_as_prepare_action_scheduler() {
	global $wpdb;
	$options       = get_option( 'commercekit', array() );
	$as_active_plp = isset( $options['attribute_swatches_plp'] ) && 1 === (int) $options['attribute_swatches_plp'] ? true : false;
	if ( ! $as_active_plp ) {
		return;
	}
	$as_scheduled = isset( $options['commercekit_as_scheduled'] ) && 1 === (int) $options['commercekit_as_scheduled'] ? true : false;
	if ( $as_scheduled ) {
		return;
	}

	$args = array(
		'hook'     => 'commercekit_attribute_swatche_build_cache_list',
		'per_page' => -1,
		'group'    => 'commercekit',
	);

	$action_ids = as_get_scheduled_actions( $args, 'ids' );
	if ( count( $action_ids ) ) {
		return;
	}

	as_schedule_single_action( time(), 'commercekit_attribute_swatche_build_cache_list', array(), 'commercekit' );

	$options['commercekit_as_scheduled'] = 1;
	update_option( 'commercekit', $options, false );

	$args2 = array(
		'hook'     => 'commercekit_attribute_swatche_build_cache_remove',
		'per_page' => -1,
		'group'    => 'commercekit',
	);

	$action_ids2 = as_get_scheduled_actions( $args2, 'ids' );
	$as_store    = ActionScheduler::store();
	if ( count( $action_ids2 ) ) {
		foreach ( $action_ids2 as $action_id ) {
			$as_store->delete_action( $action_id );
			if ( isset( $wpdb->actionscheduler_logs ) && ! empty( $wpdb->actionscheduler_logs ) ) {
				$wpdb->delete( $wpdb->actionscheduler_logs, array( 'action_id' => $action_id ), array( '%d' ) ); // phpcs:ignore
			}
		}
	}
}
add_action( 'init', 'commercekit_as_prepare_action_scheduler' );

/**
 * Run action scheduler list for attribute swatches cache
 *
 * @param  array $params array of arguments.
 */
function commercekit_as_run_action_scheduler_list( $params = array() ) {
	global $wpdb;
	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'order'          => 'DESC',
		'orderby'        => 'ID',
		'fields'         => 'ids',
		'tax_query'      => array( // phpcs:ignore
			array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => 'variable',
			),
		),
	);

	$query = new WP_Query( $args );
	$total = (int) $query->found_posts;
	if ( $total ) {
		$product_ids = wp_parse_id_list( $query->posts );
		foreach ( $product_ids as $product_id ) {
			$args2 = array(
				'hook'  => 'commercekit_attribute_swatche_build_cache',
				'args'  => array( 'product_id' => $product_id ),
				'group' => 'commercekit',
			);

			$action_ids = as_get_scheduled_actions( $args2, 'ids' );
			if ( count( $action_ids ) ) {
				continue;
			} else {
				as_schedule_single_action( time(), 'commercekit_attribute_swatche_build_cache', array( 'product_id' => $product_id ), 'commercekit' );
			}
		}
	}
}
add_action( 'commercekit_attribute_swatche_build_cache_list', 'commercekit_as_run_action_scheduler_list', 10, 1 );

/**
 * Run action scheduler remove for attribute swatches cache
 *
 * @param  array $params array of arguments.
 */
function commercekit_as_run_action_scheduler_remove( $params = array() ) {
	global $wpdb;
	$options = get_option( 'commercekit', array() );

	$args = array(
		'hook'     => 'commercekit_attribute_swatche_build_cache',
		'per_page' => -1,
		'group'    => 'commercekit',
	);

	$action_ids = as_get_scheduled_actions( $args, 'ids' );
	$as_store   = ActionScheduler::store();
	if ( count( $action_ids ) ) {
		foreach ( $action_ids as $action_id ) {
			$as_store->delete_action( $action_id );
			if ( isset( $wpdb->actionscheduler_logs ) && ! empty( $wpdb->actionscheduler_logs ) ) {
				$wpdb->delete( $wpdb->actionscheduler_logs, array( 'action_id' => $action_id ), array( '%d' ) ); // phpcs:ignore
			}
		}
	}

	$options['commercekit_as_scheduled']        = 0;
	$options['commercekit_as_scheduled_cancel'] = 0;
	update_option( 'commercekit', $options, false );
}
add_action( 'commercekit_attribute_swatche_build_cache_remove', 'commercekit_as_run_action_scheduler_remove', 10, 1 );

/**
 * Run action scheduler for attribute swatches cache
 *
 * @param  array $args array of arguments.
 */
function commercekit_as_run_action_scheduler( $args ) {
	global $wpdb, $product;
	$options    = get_option( 'commercekit', array() );
	$product_id = 0;
	if ( is_numeric( $args ) ) {
		$product_id = (int) $args;
	} elseif ( is_array( $args ) ) {
		if ( isset( $args[0] ) && is_numeric( $args[0] ) ) {
			$product_id = (int) $args[0];
		} elseif ( isset( $args['product_id'] ) && is_numeric( $args['product_id'] ) ) {
			$product_id = (int) $args['product_id'];
		}
	}

	if ( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product && method_exists( $product, 'is_type' ) && $product->is_type( 'variable' ) ) {
			try {
				commercekit_as_build_product_swatch_cache( $product );
			} catch ( Exception $e ) {
				$product = null;
			}
		}

		$options['commercekit_as_scheduled_done'] = time();
		update_option( 'commercekit', $options, false );
	}
}
add_action( 'commercekit_attribute_swatche_build_cache', 'commercekit_as_run_action_scheduler', 10, 1 );

/**
 * Run action scheduler cancel for attribute swatches cache
 */
function commercekit_as_run_action_scheduler_cancel() {
	$ajax    = array();
	$options = get_option( 'commercekit', array() );

	$as_store = ActionScheduler::store();
	$as_store->cancel_actions_by_hook( 'commercekit_attribute_swatche_build_cache' );

	$options['commercekit_as_scheduled_cancel'] = 1;
	update_option( 'commercekit', $options, false );

	$ajax['status']  = 0;
	$ajax['message'] = esc_html__( 'The caching process has been cancelled.', 'commercegurus-commercekit' );

	wp_send_json( $ajax );
}
add_action( 'wp_ajax_commercekit_get_as_build_cancel', 'commercekit_as_run_action_scheduler_cancel', 10, 1 );

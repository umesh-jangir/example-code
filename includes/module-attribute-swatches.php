<?php
/**
 *
 * Attribute swatches module
 *
 * @package CommerceKit
 * @subpackage Shoptimizer
 */

/**
 * Attribute swatches options html.
 *
 * @param string $html HTML of dropdowns.
 * @param array  $args other arguments.
 */
function commercekit_attribute_swatches_options_html( $html, $args ) {
	global $product;

	if ( empty( $args['options'] ) ) {
		return $html;
	}

	$arg_product = isset( $args['product'] ) ? $args['product'] : $product;
	$product_id  = $arg_product->get_id();

	$attribute_swatches = get_post_meta( $product_id, 'cgkit_attribute_swatches', true );
	if ( ! is_array( $attribute_swatches ) ) {
		$attribute_swatches = array();
	}
	$commercekit_options = get_option( 'commercekit', array() );

	$attribute_raw  = sanitize_title( $args['attribute'] );
	$attribute_name = commercekit_as_get_attribute_slug( $attribute_raw, true );
	$swatch_type    = isset( $attribute_swatches[ $attribute_raw ]['cgkit_type'] ) ? $attribute_swatches[ $attribute_raw ]['cgkit_type'] : 'button';
	if ( empty( $swatch_type ) ) {
		return $html;
	}
	$as_quickadd_txt = isset( $commercekit_options['as_quickadd_txt'] ) && ! empty( $commercekit_options['as_quickadd_txt'] ) ? commercekit_get_multilingual_string( stripslashes_deep( $commercekit_options['as_quickadd_txt'] ) ) : commercekit_get_default_settings( 'as_quickadd_txt' );
	$as_more_opt_txt = isset( $commercekit_options['as_more_opt_txt'] ) && ! empty( $commercekit_options['as_more_opt_txt'] ) ? commercekit_get_multilingual_string( stripslashes_deep( $commercekit_options['as_more_opt_txt'] ) ) : commercekit_get_default_settings( 'as_more_opt_txt' );
	$as_activate_atc = isset( $commercekit_options['as_activate_atc'] ) && 1 === (int) $commercekit_options['as_activate_atc'] ? true : false;
	$as_button_style = isset( $commercekit_options['as_button_style'] ) && 1 === (int) $commercekit_options['as_button_style'] ? true : false;
	$attr_count      = isset( $args['attr_count'] ) ? (int) $args['attr_count'] : 2;
	$attr_index      = isset( $args['attr_index'] ) ? (int) $args['attr_index'] : 1;
	if ( 2 < $attr_count || ! $as_activate_atc ) {
		$as_quickadd_txt = $as_more_opt_txt;
	}

	$is_taxonomy = true;
	$attr_terms  = wc_get_product_terms(
		$product->get_id(),
		$attribute_raw,
		array(
			'fields' => 'all',
		)
	);
	if ( ! count( $attr_terms ) ) {
		$_options = $args['options'];
		if ( count( $_options ) ) {
			$is_taxonomy = false;
			foreach ( $_options as $_option ) {
				$attr_terms[] = (object) array(
					'name'    => $_option,
					'slug'    => sanitize_title( $_option ),
					'term_id' => 0,
				);
			}
		}
	}
	if ( ! count( $attr_terms ) ) {
		return $html;
	}
	$_variations = array();
	$_var_images = array();
	$_gal_images = array();
	$any_attrib  = false;
	$variations  = $product->get_available_variations();
	if ( is_array( $variations ) && count( $variations ) ) {
		foreach ( $variations as $variation ) {
			if ( isset( $variation['attributes'] ) && count( $variation['attributes'] ) ) {
				$variation_img_id = get_post_thumbnail_id( $variation['variation_id'] );
				foreach ( $variation['attributes'] as $a_key => $a_value ) {
					$a_key = str_ireplace( 'attribute_', '', $a_key );

					$_variations[ $a_key ][] = $a_value;
					if ( $variation_img_id ) {
						$_var_images[ $a_key ][ $a_value ] = $variation_img_id;
					}
					if ( '' === $a_value ) {
						$any_attrib = true;
					}
				}
			}
		}
		$cgkit_image_gallery = get_post_meta( $product_id, 'commercekit_image_gallery', true );
		if ( is_array( $cgkit_image_gallery ) ) {
			$cgkit_image_gallery = array_filter( $cgkit_image_gallery );
		}
		if ( is_array( $cgkit_image_gallery ) && count( $cgkit_image_gallery ) ) {
			foreach ( $cgkit_image_gallery as $slug => $image_gallery ) {
				if ( 'global_gallery' === $slug ) {
					continue;
				}
				$images = explode( ',', trim( $image_gallery ) );
				if ( isset( $images[0] ) && ! empty( $images[0] ) ) {
					$slugs = explode( '_cgkit_', $slug );
					if ( count( $slugs ) ) {
						foreach ( $slugs as $slg ) {
							$_gal_images[ $slg ] = $images[0];
						}
					}
				}
			}
		}
	} else {
		return $html;
	}
	$attribute_css  = isset( $args['css_class'] ) && ! empty( $args['css_class'] ) ? $args['css_class'] : 'cgkit-as-wrap';
	$item_class     = '';
	$item_wrp_class = '';
	$item_oos_text  = esc_html__( 'Out of stock', 'commercegurus-commercekit' );
	$swatches_html  = sprintf( '<div class="%s"><span class="cgkit-swatch-title">%s</span><ul class="cgkit-attribute-swatches %s" data-attribute="%s" data-no-selection="%s">', $attribute_css, $as_quickadd_txt, $item_wrp_class, $attribute_name, esc_html__( 'No selection', 'commercegurus-commercekit' ) );
	foreach ( $attr_terms as $item ) {
		if ( ! isset( $attribute_swatches[ $attribute_raw ] ) ) {
			$attribute_swatches[ $attribute_raw ] = array();
		}
		if ( ! isset( $attribute_swatches[ $attribute_raw ][ $item->slug ] ) ) {
			$attribute_swatches[ $attribute_raw ][ $item->slug ]['btn'] = $item->name;
		}
		if ( $is_taxonomy && ! in_array( $item->slug, $args['options'], true ) ) {
			continue;
		}
		if ( $is_taxonomy ) {
			if ( ! $any_attrib && ( ! isset( $_variations[ $attribute_raw ] ) || ! in_array( $item->slug, $_variations[ $attribute_raw ], true ) ) ) {
				continue;
			}
		} else {
			if ( ! $any_attrib && ( ! isset( $_variations[ $attribute_raw ] ) || ! in_array( $item->name, $_variations[ $attribute_raw ], true ) ) ) {
				continue;
			}
		}
		$selected = $args['selected'] === $item->slug ? 'cgkit-swatch-selected' : '';
		if ( $as_button_style && 'button' === $swatch_type ) {
			$selected .= ' button-fluid';
		}
		$swatch_html    = commercekit_as_get_swatch_html( $swatch_type, $attribute_swatches[ $attribute_raw ][ $item->slug ], $item );
		$item_title     = 'button' === $swatch_type && isset( $attribute_swatches[ $attribute_raw ][ $item->slug ]['btn'] ) ? $attribute_swatches[ $attribute_raw ][ $item->slug ]['btn'] : $item->name;
		$item_gimg_id   = isset( $_gal_images[ $item->slug ] ) ? $_gal_images[ $item->slug ] : '';
		$item_attri_val = $is_taxonomy ? $item->slug : $item->name;
		$swatches_html .= sprintf( '<li class="cgkit-attribute-swatch cgkit-%s %s"><button type="button" data-type="%s" data-attribute-value="%s" data-attribute-text="%s" data-oos-text="%s" title="%s" class="swatch cgkit-swatch %s" data-gimg_id="%s">%s</button></li>', $swatch_type, $item_class, $swatch_type, esc_attr( $item_attri_val ), esc_attr( $item->name ), $item_oos_text, esc_attr( $item_title ), $selected, $item_gimg_id, $swatch_html );
	}
	$swatches_html .= '</ul></div>';
	if ( 'cgkit-as-swiper' === $attribute_css ) {
		$swatches_html .= '<div class="swiper-button-next"></div><div class="swiper-button-prev"></div>';
	}
	$swatches_html .= sprintf( '<div style="display: none;">%s</div>', $html );

	return $swatches_html;
}
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'commercekit_attribute_swatches_options_html', 10, 2 );

/**
 * Attribute swatches attribute label.
 *
 * @param string $label attribute label.
 * @param string $name attribute name.
 */
function commercekit_attribute_swatches_attribute_label( $label, $name ) {
	global $product;
	if ( $product && method_exists( $product, 'is_type' ) && $product->is_type( 'variable' ) && is_product() ) {
		$css_class = 'attribute_' . $name;
		return sprintf( '<strong>%s</strong><span class="ckit-chosen-attribute_semicolon">:</span> <span class="cgkit-chosen-attribute %s no-selection">%s</span>', $label, $css_class, esc_html__( 'No selection', 'commercegurus-commercekit' ) );
	} else {
		return $label;
	}
}
add_filter( 'woocommerce_attribute_label', 'commercekit_attribute_swatches_attribute_label', 102, 2 );

/**
 * Attribute swatches get attribute slug.
 *
 * @param string $slug   slug of attribute.
 * @param bool   $prefix prefix of attribute.
 */
function commercekit_as_get_attribute_slug( $slug, $prefix = false ) {
	if ( ( 'pa_' !== substr( $slug, 0, 3 ) || $prefix ) && false === strpos( $slug, 'attribute_' ) ) {
		$slug = 'attribute_' . sanitize_title( $slug );
	}

	return $slug;
}

/**
 * Attribute swatches get swatch html.
 *
 * @param string $swatch_type type of swatch.
 * @param string $data data of attribute.
 * @param string $item data of term.
 */
function commercekit_as_get_swatch_html( $swatch_type, $data, $item ) {
	$swatch_html = '';

	if ( 'image' === $swatch_type ) {
		$image = null;
		if ( isset( $data['img'] ) && ! empty( $data['img'] ) ) {
			commercekit_as_generate_attachment_size( $data['img'], 'cgkit_image_swatch' );
			$image = wp_get_attachment_image_src( $data['img'], 'cgkit_image_swatch' );
		}
		if ( $image ) {
			$swatch_html = '<span>&nbsp;</span><img alt="' . esc_attr( $item->name ) . '" width="' . esc_attr( $image[1] ) . '" height="' . esc_attr( $image[2] ) . '" src="' . esc_url( $image[0] ) . '" />';
		} else {
			$swatch_html = '<span>&nbsp;</span>';
		}
	} elseif ( 'color' === $swatch_type ) {
		if ( isset( $data['clr'] ) && ! empty( $data['clr'] ) ) {
			$swatch_html = '<span>&nbsp;</span><div style="background-color: ' . esc_attr( $data['clr'] ) . ' " data-color="' . esc_attr( $data['clr'] ) . '">&nbsp;</div>';
		} else {
			$swatch_html = '<span>&nbsp;</span><div style="" data-color="">&nbsp;</div>';
		}
	} elseif ( 'button' === $swatch_type ) {
		if ( isset( $data['btn'] ) && ! empty( $data['btn'] ) ) {
			$swatch_html = '<span>&nbsp;</span>' . esc_attr( $data['btn'] );
		} else {
			$swatch_html = '<span>&nbsp;</span>';
		}
	}

	return $swatch_html;
}

/**
 * Attribute swatches disable ajax variation threshold.
 *
 * @param string $default default value of threshold.
 * @param string $product data of product.
 */
function commercekit_as_disable_ajax_variation_threshold( $default, $product ) {
	return 9999999;
}
add_filter( 'woocommerce_ajax_variation_threshold', 'commercekit_as_disable_ajax_variation_threshold', 10, 2 );

/**
 * Add attribute swatches to product loop
 */
function commercekit_as_product_loop() {
	global $product;
	if ( ! $product || ( method_exists( $product, 'is_type' ) && ! $product->is_type( 'variable' ) ) ) {
		return;
	}
	$options       = get_option( 'commercekit', array() );
	$as_active_plp = isset( $options['attribute_swatches_plp'] ) && 1 === (int) $options['attribute_swatches_plp'] ? true : false;
	if ( ! $as_active_plp ) {
		return;
	}

	$product_id = $product ? $product->get_id() : 0;
	if ( ! $product_id ) {
		return;
	}

	$out_of_stock = get_post_meta( $product_id, '_stock_status', true );
	if ( 'outofstock' === $out_of_stock ) {
		return;
	}

	$as_swatches = get_post_meta( $product_id, 'cgkit_attribute_swatches', true );
	$enable_loop = ( isset( $as_swatches['enable_loop'] ) && 1 === (int) $as_swatches['enable_loop'] ) || ! isset( $as_swatches['enable_loop'] ) ? true : false;
	if ( ! $enable_loop ) {
		return;
	}
	$cache_key     = 'cgkit_swatch_loop_form_' . $product_id;
	$swatches_html = get_transient( $cache_key );
	wp_enqueue_script( 'wc-add-to-cart-variation' );

	if ( ! isset( $_GET['cgkit-nocache'] ) && false !== $swatches_html ) { // phpcs:ignore
		echo apply_filters( 'cgkit_loop_swatches', $swatches_html, $product ); // phpcs:ignore
		return;
	}
	$swatches_html = commercekit_as_build_product_swatch_cache( $product, true );
	echo apply_filters( 'cgkit_loop_swatches', $swatches_html, $product ); // phpcs:ignore
}
add_action( 'woocommerce_after_shop_loop_item', 'commercekit_as_product_loop', 10 );

/**
 * Update product attribute swatches cache
 *
 * @param string $post_id post ID.
 * @param string $post post.
 */
function commercegurus_update_product_as_data( $post_id, $post ) {
	global $product;
	if ( 'product' !== $post->post_type ) {
		return;
	}
	$product = wc_get_product( $post_id );
	if ( ! $product || ( method_exists( $product, 'is_type' ) && ! $product->is_type( 'variable' ) ) ) {
		return;
	}
	commercekit_as_build_product_swatch_cache( $product );
}
add_action( 'woocommerce_process_product_meta', 'commercegurus_update_product_as_data', 10, 2 );

/**
 * Update product attribute swatches cache on stock, variations updates
 *
 * @param string $product_id product ID.
 */
function commercegurus_update_product_as_cache( $product_id ) {
	global $product;
	$product = wc_get_product( $product_id );
	if ( ! $product || ( method_exists( $product, 'is_type' ) && ! $product->is_type( 'variable' ) ) ) {
		return;
	}
	commercekit_as_build_product_swatch_cache( $product );
}
add_action( 'woocommerce_updated_product_stock', 'commercegurus_update_product_as_cache', 10, 1 );
add_action( 'woocommerce_save_product_variation', 'commercegurus_update_product_as_cache', 10, 1 );
add_action( 'woocommerce_ajax_save_product_variations', 'commercegurus_update_product_as_cache', 10, 1 );

/**
 * Update product attribute swatches cache on quick edit updates
 *
 * @param string $product_id product ID.
 */
function commercegurus_quick_edit_update_product_as_cache( $product_id ) {
	global $product, $post;
	if ( isset( $post ) && 'product' === $post->post_type ) {
		$product = wc_get_product( $product_id );
		if ( ! $product || ( method_exists( $product, 'is_type' ) && ! $product->is_type( 'variable' ) ) ) {
			return;
		}
		commercekit_as_build_product_swatch_cache( $product );
	}
}
add_action( 'save_post', 'commercegurus_quick_edit_update_product_as_cache', 10, 1 );

/**
 * Update product attribute swatches cache on stock updates
 *
 * @param string $product_obj product object.
 */
function commercegurus_update_product_as_cache_stock_updates( $product_obj ) {
	global $product;
	if ( $product_obj->is_type( 'variation' ) ) {
		$product_id = $product_obj->get_parent_id();
		$product    = wc_get_product( $product_id );
	} else {
		$product = $product_obj;
	}
	if ( ! $product || ( method_exists( $product, 'is_type' ) && ! $product->is_type( 'variable' ) ) ) {
		return;
	}
	commercekit_as_build_product_swatch_cache( $product );
}
add_action( 'woocommerce_product_set_stock', 'commercegurus_update_product_as_cache_stock_updates', 10, 1 );
add_action( 'woocommerce_variation_set_stock', 'commercegurus_update_product_as_cache_stock_updates', 10, 1 );

/**
 * Attribute swatches get loop swatch image.
 *
 * @param string $attachment_id image ID.
 */
function commercekit_as_get_loop_swatch_image( $attachment_id ) {
	$image_size   = 'woocommerce_thumbnail';
	$swatch_image = wp_get_attachment_image_src( $attachment_id, $image_size );
	if ( ! $swatch_image ) {
		return false;
	}
	$swatch_image['srcset'] = wp_get_attachment_image_srcset( $attachment_id, $image_size );
	$swatch_image['sizes']  = wp_get_attachment_image_sizes( $attachment_id, $image_size );

	return $swatch_image;
}

/**
 * Attribute swatches add image size.
 */
function commercekit_as_add_image_size() {
	add_image_size( 'cgkit_image_swatch', 100, 100, true );
}
add_action( 'init', 'commercekit_as_add_image_size' );

/**
 * Attribute swatches generate attachment size if not exist.
 *
 * @param string $attachment_id image ID.
 * @param string $size image size.
 */
function commercekit_as_generate_attachment_size( $attachment_id, $size ) {
	if ( ! function_exists( 'wp_crop_image' ) ) {
		include ABSPATH . 'wp-admin/includes/image.php';
	}

	$old_metadata = wp_get_attachment_metadata( $attachment_id );
	if ( isset( $old_metadata['sizes'][ $size ] ) ) {
		return;
	}

	$fullsizepath = get_attached_file( $attachment_id );
	if ( false === $fullsizepath || is_wp_error( $fullsizepath ) || ! file_exists( $fullsizepath ) ) {
		return;
	}

	$new_metadata = wp_generate_attachment_metadata( $attachment_id, $fullsizepath );
	if ( is_wp_error( $new_metadata ) || empty( $new_metadata ) ) {
		return;
	}

	wp_update_attachment_metadata( $attachment_id, $new_metadata );
}

/**
 * Get ajax products variations
 */
function commercegurus_get_ajax_as_variations() {
	$commercekit_nonce  = isset( $_POST['commercekit_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['commercekit_nonce'] ) ) : '';
	$verify_nonce       = wp_verify_nonce( $commercekit_nonce, 'commercekit_nonce' );
	$product_ids        = isset( $_POST['product_ids'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['product_ids'] ) ) ) : '';
	$ajax               = array();
	$ajax['status']     = 1;
	$ajax['variations'] = array();
	$ajax['images']     = array();
	$product_ids        = explode( ',', $product_ids );
	if ( count( $product_ids ) ) {
		foreach ( $product_ids as $product_id ) {
			$cache_key2    = 'cgkit_swatch_loop_form_data_' . $product_id;
			$swatches_html = get_transient( $cache_key2 );
			if ( false !== $swatches_html ) {
				$swatches_data = json_decode( $swatches_html, true );

				$ajax['variations'][ $product_id ] = isset( $swatches_data['variations'] ) ? wp_json_encode( $swatches_data['variations'] ) : '';
				$ajax['images'][ $product_id ]     = isset( $swatches_data['images'] ) ? wp_json_encode( $swatches_data['images'] ) : '';
			} else {
				$ajax['variations'][ $product_id ] = '';
				$ajax['images'][ $product_id ]     = '';
			}
		}
	}
	wp_send_json( $ajax );
}
add_action( 'wp_ajax_get_ajax_as_variations', 'commercegurus_get_ajax_as_variations' );
add_action( 'wp_ajax_nopriv_get_ajax_as_variations', 'commercegurus_get_ajax_as_variations' );

/**
 * Ajax add to cart.
 */
function commercegurus_ajax_as_add_to_cart() {
	$ajax            = array();
	$ajax['status']  = 0;
	$ajax['notices'] = '';
	$ajax['message'] = esc_html__( 'Error on adding to cart.', 'commercegurus-commercekit' );

	$nonce        = wp_verify_nonce( 'commercekit_nonce', 'commercekit_nonce' );
	$product_id   = isset( $_POST['product_id'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : 0;
	$variation_id = isset( $_POST['variation_id'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['variation_id'] ) ) : 0;
	$variations   = isset( $_POST['variations'] ) ? map_deep( wp_unslash( $_POST['variations'] ), 'sanitize_text_field' ) : array();
	if ( $product_id && $variation_id ) {
		if ( WC()->cart->add_to_cart( $product_id, 1, $variation_id, $variations ) ) {
			$ajax['status']  = 1;
			$ajax['message'] = esc_html__( 'Sucessfully added to cart.', 'commercegurus-commercekit' );

			ob_start();
			woocommerce_mini_cart();
			$mini_cart = ob_get_clean();

			$ajax['fragments'] = apply_filters(
				'woocommerce_add_to_cart_fragments',
				array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
				)
			);
			$ajax['cart_hash'] = WC()->cart->get_cart_hash();
		} else {
			ob_start();
			wc_print_notices();
			$notices = ob_get_clean();

			$ajax['notices'] = $notices;
		}
	}

	wp_send_json( $ajax );
}
add_action( 'wp_ajax_ajax_as_add_to_cart', 'commercegurus_ajax_as_add_to_cart' );
add_action( 'wp_ajax_nopriv_ajax_as_add_to_cart', 'commercegurus_ajax_as_add_to_cart' );

/**
 * Attribute swatches loop add to cart link.
 *
 * @param string $html    link html.
 * @param string $product product object.
 */
function commercegurus_as_loop_add_to_cart_link( $html, $product ) {
	$options       = get_option( 'commercekit', array() );
	$as_active_plp = isset( $options['attribute_swatches_plp'] ) && 1 === (int) $options['attribute_swatches_plp'] ? true : false;
	if ( ! $as_active_plp ) {
		return $html;
	}
	$hide_button = true;
	if ( $hide_button && $product && ( method_exists( $product, 'is_type' ) && $product->is_type( 'variable' ) ) ) {
		$product_id   = $product ? $product->get_id() : 0;
		$out_of_stock = get_post_meta( $product_id, '_stock_status', true );
		if ( 'outofstock' === $out_of_stock ) {
			return $html;
		}
		$as_swatches = get_post_meta( $product_id, 'cgkit_attribute_swatches', true );
		$enable_loop = ( isset( $as_swatches['enable_loop'] ) && 1 === (int) $as_swatches['enable_loop'] ) || ! isset( $as_swatches['enable_loop'] ) ? true : false;
		if ( ! $enable_loop ) {
			return $html;
		}

		return '';
	}

	return $html;
}
add_filter( 'woocommerce_loop_add_to_cart_link', 'commercegurus_as_loop_add_to_cart_link', 99, 2 );

/**
 * Product gallery options
 *
 * @param string $options module options.
 */
function commercekit_get_as_options( $options ) {
	$commercekit_as = array();

	$commercekit_as['as_activate_atc'] = isset( $options['as_activate_atc'] ) && 1 === (int) $options['as_activate_atc'] ? 1 : 0;
	$commercekit_as['cgkit_attr_gal']  = isset( $options['pdp_attributes_gallery'] ) && 1 === (int) $options['pdp_attributes_gallery'] ? 1 : 0;

	return $commercekit_as;
}

/**
 * Product loop class
 *
 * @param array  $classes array of classes.
 * @param string $product product object.
 */
function commercegurus_as_loop_class( $classes, $product ) {
	$options     = get_option( 'commercekit', array() );
	$disable_atc = isset( $options['as_activate_atc'] ) && 1 === (int) $options['as_activate_atc'] ? false : true;
	if ( $product && ( method_exists( $product, 'is_type' ) && $product->is_type( 'variable' ) ) ) {
		$hide_button = true;
		if ( $hide_button ) {
			$can_hide     = true;
			$product_id   = $product ? $product->get_id() : 0;
			$out_of_stock = get_post_meta( $product_id, '_stock_status', true );
			if ( 'outofstock' === $out_of_stock ) {
				$can_hide = false;
			}
			$as_swatches = get_post_meta( $product_id, 'cgkit_attribute_swatches', true );
			$enable_loop = ( isset( $as_swatches['enable_loop'] ) && 1 === (int) $as_swatches['enable_loop'] ) || ! isset( $as_swatches['enable_loop'] ) ? true : false;
			if ( ! $enable_loop ) {
				$can_hide = false;
			}
			if ( $can_hide ) {
				$classes[] = 'ckit-hide-cta';
			}
		}
		$classes[] = 'cgkit-swatch-hover';
		if ( $disable_atc ) {
			$classes[] = 'cgkit-disable-atc';
		}
	}

	return $classes;
}
add_filter( 'woocommerce_post_class', 'commercegurus_as_loop_class', 10, 2 );

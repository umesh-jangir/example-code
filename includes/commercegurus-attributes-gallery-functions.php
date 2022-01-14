<?php
/**
 * Main functions for rendering gallery html and tweaks to PDP's for compatibility with other plugins
 *
 * @author   CommerceGurus
 * @package  CommerceGurus_Attributes_Gallery
 * @since    1.0.0
 */

/**
 * Get html for the main PDP attributes gallery.
 *
 * Hooks: woocommerce_gallery_thumbnail_size, woocommerce_gallery_image_size and woocommerce_gallery_full_size accept name based image sizes, or an array of width/height values.
 *
 * @since 1.0.0
 * @param int    $attachment_id Attachment ID.
 * @param bool   $main_image Is this the main image or a thumbnail?.
 * @param string $li_class   list class.
 * @return string
 */
function commercegurus_get_main_attributes_gallery_image_html( $attachment_id, $main_image = false, $li_class = '' ) {
	$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
	$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
	$image_size        = 'woocommerce_single';
	$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
	$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
	$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
	if ( false === $full_src ) {
		return '';
	}
	$full_srcset = wp_get_attachment_image_srcset( $attachment_id, $full_size );
	$alt_text    = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
	$image       = wp_get_attachment_image(
		$attachment_id,
		$image_size,
		false,
		apply_filters(
			'woocommerce_gallery_image_html_attachment_image_params',
			array(
				'title'        => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'data-caption' => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'class'        => 'wp-post-image',
			),
			$attachment_id,
			$image_size,
			$main_image
		)
	);
	return '<li class="swiper-slide ' . esc_attr( $li_class ) . '" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
	  <a class="swiper-slide-imglink" title="' . esc_html__( 'click to zoom-in', 'commercegurus-commercekit' ) . '" href="' . esc_url( $full_src[0] ) . '" itemprop="contentUrl" data-size="' . esc_attr( $full_src[1] ) . 'x' . esc_attr( $full_src[2] ) . '">
		' . $image . '
	  </a>
	</li>';
}

/**
 * Get lazy html for the main PDP attributes gallery. Used for all images after the first one.
 *
 * Hooks: woocommerce_gallery_thumbnail_size, woocommerce_gallery_image_size and woocommerce_gallery_full_size accept name based image sizes, or an array of width/height values.
 *
 * @since 1.0.0
 * @param int    $attachment_id Attachment ID.
 * @param bool   $main_image Is this the main image or a thumbnail?.
 * @param string $li_class   list class.
 * @return string
 */
function commercegurus_get_main_attributes_gallery_image_lazy_html( $attachment_id, $main_image = false, $li_class = '' ) {
	$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
	$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
	$image_size        = 'woocommerce_single';
	$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
	$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
	$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
	if ( false === $full_src ) {
		return '';
	}
	$full_srcset = wp_get_attachment_image_srcset( $attachment_id, $full_size );
	$alt_text    = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );

	$placeholder = CKIT_URI . 'assets/images/spacer.png';

	return '<li class="swiper-slide ' . esc_attr( $li_class ) . '" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
	  <a class="swiper-slide-imglink" title="' . esc_html__( 'click to zoom-in', 'commercegurus-commercekit' ) . '" href="' . esc_url( $full_src[0] ) . '" itemprop="contentUrl" data-size="' . esc_attr( $full_src[1] ) . 'x' . esc_attr( $full_src[2] ) . '">
		<img width="' . esc_attr( $full_src[1] ) . '" height="' . esc_attr( $full_src[2] ) . '" src="' . $placeholder . '" data-src="' . esc_url( $full_src[0] ) . '" data-srcset="' . $full_srcset . '" sizes="(max-width: 360px) 330px, (max-width: 800px) 100vw, 800px" alt="' . $alt_text . '" itemprop="thumbnail" class="pdp-img swiper-lazy wp-post-image" />
		<div class="cg-swiper-preloader"></div>
	  </a>
	</li>';
}

/**
 * Get html for the small thumbnail gallery under the main PDP gallery.
 *
 * Hooks: woocommerce_gallery_thumbnail_size, woocommerce_gallery_image_size and woocommerce_gallery_full_size accept name based image sizes, or an array of width/height values.
 *
 * @since 1.0.0
 * @param int  $attachment_id Attachment ID.
 * @param bool $main_image Is this the main image or a thumbnail?.
 * @param int  $index slider index.
 * @param bool $css_class Is CSS class.
 * @return string
 */
function commercegurus_get_thumbnail_attributes_gallery_image_html( $attachment_id, $main_image = false, $index = 0, $css_class = '' ) {
	$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
	$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
	$image_size        = 'woocommerce_gallery_thumbnail';
	$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
	$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
	$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
	if ( false === $full_src ) {
		return '';
	}
	$alt_text = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );

	return '	<li class="swiper-slide ' . $css_class . '" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" data-variation-id="' . esc_attr( $attachment_id ) . '" data-index="' . esc_attr( $index ) . '">' . ( 'pdp-video' === $css_class ? '<div class="cgkit-play"><svg class="play" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h48v48H0z" fill="none"></path><path d="m20 33 12-9-12-9v18zm4-29C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm0 36c-8.82 0-16-7.18-16-16S15.18 8 24 8s16 7.18 16 16-7.18 16-16 16z" fill="#ffffff" class="fill-000000"></path></svg></div>' : '' ) . '
		<img width="' . esc_attr( $full_src[1] ) . '" height="' . esc_attr( $full_src[2] ) . '" src="' . esc_url( $full_src[0] ) . '" alt="' . $alt_text . '" itemprop="thumbnail" class="wp-post-image" />
	</li>
';
}

/**
 * Remove elementors swiper instance.
 */
function remove_elementor_scripts_attributes_gallery() {
	if ( function_exists( 'is_product' ) && is_product() && in_array( 'elementor/elementor.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
		wp_dequeue_script( 'swiper' );
		wp_deregister_script( 'swiper' );
	}
}
// TODO - add condition to check if elementor is installed and remove their swiperjs if so.
add_action( 'wp_enqueue_scripts', 'remove_elementor_scripts_attributes_gallery' );

/**
 * Get product attributes gallery admin tab.
 *
 * @param string $tabs admin product tabs.
 */
function commercegurus_get_attributes_gallery_tab( $tabs ) {
	$tabs['commercekit_gallery'] = array(
		'label'    => esc_html__( 'Product Attributes Gallery', 'commercegurus-commercekit' ),
		'target'   => 'cgkit_attr_gallery',
		'class'    => array( 'commercekit-attributes-gallery', 'show_if_variable' ),
		'priority' => 61,
	);
	return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'commercegurus_get_attributes_gallery_tab' );

/**
 * Get product attributes gallery admin panel.
 */
function commercegurus_get_attributes_gallery_panel() {
	global $post;
	$product_id = $post->ID;
	$attributes = commercegurus_attributes_load_attributes( $product_id );

	$commercekit_image_gallery = get_post_meta( $product_id, 'commercekit_image_gallery', true );
	$commercekit_video_gallery = get_post_meta( $product_id, 'commercekit_video_gallery', true );
	require_once dirname( __FILE__ ) . '/templates/admin-product-attributes-gallery.php';
}
add_filter( 'woocommerce_product_data_panels', 'commercegurus_get_attributes_gallery_panel' );

/**
 * Add admin CSS and JS scripts
 */
function commercegurus_attributes_admin_scripts() {
	$screen = get_current_screen();
	if ( 'product' === $screen->post_type && 'post' === $screen->base ) {
		wp_enqueue_style( 'commercekit-attributes-select2-style', CKIT_URI . 'assets/css/select2.css', array(), CGKIT_CSS_JS_VER );
		wp_enqueue_style( 'commercekit-attributes-admin-style', CKIT_URI . 'assets/css/admin-product-attributes-gallery.css', array(), CGKIT_CSS_JS_VER );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'commercekit-attributes-select2-script', CKIT_URI . 'assets/js/select2.js', array(), CGKIT_CSS_JS_VER, true );
		wp_enqueue_script( 'commercekit-attributes-admin-script', CKIT_URI . 'assets/js/admin-product-attributes-gallery.js', array(), CGKIT_CSS_JS_VER, true );
	}
}

add_action( 'admin_enqueue_scripts', 'commercegurus_attributes_admin_scripts' );

/**
 * Load selected attributes
 *
 * @param string $product_id admin product ID.
 */
function commercegurus_attributes_load_attributes( $product_id ) {
	$product_id = intval( $product_id );
	$product    = wc_get_product_object( 'variable', $product_id );
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
function commercegurus_save_product_attributes_gallery( $post_id, $post ) {
	if ( 'product' !== $post->post_type ) {
		return;
	}
	$commercekit_nonce = isset( $_POST['commercekit_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['commercekit_nonce'] ) ) : '';
	if ( $commercekit_nonce && wp_verify_nonce( $commercekit_nonce, 'commercekit_nonce' ) ) {
		if ( $post_id ) {
			$image_gallery = isset( $_POST['commercekit_image_gallery'] ) ? map_deep( wp_unslash( $_POST['commercekit_image_gallery'] ), 'sanitize_textarea_field' ) : array();
			update_post_meta( $post_id, 'commercekit_image_gallery', $image_gallery );
			$video_gallery = isset( $_POST['commercekit_video_gallery'] ) ? map_deep( wp_unslash( $_POST['commercekit_video_gallery'] ), 'sanitize_textarea_field' ) : array();
			update_post_meta( $post_id, 'commercekit_video_gallery', $video_gallery );
		}
	}
}
add_action( 'woocommerce_process_product_meta', 'commercegurus_save_product_attributes_gallery', 10, 2 );

/**
 * Get ajax product gallery
 */
function commercegurus_get_ajax_product_gallery() {
	$ajax           = array();
	$ajax['status'] = 0;
	$ajax['html']   = '';

	$product_id = isset( $_GET['product_id'] ) ? (int) $_GET['product_id'] : 0; // phpcs:ignore
	if ( $product_id ) {
		ob_start();
		$attributes   = commercegurus_attributes_load_attributes( $product_id );
		$without_wrap = true;

		$commercekit_image_gallery = get_post_meta( $product_id, 'commercekit_image_gallery', true );
		$commercekit_video_gallery = get_post_meta( $product_id, 'commercekit_video_gallery', true );

		require_once dirname( __FILE__ ) . '/templates/admin-product-attributes-gallery.php';

		$ajax['status'] = 1;
		$ajax['html']   = ob_get_contents();
		ob_clean();
	}

	wp_send_json( $ajax );
}

add_action( 'wp_ajax_get_ajax_product_gallery', 'commercegurus_get_ajax_product_gallery' );

/**
 * Update ajax product gallery
 */
function commercegurus_update_ajax_product_gallery() {
	$ajax           = array();
	$ajax['status'] = 0;
	$ajax['html']   = '';

	$product_id = isset( $_GET['product_id'] ) ? (int) $_GET['product_id'] : 0; // phpcs:ignore
	if ( $product_id ) {
		$post = get_post( $product_id );
		commercegurus_save_product_attributes_gallery( $product_id, $post );
		$ajax['status'] = 1;
	}

	wp_send_json( $ajax );
}

add_action( 'wp_ajax_update_ajax_product_gallery', 'commercegurus_update_ajax_product_gallery' );

/**
 * Get product gallery slug
 *
 * @param string $slug          gallery slug.
 * @param string $gallery_slugs gallery slugs.
 */
function commercegurus_get_product_gallery_slug( $slug, $gallery_slugs ) {
	if ( false !== stripos( $slug, '_cgkit_' ) ) {
		$parts = explode( '_cgkit_', $slug );
		$slugs = array();
		if ( count( $parts ) ) {
			foreach ( $parts as $part ) {
				$slugs[ array_search( $part, $gallery_slugs, true ) ] = $part;
			}
		}
		ksort( $slugs );
		return implode( '_cgkit_', $slugs );
	} else {
		return $slug;
	}
}


<?php
/**
 * Main functions for rendering gallery html and tweaks to PDP's for compatibility with other plugins
 *
 * @author   CommerceGurus
 * @package  CommerceGurus_Gallery
 * @since    1.0.0
 */

/**
 * Get html for the main PDP gallery.
 *
 * Hooks: woocommerce_gallery_thumbnail_size, woocommerce_gallery_image_size and woocommerce_gallery_full_size accept name based image sizes, or an array of width/height values.
 *
 * @since 1.0.0
 * @param int    $attachment_id Attachment ID.
 * @param bool   $main_image Is this the main image or a thumbnail?.
 * @param string $li_class   list class.
 * @return string
 */
function commercegurus_get_main_gallery_image_html( $attachment_id, $main_image = false, $li_class = '' ) {
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
	return '<li class="woocommerce-product-gallery__image swiper-slide ' . esc_attr( $li_class ) . '" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
	  <a class="swiper-slide-imglink" title="' . esc_html__( 'click to zoom-in', 'commercegurus-commercekit' ) . '" href="' . esc_url( $full_src[0] ) . '" itemprop="contentUrl" data-size="' . esc_attr( $full_src[1] ) . 'x' . esc_attr( $full_src[2] ) . '">
		' . $image . '
	  </a>
	</li>';
}

/**
 * Get lazy html for the main PDP gallery. Used for all images after the first one.
 *
 * Hooks: woocommerce_gallery_thumbnail_size, woocommerce_gallery_image_size and woocommerce_gallery_full_size accept name based image sizes, or an array of width/height values.
 *
 * @since 1.0.0
 * @param int    $attachment_id Attachment ID.
 * @param bool   $main_image Is this the main image or a thumbnail?.
 * @param string $li_class   list class.
 * @return string
 */
function commercegurus_get_main_gallery_image_lazy_html( $attachment_id, $main_image = false, $li_class = '' ) {
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

	return '<li class="woocommerce-product-gallery__image swiper-slide ' . esc_attr( $li_class ) . '" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
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
function commercegurus_get_thumbnail_gallery_image_html( $attachment_id, $main_image = false, $index = 0, $css_class = '' ) {
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
 * The Elementor\Frontend class runs its register_scripts() method on
 * wp_enqueue_scripts at priority 5, so we want to hook in after this has taken place.
 */
add_action( 'wp_enqueue_scripts', 'commercegurus_elementor_frontend_scripts_modifier', 6 );

/**
 * Elementor frontend scripts modifier
 */
function commercegurus_elementor_frontend_scripts_modifier() {

	// Get all scripts.
	$scripts = wp_scripts();

	// Bail if something went wrong.
	if ( ! ( $scripts instanceof WP_Scripts ) ) {
		return;
	}

	// Array of handles to remove.
	$handles_to_remove = array( 'swiper' );

	// Flag indicating if we have removed the handles.
	$handles_updated = false;

	// Remove desired handles from the elementor-frontend script.
	foreach ( $scripts->registered as $dependency_object_id => $dependency_object ) {

		if ( 'elementor-frontend' === $dependency_object_id ) {

			// Bail if something went wrong.
			if ( ! ( $dependency_object instanceof _WP_Dependency ) ) {
				return;
			}

			// Bail if there are no dependencies for some reason.
			if ( empty( $dependency_object->deps ) ) {
				return;
			}

			// Do the handle removal.
			foreach ( $dependency_object->deps as $dep_key => $handle ) {
				if ( in_array( $handle, $handles_to_remove, true ) ) {
					unset( $dependency_object->deps[ $dep_key ] );
					$dependency_object->deps = array_values( $dependency_object->deps );  // "reindex" array
					$handles_updated         = true;
				}
			}
		}
	}

	// If we have updated the handles, dequeue the relevant dependencies which
	// were enqueued separately Elementor\Frontend.
	if ( $handles_updated ) {
		wp_dequeue_script( 'swiper' );
		wp_deregister_script( 'swiper' );
	}
}

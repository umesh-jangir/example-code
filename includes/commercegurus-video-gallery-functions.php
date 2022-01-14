<?php
/**
 * Main functions for rendering video gallery
 *
 * @author   CommerceGurus
 * @package  CommerceGurus_Video_Gallery
 * @since    1.0.0
 */

/**
 * Get product video gallery admin html.
 */
function commercegurus_get_video_gallery_html() {
	global $post;
	$product_id = $post->ID;
	require_once dirname( __FILE__ ) . '/templates/admin-product-video-gallery.php';
}
add_filter( 'woocommerce_product_data_panels', 'commercegurus_get_video_gallery_html' );

/**
 * Save product video gallery
 *
 * @param string $post_id post ID.
 * @param string $post post.
 */
function commercegurus_save_product_video_gallery( $post_id, $post ) {
	if ( 'product' !== $post->post_type ) {
		return;
	}
	$commercekit_video = isset( $_POST['commercekit_video'] ) ? sanitize_text_field( wp_unslash( $_POST['commercekit_video'] ) ) : '';
	if ( $commercekit_video && wp_verify_nonce( $commercekit_video, 'commercekit_video' ) ) {
		if ( $post_id ) {
			$cgkit_video_gallery = isset( $_POST['cgkit_video_gallery'] ) ? map_deep( wp_unslash( $_POST['cgkit_video_gallery'] ), 'sanitize_textarea_field' ) : array();
			update_post_meta( $post_id, 'cgkit_video_gallery', array_filter( $cgkit_video_gallery ) );
			$cgkit_gallery_layout = isset( $_POST['cgkit_gallery_layout'] ) ? sanitize_text_field( wp_unslash( $_POST['cgkit_gallery_layout'] ) ) : '';
			update_post_meta( $post_id, 'cgkit_gallery_layout', $cgkit_gallery_layout );
		}
	}
}
add_action( 'woocommerce_process_product_meta', 'commercegurus_save_product_video_gallery', 10, 2 );

/**
 * Get product video gallery
 *
 * @param string $post_id post ID.
 * @param string $attachment_id attachment id.
 */
function commercegurus_get_product_video_gallery( $post_id, $attachment_id ) {
	$videos    = get_post_meta( $post_id, 'cgkit_video_gallery', true );
	$video_url = isset( $videos[ $attachment_id ] ) ? $videos[ $attachment_id ] : '';
	$css_class = ! empty( $video_url ) ? 'cgkit-editvideos' : 'cgkit-addvideos';
	echo '<span class="dashicons dashicons-video-alt3 cgkit-videos ' . esc_attr( $css_class ) . '"><input type="hidden" class="cgkit-video-gallery" name="cgkit_video_gallery[' . esc_attr( $attachment_id ) . ']" value="' . esc_url( $video_url ) . '" /></span>';
}
add_action( 'woocommerce_admin_after_product_gallery_item', 'commercegurus_get_product_video_gallery', 10, 2 );

/**
 * Add admin CSS and JS scripts
 */
function commercegurus_product_video_admin_scripts() {
	$screen = get_current_screen();
	if ( 'product' === $screen->post_type && 'post' === $screen->base ) {
		wp_enqueue_style( 'commercekit-video-gallery-style', CKIT_URI . 'assets/css/admin-product-video-gallery.css', array(), CGKIT_CSS_JS_VER );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'commercekit-video-admin-script', CKIT_URI . 'assets/js/admin-product-video-gallery.js', array(), CGKIT_CSS_JS_VER, true );
	}
}

add_action( 'admin_enqueue_scripts', 'commercegurus_product_video_admin_scripts' );

/**
 * Get html for video.
 *
 * @param string $video_url video URL.
 * @param string $main_video is main video.
 * @param string $autoplay is auto play video.
 * @param string $attachment_id is default image.
 * @return string
 */
function commercegurus_get_product_gallery_video_html( $video_url, $main_video = false, $autoplay = false, $attachment_id ) {
	$full_size = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
	$full_src  = wp_get_attachment_image_src( $attachment_id, $full_size );
	if ( false === $full_src ) {
		return '';
	}
	if ( ! $full_src ) {
		$full_src    = array();
		$full_src[0] = '';
		$full_src[1] = 0;
		$full_src[2] = 0;
	}
	$full_srcset = wp_get_attachment_image_srcset( $attachment_id, $full_size );
	$alt_text    = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );

	$tmp_url = explode( '::', $video_url );
	if ( isset( $tmp_url[0] ) ) {
		$video_url = $tmp_url[0];
	}
	if ( isset( $tmp_url[1] ) ) {
		$autoplay = 1 === (int) $tmp_url[1] ? true : false;
	}
	if ( empty( $video_url ) ) {
		return '';
	}

	$placeholder = CKIT_URI . 'assets/images/spacer.png';
	$embed_video = '';
	$embed_html  = '';
	$embed_url   = '';
	$video_id    = array();
	if ( false !== stripos( $video_url, 'vimeo.com/' ) ) {
		$video_id  = explode( 'vimeo.com/', $video_url );
		$embed_url = 'https://player.vimeo.com/video/';
	} elseif ( false !== stripos( $video_url, 'youtube.com/' ) ) {
		$video_id  = explode( 'v=', $video_url );
		$embed_url = 'https://www.youtube.com/embed/';
	} elseif ( false !== stripos( $video_url, 'youtu.be/' ) ) {
		$video_id  = explode( 'youtu.be/', $video_url );
		$embed_url = 'https://www.youtube.com/embed/';
	}
	if ( isset( $video_id[1] ) && ! empty( $video_id[1] ) ) {
		$video_id = $video_id[1];
		if ( false !== strpos( $video_id, '&' ) ) {
			$video_id = explode( '&', $video_id );
			$video_id = isset( $video_id[0] ) ? $video_id[0] : '';
		}
		if ( $video_id ) {
			$embed_url   = $embed_url . $video_id . '?rel=0' . ( $autoplay ? '&autoplay=1&mute=1&loop=1' : '' );
			$embed_video = '<div class="cgkit-iframe-wrap"><iframe src="' . ( $main_video ? $embed_url : $placeholder ) . '" data-src="' . $embed_url . '" itemprop="video" class="pdp-video ' . ( $main_video ? '' : 'swiper-lazy' ) . '" frameborder="0" width="560" height="340" allowfullscreen></iframe></div>';
			$embed_html  = '<div class="cgkit-iframe-wrap"><iframe src="' . $embed_url . '" itemprop="video" class="pdp-video" frameborder="0" width="560" height="340" allowfullscreen></iframe></div>';
		}
	} else {
		$image = '';
		if ( ! $autoplay ) {
			$image = '<img width="' . esc_attr( $full_src[1] ) . '" height="' . esc_attr( $full_src[2] ) . '" src="' . ( $main_video ? esc_url( $full_src[0] ) : $placeholder ) . '" data-src="' . esc_url( $full_src[0] ) . '" data-srcset="' . $full_srcset . '" sizes="(max-width: 360px) 330px, (max-width: 800px) 100vw, 800px" alt="' . $alt_text . '" itemprop="thumbnail" class="pdp-img ' . ( $main_video ? '' : 'swiper-lazy' ) . ' wp-post-image" />';
		}
		$embed_video = '<div class="cgkit-video-wrap">' . $image . '<video src="' . ( $main_video ? $video_url : $placeholder ) . '" data-src="' . $video_url . '" itemprop="video" class="pdp-video ' . ( $main_video ? '' : 'swiper-lazy' ) . '" width="560" height="340" ' . ( $autoplay ? ' autoplay loop muted ' : ' controls style="display: none;" ' ) . '></video><div class="cgkit-play cgkit-video-play ' . ( $autoplay ? '' : 'not-autoplay' ) . '"><svg class="pause" ' . ( $autoplay ? '' : 'style="display:none"' ) . ' viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h48v48H0z" fill="none"></path><path d="M18 32h4V16h-4v16zm6-28C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm0 36c-8.82 0-16-7.18-16-16S15.18 8 24 8s16 7.18 16 16-7.18 16-16 16zm2-8h4V16h-4v16z" fill="#ffffff" class="fill-000000"></path></svg><svg class="play" ' . ( $autoplay ? 'style="display:none"' : '' ) . ' viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h48v48H0z" fill="none"></path><path d="m20 33 12-9-12-9v18zm4-29C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm0 36c-8.82 0-16-7.18-16-16S15.18 8 24 8s16 7.18 16 16-7.18 16-16 16z" fill="#ffffff" class="fill-000000"></path></svg></div></div>';
		$embed_html  = '<div class="cgkit-video-wrap"><video src="' . $video_url . '" itemprop="video" class="pdp-video" width="560" height="340" ' . ( $autoplay ? ' autoplay loop muted ' : ' controls ' ) . '></video><div class="cgkit-play cgkit-video-play ' . ( $autoplay ? '' : 'not-autoplay' ) . '"><svg class="pause" ' . ( $autoplay ? '' : 'style="display:none"' ) . ' viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h48v48H0z" fill="none"></path><path d="M18 32h4V16h-4v16zm6-28C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm0 36c-8.82 0-16-7.18-16-16S15.18 8 24 8s16 7.18 16 16-7.18 16-16 16zm2-8h4V16h-4v16z" fill="#ffffff" class="fill-000000"></path></svg><svg class="play" ' . ( $autoplay ? 'style="display:none"' : '' ) . ' viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h48v48H0z" fill="none"></path><path d="m20 33 12-9-12-9v18zm4-29C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm0 36c-8.82 0-16-7.18-16-16S15.18 8 24 8s16 7.18 16 16-7.18 16-16 16z" fill="#ffffff" class="fill-000000"></path></svg></div></div>';
	}

	return '<li class="swiper-slide swiper-slide-video cgkit-video" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject"><a class="swiper-slide-imglink cgkit-video" href="' . esc_url( $full_src[0] ) . '" itemprop="contentUrl" data-size="' . esc_attr( $full_src[1] ) . 'x' . esc_attr( $full_src[2] ) . '" data-html="' . str_replace( array( '<', '>', '"' ), array( '&lt;', '&gt;', '&quot;' ), $embed_html ) . '">' . $embed_video . '</a></li>'; // phpcs:ignore
}


/**
 * Product gallery layout meta box
 */
function commercegurus_product_gallery_layout_meta_box() {
	add_meta_box( 'commercegurus-product-gallery', esc_html__( 'Product gallery layout', 'commercegurus-commercekit' ), 'commercegurus_product_gallery_layout_meta', 'product', 'side', 'low' );
}
add_action( 'admin_init', 'commercegurus_product_gallery_layout_meta_box' );

/**
 * Product gallery layout meta
 */
function commercegurus_product_gallery_layout_meta() {
	global $post;
	if ( isset( $post->ID ) && $post->ID ) {
		$cgkit_gallery_layout = get_post_meta( $post->ID, 'cgkit_gallery_layout', true );
		?>
<p>
	<label><?php esc_html_e( 'Product gallery layout', 'sayspotwc' ); ?></label>
	<select name="cgkit_gallery_layout" id="cgkit_gallery_layout">
		<option value=""><?php esc_html_e( 'Global default', 'commercegurus-commercekit' ); ?></option>
		<option value="horizontal" <?php echo 'horizontal' === $cgkit_gallery_layout ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Horizontal', 'commercegurus-commercekit' ); ?></option>
		<option value="vertical-left" <?php echo 'vertical-left' === $cgkit_gallery_layout ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Vertical left', 'commercegurus-commercekit' ); ?></option>
		<option value="vertical-right" <?php echo 'vertical-right' === $cgkit_gallery_layout ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Vertical right', 'commercegurus-commercekit' ); ?></option>
		<option value="grid-2-4" <?php echo 'grid-2-4' === $cgkit_gallery_layout ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Grid: 2 cols x 4 rows', 'commercegurus-commercekit' ); ?></option>
		<option value="grid-3-1-2" <?php echo 'grid-3-1-2' === $cgkit_gallery_layout ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Grid: 3 cols, 1 col, 2 cols', 'commercegurus-commercekit' ); ?></option>
		<option value="grid-1-2-2" <?php echo 'grid-1-2-2' === $cgkit_gallery_layout ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Grid: 1 col, 2 cols, 2 cols', 'commercegurus-commercekit' ); ?></option>
	</select>
</p>
		<?php
	}
}

/**
 * Product gallery options
 *
 * @param string $options module options.
 */
function commercekit_get_gallery_options( $options ) {
	global $post;
	$commercekit_pdp = array();

	$commercekit_pdp['pdp_thumbnails'] = 4;
	$commercekit_pdp['pdp_lightbox']   = ( ( isset( $options['pdp_lightbox'] ) && 1 === (int) $options['pdp_lightbox'] ) || ! isset( $options['pdp_lightbox'] ) ) ? 1 : 0;

	$commercekit_pdp['pdp_gallery_layout'] = isset( $options['pdp_gallery_layout'] ) && ! empty( $options['pdp_gallery_layout'] ) ? $options['pdp_gallery_layout'] : commercekit_get_default_settings( 'pdp_gallery_layout' );

	if ( function_exists( 'is_product' ) && is_product() ) {
		if ( isset( $post->ID ) && $post->ID ) {
			$cgkit_gallery_layout = get_post_meta( $post->ID, 'cgkit_gallery_layout', true );
			if ( ! empty( $cgkit_gallery_layout ) ) {
				$commercekit_pdp['pdp_gallery_layout'] = $cgkit_gallery_layout;
			}
		}
	}

	return $commercekit_pdp;
}

<?php
/**
 *
 * Admin PDP Triggers
 *
 * @package CommerceKit
 * @subpackage Shoptimizer
 */

?>
<div id="settings-content" class="postbox content-box">
	<h2><span class="table-heading"><?php esc_html_e( 'Product Gallery', 'commercegurus-commercekit' ); ?></span></h2>

	<div class="inside">

		<div class="explainer">
			<h3><?php esc_html_e( 'Should I enable CommerceKit Product Gallery?', 'commercegurus-commercekit' ); ?></h3>
	<p><?php esc_html_e( 'Right now CommerceKit Product Gallery is disabled by default, but has been tested heavily on live WooCommerce stores. It is compatible with WooCommerce variation images, and with our own Attribute Swatches module. You should evaluate CommerceKit Product Gallery on a test or staging environment first.', 'commercegurus-commercekit' ); ?></p>

	<p><?php esc_html_e( 'To report any issues or if there is a specific extension you would like us to make compatible with CommerceKit Product Gallery', 'commercegurus-commercekit' ); ?> <a href="?page=commercekit&tab=support"><?php esc_html_e( 'let us know', 'commercegurus-commercekit' ); ?></a>. <?php esc_html_e( 'Read our blog post to', 'commercegurus-commercekit' ); ?> <a href="https://www.commercegurus.com/woocommerce-product-gallery-speed/"><?php esc_html_e( 'learn more', 'commercegurus-commercekit' ); ?></a> <?php esc_html_e( 'about the CommerceKit Product Gallery.', 'commercegurus-commercekit' ); ?></p>

		</div>
		<table class="form-table product-gallery" role="presentation">
			<tr> <th scope="row"><?php esc_html_e( 'Enable', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_pdp_gallery" class="toggle-switch"> <input name="commercekit[pdp_gallery]" type="checkbox" id="commercekit_pdp_gallery" value="1" <?php echo isset( $commercekit_options['pdp_gallery'] ) && 1 === (int) $commercekit_options['pdp_gallery'] ? 'checked="checked"' : ''; ?>><span class="toggle-slider"></span></label><label>&nbsp;&nbsp;<?php esc_html_e( 'Product Gallery', 'commercegurus-commercekit' ); ?></label></td> </tr>
			<tr style="display:none;"> <th scope="row"><?php esc_html_e( 'Visible thumbnails', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_pdp_thumbnails"> <input name="commercekit[pdp_thumbnails]" type="number" min="3" max="8" id="commercekit_pdp_thumbnails" value="<?php echo isset( $commercekit_options['pdp_thumbnails'] ) && ! empty( $commercekit_options['pdp_thumbnails'] ) ? esc_attr( (int) $commercekit_options['pdp_thumbnails'] ) : 4; ?>" size="70" style="min-width: 200px;" /></label><br /><small><em><?php esc_html_e( 'Number of gallery thumbnails to display at a time. Minimum 3 and maximum 8.', 'commercegurus-commercekit' ); ?></em></small><div class="input-error" id="pdp_thumbnails_error" style="display: none;"><?php esc_html_e( 'Please enter number between 3 and 8.', 'commercegurus-commercekit' ); ?></div></td> </tr>
			<tr> <th scope="row"><?php esc_html_e( 'Enable lightbox?', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_pdp_lightbox" class="toggle-switch"> <input name="commercekit[pdp_lightbox]" type="checkbox" id="commercekit_pdp_lightbox" value="1" <?php echo ( ( isset( $commercekit_options['pdp_lightbox'] ) && 1 === (int) $commercekit_options['pdp_lightbox'] ) || ! isset( $commercekit_options['pdp_lightbox'] ) ) ? 'checked="checked"' : ''; ?>><span class="toggle-slider"></span></label><label>&nbsp;&nbsp;<?php esc_html_e( 'Display product images in a lightbox when clicked on.', 'commercegurus-commercekit' ); ?></label></td> </tr>
			<tr> <th scope="row"><?php esc_html_e( 'Enable video auto play?', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_pdp_video_autoplay" class="toggle-switch"> <input name="commercekit[pdp_video_autoplay]" type="checkbox" id="commercekit_pdp_video_autoplay" value="1" <?php echo ( ( isset( $commercekit_options['pdp_video_autoplay'] ) && 1 === (int) $commercekit_options['pdp_video_autoplay'] ) || ! isset( $commercekit_options['pdp_video_autoplay'] ) ) ? 'checked="checked"' : ''; ?>><span class="toggle-slider"></span></label><label>&nbsp;&nbsp;<?php esc_html_e( 'Enable video auto play on Product Gallery.', 'commercegurus-commercekit' ); ?></label></td> </tr>
			<tr> <th scope="row" valign="top" style="vertical-align:top;"><?php esc_html_e( 'Gallery layout', 'commercegurus-commercekit' ); ?></th> <td>
				<select name="commercekit[pdp_gallery_layout]" id="commercekit_pdp_gallery_layout">
					<?php $pdp_gallery_layout = isset( $commercekit_options['pdp_gallery_layout'] ) ? $commercekit_options['pdp_gallery_layout'] : commercekit_get_default_settings( 'pdp_gallery_layout' ); ?>
					<option value="horizontal" <?php echo 'horizontal' === $pdp_gallery_layout ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Horizontal', 'commercegurus-commercekit' ); ?></option>
					<option value="vertical-left" <?php echo 'vertical-left' === $pdp_gallery_layout ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Vertical left', 'commercegurus-commercekit' ); ?></option>
					<option value="vertical-right" <?php echo 'vertical-right' === $pdp_gallery_layout ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Vertical right', 'commercegurus-commercekit' ); ?></option>
					<option value="grid-2-4" <?php echo 'grid-2-4' === $pdp_gallery_layout ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Grid: 2 cols x 4 rows', 'commercegurus-commercekit' ); ?></option>
					<option value="grid-3-1-2" <?php echo 'grid-3-1-2' === $pdp_gallery_layout ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Grid: 3 cols, 1 col, 2 cols', 'commercegurus-commercekit' ); ?></option>
					<option value="grid-1-2-2" <?php echo 'grid-1-2-2' === $pdp_gallery_layout ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Grid: 1 col, 2 cols, 2 cols', 'commercegurus-commercekit' ); ?></option>
				</select><br />
				<div id="gallery-layout-preview">
					<div class="layout-preview" id="horizontal-preview" style="display:none;"><div class="grid-full"></div><div class="grid-small"></div></div>
					<div class="layout-preview" id="vertical-left-preview" style="display:none;"><div class="grid-small"></div><div class="grid-full"></div></div>
					<div class="layout-preview" id="vertical-right-preview" style="display:none;"><div class="grid-full"></div><div class="grid-small"></div></div>
					<div class="layout-preview" id="grid-2-4-preview" style="display:none;"><div class="grid-2"></div><div class="grid-2"></div><div class="grid-2"></div><div class="grid-2"></div><div class="grid-2"></div><div class="grid-2"></div><div class="grid-2"></div><div class="grid-2"></div></div>
					<div class="layout-preview" id="grid-3-1-2-preview" style="display:none;"><div class="grid-3"></div><div class="grid-3"></div><div class="grid-3"></div><div class="grid-1"></div><div class="grid-2"></div><div class="grid-2"></div></div>
					<div class="layout-preview" id="grid-1-2-2-preview" style="display:none;"><div class="grid-1"></div><div><div class="grid-2"></div><div class="grid-2"></div><div class="grid-2"></div><div class="grid-2"></div></div></div>
				</div>
				</td></tr>
		</table>
		<input type="hidden" name="tab" value="pdp-gallery" />
		<input type="hidden" name="action" value="commercekit_save_settings" />
	</div>
</div>

<div class="postbox" id="settings-note">
	<h4><?php esc_html_e( 'Product Gallery', 'commercegurus-commercekit' ); ?></h4>
	<p><?php esc_html_e( 'CommerceKit Product Gallery is a lightning fast replacement for the core WooCommerce product gallery that will significantly improve your Google PageSpeed Insights scores on product pages.', 'commercegurus-commercekit' ); ?></p>

	<p><?php esc_html_e( 'It is the first WooCommerce Product Gallery extension built specifically for web performance optimization which is now a key Google ranking signal.', 'commercegurus-commercekit' ); ?></p>

</div>

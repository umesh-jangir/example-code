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
	<h2><span class="table-heading"><?php esc_html_e( 'Product Attributes Gallery', 'commercegurus-commercekit' ); ?></span></h2>

	<div class="inside">

		<div class="explainer">
			<h3><?php esc_html_e( 'What is the CommerceKit Product Attributes Gallery?', 'commercegurus-commercekit' ); ?></h3>
	<p><?php esc_html_e( 'On a WooCommerce product page, if you have a product with two attributes, say "Size" and "Color", you ordinarily need to make a selection from both attributes before the gallery image updates.', 'commercegurus-commercekit' ); ?></p>

	<p><?php esc_html_e( 'Our new attributes gallery allows you to assign images to a single attribute. So when a user selects "Blue", the Blue gallery immediately appears. No need to select a size.', 'commercegurus-commercekit' ); ?></p>

	<p><?php esc_html_e( 'This is far more usable and powerful than the standard WooCommerce implementation and is inline with how best-in-class eCommerce stores display variations.', 'commercegurus-commercekit' ); ?></p>

		</div>

		<table class="form-table product-gallery" role="presentation">
			<tr> <th scope="row"><?php esc_html_e( 'Enable', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_pdp_attributes_gallery" class="toggle-switch"> <input name="commercekit[pdp_attributes_gallery]" type="checkbox" id="commercekit_pdp_attributes_gallery" value="1" <?php echo isset( $commercekit_options['pdp_attributes_gallery'] ) && 1 === (int) $commercekit_options['pdp_attributes_gallery'] ? 'checked="checked"' : ''; ?>><span class="toggle-slider"></span></label><label>&nbsp;&nbsp;<?php esc_html_e( 'Product Attributes Gallery', 'commercegurus-commercekit' ); ?></label></td> </tr>
		</table>
		<input type="hidden" name="tab" value="pdp-attributes-gallery" />
		<input type="hidden" name="action" value="commercekit_save_settings" />
	</div>
</div>

<div class="postbox" id="settings-note">
	<h4><?php esc_html_e( 'Product Attributes Gallery', 'commercegurus-commercekit' ); ?></h4>
	<p><?php esc_html_e( 'Optimize your product page gallery by assigning images on an attribute basis. Once configured, visitors will see an updated gallery image when a single attribute has been selected.', 'commercegurus-commercekit' ); ?></p>

</div>

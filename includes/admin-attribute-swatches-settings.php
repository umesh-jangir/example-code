<?php
/**
 *
 * Admin attribute swatches
 *
 * @package CommerceKit
 * @subpackage Shoptimizer
 */

?>
<?php
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

$query   = new WP_Query( $args );
$p_total = (int) $query->found_posts;
$sql     = 'SELECT COUNT(*) FROM ' . $wpdb->options . ' WHERE option_name LIKE "%transient_cgkit_swatch_loop_form_data%"';
$c_total = (int) $wpdb->get_var( $sql ); // phpcs:ignore
$c_total = (int) min( $c_total, $p_total );

$args2 = array(
	'hook'     => 'commercekit_attribute_swatche_build_cache',
	'per_page' => -1,
	'group'    => 'commercekit',
	'status'   => ActionScheduler_Store::STATUS_PENDING,
);

$action_ids  = as_get_scheduled_actions( $args2, 'ids' );
$building    = 0 < count( $action_ids ) ? 1 : 0;
$enabled     = isset( $commercekit_options['attribute_swatches'] ) ? (int) $commercekit_options['attribute_swatches'] : 0;
$enabled_plp = isset( $commercekit_options['attribute_swatches_plp'] ) ? (int) $commercekit_options['attribute_swatches_plp'] : 0;
$enabled     = 1 === $enabled && 1 === $enabled_plp ? 1 : 0;
$c_percent   = $p_total > 0 ? (int) ( ( $c_total * 100 ) / $p_total ) : 0;
$build_done  = isset( $commercekit_options['commercekit_as_scheduled_done'] ) ? gmdate( 'M j H:i:s', $commercekit_options['commercekit_as_scheduled_done'] ) : '';
?>
<div id="settings-content" class="postbox content-box">
	<h2><span class="table-heading"><?php esc_html_e( 'Attribute Swatches', 'commercegurus-commercekit' ); ?></span></h2>

	<div class="inside">
		<table class="form-table product-gallery" role="presentation">
			<tr> <th scope="row"><?php esc_html_e( 'Enable', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_attribute_swatches" class="toggle-switch"> <input name="commercekit[attribute_swatches]" type="checkbox" id="commercekit_attribute_swatches" value="1" <?php echo isset( $commercekit_options['attribute_swatches'] ) && 1 === (int) $commercekit_options['attribute_swatches'] ? 'checked="checked"' : ''; ?>><span class="toggle-slider"></span></label><label>&nbsp;&nbsp;<?php esc_html_e( 'Attribute Swatches on Product Pages', 'commercegurus-commercekit' ); ?></label></td> </tr>
			<tr id="as-button-style" <?php echo isset( $commercekit_options['attribute_swatches'] ) && 0 === (int) $commercekit_options['attribute_swatches'] ? 'style="display: none;"' : ''; ?>> <th scope="row"><?php esc_html_e( 'Button style', 'commercegurus-commercekit' ); ?></th> <td> <label><input type="radio" value="0" name="commercekit[as_button_style]" <?php echo ( isset( $commercekit_options['as_button_style'] ) && 0 === (int) $commercekit_options['as_button_style'] ) || ! isset( $commercekit_options['as_button_style'] ) ? 'checked="checked"' : ''; ?> />&nbsp;<?php esc_html_e( 'Square', 'commercegurus-commercekit' ); ?></label> <span class="radio-space">&nbsp;</span><label><input type="radio" value="1" name="commercekit[as_button_style]" <?php echo isset( $commercekit_options['as_button_style'] ) && 1 === (int) $commercekit_options['as_button_style'] ? 'checked="checked"' : ''; ?> />&nbsp;<?php esc_html_e( 'Fluid (better for long labels)', 'commercegurus-commercekit' ); ?></label></td> </tr>
		</table>
	</div>

	<div class="inside">
		<div class="explainer" id="cgkit-as-plp-options" style="margin-top: 0">
			<h3><?php esc_html_e( 'Swatches on Product Listing Pages', 'commercegurus-commercekit' ); ?></h3>
			<p><?php esc_html_e( 'Enabling swatches on product listing pages (i.e. the shop, category screens) is an opinionated feature. This means that it does things in a certain way, based upon research from top eCommerce DTC brands. This should only be activated if your catalog contains primarily', 'commercegurus-commercekit' ); ?> <strong><?php esc_html_e( 'variable products', 'commercegurus-commercekit' ); ?></strong> <?php esc_html_e( '- i.e. you sell items which all have colors and sizes, and you wish to display these on the catalog.', 'commercegurus-commercekit' ); ?></p>

			<p><?php esc_html_e( 'A maximum of two attributes will appear on product listings pages. Quick add to cart will only work with two or fewer attributes.', 'commercegurus-commercekit' ); ?></p>

			<p><?php esc_html_e( 'Enabling this will change how your product listing pages will look and behave on desktop and mobile. If this is not suitable for your store leave this option', 'commercegurus-commercekit' ); ?> <strong><?php esc_html_e( 'switched off', 'commercegurus-commercekit' ); ?></strong> <?php esc_html_e( 'or look at an', 'commercegurus-commercekit' ); ?> <a target="_blank" href="https://www.commercegurus.com/woocommerce-variation-swatches/"><?php esc_html_e( 'alternative swatches plugin', 'commercegurus-commercekit' ); ?></a> <?php esc_html_e( 'more suitable to your catalog.', 'commercegurus-commercekit' ); ?></p>

			<table class="form-table product-gallery" role="presentation">
				<tr> <th scope="row" style="padding-left: 0"><?php esc_html_e( 'Enable', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_attribute_swatches_plp" class="toggle-switch"> <input name="commercekit[attribute_swatches_plp]" type="checkbox" id="commercekit_attribute_swatches_plp" value="1" <?php echo isset( $commercekit_options['attribute_swatches_plp'] ) && 1 === (int) $commercekit_options['attribute_swatches_plp'] ? 'checked="checked"' : ''; ?>><span class="toggle-slider"></span></label><label>&nbsp;&nbsp;<?php esc_html_e( 'Attribute Swatches on Listing Pages', 'commercegurus-commercekit' ); ?></label></td> </tr>

				<tr id="as-quick-atc"> <th style="padding-left: 0" scope="row"><?php esc_html_e( 'Quick add to cart', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_as_activate_atc" class="toggle-switch"> <input name="commercekit[as_activate_atc]" type="checkbox" id="commercekit_as_activate_atc" value="1" <?php echo isset( $commercekit_options['as_activate_atc'] ) && 1 === (int) $commercekit_options['as_activate_atc'] ? 'checked="checked"' : ''; ?>><span class="toggle-slider"></span></label><label>&nbsp;&nbsp;<?php esc_html_e( 'Activate Quick add to cart', 'commercegurus-commercekit' ); ?></label></td> </tr>

				<tr id="as-quick-atc-txt"> <th style="padding-left: 0" scope="row"><?php esc_html_e( 'Quick add to cart label', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_as_quickadd_txt"> <input name="commercekit[as_quickadd_txt]" class="pc100" type="text" id="commercekit_as_quickadd_txt" value="<?php echo isset( $commercekit_options['as_quickadd_txt'] ) && ! empty( $commercekit_options['as_quickadd_txt'] ) ? esc_attr( stripslashes_deep( $commercekit_options['as_quickadd_txt'] ) ) : commercekit_get_default_settings( 'as_quickadd_txt' ); // phpcs:ignore ?>" /></label></td>  </tr>

				<tr id="as-more-opt-txt"> <th style="padding-left: 0" scope="row"><?php esc_html_e( 'More options label', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_as_more_opt_txt"> <input name="commercekit[as_more_opt_txt]" class="pc100" type="text" id="commercekit_as_more_opt_txt" value="<?php echo isset( $commercekit_options['as_more_opt_txt'] ) && ! empty( $commercekit_options['as_more_opt_txt'] ) ? esc_attr( stripslashes_deep( $commercekit_options['as_more_opt_txt'] ) ) : commercekit_get_default_settings( 'as_more_opt_txt' ); // phpcs:ignore ?>" /></label></td>  </tr>

		</table>

		<h3 class="ckit_sub-heading"><?php esc_html_e( 'Clear swatches cache', 'commercegurus-commercekit' ); ?></h3>

		<div class="mini-explainer">
		<p><?php esc_html_e( 'For large product catalogs, displaying swatches on your archive/product listing pages can cause performance and speed issues especially when if you manage inventory at the variation/SKU level. To solve this we create a dedicated swatches cache which means your PLP pages load lightning fast even for products with lots of variations.', 'commercegurus-commercekit' ); ?></p>
		<p><?php esc_html_e( 'On larger catalogs this cache can take some time to build when you first activate this feature. We use the WooCommerce Action Scheduler to ensure long running cache building tasks can be run in the background over time.', 'commercegurus-commercekit' ); ?> <a target="_blank" href="https://www.commercegurus.com/docs/commercekit/attribute-swatches/"><?php esc_html_e( 'Learn more', 'commercegurus-commercekit' ); ?></a></p>
		</div>

		<table class="form-table product-gallery" role="presentation">

			<tr> <th style="padding-left: 0" scope="row"><?php esc_html_e( 'Cached variable products', 'commercegurus-commercekit' ); ?></th> <td> <strong id="cgkit-as-cached" style="position: relative; bottom: -1px"><?php echo esc_attr( $c_total ); ?> / <?php echo esc_attr( $p_total ); ?></strong> <input type="button" name="btn-submit2" id="btn-submit2" class="button button-primary" value="<?php esc_html_e( 'Clear all cache / Re-build all cache', 'commercegurus-commercekit' ); ?>" onclick="if(confirm('<?php esc_html_e( 'Are you sure you want to clear all attribute swatches cache and re-build?', 'commercegurus-commercekit' ); ?>')){jQuery('#clear_as_cache').val(1); jQuery('#btn-submit').click();}" style="margin-left: 40px; vertical-align: middle;" disabled="disabled" /> <input type="button" name="btn-submit3" id="btn-submit3" class="button button-secondary" value="<?php esc_html_e( 'Cancel', 'commercegurus-commercekit' ); ?>" onclick="if(confirm('<?php esc_html_e( 'Are you sure you want to cancel all pending attribute swatches cache events?', 'commercegurus-commercekit' ); ?>')){ cancelASBuild(); }" style="visibility: hidden; margin-left: 10px;" /></td> </tr>
			<tr style="display: none"> <th style="padding-left: 0" scope="row"><?php esc_html_e( 'Background cache building', 'commercegurus-commercekit' ); ?></th> <td> <strong id="cgkit-as-cache-status" data-status="<?php echo esc_attr( $building ); ?>" data-status0="<?php esc_html_e( 'Complete', 'commercegurus-commercekit' ); ?>" data-status1="<?php esc_html_e( 'Running', 'commercegurus-commercekit' ); ?>"><?php echo 1 === $building || ( $enabled && 0 === $c_total ) ? esc_html__( 'Running', 'commercegurus-commercekit' ) : esc_html__( 'Complete', 'commercegurus-commercekit' ); ?></strong> </td> </tr>

		</table>

		<!-- When starting -->
		<div class="cache-event-alert" id="event-created">
			<div class="cache-loader">
				<div class="att-loader"></div>Cache event being created...
			</div>
		</div>

		<!-- Switch to this when processing -->
		<div class="cache-event-alert" id="event-processing">
			<div class="cache-processing">
				<div class="cache-bar">
					<div class="cache-progress" id="c_percent" style="width: <?php echo esc_attr( $c_percent ); ?>%"></div>
				</div>
				<div class="cache-value">
					<div class="att-loader"></div>Processing cache event.&nbsp;<span id="c_total"><?php echo esc_attr( $c_total ); ?></span>/<span id="p_total"><?php echo esc_attr( $p_total ); ?></span>&nbsp;completed...
				</div>
			</div>
		</div>

		<!-- Switch to this when completed -->
		<div class="cache-event-alert" id="event-completed">
			<div class="cache-processing">
				<div class="cache-bar">
					<div class="cache-completed" style="width: 100%"></div>
				</div>
				<div class="cache-value completed">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					Cache event completed
				</div>
			</div>

			<h3 class="ckit_sub-heading"><?php esc_html_e( 'Last log message', 'commercegurus-commercekit' ); ?></h3>

			<div class="log-message">The caching process apparently succeeded and is now complete (<span id="build_done"><?php echo esc_attr( $build_done ); ?></span>)</div>
		</div>
		<div class="cache-event-alert" id="event-error">
			<div class="log-message log-failed">We couldn't create the cache event. For assistance, copy and paste the <a href="admin.php?page=wc-status">WooCommerce System Report</a> and include it in a <a href="admin.php?page=commercekit&tab=support">support ticket</a>.</div>
		</div>
		<div class="cache-event-alert" id="event-cancel">
			<div class="log-message log-failed">The caching process has been cancelled.</div>
		</div>

		</div>

		<input type="hidden" name="clear_as_cache" id="clear_as_cache" value="0" />
		<input type="hidden" name="tab" value="attribute-swatches" />
		<input type="hidden" name="action" value="commercekit_save_settings" />
	</div>
</div>

<div class="postbox" id="settings-note">
	<h4><?php esc_html_e( 'Attribute Swatches', 'commercegurus-commercekit' ); ?></h4>
	<p><?php esc_html_e( 'A replacement for the core WooCommerce product variation dropdowns with color / image / button display options that will significantly improve user experience on product and listing pages.', 'commercegurus-commercekit' ); ?></p>

</div>
<script>
jQuery(document).ready(function($){
	updateASBuildStatus();
});
function updateASBuildStatus(){
	var btnSub = jQuery('#btn-submit2');
	var cnlsSub = jQuery('#btn-submit3');
	var cacheSts = jQuery('#cgkit-as-cached');
	var buildSts = jQuery('#cgkit-as-cache-status');
	var formData = new FormData();
	formData.append('action', 'commercekit_get_as_build_status');
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		dataType: 'json',
		data: formData,
		processData: false,
		contentType: false,
		success: function( json ) {
			cacheSts.html(json.message);
			if( json.status == 1 ){
				btnSub.attr('disabled', 'disabled');
				if( json.alert_id == 'event-processing' ){
					cnlsSub.css('visibility', 'visible');
				}
			} else {
				btnSub.removeAttr('disabled');
				cnlsSub.css('visibility', 'hidden');
			}
			buildSts.html(buildSts.data('status'+json.status));
			jQuery('#cgkit-as-plp-options .cache-event-alert').hide();
			jQuery('#c_total').html(json.c_total);
			jQuery('#p_total').html(json.p_total);
			jQuery('#c_percent').css('width', json.c_percent+'%');
			jQuery('#build_done').html(json.build_done);
			if( json.alert_id != '' ){
				jQuery('#'+json.alert_id).show();
			}
			if( json.alert_id != 'event-completed' ){
				setTimeout(function(){
					updateASBuildStatus();
				}, 5000);
			}
		}
	});
}
function cancelASBuild(){
	var btnSub = jQuery('#btn-submit3');
	var formData = new FormData();
	formData.append('action', 'commercekit_get_as_build_cancel');
	btnSub.attr('disabled', 'disabled');
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		dataType: 'json',
		data: formData,
		processData: false,
		contentType: false,
		success: function( json ) {
			btnSub.removeAttr('disabled');
			btnSub.css('visibility', 'hidden');
		}
	});
}
</script>

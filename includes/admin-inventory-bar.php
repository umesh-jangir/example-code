<?php
/**
 *
 * Admin Inventory Bar
 *
 * @package CommerceKit
 * @subpackage Shoptimizer
 */

?>
<div id="settings-content" class="postbox content-box">
	<h2><span class="table-heading"><?php esc_html_e( 'Stock Meter', 'commercegurus-commercekit' ); ?></span></h2>

	<div class="inside">
		<table class="form-table" role="presentation">
			<tr> <th scope="row"><?php esc_html_e( 'Enable stock meter', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_inventory_display" class="toggle-switch"> <input name="commercekit[inventory_display]" type="checkbox" id="commercekit_inventory_display" value="1" <?php echo isset( $commercekit_options['inventory_display'] ) && 1 === (int) $commercekit_options['inventory_display'] ? 'checked="checked"' : ''; ?>><span class="toggle-slider"></span></label><label>&nbsp;&nbsp;<?php esc_html_e( 'Show stock meter on the single product page', 'commercegurus-commercekit' ); ?></label></td> </tr>
			<?php /* translators: %s: stock counter. */ ?>
			<tr> <th scope="row"><?php esc_html_e( 'Low stock text', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_inventory_text"> <input name="commercekit[inventory_text]" type="text" class="pc100" id="commercekit_inventory_text" value="<?php echo isset( $commercekit_options['inventory_text'] ) && ! empty( $commercekit_options['inventory_text'] ) ? esc_attr( stripslashes_deep( $commercekit_options['inventory_text'] ) ) : commercekit_get_default_settings( 'inventory_text' ); // phpcs:ignore ?>" /></label><br /><small><em>
			<?php /* translators: %s: stock counter. */ ?>
			<?php esc_html_e( 'Add &ldquo;%s&rdquo; to replace the stock number, ', 'commercegurus-commercekit' ); ?>
			<?php /* translators: %s: stock counter. */ ?>
			<?php esc_html_e( 'e.g. Only %s items left in stock!', 'commercegurus-commercekit' ); ?>
			</em></small></td> </tr>
			<?php /* translators: %s: stock counter. */ ?>
			<tr> <th scope="row"><?php esc_html_e( 'Regular stock text', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_inventory_text_31"> <input name="commercekit[inventory_text_31]" type="text" class="pc100" id="commercekit_inventory_text_31" value="<?php echo isset( $commercekit_options['inventory_text_31'] ) && ! empty( $commercekit_options['inventory_text_31'] ) ? esc_attr( stripslashes_deep( $commercekit_options['inventory_text_31'] ) ) : commercekit_get_default_settings( 'inventory_text_31' ); // phpcs:ignore ?>" /></label><br /><small><em>
			<?php /* translators: %s: stock counter. */ ?>
			<?php esc_html_e( 'Add &ldquo;%s&rdquo; to replace the stock number, ', 'commercegurus-commercekit' ); ?>
			<?php /* translators: %s: stock counter. */ ?>
			<?php esc_html_e( 'e.g. Less than %s items left!', 'commercegurus-commercekit' ); ?></em></small></td> </tr>
			<tr> <th scope="row"><?php esc_html_e( 'When stock > 100', 'commercegurus-commercekit' ); ?></th> <td> <label for="commercekit_inventory_text_100"> <input name="commercekit[inventory_text_100]" type="text" class="pc100" id="commercekit_inventory_text_100" value="<?php echo isset( $commercekit_options['inventory_text_100'] ) && ! empty( $commercekit_options['inventory_text_100'] ) ? esc_attr( stripslashes_deep( $commercekit_options['inventory_text_100'] ) ) : commercekit_get_default_settings( 'inventory_text_100' ); // phpcs:ignore ?>" /></label></td> </tr>
			<tr> 
				<th><?php esc_html_e( 'Conditions', 'commercegurus-commercekit' ); ?>: </th>
				<td> 
					<?php
					$ctype = 'all';
					if ( isset( $commercekit_options['inventory_condition'] ) && in_array( $commercekit_options['inventory_condition'], array( 'products', 'non-products' ), true ) ) {
						$ctype = 'products';
					}
					if ( isset( $commercekit_options['inventory_condition'] ) && in_array( $commercekit_options['inventory_condition'], array( 'categories', 'non-categories' ), true ) ) {
						$ctype = 'categories';
					}
					?>
					<select name="commercekit[inventory_condition]" class="conditions" style="max-width: 100%;">
						<option value="all" <?php echo isset( $commercekit_options['inventory_condition'] ) && 'all' === $commercekit_options['inventory_condition'] ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'All products', 'commercegurus-commercekit' ); ?></option>
						<option value="products" <?php echo isset( $commercekit_options['inventory_condition'] ) && 'products' === $commercekit_options['inventory_condition'] ? 'selected="selected"' : ''; ?> ><?php esc_html_e( 'Specific products', 'commercegurus-commercekit' ); ?></option>
						<option value="non-products" <?php echo isset( $commercekit_options['inventory_condition'] ) && 'non-products' === $commercekit_options['inventory_condition'] ? 'selected="selected"' : ''; ?> ><?php esc_html_e( 'All products apart from', 'commercegurus-commercekit' ); ?></option>
						<option value="categories" <?php echo isset( $commercekit_options['inventory_condition'] ) && 'categories' === $commercekit_options['inventory_condition'] ? 'selected="selected"' : ''; ?> ><?php esc_html_e( 'Specific categories', 'commercegurus-commercekit' ); ?></option>
						<option value="non-categories" <?php echo isset( $commercekit_options['inventory_condition'] ) && 'non-categories' === $commercekit_options['inventory_condition'] ? 'selected="selected"' : ''; ?> ><?php esc_html_e( 'All categories apart from', 'commercegurus-commercekit' ); ?></option>
					</select>
				</td> 
			</tr>
			<tr class="product-ids" <?php echo 'all' === $ctype ? 'style="display:none;"' : ''; ?>>
				<th class="options">
				<?php
				echo 'all' === $ctype || 'products' === $ctype ? esc_attr( 'Specific products:' ) : '';
				echo 'categories' === $ctype ? esc_html__( 'Specific categories:', 'commercegurus-commercekit' ) : '';
				?>
				</th>
				<td> <label><select name="commercekit_inventory_pids[]" class="select2" data-type="<?php echo esc_attr( $ctype ); ?>" data-tab="inventory-bar" data-mode="full" multiple="multiple" style="width:100%;">
				<?php
				$pids = isset( $commercekit_options['inventory_pids'] ) ? explode( ',', $commercekit_options['inventory_pids'] ) : array();
				if ( 'all' !== $ctype && count( $pids ) ) {
					foreach ( $pids as $pid ) {
						if ( empty( $pid ) ) {
							continue;
						}
						if ( 'products' === $ctype ) {
							echo '<option value="' . esc_attr( $pid ) . '" selected="selected">#' . esc_attr( $pid ) . ' - ' . esc_html( commercekit_limit_title( get_the_title( $pid ) ) ) . '</option>';
						}
						if ( 'categories' === $ctype ) {
							$nterm       = get_term_by( 'id', $pid, 'product_cat' );
							$nterm->name = isset( $nterm->name ) ? $nterm->name : '';
							echo '<option value="' . esc_attr( $pid ) . '" selected="selected">#' . esc_attr( $pid ) . ' - ' . esc_html( $nterm->name ) . '</option>';
						}
					}
				}
				?>
				</select><input type="hidden" name="commercekit[inventory_pids]" class="select3 text" value="<?php echo esc_html( implode( ',', $pids ) ); ?>" /></label></td> 
			</tr>
		</table>
		<input type="hidden" name="tab" value="inventory-bar" />
		<input type="hidden" name="action" value="commercekit_save_settings" />
	</div>
</div>

<div class="postbox" id="settings-note">
	<h4><?php esc_html_e( 'Stock Meter', 'commercegurus-commercekit' ); ?></h4>
	<p><?php esc_html_e( 'This feature allows you to show a stock meter counter on the single product page. It&lsquo;s a more visually effective way to alert customers when the stock level is low.', 'commercegurus-commercekit' ); ?></p>
</div>

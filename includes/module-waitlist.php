<?php
/**
 *
 * Waitlist module
 *
 * @package CommerceKit
 * @subpackage Shoptimizer
 */

/**
 * Ajax save waitlist
 */
function commercekit_ajax_save_waitlist() {
	global $wpdb;
	$commercekit_options = get_option( 'commercekit', array() );

	$ajax            = array();
	$ajax['status']  = 0;
	$ajax['message'] = esc_html__( 'Error on submitting for waiting list.', 'commercegurus-commercekit' );

	$table  = $wpdb->prefix . 'commercekit_waitlist';
	$nonce  = wp_verify_nonce( 'commercekit_nonce', 'commercekit_settings' );
	$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$pid    = isset( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : 0;
	$data   = array(
		'email'      => $email,
		'product_id' => $pid,
		'created'    => time(),
	);
	$format = array( '%s', '%d', '%d' );
	if ( $email && $pid ) {
		$found = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'commercekit_waitlist WHERE product_id = %d AND email = %s', $pid, $email ) ); // db call ok; no-cache ok.
		if ( ! $found ) {
			$wpdb->insert( $table, $data, $format ); // db call ok; no-cache ok.
		}
		$ajax['status']  = 1;
		$ajax['message'] = isset( $commercekit_options['wtl_success_text'] ) && ! empty( $commercekit_options['wtl_success_text'] ) ? commercekit_get_multilingual_string( stripslashes_deep( $commercekit_options['wtl_success_text'] ) ) : commercekit_get_default_settings( 'wtl_success_text' );

		$product = wc_get_product( $pid );
		if ( $product ) {
			$finds   = array( '{site_name}', '{site_url}', '{product_title}', '{product_sku}', '{product_link}', '{customer_email}' );
			$replace = array( get_option( 'blogname' ), home_url( '/' ), $product->get_title(), $product->get_sku(), $product->get_permalink(), $email );

			commercekit_remove_wc_email_name_filters();

			$enabled_admin_mail = ( ! isset( $commercekit_options['waitlist_admin_mail'] ) || 1 === (int) $commercekit_options['waitlist_admin_mail'] ) ? true : false;
			if ( $enabled_admin_mail ) {
				$to_mail       = isset( $commercekit_options['wtl_recipient'] ) && ! empty( $commercekit_options['wtl_recipient'] ) ? stripslashes_deep( $commercekit_options['wtl_recipient'] ) : commercekit_get_default_settings( 'wtl_recipient' );
				$from_mail     = isset( $commercekit_options['wtl_from_email'] ) && ! empty( $commercekit_options['wtl_from_email'] ) ? stripslashes_deep( $commercekit_options['wtl_from_email'] ) : commercekit_get_default_settings( 'wtl_from_email' );
				$email_headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $from_mail, 'Reply-To: ' . $from_mail );
				$email_subject = isset( $commercekit_options['wtl_admin_subject'] ) && ! empty( $commercekit_options['wtl_admin_subject'] ) ? stripslashes_deep( $commercekit_options['wtl_admin_subject'] ) : commercekit_get_default_settings( 'wtl_admin_subject' );
				$email_body    = isset( $commercekit_options['wtl_admin_content'] ) && ! empty( $commercekit_options['wtl_admin_content'] ) ? stripslashes_deep( $commercekit_options['wtl_admin_content'] ) : commercekit_get_default_settings( 'wtl_admin_content' );
				$email_subject = str_replace( $finds, $replace, $email_subject );
				$email_body    = str_replace( $finds, $replace, $email_body );
				$email_body    = html_entity_decode( $email_body );
				$email_body    = str_replace( "\r\n", '<br />', $email_body );

				$success = wp_mail( $to_mail, $email_subject, $email_body, $email_headers );
			}

			$enabled_user_mail = ( ! isset( $commercekit_options['waitlist_user_mail'] ) || 1 === (int) $commercekit_options['waitlist_user_mail'] ) ? true : false;
			if ( $enabled_user_mail ) {
				$to_mail       = $email;
				$email         = get_option( 'admin_email' );
				$from_mail     = isset( $commercekit_options['wtl_from_email'] ) && ! empty( $commercekit_options['wtl_from_email'] ) ? stripslashes_deep( $commercekit_options['wtl_from_email'] ) : commercekit_get_default_settings( 'wtl_from_email' );
				$email_headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $from_mail, 'Reply-To: ' . $from_mail );
				$email_subject = isset( $commercekit_options['wtl_user_subject'] ) && ! empty( $commercekit_options['wtl_user_subject'] ) ? stripslashes_deep( $commercekit_options['wtl_user_subject'] ) : commercekit_get_default_settings( 'wtl_user_subject' );
				$email_body    = isset( $commercekit_options['wtl_user_content'] ) && ! empty( $commercekit_options['wtl_user_content'] ) ? stripslashes_deep( $commercekit_options['wtl_user_content'] ) : commercekit_get_default_settings( 'wtl_user_content' );
				$email_subject = str_replace( $finds, $replace, $email_subject );
				$email_body    = str_replace( $finds, $replace, $email_body );
				$email_body    = html_entity_decode( $email_body );
				$email_body    = str_replace( "\r\n", '<br />', $email_body );

				$success = wp_mail( $to_mail, $email_subject, $email_body, $email_headers );
			}
		}
	}

	wp_send_json( $ajax );
}

add_action( 'wp_ajax_commercekit_save_waitlist', 'commercekit_ajax_save_waitlist' );
add_action( 'wp_ajax_nopriv_commercekit_save_waitlist', 'commercekit_ajax_save_waitlist' );

/**
 * Waitlist form
 */
function commercekit_waitlist_form() {
	global $post;
	$commercekit_options = get_option( 'commercekit', array() );
	$enable_waitlist     = isset( $commercekit_options['waitlist'] ) && 1 === (int) $commercekit_options['waitlist'] ? 1 : 0;
	if ( ! $enable_waitlist ) {
		return;
	}
	if ( 'product' === get_post_type( $post->ID ) && is_product() ) {
		$product = wc_get_product( $post->ID );
		if ( ! $product ) {
			return;
		}
		if ( $product->is_type( 'composite' ) ) {
			return;
		}
		add_filter( 'woocommerce_get_stock_html', 'commercekit_waitlist_output_form', 30, 2 );
	}
}
add_action( 'woocommerce_before_single_product', 'commercekit_waitlist_form' );
add_action( 'woocommerce_after_single_product', 'commercekit_waitlist_output_form_script' );

/**
 * Waitlist ajax form
 *
 * @param string $html of output.
 * @param object $product of output.
 */
function commercekit_waitlist_ajax_form( $html, $product ) {
	global $wp_query;
	$action = $wp_query->get( 'wc-ajax' );
	if ( 'get_variation' !== $action ) {
		return $html;
	}
	$commercekit_options = get_option( 'commercekit', array() );
	$enable_waitlist     = isset( $commercekit_options['waitlist'] ) && 1 === (int) $commercekit_options['waitlist'] ? 1 : 0;
	if ( ! $enable_waitlist ) {
		return $html;
	}
	if ( ! $product ) {
		return $html;
	}
	if ( $product->is_type( 'variation' ) ) {
		return commercekit_waitlist_output_form( $html, $product );
	}
}
add_filter( 'woocommerce_get_stock_html', 'commercekit_waitlist_ajax_form', 30, 2 );

/**
 * Waitlist output form
 *
 * @param string $html of output.
 * @param object $product of output.
 */
function commercekit_waitlist_output_form( $html, $product ) {
	global $can_show_waitlist_form;
	$commercekit_options = get_option( 'commercekit', array() );

	$intro   = isset( $commercekit_options['wtl_intro'] ) && ! empty( $commercekit_options['wtl_intro'] ) ? commercekit_get_multilingual_string( stripslashes_deep( $commercekit_options['wtl_intro'] ) ) : commercekit_get_default_settings( 'wtl_intro' );
	$pholder = isset( $commercekit_options['wtl_email_text'] ) && ! empty( $commercekit_options['wtl_email_text'] ) ? commercekit_get_multilingual_string( stripslashes_deep( $commercekit_options['wtl_email_text'] ) ) : commercekit_get_default_settings( 'wtl_email_text' );
	$blabel  = isset( $commercekit_options['wtl_button_text'] ) && ! empty( $commercekit_options['wtl_button_text'] ) ? commercekit_get_multilingual_string( stripslashes_deep( $commercekit_options['wtl_button_text'] ) ) : commercekit_get_default_settings( 'wtl_button_text' );
	$alabel  = isset( $commercekit_options['wtl_consent_text'] ) && ! empty( $commercekit_options['wtl_consent_text'] ) ? commercekit_get_multilingual_string( stripslashes_deep( $commercekit_options['wtl_consent_text'] ) ) : commercekit_get_default_settings( 'wtl_consent_text' );
	$rmlabel = isset( $commercekit_options['wtl_readmore_text'] ) && ! empty( $commercekit_options['wtl_readmore_text'] ) ? commercekit_get_multilingual_string( stripslashes_deep( $commercekit_options['wtl_readmore_text'] ) ) : commercekit_get_default_settings( 'wtl_readmore_text' );

	if ( ( $product->managing_stock() && 0 === (int) $product->get_stock_quantity() && 'no' === $product->get_backorders() ) || 'outofstock' === $product->get_stock_status() ) {
		$can_show_waitlist_form = true;

		$whtml = '
<input type="button" id="ckwtl-button2" name="ckwtl_button2" value="' . $rmlabel . '" onclick="popupCKITWaitlist();" />
<div id="commercekit-waitlist-popup" style="display: none;">
	<div id="commercekit-waitlist-wrap">
		<div id="commercekit-waitlist-close" onclick="closeCKITWaitlistPopup();"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
</svg></div>
		<div class="commercekit-waitlist">
			<p>' . $intro . '</p>
			<input type="email" id="ckwtl-email" name="ckwtl_email" placeholder="' . $pholder . '" />
			<label><input type="checkbox" id="ckwtl-consent" name="ckwtl_consent" value="1" />&nbsp;&nbsp;' . $alabel . '</label>
			<input type="button" id="ckwtl-button" name="ckwtl_button" value="' . $blabel . '" disabled="disabled" onclick="submitCKITWaitlist();" />
			<input type="hidden" id="ckwtl-pid" name="ckwtl_pid" value="' . $product->get_id() . '" />
		</div>
	</div>
</div>';

		$html .= $whtml;
	}

	return $html;
}

/**
 * Waitlist output form script
 */
function commercekit_waitlist_output_form_script() {
	global $can_show_waitlist_form, $product;
	$commercekit_options = get_option( 'commercekit', array() );
	$attribute_names     = array();
	$wtl_readmore_text   = isset( $commercekit_options['wtl_readmore_text'] ) && ! empty( $commercekit_options['wtl_readmore_text'] ) ? commercekit_get_multilingual_string( stripslashes_deep( $commercekit_options['wtl_readmore_text'] ) ) : commercekit_get_default_settings( 'wtl_readmore_text' );
	if ( isset( $can_show_waitlist_form ) && true === $can_show_waitlist_form ) {
		if ( $product && method_exists( $product, 'is_type' ) && $product->is_type( 'variable' ) ) {
			foreach ( $product->get_attributes() as $attribute ) {
				if ( $attribute->get_variation() ) {
					$attribute_names[] = sanitize_title( $attribute->get_name() );
				}
			}
		}
		?>
<style>
.commercekit-waitlist { margin: 30px; padding: 25px; background-color: #fff; border: 1px solid #eee; box-shadow: 0 3px 15px -5px rgba(0, 0, 0, 0.08); }
.commercekit-waitlist p { font-weight: 600; margin-bottom: 10px; width: 100%; font-size: 16px; }
.commercekit-waitlist p.error { color: #F00; margin-bottom: 0; }
.commercekit-waitlist p.success { color: #009245; margin-bottom: 0; }
.commercekit-waitlist #ckwtl-email { width: 100%; background: #fff; margin-bottom: 10px; }
.commercekit-waitlist #ckwtl-email.error { border: 1px solid #F00; }
.commercekit-waitlist label { width: 100%; margin-bottom: 10px; font-size: 14px; display: block; }
.commercekit-waitlist label.error { color: #F00; }
.commercekit-waitlist label input { transform: scale(1.1); top: 2px; }
.commercekit-waitlist #ckwtl-button { width: 100%; margin-top: 5px; text-align: center; border-radius: 3px; transition: 0.2s all; }
.commercekit-waitlist #ckwtl-button { width: 100%; text-align: center; }
#ckwtl-button2 { min-width: 200px; width: 100%; margin: 15px 0; padding: 12px 0; text-decoration: none; }
#commercekit-waitlist-popup { position: fixed; width: 100%; height: 100%; max-width: 100%; max-height: 100%; background-color: rgba(0,0,0,0.4); z-index: 9999999; top: 0; left: 0; bottom: 0; right: 0; align-items: center; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),0 10px 10px -5px rgba(0, 0, 0, 0.04); }
#commercekit-waitlist-popup .commercekit-waitlist { margin: 5px; border: none; box-shadow: none; }
#commercekit-waitlist-wrap { background-color: #fff; color: #000; overflow: hidden; position: relative; margin: 75px auto; width: 500px; height: auto; max-width: 100%; border-radius: 6px;}
#commercekit-waitlist-wrap .commercekit-waitlist p { font-size: 20px; }
#commercekit-waitlist-close { position: absolute; width: 25px; height: 25px; cursor: pointer; right: 5px; top: 10px; }
#commercekit-waitlist-close svg { width: 22px; height: 22px; }
body.stop-scroll { margin: 0; height: 100%; overflow: hidden; }
form.variations_form #ckwtl-button2 { display: none; }
form.variations_form #ckwtl-button3 { display: none; position: relative; background: #43454b; border-color: #43454b; color: #fff; font-size: 18px; font-weight: 600; letter-spacing: 0px; text-transform: none; float: left; width: calc(100% - 95px); height: 58px; margin-left: 40px; padding-top: 0; padding-bottom: 0; border-radius: 4px; outline: 0; line-height: 58px; text-align: center; transition: all .2s;}
form.variations_form .variations label { width: 100%; }
form.variations_form label .ckwtl-os-label { display: none; position: relative; cursor: pointer; font-weight: normal; margin: 2px 0 10px 0;}
form.variations_form label .ckwtl-os-label-text { text-decoration: underline; text-transform: none; letter-spacing: 0px; font-weight: bold; }
form.variations_form label .ckwtl-os-label-tip { display: none; position: absolute; width: 250px; background: white; padding: 10px; left: 0px; bottom: 25px; border: 1px solid #ccc; text-transform: none; letter-spacing: 0; line-height: 1.38; transition: all 1s; z-index: 1; box-shadow: 0 5px 5px -5px rgb(0 0 0 / 10%), 0 5px 10px -5px rgb(0 0 0 / 4%);}
form.variations_form label .ckwtl-os-label:hover .ckwtl-os-label-tip { display: block;}
</style>
<script>
function validateCKITWaitlistForm(){
	var email = document.querySelector('#ckwtl-email');
	var consent = document.querySelector('#ckwtl-consent');
	var button = document.querySelector('#ckwtl-button');
	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var error = false;
	if( !regex.test(email.value) ){
		email.classList.add('error');
		error = true;
	} else {
		email.classList.remove('error');
	}
	if( !consent.checked ){
		consent.parentNode.classList.add('error');
		error = true;
	} else {
		consent.parentNode.classList.remove('error');
	}
	if( !error ){
		button.removeAttribute('disabled');
	} else {
		button.setAttribute('disabled', 'disabled');
	}
}
function submitCKITWaitlist(){
	var pid = document.querySelector('#ckwtl-pid').value;
	var email = document.querySelector('#ckwtl-email').value;
	var button = document.querySelector('#ckwtl-button');
	var container = document.querySelector('.commercekit-waitlist');
	button.setAttribute('disabled', 'disabled');
	var formData = new FormData();
	formData.append('product_id', pid);
	formData.append('email', email);
	fetch( commercekit_ajs.ajax_url + '?action=commercekit_save_waitlist', {
		method: 'POST',
		body: formData,
	}).then(response => response.json()).then( json => {
		if( json.status == 1 ){
			container.innerHTML = '<p class="success">'+json.message+'</p>';
		} else {
			container.innerHTML = '<p class="error">'+json.message+'</p>';
		}
	});
}
function popupCKITWaitlist(){
	var popup = document.querySelector('#commercekit-waitlist-popup');
	if( popup ){
		popup.style.display = 'flex';
		document.querySelector('body').classList.add('stop-scroll');
	}
}
function closeCKITWaitlistPopup(){
	var popup = document.querySelector('#commercekit-waitlist-popup');
	if( popup ){
		popup.style.display = 'none';
		document.querySelector('body').classList.remove('stop-scroll');
	}
}
var wtl_attribute_names = <?php echo wp_json_encode( array_unique( $attribute_names ) ); ?>;
function preparePopupCKITWaitlist(input){
	var form = input.closest('form.variations_form');
	if( form ){
		if( form.classList.contains('cgkit-swatch-form') ){
			return true;
		}
		var btn3 = form.querySelector('#ckwtl-button3');
		var cbtn = form.querySelector('.single_add_to_cart_button');
		if( !btn3 && cbtn ){
			var btn3 = document.createElement('button');
			btn3.setAttribute('type', 'button');
			btn3.setAttribute('name', 'ckwtl-button3');
			btn3.setAttribute('id', 'ckwtl-button3');
			btn3.setAttribute('onclick', 'popupCKITWaitlist();');
			btn3.innerHTML = '<?php echo esc_attr( $wtl_readmore_text ); ?>';
			cbtn.parentNode.insertBefore(btn3, cbtn);
		}
		var ostock = form.querySelector('.stock.out-of-stock');
		var display_label = false;
		if( btn3 && cbtn && ostock && input.value != '' ){
			cbtn.style.display = 'none';
			btn3.style.display = 'block';
			display_label = true;
		} else {
			cbtn.style.display = 'block';
			btn3.style.display = 'none';
		}
		for( i = 0; i < wtl_attribute_names.length; i++ ){
			updateLabelsCKITWaitlist(form, wtl_attribute_names[i], display_label)
		}
		var divsum = form.closest('div.summary');
		if( divsum ){
			var sumprt = divsum.parentNode;
			var new_popup = sumprt.querySelector('.woocommerce-variation-availability #commercekit-waitlist-popup');
			if( new_popup ){
				var wrap = sumprt.querySelector('#commercekit-waitlist-popup-wrap');
				if( wrap ){
					wrap.innerHTML = '';
					wrap.appendChild(new_popup);
				} else {
					var wrap = document.createElement('div');
					wrap.setAttribute('id', 'commercekit-waitlist-popup-wrap');
					wrap.appendChild(new_popup);
					sumprt.appendChild(wrap);
				}
			}
		}
	}
}
function updateLabelsCKITWaitlist(form, attribute, display_label){
	var label = form.querySelector('label[for="'+attribute+'"] .ckwtl-os-label');
	if( !label ){
		var label2 = form.querySelector('label[for="'+attribute+'"]');
		if( label2 ) {
			var label = document.createElement('span');
			label.setAttribute('class', 'ckwtl-os-label');
			label.innerHTML = '<span class="ckwtl-os-label-text"></span><span class="ckwtl-os-label-tip"><?php esc_html_e( 'Select your desired options and click on the "Get notified" button to be alerted when new stock arrives.', 'commercegurus-commercekit' ); ?></span>';
			label2.appendChild(label);
		}
	}
	var sel = form.querySelector('[name="attribute_'+attribute+'"]');
	var sel_text = '';
	if( sel && sel.options ){
		sel_text = sel.options[sel.selectedIndex].text;
		if( sel_text != '' ){
			sel_text = sel_text + ' <?php esc_html_e( 'sold out?', 'commercegurus-commercekit' ); ?>';
		}
	}
	if( label ){
		var label_text = label.querySelector('.ckwtl-os-label-text');
		if( label_text ){
			label_text.innerHTML = sel_text;
		}
		if( display_label ){
			label.style.display = 'block';
		} else {
			label.style.display = 'none';
		}
	}
}
document.addEventListener('change', function(e){
	if( e.target && ( e.target.id == 'ckwtl-email' || e.target.id == 'ckwtl-consent' ) ){
		validateCKITWaitlistForm();
	}
});
document.addEventListener('keyup', function(e){
	if( e.target && ( e.target.id == 'ckwtl-email' || e.target.id == 'ckwtl-consent' ) ){
		validateCKITWaitlistForm();
	}
});
var var_input = document.querySelector('input.variation_id');
if( var_input ) {
	observer = new MutationObserver((changes) => {
		changes.forEach(change => {
			if(change.attributeName.includes('value')){
				preparePopupCKITWaitlist(var_input);
			}
		});
	});
	observer.observe(var_input, {attributes : true});
}
</script>
		<?php
	}
}

/**
 * Waitlist automail on stock status
 *
 * @param string $product_id Product ID.
 * @param string $stockstatus Stock status.
 * @param string $product Product Object.
 */
function commercekit_waitlist_automail_on_stock_status( $product_id, $stockstatus, $product ) {
	global $wpdb;
	if ( 'instock' === $stockstatus ) {
		$commercekit_options = get_option( 'commercekit', array() );
		$enabled_auto_mail   = ( ! isset( $commercekit_options['waitlist_auto_mail'] ) || 1 === (int) $commercekit_options['waitlist_auto_mail'] ) ? true : false;
		if ( $enabled_auto_mail ) {
			$limit = 99999;
			if ( 0 < (int) $product->get_stock_quantity() ) {
				$limit = (int) $product->get_stock_quantity();
			}
			$rows    = $wpdb->get_results( $wpdb->prepare( 'SELECT DISTINCT email, product_id FROM ' . $wpdb->prefix . 'commercekit_waitlist WHERE product_id = %d AND mail_sent = %d ORDER BY created ASC LIMIT %d, %d', $product_id, 0, 0, $limit ), ARRAY_A ); // db call ok; no-cache ok.
			$finds   = array( '{site_name}', '{site_url}', '{product_title}', '{product_sku}', '{product_link}' );
			$replace = array( get_option( 'blogname' ), home_url( '/' ), $product->get_title(), $product->get_sku(), $product->get_permalink() );

			commercekit_remove_wc_email_name_filters();

			$email         = get_option( 'admin_email' );
			$from_mail     = isset( $commercekit_options['wtl_from_email'] ) && ! empty( $commercekit_options['wtl_from_email'] ) ? stripslashes_deep( $commercekit_options['wtl_from_email'] ) : commercekit_get_default_settings( 'wtl_from_email' );
			$email_headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $from_mail, 'Reply-To: ' . $from_mail );
			$email_subject = isset( $commercekit_options['wtl_auto_subject'] ) && ! empty( $commercekit_options['wtl_auto_subject'] ) ? stripslashes_deep( $commercekit_options['wtl_auto_subject'] ) : commercekit_get_default_settings( 'wtl_auto_subject' );
			$email_body    = isset( $commercekit_options['wtl_auto_content'] ) && ! empty( $commercekit_options['wtl_auto_content'] ) ? stripslashes_deep( $commercekit_options['wtl_auto_content'] ) : commercekit_get_default_settings( 'wtl_auto_content' );
			$email_subject = str_replace( $finds, $replace, $email_subject );
			$email_body    = str_replace( $finds, $replace, $email_body );
			$email_body    = html_entity_decode( $email_body );
			$email_body    = str_replace( "\r\n", '<br />', $email_body );

			if ( is_array( $rows ) && count( $rows ) ) {
				foreach ( $rows as $row ) {
					$email_subject2 = str_replace( '{customer_email}', $row['email'], $email_subject );
					$email_body2    = str_replace( '{customer_email}', $row['email'], $email_body );

					$success = wp_mail( $row['email'], $email_subject2, $email_body2, $email_headers );
					$table   = $wpdb->prefix . 'commercekit_waitlist';
					$data    = array(
						'mail_sent' => 1,
					);
					$where   = array(
						'email'      => $row['email'],
						'product_id' => $row['product_id'],
					);

					$data_format  = array( '%d' );
					$where_format = array( '%s', '%d' );
					$wpdb->update( $table, $data, $where, $data_format, $where_format ); // db call ok; no-cache ok.
				}
			}
		}
	}
}

add_action( 'woocommerce_product_set_stock_status', 'commercekit_waitlist_automail_on_stock_status', 99, 3 );
add_action( 'woocommerce_variation_set_stock_status', 'commercekit_waitlist_automail_on_stock_status', 99, 3 );

/**
 * Email from name
 *
 * @param  string $from_name from name.
 * @return string $from_name from name.
 */
function commercekit_email_from_name( $from_name ) {
	$options   = get_option( 'commercekit', array() );
	$from_name = isset( $options['wtl_from_name'] ) && ! empty( $options['wtl_from_name'] ) ? stripslashes_deep( $options['wtl_from_name'] ) : commercekit_get_default_settings( 'wtl_from_name' );

	return $from_name;
}
add_filter( 'wp_mail_from_name', 'commercekit_email_from_name', 9, 1 );

/**
 * Email from email
 *
 * @param  string $from_email from email.
 * @return string $from_email from name.
 */
function commercekit_email_from_email( $from_email ) {
	$options    = get_option( 'commercekit', array() );
	$from_email = isset( $options['wtl_from_email'] ) && ! empty( $options['wtl_from_email'] ) ? stripslashes_deep( $options['wtl_from_email'] ) : commercekit_get_default_settings( 'wtl_from_email' );

	return $from_email;
}
add_filter( 'wp_mail_from', 'commercekit_email_from_email', 9, 1 );

/**
 * Remove WooCommerce from email, from name filters.
 */
function commercekit_remove_wc_email_name_filters() {
	remove_filter( 'wp_mail_from', array( 'WC_Email', 'get_from_address' ) );
	remove_filter( 'wp_mail_from_name', array( 'WC_Email', 'get_from_name' ) );
}

/**
 * Waitlist add to cart text
 *
 * @param  string $text add to cart text.
 * @param  string $product product object.
 * @return string $text add to cart text.
 */
function commercekit_waitlist_add_to_cart_text( $text, $product ) {
	$options         = get_option( 'commercekit', array() );
	$enable_waitlist = isset( $options['waitlist'] ) && 1 === (int) $options['waitlist'] ? 1 : 0;
	$readmore_label  = isset( $options['wtl_readmore_text'] ) && ! empty( $options['wtl_readmore_text'] ) ? commercekit_get_multilingual_string( stripslashes_deep( $options['wtl_readmore_text'] ) ) : commercekit_get_default_settings( 'wtl_readmore_text' );
	if ( ! $enable_waitlist ) {
		return $text;
	}
	if ( $product && ( ( $product->managing_stock() && 0 === (int) $product->get_stock_quantity() && 'no' === $product->get_backorders() ) || 'outofstock' === $product->get_stock_status() ) ) {
		return $readmore_label;
	}

	return $text;
}
add_filter( 'woocommerce_product_add_to_cart_text', 'commercekit_waitlist_add_to_cart_text', 10, 2 );

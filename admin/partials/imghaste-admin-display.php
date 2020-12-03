<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.imghaste.com/
 * @since      1.0.0
 *
 * @package    Imghaste
 * @subpackage Imghaste/admin/partials
 */

function imghaste_options_page_cb() {
    // Check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>

	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'imghaste' );
			do_settings_sections( 'imghaste' );
			submit_button( 'Save Settings' );
			?>
		</form>
		<br><br>
		<span><?php echo __('If you enjoy our 100% White Labeled Image Optimization Service, Leave a ', 'imghaste'); ?></span>
		<a style="display: inline flow-root; display: inline-block;" href="https://wordpress.org/plugins/imghaste/#reviews" target="_blank"><?php wp_star_rating( array( 'rating' => 5, 'type' => 'rating')); ?></a> <span><?php echo __('rating to endorse the efforts!', 'imghaste') ; ?></span>
	</div>
	<?php
}



/*
** Callback functions for setting fields
*/

function imghaste_section_main_cb( $args ) {
	?><p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Settings of imghaste Plugin. You need to add your CDN url to start using the service', 'imghaste' ); ?></p><?php
}

/**
 * Asset function to check if on localhost
 *
 * @since   1.0.5
 *
 * @return  boolean
 */
function imghaste_is_localhost(){
	$localhost_list = array('127.0.0.1', '::1');
	if (in_array($_SERVER['REMOTE_ADDR'], $localhost_list)) {
		return true;
	} else {
		return false;
	}
}

/*
** Callback for status check section
*/
function imghaste_section_status_check_cb(){

    //Check if localhost
	if (imghaste_is_localhost()): ?>

		<input id="imghaste_localhost_check" type="hidden" value="true" name="imghaste_localhost_check">
		<h4><?php _e('Service Worker can not be effective on localhost','imghaste'); ?></h4>

	<?php else:

		//Start Checking
		$correct_style = 'color: green;';
		$error_style = 'color: red';
		$correct_icon = '<span class="dashicons dashicons-yes"></span> ';
		$error_icon = '<span class="dashicons dashicons-no"></span> ';

		//HTTPS check
		$correct_https_message = $correct_icon . __('Your website is running safely on HTTPS', 'imghaste');
		$error_https_message = $error_icon . __('Your website is not running on HTTPS the Service Worker can not be registered, unfortunately you can only use this service using Rewrite URLS', 'imghaste');
		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
			$https_message = $error_https_message;
			$https_style = $error_style;
		} else {
			$https_message = $correct_https_message;
			$https_style = $correct_style;
		}

		//Service Worker Test Runs on JS

		//Origin Server Test
		$options = get_option('imghaste_options');
		$OriginTestReq = array(
			"cdn_url" => $options['imghaste_field_cdn_url'],
			"origin" => get_site_url(),
		);
		$OriginTestConnection = curl_init('https://cdn.imghaste.com/v1/check/origin');
		curl_setopt($OriginTestConnection, CURLOPT_POSTFIELDS, $OriginTestReq);
		curl_setopt($OriginTestConnection, CURLOPT_RETURNTRANSFER, true);
		$OriginTestResJson = curl_exec($OriginTestConnection);
		curl_close($OriginTestConnection);
		$OriginTestRes = json_decode($OriginTestResJson);
		if ($OriginTestRes->status == 'REQUEST_OK') {
			$origin_message = $correct_icon . $OriginTestRes->notification;
			$origin_style = $correct_style;
		} else {
			$origin_message = $error_icon . $OriginTestRes->notification;
			$origin_style = $error_style;
		}

		?>
		<input id="imghaste_localhost_check" type="hidden" value="false" name="imghaste_localhost_check">
		<table class="form-table" role="presentation">
			<tbody>
				<tr class="imghaste_row">
					<th scope="row"><label><?php _e('Check: Https','imghaste'); ?></label></th>
					<td id="running-on-https-test" style="<?php echo $https_style; ?>"><?php echo $https_message; ?></td>
				</tr>
				<tr class="imghaste_row">
					<th scope="row"><label><?php _e('Check: Origin Server','imghaste'); ?></label></th>
					<td id="origin-server-test" style="<?php echo $origin_style; ?>"><?php echo $origin_message ; ?></td>
				</tr>
				<tr class="imghaste_row">
					<th scope="row"><label><?php _e('Check: Service Worker','imghaste'); ?></label></th>
					<td id="service-worker-test"><?php echo __('Checking the Service Worker status...', 'imghaste'); ?></td>
				</tr>
			</tbody>
		</table>
		<?php
	endif;
}

/*
** CDN URL field
*/
function imghaste_field_cdn_url_cb( $args ) {

	$options = get_option( 'imghaste_options' );
	if(isset($_POST['imghaste_field_cdn_url'])){
		$field_value = esc_url($_POST('imghaste_field_cdn_url'));
	} else {
		$field_value = $options['imghaste_field_cdn_url'];
	} ?>

	<input style="width:350px;"
	id="<?php echo esc_attr( $args['label_for'] ); ?>"
	data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	type="text"
	value="<?php echo $field_value; ?>"
	>

	<p class="description"><?php echo __('To get your own CDN URL, register', 'imghaste' ) . ' ' . '<a href="//app.imghaste.com/signup" target="_blank">' . __('here', 'imghaste') . '</a>' . '.'; ?></p>
	<?php
}

/*
** Rewrite Checkbox
*/
function imghaste_field_rewrite_cb( $args ) {

	$options = get_option( 'imghaste_options' );

	$current_checkbox = isset($options['imghaste_field_rewrite']) ? $options['imghaste_field_rewrite'] : '0';
	$checked_attribute = '';
	if ($current_checkbox == '1') {
		$checked_attribute .= 'checked';
	}
	?>


	<input
	id="<?php echo esc_attr( $args['label_for'] ); ?>"
	data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	type="checkbox"
	value="1"
	<?php echo $checked_attribute; ?>
	>

	<p class="description">
		<?php echo __('Enabling will re-write your URLs. It will force a fast first-impression but you will leak SEO. We advice against. Read me here.: ', 'imghaste' ); ?>

		<a href="https://www.imghaste.com/blog/service-worker-as-your-image-optimization-service" target="_blank"><?php echo __('a Service Worker as your image Service', 'imghaste');?></a>
		<?php /* echo __('As well as: ', 'imghaste' ); ?>
		<a href="https://www.imghaste.com/blog/how-does-google-measure-your-site-speed" target="_blank"><?php echo __('a Service Worker as your image Service', 'imghaste');?></a>
		<?php */ ?>
	</p>
	<?php
}

function imghaste_field_slimcss_cb( $args ) {

	$options = get_option( 'imghaste_options' );

	$current_checkbox = isset($options['imghaste_field_slimcss']) ? $options['imghaste_field_slimcss'] : '0';
	$checked_attribute = '';
	if ($current_checkbox == '1') {
		$checked_attribute .= 'checked';
	}
	?>



	<input
	id="<?php echo esc_attr( $args['label_for'] ); ?>"
	data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	type="checkbox"
	value="1"
	<?php echo $checked_attribute; ?>
	>

	<p class="description">
		<?php echo __('SlimCSS (Open Beta) Will remove the unused CSS from your homepage.', 'imghaste' ); ?>
	</p>
	<?php
}


function imghaste_field_slimcss_buffer_cb( $args ) {

	$options = get_option( 'imghaste_options' );

	$current_checkbox = isset($options['imghaste_field_slimcss_buffer']) ? $options['imghaste_field_slimcss_buffer'] : '0';
	$checked_attribute = '';
	if ($current_checkbox == '1') {
		$checked_attribute .= 'checked';
	}
	?>

	<input
	id="<?php echo esc_attr( $args['label_for'] ); ?>"
	data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	type="checkbox"
	value="1"
	<?php echo $checked_attribute; ?>
	>

	<p class="description">
		<?php echo __('SlimCSS Buffer removes styles that are not enqueued through the WordPress API. Check to remove if not needed.', 'imghaste' ); ?>
	</p>
	<?php
}

// Purge SlimCss
function imghaste_field_purge_slimcss_cb($args){

	$options = get_option('imghaste_options');

	//Get & initiate Purge Version
	$current_purgeversion = 1;
	if (isset($options['imghaste_field_slimcss_purgeversion'])) {
		$current_purgeversion = $options['imghaste_field_slimcss_purgeversion'];
	}
	?>

	<input type="button" name="slimcss_purge_button" id="slimcss_purge_button" class="button button-primary" value="<?php echo __('Purge SlimCSS', 'imghaste'); ?>">
	<input id="<?php echo esc_attr($args['label_for']); ?>" data-custom="<?php echo esc_attr($args['imghaste_custom_data']); ?>" name="imghaste_options[<?php echo esc_attr($args['label_for']); ?>]" type="hidden" value="<?php echo $current_purgeversion; ?>">
	<p class="description">
		<?php echo __('Purge the SlimCSS cache. Each url will be reanalyzed and compiled', 'imghaste'); ?>
	</p>

<?php } ?>

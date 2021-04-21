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

		// Manifest check
		$correct_manifest_message = $correct_icon . __('Manifest generated successfully. You can <a href="'.imghaste_manifest( 'src' ).'" target="_blank">See it here &rarr;</a>','imghaste');
		$error_manifest_message = $error_icon . __('Manifest generation failed. <a href="%s" target="_blank">Fix it &rarr;</a>','imghaste');

		if ( imghaste_file_exists( imghaste_manifest( 'src' ) ) || imghaste_generate_manifest() ) {

			$pwa_message = $correct_manifest_message;
			$pwa_style = $correct_style;

		} else {

			$pwa_message = $error_manifest_message;
			$pwa_style = $error_style;

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
				<tr class="imghaste_row">
					<th scope="row"><label><?php _e('Check: Manifest','imghaste'); ?></label></th>
					<td id="manifest-generated-test " style="<?php echo $pwa_style; ?>"><?php echo $pwa_message; ?></td>
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

	<?php 
}

// Pwa App Name
function imghaste_field_pwa_appname_cb( $args ) {

	// Get options
	$options = imghaste_get_options();

	if(isset($_POST['imghaste_field_pwa_appname'])){
		$field_value = esc_url($_POST('imghaste_field_pwa_appname'));
	} else {
		$field_value = $options['imghaste_field_pwa_appname'];
	} ?>


	<input style="width:350px;"
	id="<?php echo esc_attr( $args['label_for'] ); ?>"
	data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	type="text"
	value="<?php echo $field_value; ?>"
	>
	<?php
}


// Pwa App Shortname
function imghaste_field_pwa_short_appname_cb( $args ) {

	// Get options
	$options = imghaste_get_options();

	if(isset($_POST['imghaste_field_pwa_short_appname'])){
		$field_value = esc_url($_POST('imghaste_field_pwa_short_appname'));
	} else {
		$field_value = $options['imghaste_field_pwa_short_appname'];
	} ?>

	<input style="width:350px;"
	id="<?php echo esc_attr( $args['label_for'] ); ?>"
	data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	type="text"
	value="<?php echo $field_value; ?>"
	>
	<?php
}

// Pwa App Description
function imghaste_field_pwa_description_cb( $args ) {

	// Get options
	$options = imghaste_get_options();

	if(isset($_POST['imghaste_field_pwa_description'])){
		$field_value = esc_url($_POST('imghaste_field_pwa_description'));
	} else {
		$field_value = $options['imghaste_field_pwa_description'];
	} ?>

	<input style="width:350px;"
	id="<?php echo esc_attr( $args['label_for'] ); ?>"
	data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	type="text"
	value="<?php echo $field_value; ?>"
	>
	<?php
}

// Pwa App Icon
function imghaste_field_pwa_app_icon_cb( $args ) {

	// Get options
	$options = imghaste_get_options();

	?>

	<input type="text" name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="imghaste-icon regular-text" size="50" value="<?php echo isset( $options['imghaste_field_pwa_app_icon'] ) ? esc_attr( $options['imghaste_field_pwa_app_icon']) : ''; ?>">

	<button type="button" class="button imghaste-pwa-icon-upload" data-editor="content">
		<span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php _e( 'Choose Icon', 'imghaste' ); ?>
	</button>

	<p class="description">
		<?php _e('This will be the icon of your app when installed on the phone. Must be a <code>PNG</code> image exactly <code>192x192</code> in size.', 'imghaste'); ?>
	</p>
	<?php
}

// Pwa App Splash Icon
function imghaste_field_pwa_splash_screen_icon_cb( $args ) {

	// Get options
	$options = imghaste_get_options();
	
	?>

	<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="imghaste-splash-icon regular-text" size="50" data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo isset( $options['imghaste_field_pwa_splash_screen_icon'] ) ? esc_attr( $options['imghaste_field_pwa_splash_screen_icon']) : ''; ?>"
	>
	<button type="button" class="button imghaste-pwa-splash-icon-upload" data-editor="content">
		<span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php _e( 'Choose Icon', 'imghaste' ); ?>
	</button>

	<p class="description">
		<?php _e('This icon will be displayed on the splash screen of your app on supported devices. Must be a <code>PNG</code> image exactly <code>512x512</code> in size.', 'imghaste'); ?>
	</p>
	<?php
}

// Pwa App Background Color
function imghaste_field_pwa_background_color_cb( $args ) {

	// Get options
	$options = imghaste_get_options();

	if(isset($_POST['imghaste_field_pwa_background_color'])){
		$field_value = esc_url($_POST('imghaste_field_pwa_background_color'));
	} else {
		$field_value = $options['imghaste_field_pwa_background_color'];
	} ?>

	<input type="text" name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="imghaste-colorpicker" value="<?php echo $field_value; ?>"
	data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	>

	<p class="description">
		<?php _e('Background color of the splash screen.', 'imghaste'); ?>
	</p>
	<?php
}


// Pwa App Theme Color
function imghaste_field_pwa_theme_color_cb( $args ) {

	// Get options
	$options = imghaste_get_options();

	if(isset($_POST['imghaste_field_pwa_theme_color'])){
		$field_value = esc_url($_POST('imghaste_field_pwa_theme_color'));
	} else {
		$field_value = $options['imghaste_field_pwa_theme_color'];
	} ?>

	<input type="text" name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="imghaste-colorpicker" value="<?php echo $field_value; ?>"
	data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	>

	<p class="description">
		<?php _e('Theme color is used on supported devices to tint the UI elements of the browser and app switcher. When in doubt, use the same color as <code>Background Color</code>.', 'imghaste'); ?>
	</p>
	<?php
}

// Pwa Start Url
function imghaste_field_pwa_start_url_cb() {

	// Get options
	$options = imghaste_get_options();

	?>
	
	<fieldset>

		<!-- WordPress Pages Dropdown -->
		<label for="imghaste_options[imghaste_field_pwa_start_url]">
			<?php echo wp_dropdown_pages( array( 
				'name' => 'imghaste_options[imghaste_field_pwa_start_url]', 
				'echo' => 0, 
				'show_option_none' => __( '&mdash; Homepage &mdash;' ), 
				'option_none_value' => '0', 
				'selected' =>  isset($options['imghaste_field_pwa_start_url']) ? $options['imghaste_field_pwa_start_url'] : '',
			)); ?>
		</label>
		
		<p class="description">
			<?php printf( __( 'Specify the page to load when the application is launched from a device. Current start page is <code>%s</code>', 'imghaste' ), imghaste_get_start_url() ); ?>
		</p>
		
		<?php if ( imghaste_is_amp() ) { ?>

			<!--  AMP Page As Start Page -->
			<br><input type="checkbox" name="imghaste_options[start_url_amp]" id="imghaste_options[start_url_amp]" value="1" 
			<?php if ( isset( $options['start_url_amp'] ) ) { checked( '1', $options['start_url_amp'] ); } ?>>
			<label for="imghaste_options[start_url_amp]"><?php _e('Use AMP version of the start page.', 'imghaste') ?></label>
			<br>
			
			<!-- AMP for WordPress 0.6.2 doesn't support homepage, the blog index, and archive pages. -->
			<?php if ( is_plugin_active( 'amp/amp.php' ) ) { ?>
				<p class="description">
					<?php _e( 'Do not check this if your start page is the homepage, the blog index, or the archives page. AMP for WordPress does not create AMP versions for these pages.', 'imghaste' ); ?>
				</p>
			<?php } ?>
			
			<!-- tagDiv AMP 1.2 doesn't enable AMP for pages by default and needs to be enabled manually in options -->			
			<?php if ( is_plugin_active( 'td-amp/td-amp.php' ) && method_exists( 'td_util', 'get_option' ) ) { 
				
				// Read option value from db
				$td_amp_page_post_type = td_util::get_option( 'tds_amp_post_type_page' );

				// Show notice if option to enable AMP for pages is disabled.
				if ( empty( $td_amp_page_post_type ) ) { ?>
					<p class="description">
						<?php printf( __( 'Please enable AMP support for Page in <a href="%s">Theme options > Theme Panel</a> > AMP > Post Type Support.', 'imghaste' ), admin_url( 'admin.php?page=td_theme_panel' ) ); ?>
					</p>
				<?php }
			} ?>

		<?php } ?>

	</fieldset>

	<?php
}

//Pwa Offline Page Dropdown
function imghaste_field_pwa_offline_page_cb() {

	// Get options
	$options = imghaste_get_options();

	?>

	<!-- WordPress Pages Dropdown -->
	<label for="imghaste_options[imghaste_field_pwa_offline_page]">
		<?php echo wp_dropdown_pages( array(
			'name' => 'imghaste_options[imghaste_field_pwa_offline_page]', 
			'echo' => 0, 
			'show_option_none' => __( '&mdash; Default &mdash;' ), 
			'option_none_value' => '0', 
			'selected' =>  isset($options['imghaste_field_pwa_offline_page']) ? $options['imghaste_field_pwa_offline_page'] : '',
		)); ?>
	</label>

	<p class="description">
		<?php printf( __( 'Offline page is displayed when the device is offline and the requested page is not already cached. Current offline page is <code>%s</code>', 'imghaste' ), imghaste_get_offline_page() ); ?>
	</p>

	<?php
}

// Pwa Orientation Dropdown
function imghaste_field_pwa_orientation_cb() {

	// Get options
	$options = imghaste_get_options();
	
	?>
	
	<!-- Orientation Dropdown -->
	<label for="imghaste_options[imghaste_field_pwa_orientation]">
		<select name="imghaste_options[imghaste_field_pwa_orientation]" id="imghaste_options[imghaste_field_pwa_orientation]">
			<option value="0" <?php if ( isset( $options['imghaste_field_pwa_orientation'] ) ) { selected( $options['imghaste_field_pwa_orientation'], 0 ); } ?>>
				<?php _e( 'Follow Device Orientation', 'imghaste' ); ?>
			</option>
			<option value="1" <?php if ( isset( $options['imghaste_field_pwa_orientation'] ) ) { selected( $options['imghaste_field_pwa_orientation'], 1 ); } ?>>
				<?php _e( 'Portrait', 'imghaste' ); ?>
			</option>
			<option value="2" <?php if ( isset( $options['imghaste_field_pwa_orientation'] ) ) { selected( $options['imghaste_field_pwa_orientation'], 2 ); } ?>>
				<?php _e( 'Landscape', 'imghaste' ); ?>
			</option>
		</select>
	</label>

	<p class="description">
		<?php _e( 'Set the orientation of your app on devices. When set to <code>Follow Device Orientation</code> your app will rotate as the device is rotated.', 'imghaste' ); ?>
	</p>

	<?php
}

// Pwa Display
function imghaste_field_pwa_display_cb() {

	// Get options
	$options = imghaste_get_options();
	?>
	
	<!-- Display Dropdown -->
	<label for="imghaste_options[imghaste_field_pwa_display]">
		<select name="imghaste_options[imghaste_field_pwa_display]" id="imghaste_options[imghaste_field_pwa_display]">
			<option value="0" <?php if ( isset( $options['imghaste_field_pwa_display'] ) ) { selected( $options['imghaste_field_pwa_display'], 0 ); } ?>>
				<?php _e( 'Full Screen', 'imghaste' ); ?>
			</option>
			<option value="1" <?php if ( isset( $options['imghaste_field_pwa_display'] ) ) { selected( $options['imghaste_field_pwa_display'], 1 ); } ?>>
				<?php _e( 'Standalone', 'imghaste' ); ?>
			</option>
			<option value="2" <?php if ( isset( $options['imghaste_field_pwa_display'] ) ) { selected( $options['imghaste_field_pwa_display'], 2 ); } ?>>
				<?php _e( 'Minimal UI', 'imghaste' ); ?>
			</option>
			<option value="3" <?php if ( isset( $options['imghaste_field_pwa_display'] ) ) { selected( $options['imghaste_field_pwa_display'], 3 ); } ?>>
				<?php _e( 'Browser', 'imghaste' ); ?>
			</option>
		</select>
	</label>
	
	<p class="description">
		<?php printf( _e( 'Display mode decides what browser UI is shown when your app is launched. <code>Standalone</code> is default.','imghaste')); ?>
	</p>

	<?php
}
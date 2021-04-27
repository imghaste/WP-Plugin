<?php
/**
 * Provide a admin area view for the PWA settings
 *
 * This file is used to markup the admin-facing aspects of the PWA settings section
 *
 * @link       https://www.imghaste.com/
 * @since      1.1.2
 *
 * @package    Imghaste
 * @subpackage Imghaste/admin/partials
 */


/*
** Callback function to display PWA Section
*/
function imghaste_section_pwa_cb( $args ) {
	?><h2 id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Settings for PWA', 'imghaste' ); ?></h2><?php
}

/*
** Callback functions to display PWA fields
*/

// PWA App Name
function imghaste_field_pwa_appname_cb( $args ) {

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


// PWA App Shortname
function imghaste_field_pwa_short_appname_cb( $args ) {

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

// PWA App Description
function imghaste_field_pwa_description_cb( $args ) {

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

// PWA App Icon
function imghaste_field_pwa_app_icon_cb( $args ) {

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

// PWA App Splash Icon
function imghaste_field_pwa_splash_screen_icon_cb( $args ) {

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

// PWA App Background Color
function imghaste_field_pwa_background_color_cb( $args ) {

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


// PWA App Theme Color
function imghaste_field_pwa_theme_color_cb( $args ) {

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

// PWA start Url
function imghaste_field_pwa_start_url_cb() {

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

//PWA Offline Page Dropdown
function imghaste_field_pwa_offline_page_cb() {

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

// PWA Orientation Dropdown
function imghaste_field_pwa_orientation_cb() {

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

// PWA Display
function imghaste_field_pwa_display_cb() {

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
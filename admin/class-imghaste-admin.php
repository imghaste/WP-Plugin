<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.imghaste.com/
 * @since      1.0.0
 *
 * @package    Imghaste
 * @subpackage Imghaste/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Imghaste
 * @subpackage Imghaste/admin
 * @author     IMGHaste <dev@imghaste.com>
 */
class Imghaste_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function imghaste_admin_enqueue_scripts() {

		// Color picker CSS
		// @refer https://make.wordpress.org/core/2012/11/30/new-color-picker-in-wp-3-5/
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script('plugin-imghaste-options', IMGHASTE_PATH_SRC . 'admin/js/plugin-imghaste-settings.js', array('wp-color-picker'));

		// Everything needed for media upload
		wp_enqueue_media();

		global $pagenow;

		if( ('options-general.php' === $pagenow) && ('imghaste' === $_GET['page']) ){
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-imghaste-settings.js', array( 'jquery' ), $this->version, false );
		}
	}

	/**
	 * Register Admin Settings
	 *
	 * @since    1.0.0
	 */
	public function imghaste_settings_init() {

		/**
		 * Register Settings
	 	 */
		register_setting(
			'imghaste', 			// Group name
			'imghaste_options',		// Setting name = html form <input> name on settings form
			'imghaste_validater_and_sanitizer' // Input sanitizer
		);

		/**
		 *  Add Section in Registered Settings
	 	 */

		add_settings_section(
			 'imghaste_section_main', // Section ID
			__( '', 'imghaste' ),	  // Section heading
			 'imghaste_section_main_cb', // Callback function
			 'imghaste'					 // Related registered option
			);

		/**
		 *  Add Fields in Sections
	 	 */

		//Get current options
		$options = imghaste_get_options();

		// CDN URL
		add_settings_field(
			 'imghaste_field_cdn_url', 		// Field Name
			 __('CDN URL', 'imghaste'),		// Field Label
			 'imghaste_field_cdn_url_cb',	// Callback Function
			 'imghaste',					// Related registered option
			 'imghaste_section_main',		// Related added section
			 [
			 	'label_for'				=> 'imghaste_field_cdn_url',
			 	'class' 				=> 'imghaste_row',
			 	'imghaste_custom_data'	=> 'custom',
			 ]
			);

		// Enable CDN rewrite
		add_settings_field(
			'imghaste_field_rewrite', 		// Field Name
			__('Use URL Rewrite', 'imghaste'),	// Field Label
			'imghaste_field_rewrite_cb',	// Callback Function
			'imghaste',						// Related registered option
			'imghaste_section_main',		// Related added section
			[
				'label_for'				=> 'imghaste_field_rewrite',
				'class' 				=> 'imghaste_row',
				'imghaste_custom_data'	=> 'custom',
			]
		);

		// Enable SlimCSS
		add_settings_field(
			'imghaste_field_slimcss', 		// Field Name
			__('Enable SlimCSS', 'imghaste'),	// Field Label
			'imghaste_field_slimcss_cb',	// Callback Function
			'imghaste',						// Related registered option
			'imghaste_section_main',		// Related added section
			[
				'label_for'				=> 'imghaste_field_slimcss',
				'class' 				=> 'imghaste_row',
				'imghaste_custom_data'	=> 'custom',
			]
		);

		$show_slim = false;
		//Check if SlimCSS is enabled to show purge button
		if (isset($options['imghaste_field_slimcss'])) {
			if ($options['imghaste_field_slimcss'] == 1) {
				$show_slim = true;
			}
		}
		if ($show_slim) {
			/*
			// Enable SlimCSS Buffer
			add_settings_field(
				'imghaste_field_slimcss_buffer',
				__('Remove SlimCSS Buffer', 'imghaste'),
				'imghaste_field_slimcss_buffer_cb',
				'imghaste',
				'imghaste_section_main',
				[
					'label_for'				=> 'imghaste_field_slimcss_buffer',
					'class' 				=> 'imghaste_row',
					'imghaste_custom_data'	=> 'custom',
				]
			);*/
			//SlimCss Purge Version
			add_settings_field(
				'imghaste_field_slimcss_purgeversion',
				'',
				'imghaste_field_purge_slimcss_cb',
				'imghaste',
				'imghaste_section_main',
				[
					'label_for'				=> 'imghaste_field_slimcss_purgeversion',
					'class' 				=> 'imghaste_row',
					'imghaste_custom_data'	=> 'custom',
				]
			);
		}

		// Ensure that the CDN field is not empty
		if(!empty($options['imghaste_field_cdn_url'])){
			add_settings_section(
				'imghaste_section_health',
				__( 'Health Check', 'imghaste' ),
				'imghaste_section_status_check_cb',
				'imghaste'
			);
		}

		// Application Name
		add_settings_field(
			 'imghaste_field_pwa_appname', 			// Field Name
			 __('Application Name', 'imghaste'),	// Field Label
			 'imghaste_field_pwa_appname_cb',		// Callback Function
			 'imghaste',							// Related registered option
			 'imghaste_section_main',				// Related added section
			 [
			 	'label_for'				=> 'imghaste_field_pwa_appname',
			 	'class' 				=> 'imghaste_row',
			 	'imghaste_custom_data'	=> 'custom',
			 ]
			);

		// Application Short Name
		add_settings_field(
			 'imghaste_field_pwa_short_appname', 		// Field Name
			 __('Application Short Name', 'imghaste'),	// Field Label
			 'imghaste_field_pwa_short_appname_cb',		// Callback Function
			 'imghaste',								// Related registered option
			 'imghaste_section_main',					// Related added section
			 [
			 	'label_for'				=> 'imghaste_field_pwa_short_appname',
			 	'class' 				=> 'imghaste_row',
			 	'imghaste_custom_data'	=> 'custom',
			 ]
			);

		// Description
		add_settings_field(
			 'imghaste_field_pwa_description', 		// Field Name
			 __('Description', 'imghaste'),			// Field Label
			 'imghaste_field_pwa_description_cb',	// Callback Function
			 'imghaste',							// Related registered option
			 'imghaste_section_main',				// Related added section
			 [
			 	'label_for'				=> 'imghaste_field_pwa_description',
			 	'class' 				=> 'imghaste_row',
			 	'imghaste_custom_data'	=> 'custom',
			 ]
			);

		// Application Icon	
		add_settings_field(
			 'imghaste_field_pwa_app_icon', 		// Field Name
			 __('Application Icon', 'imghaste'),	// Field Label
			 'imghaste_field_pwa_app_icon_cb',		// Callback Function
			 'imghaste',							// Related registered option
			 'imghaste_section_main',				// Related added section
			 [
			 	'label_for'				=> 'imghaste_field_pwa_app_icon',
			 	'class' 				=> 'imghaste_row',
			 	'imghaste_custom_data'	=> 'custom',
			 ]
			);

		// Splash Screen Icon
		add_settings_field(
			 'imghaste_field_pwa_splash_screen_icon', 		// Field Name
			 __('Splash Screen Icon', 'imghaste'),			// Field Label
			 'imghaste_field_pwa_splash_screen_icon_cb',	// Callback Function
			 'imghaste',									// Related registered option
			 'imghaste_section_main',						// Related added section
			 [
			 	'label_for'				=> 'imghaste_field_pwa_splash_screen_icon',
			 	'class' 				=> 'imghaste_row',
			 	'imghaste_custom_data'	=> 'custom',
			 ]
			);

		// Background Color	
		add_settings_field(
			 'imghaste_field_pwa_background_color', 		// Field Name
			 __('Background Color', 'imghaste'),			// Field Label
			 'imghaste_field_pwa_background_color_cb',		// Callback Function
			 'imghaste',									// Related registered option
			 'imghaste_section_main',						// Related added section
			 [
			 	'label_for'				=> 'imghaste_field_pwa_background_color',
			 	'class' 				=> 'imghaste_row',
			 	'imghaste_custom_data'	=> 'custom',
			 ]
			);

		// Theme Color		
		add_settings_field(
			 'imghaste_field_pwa_theme_color', 		// Field Name
			 __('Theme Color', 'imghaste'),			// Field Label
			 'imghaste_field_pwa_theme_color_cb',	// Callback Function
			 'imghaste',							// Related registered option
			 'imghaste_section_main',				// Related added section
			 [
			 	'label_for'				=> 'imghaste_field_pwa_theme_color',
			 	'class' 				=> 'imghaste_row',
			 	'imghaste_custom_data'	=> 'custom',
			 ]
			);

		// Start URL
		add_settings_field(
			'imghaste_field_pwa_start_url',			// Field Name
			__('Start Page', 'imghaste'),			// Field Label
			'imghaste_field_pwa_start_url_cb',		// Callback Function
			'imghaste',								// Related registered option
			 'imghaste_section_main',				// Related added section
			 [
			 	'label_for'				=> 'imghaste_field_pwa_start_url',
			 	'class' 				=> 'imghaste_row',
			 	'imghaste_custom_data'	=> 'custom',
			 ]
			);

		// Offline Page
		add_settings_field(
			'imghaste_field_pwa_offline_page',		// Field Name
			__('Offline Page', 'imghaste'),			// Field Label
			'imghaste_field_pwa_offline_page_cb',	// Callback Function
			'imghaste',								// Related registered option
			 'imghaste_section_main',				// Related added section
			 [
			 	'label_for'				=> 'imghaste_field_pwa_offline_page',
			 	'class' 				=> 'imghaste_row',
			 	'imghaste_custom_data'	=> 'custom',
			 ]
			);

		// Orientation
		add_settings_field(
			'imghaste_field_pwa_orientation',		// Field Name
			__('Orientation', 'imghaste'),			// Field Label
			'imghaste_field_pwa_orientation_cb',	// Callback Function
			'imghaste',								// Related registered option
			 'imghaste_section_main',				// Related added section
			 [
			 	'label_for'				=> 'imghaste_field_pwa_orientation',
			 	'class' 				=> 'imghaste_row',
			 	'imghaste_custom_data'	=> 'custom',
			 ]
			);

		// Display
		add_settings_field(
			'imghaste_field_pwa_display',		// Field Name
			__('Display', 'imghaste'),			// Field Label
			'imghaste_field_pwa_display_cb',	// Callback Function
			'imghaste',							// Related registered option
			 'imghaste_section_main',			// Related added section
			 [
			 	'label_for'				=> 'imghaste_field_pwa_display',
			 	'class' 				=> 'imghaste_row',
			 	'imghaste_custom_data'	=> 'custom',
			 ]
			);

	}


	/**
	 * Register Settings Page
	 *
	 * @since    1.0.0
	 */

	public function imghaste_options_page() {

		add_submenu_page(
			'options-general.php',
			'IMGHaste Options',
			'IMGHaste',
			'manage_options',
			'imghaste',
			'imghaste_options_page_cb'
		);

	}

	/**
	 * Clean Filter option input before saving
	 *
	 * @since    1.0.0
	 */

	public function imghaste_update_field_imghaste_field_cdn_url($new_value, $old_value){
		$options = get_option( 'imghaste_options' );
    	// Filter out various characters
    	// *** "\" and "%" are currently not filtered out
		$new_value = preg_replace('/(?:\s+|<|>|\*|@|"|\[|\]|\^|\+|&|#|\\|%|\?|=|~|_|\||!|;|,|\(|\)|\')/', '', $new_value);
    	// Add trailing slash
    	// ** Check last 2 characters (thus booleans not editted), add / if not there
		$new_value = preg_replace('/(.(?!\/).)$/', '${1}${2}/', $new_value);
		return $new_value;
	}


	/**
	 * Action function for admin notice on incomplete settings
	 *
	 * @since	1.0.5
	 */

	public function imghaste_incomplete_settings_notice(){

		$options = get_option( 'imghaste_options' );
		$field_value = $options['imghaste_field_cdn_url'];
		if (empty($field_value)) {
			?>
			<div style="padding: 10px;" class="notice notice-error">
				<?php if( 'imghaste' != $_GET['page'] ): ?>
					<strong><?php _e( 'IMGHaste - Settings not saved', 'imghaste'); ?></strong>
					<hr>
					<span><?php _e( 'Complete the settings for imghaste plugin', 'imghaste' ) . ' '; ?>
					<a href="<?php echo get_admin_url() . 'options-general.php?page=imghaste'; ?>"><?php echo __('here', 'imghaste'); ?></a>
				</span>
			<?php endif; ?>
			<p>
				<?php _e( 'This plugin is active but it won’t be functional unless configured with a valid CDN URL Provided at ', 'imghaste'); ?><a href="https://app.imghaste.com" target="_blank"><?php echo __('app.imghaste.com', 'imghaste'); ?></a>
			</p>
		</div>
		<?php
	}

}

// Validate and sanitize user input before its saved to database

public function imghaste_validater_and_sanitizer( $options ) {

	// Sanitize Application Name
	$options['imghaste_field_pwa_appname'] = sanitize_text_field( $options['imghaste_field_pwa_appname'] ) == '' ? get_bloginfo( 'name' ) : sanitize_text_field( $options['imghaste_field_pwa_appname'] );

	// Sanitize Application Short Name
	$options['imghaste_field_pwa_short_appname'] = substr( sanitize_text_field( $options['imghaste_field_pwa_short_appname'] ) == '' ? get_bloginfo( 'name' ) : sanitize_text_field( $options['imghaste_field_pwa_short_appname'] ), 0, 15 );

	// Sanitize description
	$options['imghaste_field_pwa_description'] = sanitize_text_field( $options['imghaste_field_pwa_description'] );

	// Sanitize application icon
	$options['imghaste_field_pwa_app_icon'] = sanitize_text_field( $options['imghaste_field_pwa_app_icon'] ) == '' ? imghaste_httpsify( IMGHASTE_PATH_SRC . 'public/images/logo.png' ) : sanitize_text_field( imghaste_httpsify( $options['imghaste_field_pwa_app_icon'] ) );

	// Sanitize splash screen icon
	$options['imghaste_field_pwa_splash_screen_icon'] = sanitize_text_field( imghaste_httpsify( $options['imghaste_field_pwa_splash_screen_icon'] ) );

	// Sanitize hex color input for background_color
	$options['imghaste_field_pwa_background_color'] = preg_match( '/#([a-f0-9]{3}){1,2}\b/i', $options['imghaste_field_pwa_background_color'] ) ? sanitize_text_field( $options['imghaste_field_pwa_background_color'] ) : '#D5E0EB';

	// Sanitize hex color input for theme_color
	$options['imghaste_field_pwa_theme_color'] = preg_match( '/#([a-f0-9]{3}){1,2}\b/i', $options['imghaste_field_pwa_theme_color'] ) ? sanitize_text_field( $options['imghaste_field_pwa_theme_color'] ) : '#D5E0EB';

	/**
	 * Get current options already saved in the database.
	 * 
	 * When the Imghaste > options page is saved, the form does not have the values for
	 * is_static_sw or is_static_manifest. So this is added here to match the already saved 
	 * values in the database. 
	 */
	$current_settings = imghaste_get_options();
	
	if ( ! isset( $options['is_static_sw'] ) ) {
		$options['is_static_sw'] = $current_settings['is_static_sw'];
	}
	
	if ( ! isset( $options['is_static_manifest'] ) ) {
		$options['is_static_manifest'] = $current_settings['is_static_manifest'];
	}
	
	return $options;
}

}


//Import Display Callbacks
// require_once plugin_dir_path( __FILE__ ).'partials/imghaste-admin-display.php';

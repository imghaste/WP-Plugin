<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.imghaste.com/
 * @since      1.0.0
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Imghaste
 * @subpackage Imghaste/admin
 * @author     IMGHaste <dev@imghaste.com>
 */

class Imghaste_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	* Helper methods for this plugin.
	*
	* @since    1.3.0
	* @access   private
	* @var      Imghaste_Helpers $helpers Helper methods for this plugin.
	*/
	private $helpers;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 * @since    1.0.0
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		/* Get Helpers */
		$this->helpers = new Imghaste_Helpers();

		/* Get Displays for Admin Area */

		//Main Section display assets
		require_once plugin_dir_path(__FILE__) . 'partials/imghaste-admin-display.php';
		//PWA Section display assets
		require_once plugin_dir_path(__FILE__) . 'partials/imghaste-admin-pwa.php';
		//Heath Check
		require_once plugin_dir_path(__FILE__) . 'partials/imghaste-admin-check.php';

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function imghaste_admin_enqueue_scripts()
	{

		global $pagenow;

		if (('options-general.php' === $pagenow) && isset($_GET['page']) && ('imghaste' === $_GET['page'])) {

			//Enqueue General Settings
			wp_enqueue_script($this->plugin_name . '-settings', plugin_dir_url(__FILE__) . 'js/plugin-imghaste-settings.js', '', $this->version, false);

			$options = Imghaste_Helpers::imghaste_get_options();

			//Check if PWA is enabled
			if (isset($options['imghaste_field_pwa'])) {
				if ($options['imghaste_field_pwa'] == 1) {

					//Everything needed for media upload
					wp_enqueue_media();

					/**
					 * Color picker CSS
					 * @refer https://make.wordpress.org/core/2012/11/30/new-color-picker-in-wp-3-5/
					 */
					wp_enqueue_style('wp-color-picker');
					//PWA Settings
					wp_enqueue_script($this->plugin_name . '-pwa-settings', plugin_dir_url(__FILE__) . 'js/plugin-imghaste-pwa-settings.js', array('jquery', 'wp-color-picker'), $this->version, false);

				}
			}
		}
	}

	/**
	 * Register Admin Settings
	 *
	 * @since    1.0.0
	 */
	public function imghaste_settings_init()
	{

		//Get current options
		$options = $this->helpers->imghaste_get_options();

		/**
		 * Register Settings
		 */
		register_setting(
			'imghaste',            // Group name
			'imghaste_options',        // Setting name = html form <input> name on settings form
			'imghaste_validater_and_sanitizer' // Input sanitizer
		);

		/**
		 *  Add Sections in Registered Settings
		 */


		//Main Settings
		add_settings_section(
			'imghaste_section_main', // Section ID
			__('General Settings', 'imghaste'),      // Section heading
			'imghaste_section_main_cb', // Callback function
			'imghaste'
		);


		//PWA Settinsg
		add_settings_section(
			'imghaste_section_pwa', // Section ID
			__('Settings for PWA', 'imghaste'),      // Section heading
			'imghaste_section_pwa_cb', // Callback function
			'imghaste_pwa'
		);


		//Health Check
		if (!empty($options['imghaste_field_cdn_url'])) {
			add_settings_section(
				'imghaste_section_health',
				__('Health Check', 'imghaste'),
				'imghaste_section_status_check_cb',
				'imghaste_check'
			);
		}


		/**
		 *  Add Fields in Sections
		 */

		/**
		 * Main Setting
		 *
		 * @since  1.0.0
		 */

		// CDN URL
		add_settings_field(
			'imghaste_field_cdn_url',
			__('CDN URL', 'imghaste'),
			'imghaste_field_cdn_url_cb',
			'imghaste',
			'imghaste_section_main',
			[
				'label_for' => 'imghaste_field_cdn_url',
				'class' => 'imghaste_row',
				'imghaste_custom_data' => 'custom',
			]
		);

		// Enable CDN rewrite
		add_settings_field(
			'imghaste_field_rewrite',
			__('Use URL Rewrite', 'imghaste'),
			'imghaste_field_rewrite_cb',
			'imghaste',
			'imghaste_section_main',
			[
				'label_for' => 'imghaste_field_rewrite',
				'class' => 'imghaste_row',
				'imghaste_custom_data' => 'custom',
			]
		);


		/* NOT IN USE
		// Enable SlimCSS
		add_settings_field(
			'imghaste_field_slimcss',
			__('Enable SlimCSS', 'imghaste'),
			'imghaste_field_slimcss_cb',
			'imghaste',
			'imghaste_section_main',
			[
				'label_for'				=> 'imghaste_field_slimcss',
				'class' 				=> 'imghaste_row',
				'imghaste_custom_data'	=> 'custom',
			]
		);

		$show_slim = false;
		Check if SlimCSS is enabled to show purge button
		if (isset($options['imghaste_field_slimcss'])) {
			if ($options['imghaste_field_slimcss'] == 1) {
				$show_slim = true;
			}
		}
		if ($show_slim) {
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
		*/

		/**
		 * PWA Setting
		 *
		 * @since    1.1.2
		 */

		// Enable PWA
		add_settings_field(
			'imghaste_field_pwa',
			__('Enable PWA', 'imghaste'),
			'imghaste_field_pwa_cb',
			'imghaste',
			'imghaste_section_main',
			[
				'label_for' => 'imghaste_field_pwa',
				'class' => 'imghaste_row',
				'imghaste_custom_data' => 'custom',
			]
		);


		// Application Name
		add_settings_field(
			'imghaste_field_pwa_appname',
			__('Application Name', 'imghaste'),
			'imghaste_field_pwa_appname_cb',
			'imghaste_pwa',
			'imghaste_section_pwa',
			[
				'label_for' => 'imghaste_field_pwa_appname',
				'class' => 'imghaste_row',
				'imghaste_custom_data' => 'custom',
			]
		);

		// Application Short Name
		add_settings_field(
			'imghaste_field_pwa_short_appname',
			__('Application Short Name', 'imghaste'),
			'imghaste_field_pwa_short_appname_cb',
			'imghaste_pwa',
			'imghaste_section_pwa',
			[
				'label_for' => 'imghaste_field_pwa_short_appname',
				'class' => 'imghaste_row',
				'imghaste_custom_data' => 'custom',
			]
		);

		// Description
		add_settings_field(
			'imghaste_field_pwa_description',
			__('Description', 'imghaste'),
			'imghaste_field_pwa_description_cb',
			'imghaste_pwa',
			'imghaste_section_pwa',
			[
				'label_for' => 'imghaste_field_pwa_description',
				'class' => 'imghaste_row',
				'imghaste_custom_data' => 'custom',
			]
		);

		// Application Icon
		add_settings_field(
			'imghaste_field_pwa_app_icon',
			__('Application Icon', 'imghaste'),
			'imghaste_field_pwa_app_icon_cb',
			'imghaste_pwa',
			'imghaste_section_pwa',
			[
				'label_for' => 'imghaste_field_pwa_app_icon',
				'class' => 'imghaste_row',
				'imghaste_custom_data' => 'custom',
			]
		);

		// Splash Screen Icon
		add_settings_field(
			'imghaste_field_pwa_splash_screen_icon',
			__('Splash Screen Icon', 'imghaste'),
			'imghaste_field_pwa_splash_screen_icon_cb',
			'imghaste_pwa',
			'imghaste_section_pwa',
			[
				'label_for' => 'imghaste_field_pwa_splash_screen_icon',
				'class' => 'imghaste_row',
				'imghaste_custom_data' => 'custom',
			]
		);

		// Background Color
		add_settings_field(
			'imghaste_field_pwa_background_color',
			__('Background Color', 'imghaste'),
			'imghaste_field_pwa_background_color_cb',
			'imghaste_pwa',
			'imghaste_section_pwa',
			[
				'label_for' => 'imghaste_field_pwa_background_color',
				'class' => 'imghaste_row',
				'imghaste_custom_data' => 'custom',
			]
		);

		// Theme Color
		add_settings_field(
			'imghaste_field_pwa_theme_color',
			__('Theme Color', 'imghaste'),
			'imghaste_field_pwa_theme_color_cb',
			'imghaste_pwa',
			'imghaste_section_pwa',
			[
				'label_for' => 'imghaste_field_pwa_theme_color',
				'class' => 'imghaste_row',
				'imghaste_custom_data' => 'custom',
			]
		);

		// Start URL
		add_settings_field(
			'imghaste_field_pwa_start_url',
			__('Start Page', 'imghaste'),
			'imghaste_field_pwa_start_url_cb',
			'imghaste_pwa',
			'imghaste_section_pwa',
			[
				'label_for' => 'imghaste_field_pwa_start_url',
				'class' => 'imghaste_row',
				'imghaste_custom_data' => 'custom',
			]
		);

		// Orientation
		add_settings_field(
			'imghaste_field_pwa_orientation',
			__('Orientation', 'imghaste'),
			'imghaste_field_pwa_orientation_cb',
			'imghaste_pwa',
			'imghaste_section_pwa',
			[
				'label_for' => 'imghaste_field_pwa_orientation',
				'class' => 'imghaste_row',
				'imghaste_custom_data' => 'custom',
			]
		);

		// Display
		add_settings_field(
			'imghaste_field_pwa_display',
			__('Display', 'imghaste'),
			'imghaste_field_pwa_display_cb',
			'imghaste_pwa',
			'imghaste_section_pwa',
			[
				'label_for' => 'imghaste_field_pwa_display',
				'class' => 'imghaste_row',
				'imghaste_custom_data' => 'custom',
			]
		);



	}


	/**
	 * Register Settings Page
	 *
	 * @since    1.0.0
	 */

	public function imghaste_options_page()
	{

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
	 * Action function for admin notice on incomplete settings
	 *
	 * @since    1.0.5
	 */

	public function imghaste_incomplete_settings_notice()
	{

		$options = get_option('imghaste_options');
		$check_incomplete = true;
		if(empty($options)){
			//Do nothing
		}else{
			$field_value = $options['imghaste_field_cdn_url'];
				if (empty($field_value)) {
						//Do nothing
				}else{
					$check_incomplete = false;
				}

		}
		if($check_incomplete):	?>
			<div style="padding: 10px;" class="notice notice-error">
				<?php if (isset($_GET['page']) && 'imghaste' != $_GET['page']): ?>
					<strong><?php _e('IMGHaste - Settings not saved', 'imghaste'); ?></strong>
					<hr>
					<span><?php _e('Complete the settings for imghaste plugin', 'imghaste') . ' '; ?>
					<a href="<?php echo get_admin_url() . 'options-general.php?page=imghaste'; ?>"><?php echo __('here', 'imghaste'); ?></a>
				</span>
			<?php endif; ?>
			<p>
				<?php _e('This plugin is active but it won’t be functional unless configured with a valid CDN URL Provided at ', 'imghaste'); ?>
				<a href="https://app.imghaste.com" target="_blank"><?php echo __('app.imghaste.com', 'imghaste'); ?></a>
			</p>
			</div><?php
		endif;
	}

	/**
	 * Assey Function to Sanitize settings
	 *
	 * @since    1.1.2
	 */

	public function imghaste_validater_and_sanitizer($options)
	{

		/* Sanitize CDN Fields */

		$cdn_url = $options['imghaste_field_cdn_url'];
		// Filter out various characters, "\" and "%" are currently not filtered out
		$cdn_url = preg_replace('/(?:\s+|<|>|\*|@|"|\[|\]|\^|\+|&|#|\\|%|\?|=|~|_|\||!|;|,|\(|\)|\')/', '', $cdn_url);
		// Add trailing slash
		$cdn_url = preg_replace('/(.(?!\/).)$/', '${1}${2}/', $cdn_url);
		$options['imghaste_field_cdn_url'] = $cdn_url;

		/* Sanitize PWA Fields */

		// Sanitize Application Name
		$options['imghaste_field_pwa_appname'] = sanitize_text_field($options['imghaste_field_pwa_appname']) == '' ? get_bloginfo('name') : sanitize_text_field($options['imghaste_field_pwa_appname']);

		// Sanitize Application Short Name
		$options['imghaste_field_pwa_short_appname'] = substr(sanitize_text_field($options['imghaste_field_pwa_short_appname']) == '' ? get_bloginfo('name') : sanitize_text_field($options['imghaste_field_pwa_short_appname']), 0, 15);

		// Sanitize description
		$options['imghaste_field_pwa_description'] = sanitize_text_field($options['imghaste_field_pwa_description']);

		// Sanitize application icon
		$options['imghaste_field_pwa_app_icon'] = sanitize_text_field($options['imghaste_field_pwa_app_icon']) == '' ? imghaste_httpsify(IMGHASTE_PATH_SRC . 'public/images/logo.png') : sanitize_text_field(imghaste_httpsify($options['imghaste_field_pwa_app_icon']));

		// Sanitize splash screen icon
		$options['imghaste_field_pwa_splash_screen_icon'] = sanitize_text_field(imghaste_httpsify($options['imghaste_field_pwa_splash_screen_icon']));

		// Sanitize hex color input for background_color
		$options['imghaste_field_pwa_background_color'] = preg_match('/#([a-f0-9]{3}){1,2}\b/i', $options['imghaste_field_pwa_background_color']) ? sanitize_text_field($options['imghaste_field_pwa_background_color']) : '#D5E0EB';

		// Sanitize hex color input for theme_color
		$options['imghaste_field_pwa_theme_color'] = preg_match('/#([a-f0-9]{3}){1,2}\b/i', $options['imghaste_field_pwa_theme_color']) ? sanitize_text_field($options['imghaste_field_pwa_theme_color']) : '#D5E0EB';

		/**
		 * Get current options already saved in the database.
		 *
		 * When the Imghaste > options page is saved, the form does not have the values for
		 * is_static_sw or is_static_manifest. So this is added here to match the already saved
		 * values in the database.
		 */
		$current_settings = $this->helpers->imghaste_get_options();

		if (!isset($options['is_static_sw'])) {
			$options['is_static_sw'] = $current_settings['is_static_sw'];
		}

		if (!isset($options['is_static_manifest'])) {
			$options['is_static_manifest'] = $current_settings['is_static_manifest'];
		}

		return $options;
	}

}

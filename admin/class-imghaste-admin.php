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
		register_setting( 'imghaste', 'imghaste_options' );

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
		$options = get_option( 'imghaste_options' );

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

}


//Import Display Callbacks
require_once plugin_dir_path( __FILE__ ).'partials/imghaste-admin-display.php';

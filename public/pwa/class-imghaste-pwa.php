<?php
/**
 * PWA funtcionality for Image Haste
 *
 * @since    1.1.2
 * @access   private
 */

class Imghaste_PWA
{

	public function __construct()
	{

	}

	/**
	 * Rewrite rule to show PWA Manifest
	 *
	 * @since   1.1.2
	 *
	 * This is called when options are update
	 */
	public function imghaste_pwa_rewrite()
	{

		add_rewrite_rule('^/' . $this->imghaste_get_manifest_filename() . '$',
			'index.php?' . $this->imghaste_get_manifest_filename() . '=1',
			'top'
		);

	}


	/**
	 * Generate the Manifest File on the Fly
	 *
	 * @since    1.1.2
	 *
	 */
	public function imghaste_pwa_generate($query)
	{

		if (!property_exists($query, 'query_vars') || !is_array($query->query_vars)) {
			return;
		}

		$query_vars_as_string = implode(',', $query->query_vars);

		//Check if manifest filename in Query
		if (strpos($query_vars_as_string, $this->imghaste_get_manifest_filename()) !== false) {
			header('Content-Type: application/json');
			header('Cache-Control: max-age=3600');
			//Return Manifest as a JSON file
			echo json_encode($this->imghaste_manifest_template());
			exit();
		}

	}


	/**
	 * The Manifest file Template
	 *
	 * @return  array
	 *
	 * This is used to print the Manifest file
	 * @since   1.1.2
	 */
	function imghaste_manifest_template()
	{

		$options = Imghaste_Helpers::imghaste_get_options();

		$manifest = array();

		//Check name value as representative for all to see it user has submitted data
		if (isset($options['imghaste_field_pwa_appname'])) {

			//Name
			$manifest['name'] = $options['imghaste_field_pwa_appname'];
			//Short Name
			$manifest['short_name'] = $options['imghaste_field_pwa_short_appname'];
			// Description
			if (isset($options['imghaste_field_pwa_description']) && !empty($options['imghaste_field_pwa_description'])) {
				$manifest['description'] = $options['imghaste_field_pwa_description'];
			}
			//Icons
			$manifest['icons'] = $this->imghaste_get_pwa_icons();
			//Backgroud Color
			$manifest['background_color'] = $options['imghaste_field_pwa_background_color'];
			//Theme Color
			$manifest['theme_color'] = $options['imghaste_field_pwa_theme_color'];
			//Start Page
			$manifest['start_url'] = strlen($this->imghaste_get_start_url(true)) > 2 ? user_trailingslashit($this->imghaste_get_start_url(true)) : $this->imghaste_get_start_url(true);
			//Orientation
			$manifest['orientation'] = $this->imghaste_get_orientation();
			//Display
			$manifest['display'] = $this->imghaste_get_display();
			//Scope is home by default
			$manifest['scope'] = '/';

		} else {
			$manifest['error'] = __('You need to go to Settings and set the PWA fields', 'imghaste');
		}

		/**
		 * Values that go in to Manifest JSON.
		 *
		 * The Web app manifest is a simple JSON file that tells the browser about your web application.
		 *
		 * @param array $manifest
		 */
		return apply_filters('imghaste_manifest', $manifest);
	}


	/**
	 * Add manifest to header
	 *
	 * @return  string
	 *
	 * It run on action wp_head
	 * @since   1.1.2
	 */
	public function imghaste_add_manifest_to_wp_head()
	{

		$tags = '<!-- Manifest added by ImgHaste -->' . PHP_EOL;
		$tags .= '<link rel="manifest" href="' . parse_url($this->imghaste_manifest('src'), PHP_URL_PATH) . '">' . PHP_EOL;

		// theme-color meta tag
		if (apply_filters('imghaste_add_theme_color', true)) {

			// Get options
			$options = Imghaste_Helpers::imghaste_get_options();
			$tags .= '<meta name="theme-color" content="' . $options['imghaste_field_pwa_theme_color'] . '">' . PHP_EOL;
		}

		$tags = apply_filters('imghaste_wp_head_tags', $tags);

		$tags .= '<!-- / imghaste.com -->' . PHP_EOL;

		echo $tags;
	}

	/**
	 * Asset Functions for PWA
	 *
	 * @since   1.1.2
	 * /
	 *
	 * /*
	 * Asset functions to get Manifest filename or link
	 */
	public static function imghaste_manifest($arg = 'src')
	{
		$manifest_filename = Imghaste_PWA::imghaste_get_manifest_filename();
		switch ($arg) {
			//Get filename
			case 'filename':
				return $manifest_filename;
				break;
			//Get link to manifest
			case 'src':
			default:
				return home_url('/') . $manifest_filename;
				break;
		}
	}

	/*
	* Asset Function to return manifest filename
	*/
	public static function imghaste_get_manifest_filename()
	{
		return 'imghaste-manifest' . Imghaste_PWA::imghaste_multisite_filename_postfix() . '.webmanifest';
	}


	/*
	* Asset Function to return manifest filename on multisite
	*/
	public static function imghaste_multisite_filename_postfix()
	{
		// Return empty string if not a multisite
		if (!is_multisite()) {
			return '';
		}
		return '-' . get_current_blog_id();
	}


	/*
	* Check if any AMP plugin is installed
	*/
	public static function imghaste_is_amp()
	{

		if (!function_exists('is_plugin_active')) {
			require_once(ABSPATH . '/wp-admin/includes/plugin.php');
		}

		// AMP for WordPress - https://wordpress.org/plugins/amp
		if (is_plugin_active('amp/amp.php')) {
			return defined('AMP_QUERY_VAR') ? AMP_QUERY_VAR . '/' : 'amp/';
		}

		// AMP for WP - https://wordpress.org/plugins/accelerated-mobile-pages/
		if (is_plugin_active('accelerated-mobile-pages/accelerated-moblie-pages.php')) {
			return defined('AMPFORWP_AMP_QUERY_VAR') ? AMPFORWP_AMP_QUERY_VAR . '/' : 'amp/';
		}

		// Better AMP - https://wordpress.org/plugins/better-amp/
		if (is_plugin_active('better-amp/better-amp.php')) {
			return 'amp/';
		}

		// AMP Supremacy - https://wordpress.org/plugins/amp-supremacy/
		if (is_plugin_active('amp-supremacy/amp-supremacy.php')) {
			return 'amp/';
		}

		// WP AMP - https://wordpress.org/plugins/wp-amp-ninja/
		if (is_plugin_active('wp-amp-ninja/wp-amp-ninja.php')) {
			return '?wpamp';
		}

		// tagDiv AMP - http://forum.tagdiv.com/tagdiv-amp/
		if (is_plugin_active('td-amp/td-amp.php')) {
			return defined('AMP_QUERY_VAR') ? AMP_QUERY_VAR . '/' : 'amp/';
		}

		return false;
	}


	/*
	* Asset Functions to get fields
	*/

	// Get PWA Icons
	function imghaste_get_pwa_icons()
	{

		$options = Imghaste_Helpers::imghaste_get_options();

		// Application icon
		$icons_array[] = array(
			'src' => $options['imghaste_field_pwa_app_icon'],
			'sizes' => '192x192', // must be 192x192. Todo: use getimagesize($options['icon'])[0].'x'.getimagesize($options['icon'])[1] in the future
			'type' => 'image/png', // must be image/png. Todo: use getimagesize($options['icon'])['mime']
			//'purpose'=> 'any maskable', // any maskable to support adaptive icons
		);

		// Splash screen icon
		if (@$options['imghaste_field_pwa_splash_screen_icon'] != '') {

			$icons_array[] = array(
				'src' => $options['imghaste_field_pwa_splash_screen_icon'],
				'sizes' => '512x512', // must be 512x512.
				'type' => 'image/png', // must be image/png
			);
		}

		return $icons_array;
	}


	// Get start url
	public static function imghaste_get_start_url($rel = false)
	{

		$options = Imghaste_Helpers::imghaste_get_options();

		if (isset($options['imghaste_field_pwa_start_url'])) {

			// Start Page
			$start_url = get_permalink($options['imghaste_field_pwa_start_url']);

			// Force HTTPS
			$start_url = Imghaste_Helpers::imghaste_httpsify($start_url);

			// AMP URL
			if (Imghaste_PWA::imghaste_is_amp() !== false && isset($options['start_url_amp']) && $options['start_url_amp'] == 1) {
				$start_url = trailingslashit($start_url) . Imghaste_PWA::imghaste_is_amp();
			}

			//Relative URL for manifest
			if ($rel === true) {
				// Make start_url relative for manifest
				$start_url = (parse_url($start_url, PHP_URL_PATH) == '') ? '/?source=imghaste' : parse_url($start_url, PHP_URL_PATH);
				return apply_filters('imghaste_manifest_start_url', $start_url);
			}
			return $start_url . '?source=imghaste';

		} else {
			return 0;
		}

	}

	// Get orientation of PWA
	function imghaste_get_orientation()
	{

		$options = Imghaste_Helpers::imghaste_get_options();

		$orientation = isset($options['imghaste_field_pwa_orientation']) ? $options['imghaste_field_pwa_orientation'] : 0;

		switch ($orientation) {

			case 0:
				return 'any';
				break;

			case 1:
				return 'portrait';
				break;

			case 2:
				return 'landscape';
				break;

			default:
				return 'any';
		}
	}


	// Get display of PWA
	function imghaste_get_display()
	{

		$options = Imghaste_Helpers::imghaste_get_options();

		$display = isset($options['imghaste_field_pwa_display']) ? $options['imghaste_field_pwa_display'] : 1;

		switch ($display) {

			case 0:
				return 'fullscreen';
				break;

			case 1:
				return 'standalone';
				break;

			case 2:
				return 'minimal-ui';
				break;

			case 3:
				return 'browser';
				break;

			default:
				return 'standalone';
		}
	}


}










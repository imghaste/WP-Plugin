<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.imghaste.com/
 * @since      1.0.0
 *
 * @package    Imghaste
 * @subpackage Imghaste/public
 * @author     ImgHaste <dev@imghaste.com>
 */

class Imghaste_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	protected $version;

	/**
	 * The file extensions supported by the CDN
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $file_extensions The file extensions supported by the CDN
	 */
	protected $file_extensions;

	/**
	 * The Name of service worker
	 * @since    1.0.5
	 * @access   private
	 * @var      string $sw_name The Name of service worker
	 */
	protected $sw_name;

	/**
	 * The filename of the Service Woker
	 * @since    1.0.2
	 * @access   private
	 * @var      string $scope_name The path under which  WP is running for example /blog/
	 */
	private $scope_name;

	/**
	 * Initialize the class and set its properties.
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 * @param $file_extensions
	 * @param $sw_name
	 * @since    1.0.0
	 */
	public function __construct($plugin_name, $version, $file_extensions, $sw_name)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->file_extensions = $file_extensions;
		$this->sw_name = $sw_name;
	}

	/**
	 * Rewrite Rule for Service Worker
	 *
	 * @since    1.0.0
	 */

	public function imghaste_sw_rewrite()
	{

		add_rewrite_rule('^/' . $this->sw_name . '$',
			'index.php?' . $this->sw_name . '=1',
			'top'
		);

	}

	/**
	 * Generate the Service Worker on the Fly
	 *
	 * @since    1.0.0
	 */
	public function imghaste_sw_generate($query)
	{

		if (!property_exists($query, 'query_vars') || !is_array($query->query_vars)) {
			return;
		}

		$query_vars_as_string = implode(',', $query->query_vars);

		//Check if sw_name in Query
		if (strpos($query_vars_as_string, $this->sw_name) !== false) {
			header('Content-Type: application/javascript');
			header('Service-Worker-Allowed: /');
			header('Cache-Control: max-age=3600');
			//Return SW as a JS file
			echo $this->imghaste_sw_template();
			exit();
		}

	}

	/**
	 * Generate a Client Hints Feature Policy
	 *
	 * @since    1.0.12
	 */
	public function imghaste_feature_policy_header($query)
	{

		$options = get_option('imghaste_options');
		$domain = $this->get_top_level_domain(get_site_url());
		$cdn_url = parse_url($options['imghaste_field_cdn_url'])['host'];


		$domains = ['imghaste.com', $domain, $cdn_url];
		$domains = array_unique($domains);
		$policies = ['width','downlink',];

		$feature_policy = "ch-width *;";

		header('Service-Worker-Allowed: /');
		header("Feature-Policy: {$feature_policy}");

	}

	protected function get_top_level_domain($domain){
		$tld = preg_replace('/.*\.([a-zA-Z]+)$/','$1',$domain);
		return trim(preg_replace('/.*(([\.\/][a-zA-Z]{2,}){'.((substr_count($domain, '.') <= 2 && mb_strlen( $tld) != 2) ? '2,3' : '3,4').'})/im','$1',$domain),'./');
	}

	/**
	 * Template to Create the Service Worker
	 *
	 * @since    1.0.0
	 */

	public function imghaste_sw_template()
	{
		//Get CDN from Settings
		$options = get_option('imghaste_options');
		//Print Service Worker
		ob_start();
		$IH_SW_URL = "{$options['imghaste_field_cdn_url']}service-worker.js";
		echo "self.importScripts('{$IH_SW_URL}');";
		return apply_filters('imghaste_sw_template', ob_get_clean());
	}


	/**
	 * Register the Service Worker
	 *
	 * @since    1.0.0
	 */

	public function imghaste_sw_register()
	{
		$i = date('i');
		$i = intval($i / 10);
		$d = date('Ymd-H-') . $i;
		wp_enqueue_script('imghaste-register-sw', "https://cdn.imghaste.com/sw/sdk.js?f={$this->sw_name}&pv=v{$this->version}-{$d}", [], null, true);
		//This is for local host Developement but you need SSL
		/*wp_enqueue_script( 'imghaste-register-sw', 'https://cdn.imghaste.com/sw/sdk.js?f='.$this->imghaste_get_base_folder().$this->sw_name.'&s='.$this->imghaste_get_base_folder(),array(), null, true );*/
	}


	/**
	 * Add meta in head for testing
	 *
	 * @since    1.0.0
	 */

	public function imghaste_accept_ch()
	{
		?>
		<meta http-equiv="Accept-CH" content="Width, Viewport-Width, DPR, Downlink, Save-Data, Device-Memory, RTT, ECT">
		<?php
	}

	/**
	 * Overide core function wp_get_attachment_url to get image from CDN.
	 * https://developer.wordpress.org/reference/functions/wp_get_attachment_url/
	 * @since    1.0.0
	 */
	public function imghaste_get_attachment_url($image_url)
	{
		if (is_admin()) {
			return $image_url;
		}
		//Replace URL
		return $this->imghaste_get_remote_image_url($image_url);
	}

	/**
	 * Overide core function wp_get_attachment_image_src to get image from CDN.
	 * https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/
	 * @since    1.0.0
	 */
	public function imghaste_get_attachment_image_src($image)
	{
		if (is_admin()) {
			return $image;
		}
		//Replace url
		$image[0] = $this->imghaste_get_remote_image_url($image[0]);
		return $image;
	}

	/**
	 * Overide core function wp_calculate_image_srcset to get image from CDN.
	 * https://developer.wordpress.org/reference/functions/wp_calculate_image_srcset/
	 * @since    1.0.0
	 */
	public function imghaste_calculate_image_srcset($sources)
	{
		if (is_admin()) {
			return $sources;
		}
		//Replace Sources
		foreach ($sources as &$source) {
			if (!file_exists($source['url'])) {
				$source['url'] = $this->imghaste_get_remote_image_url($source['url']);
			}
		}
		return $sources;
	}

	/**
	 * Apply Hook 'the_content' to replace all images sources from CDN
	 * https://developer.wordpress.org/reference/functions/the_content/
	 * @since    1.0.0
	 */
	public function imghaste_get_the_content($content)
	{

		if (is_admin() || empty($content)) {
			return $content;
		}

		//Load HTML to php
		$doc = new DOMDocument();
		libxml_use_internal_errors(true);
		@$doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES'));
		libxml_clear_errors();

		//Replace Image Src
		$images = $doc->getElementsByTagName('img');

		foreach ($images as $img) {
			$url = $img->getAttribute('src');
			$url = $this->imghaste_get_remote_image_url($url);
			$img->setAttribute('src', $url);
		}


		return $doc->saveHTML();
	}

	/**
	 * Asset function to get wordpress folder
	 *
	 * @since    1.0.0
	 */
	public function imghaste_get_base_folder()
	{
		$url = $this->imghaste_get_site_url();
		$base_url = str_replace($_SERVER['SERVER_NAME'], '', $url);
		$base_url = str_replace('https://', '', $base_url);
		$base_url = str_replace('http://', '', $base_url);
		return $base_url;
	}


	/**
	 * Asset function to get site url
	 *
	 * @since    1.0.0
	 */

	public function imghaste_get_site_url()
	{
		return get_site_url().'/';
	}


	/**
	 * Asset function for Image Haste Url
	 *
	 * @since    1.0.0
	 */
	public function imghaste_get_remote_image_url($image_url)
	{

		$root_site_url = $this->imghaste_get_site_url();
		$file_extensions = explode('|', $this->file_extensions);

		//Check if image is not hosted in the this domain
		$image_url_parsed = parse_url($image_url);
		$root_site_url_parsed = parse_url($root_site_url);
		if (!$image_url_parsed['host'] == $root_site_url_parsed['host']) {
			return $image_url;
		}

		//Check image if is accepted extensions
		$ext = pathinfo(
			parse_url($image_url, PHP_URL_PATH),
			PATHINFO_EXTENSION
		);
		if (!in_array($ext, $file_extensions)) {
			return $image_url;
		}

		//Get Options
		$options = get_option('imghaste_options');

		//Return Remote Url
		$new_image_url = str_replace($root_site_url, $options['imghaste_field_cdn_url'], $image_url);

		return $new_image_url;

	}


}

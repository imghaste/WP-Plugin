<?php

class Imghaste_Slimcss extends Imghaste_Public
{

	protected $cache_version = 1;

	public function __construct($plugin_name, $version, $file_extensions, $sw_name)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->file_extensions = $file_extensions;
		$this->sw_name = $sw_name;

		$options = get_option('imghaste_options');

		$this->cache_version = intval($options["imghaste_field_slimcss_purgeversion"] ?? '1');

		//Crate the Cache
		$this->cache = new SlimCache(SLIMCSS_CACHE_DIR, $this->cache_version);
	}

	/**
	 * Implements Slimcss funcionality
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	public function imghaste_slimcss()
	{

		//For now only for home page
		if (home_url($this->cleanUrl($_SERVER['REQUEST_URI'])) != get_home_url() . '/') {
			return;
		}

		//Init page style
		global $page_style;

		//Get Options
		$options = get_option('imghaste_options');

		//Make checks to see if slimm css should run
		$is_slim_css_active = '1';

		//Check if user is logged in
		if (is_user_logged_in()) {
			$is_slim_css_active = '0';
		}
		//Check Option again
		if (!isset($options["imghaste_field_slimcss"])) {
			$is_slim_css_active = '0';
		}
		if (isset($options["imghaste_field_slimcss"]) && is_null($options["imghaste_field_slimcss"])) {
			$is_slim_css_active = '0';
		}
		//Check force request from crawler
		if (isset($_GET["slimcss"]) && $_GET["slimcss"] == '0') {
			$is_slim_css_active = '0';
		}
		//Check force request for testing
		if (isset($_GET["slimcss"]) && $_GET["slimcss"] == '1') {
			$is_slim_css_active = '1';
		}


		//Check from wp-cron.php and disable slim css
		if (strpos($_SERVER['REQUEST_URI'], 'wp-cron.php') !== false) {
			$is_slim_css_active = '0';
		}
		//Check from xmlrpc.php and disable slim css
		if (strpos($_SERVER['REQUEST_URI'], 'xmlrpc.php') !== false) {
			$is_slim_css_active = '0';
		}
		//Check from image-service.ih.js and disable slim css
		if (strpos($_SERVER['REQUEST_URI'], 'image-service.ih.js') !== false) {
			$is_slim_css_active = '0';
		}
		//Check from wp-json api
		if (strpos($_SERVER['REQUEST_URI'], 'wp-json/v2') !== false) {
			$is_slim_css_active = '0';
		}
		//Check from manifest json
		if (strpos($_SERVER['REQUEST_URI'], 'manifest.json') !== false) {
			$is_slim_css_active = '0';
		}
		//Check for .xml files
		if (strpos($_SERVER['REQUEST_URI'], '.xml') !== false) {
			$is_slim_css_active = '0';
		}
		//Check for .jpg files
		if (strpos($_SERVER['REQUEST_URI'], '.jpg') !== false) {
			$is_slim_css_active = '0';
		}
		//Check for .png files
		if (strpos($_SERVER['REQUEST_URI'], '.png') !== false) {
			$is_slim_css_active = '0';
		}
		//Check for .js files
		if (strpos($_SERVER['REQUEST_URI'], '.js') !== false) {
			$is_slim_css_active = '0';
		}
		//Check for .js.map files
		if (strpos($_SERVER['REQUEST_URI'], '.js.map') !== false) {
			$is_slim_css_active = '0';
		}

		//If still active start the process
		if ('1' === $is_slim_css_active) {

			//Create CDN Url for Slimm CSS
			$cdn_url = $options["imghaste_field_cdn_url"];

			//Check if default cdn
			if (strpos($cdn_url, 'https://cdn.imghaste.com') !== false) {
				//Just change to cdnb
				$cdn_url = str_replace('https://cdn.imghaste.com/', 'https://cdnb.imghaste.com/', $cdn_url);
			} else {
				//Use the whole custom cdn
				$bucket_id = parse_url($cdn_url, PHP_URL_HOST);
				$cdn_url = "https://cdnb.imghaste.com/{$bucket_id}/";
			}

			$cdn_url .= "minimal/" . home_url($this->cleanUrl($_SERVER['REQUEST_URI']));

			//Add Get Variable for versioning
			$purge_version = (parse_url($cdn_url, PHP_URL_QUERY) ? '&' : '?') . "slimcss_purgeversion=" . intval($options["imghaste_field_slimcss_purgeversion"] ?? '1');
			$cdn_url .= urlencode($purge_version);

			//Get css from cache
			$results = $this->cache->get($cdn_url, 60);
			if (false === $results && $is_slim_css_active == 1) {
				//If not get css from CDN
				$results = $this->fetch_css($cdn_url);
			}
			//If returns nothing disable slim css
			if (is_string($results) && $this->isJson($results)) {
				$is_slim_css_active = '0';
			}
			if (false == $results) {
				$is_slim_css_active = '0';
			}
			$page_style = $results;

		}

		//Implement SlimCSS
		if ('1' === $is_slim_css_active) {
			//Add Slim CSS to header
			add_action('wp_head', 'page_style', 10);
			//Remove other styles from Header
			add_action('wp_print_styles', 'remove_all_styles', 100000);
			//Remove other styles from Footer
			add_action('wp_print_footer_scripts', 'remove_all_styles', 100000);
			//Use Buffer to remove styles
			$use_buffer = true;
			if (isset($options["imghaste_field_slimcss_buffer"])) {
				if ($options["imghaste_field_slimcss_buffer"] == "1") {
					$use_buffer = false;
				}
			}
			if ($use_buffer) {
				add_filter('template_redirect', 'imghaste_slime_css_buffer_start');
				add_filter('shutdown', 'imghaste_slime_css_buffer_end');
			}
		}

		//Asset to Append Header Style
		function page_style()
		{
			global $page_style;
			?>
			<style class="slim-css" type="text/css"><?php echo $page_style; ?></style>
			<?php
		}

		//Asset to Remove All Other Styles
		function remove_all_styles()
		{
			global $wp_styles;
			foreach ($wp_styles->queue as $style_handle) {
				$style = $wp_styles->registered[$style_handle];
				$remove_style = true;
				/*//If has font we keep
				if (strpos($style->src, 'font') != false || strpos($style->handle, 'font') != false) {
					$remove_style = false;
				}
				//If has icon we keep
				if (strpos($style->src, 'icon') != false || strpos($style->handle, 'icon') != false) {
					$remove_style = false;
				}*/
				//Keep only Google Fonts
				if (strpos($style->src, 'fonts.googleapis.com') != false) {
					$remove_style = false;
				}
				if ($remove_style == true) {
					wp_deregister_style($style_handle);
					wp_dequeue_style($style_handle);
				}
			}
		}

		//Buffer to remove styles
		function imghaste_slime_css_buffer_start()
		{
			ob_start('imghaste_slimcss_buffer_replace');
		}

		function imghaste_slime_css_buffer_end()
		{
			ob_end_flush();
		}

		function imghaste_slimcss_buffer_replace($content)
		{
			if (is_admin() || empty($content)) {
				return $content;
			}
			if (!class_exists('DOMDocument', false)) {
				return $content;
			}
			//Load HTML to php
			$doc = new DOMDocument(null, 'UTF-8');
			@$doc->loadHtml($content);
			//Remove Styles
			removeElementsByTagName('style', $doc);
			//Remove Links
			removeElementsByTagName('link', $doc);
			$doc->normalizeDocument();
			$buffered_content = @$doc->saveHTML($doc->documentElement);
			return $buffered_content;
		}

		//Buffer function to remove Styles and Links
		function removeElementsByTagName($tagName, $document)
		{
			$nodeList = $document->getElementsByTagName($tagName);
			for ($nodeIdx = $nodeList->length; --$nodeIdx >= 0;) {
				$node = $nodeList->item($nodeIdx);
				$remove_item = false;
				//Make checks if link
				if ($tagName == 'link') {
					//Remove if stylesheet
					if ('stylesheet' == $node->getAttribute('rel')) {
						$remove_item = true;
						//Except only Google API
						if (strpos($node->getAttribute('href'), 'fonts.googleapis.com') != false) {
							$remove_item = false;
						}
					}
				}
				//Remove all styles
				if ($tagName == 'style') {
					$remove_item = true;
					//Except Us!
					if ('slim-css' == $node->getAttribute('class')) {
						$remove_item = false;
					}
				}
				if ($remove_item) {
					$node->parentNode->removeChild($node);
				}
			}
		}
	}

	//Get CSS from CDN
	private function fetch_css($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		$response = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = $this->get_headers_from_curl_response($response);

		$body = substr($response, $header_size);

		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
			$body = 'false';
		}
		curl_close($ch);
		$this->cache->set($url, $body);

		return $body;

	}

	//Get headers
	private function get_headers_from_curl_response($response)
	{
		$headers = [];
		$header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
		foreach (explode("\r\n", $header_text) as $i => $line) {
			if ($i === 0) {
				$headers['http_code'] = $line;
			} else {
				list ($key, $value) = explode(': ', $line);
				$headers[$key] = $value;
			}
		}
		return $headers;
	}

	//Check for JSON response
	private function isJson($string)
	{
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	//Clean url for CDN request
	private function cleanUrl($url)
	{
		list($urlpart, $qspart) = array_pad(explode('?', $url), 2, '');
		parse_str($qspart, $qsvars);
		//Remove FB Reference
		unset($qsvars['fbclid']);
		if (empty($qsvars)) {
			return $urlpart;
		} else {
			$newqs = http_build_query($qsvars);
			return $urlpart . '?' . $newqs;
		}
	}

}


interface ISlimCache
{
	public function get($key, $ttl);

	public function set($key, $val);
}

class SlimCache implements ISlimCache
{

	protected $dir;
	protected $prefix;

	public function __construct($dir, $prefix = 7)
	{
		$this->prefix = $prefix;
		$this->dir = $dir;
		if (!file_exists($this->dir)) {
			mkdir($this->dir, 0755, true);
		}
	}

	/**
	 * @param $key
	 * @return string
	 */
	protected function getFileName($key)
	{
		return $this->dir . "$this->prefix." . md5($key);
	}


	/**
	 * @param $key
	 * @param int $ttl
	 * @return bool|false|string
	 */
	public function get($key, $ttl = 0)
	{
		$key = $this->getFileName($key);
		if (!file_exists($key)) return false;
		if (!is_readable($key)) return false;
		if (filesize($key) > 1024) $ttl *= 32200;

		$filemtime = filemtime($key);
		if (time() - $filemtime >= $ttl) {
			$this->delete($key);
			return false;
		}
		$data = @file_get_contents($key);
		if (!$data) {
			$this->delete($key);
			return false;
		};
		return $data;
	}

	/**
	 * @param $key
	 * @param $val
	 * @return bool
	 */
	public function set($key, $val)
	{
		$key = $this->getFileName($key);
		$h = fopen($key, 'w');
		if (!$h) return false;
		if (fwrite($h, $val) === false) return false;
		fclose($h);
		return true;
	}


	/**
	 * @param $key
	 * @return bool
	 */
	protected function delete($key)
	{
		if (file_exists($key)) unlink($key);
		return true;
	}

}

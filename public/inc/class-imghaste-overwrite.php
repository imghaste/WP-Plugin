<?php

/**
* We Overwrite here core WordPress functions to work along with the Imghaste_Buffer
*
* @since    1.0.0
* @access   private
*/

class Imghaste_Overwrite extends Imghaste_Public
{

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

}
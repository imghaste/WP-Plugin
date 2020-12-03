<?php

class Imghaste_Buffer extends Imghaste_Public
{

	public function imghaste_buffer_start()
	{
		ob_start(array($this, 'imghaste_buffer_replace'));
	}

	public function imghaste_buffer_end()
	{
		ob_end_flush();
	}

	public function imghaste_buffer_replace($content)
	{

		if (is_admin() || empty($content)) {
			return $content;
		}

		if (!class_exists('DOMDocument', false)) {
			return $content;
		}

		//Get Options
		$options = get_option('imghaste_options');


		//Load HTML to php
		$doc = new DOMDocument(null, 'UTF-8');
		@$doc->loadHtml($content);


		//Replace Attrs in Image Tag
		$images = $doc->getElementsByTagName('img');
		foreach ($images as $img) {
			//Replace Img Src
			$url = $img->getAttribute('src');
			$url = $this->imghaste_get_remote_image_url($url);
			$img->setAttribute('src', $url);
			//Replace Img Srcset
			$srcset = $img->getAttribute('srcset');
			$srcset = $this->imghaste_get_remote_image_url($srcset);
			$img->setAttribute('srcset', $srcset);
		}

		//Replace Atts in Picture Tag
		$pictures = $doc->getElementsByTagName('picture');
		foreach ($pictures as $picture) {
			//Replace Src in Sources
			$sources = $picture->getElementsByTagName('source');
			foreach ($sources as $source) {
				$srcset = $source->getAttribute('srcset');
				$srcset = $this->imghaste_get_remote_image_url($srcset);
				$source->setAttribute('srcset', $srcset);
			}
		}

		//Returned Buffered Content
		$doc->normalizeDocument();
		$buffered_content = @$doc->saveHTML( $doc->documentElement );

		//Replace Style Background Url
		$buffered_content = str_replace('background-image: url(' . $this->imghaste_get_site_url(), 'background-image: url(' . $options['imghaste_field_cdn_url'], $buffered_content);

		return $buffered_content;

	}

}

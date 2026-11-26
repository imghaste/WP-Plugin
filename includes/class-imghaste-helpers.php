<?php
class Imghaste_Helpers{


	/**
 	* Asset function to check if on localhost
 	*
 	* @since   1.0.5
 	*
 	* @return  boolean
 	*/
 	public static function imghaste_is_localhost(){
 		$localhost_list = array('127.0.0.1', '::1');
 		if (in_array($_SERVER['REMOTE_ADDR'], $localhost_list)) {
 			return true;
 		} else {
 			return false;
 		}
 	}

	/**
 	* Asset function get PWA options
 	*
 	* @since   1.1.2
 	*
 	* @return  array
 	*/
 	public static function imghaste_get_options() {

 		$defaults = array(
 			'imghaste_field_pwa_appname'			=> get_bloginfo( 'name' ),
 			'imghaste_field_pwa_short_appname'	=> substr( get_bloginfo( 'name' ), 0, 15 ),
 			'imghaste_field_pwa_description'		=> get_bloginfo( 'description' ),
 			'imghaste_field_pwa_app_icon'				=> IMGHASTE_PATH_SRC . 'public/images/logo.png',
 			'imghaste_field_pwa_splash_screen_icon'		=> IMGHASTE_PATH_SRC . 'public/images/logo-512x512.png',
 			'imghaste_field_pwa_background_color' 	=> '#D5E0EB',
 			'imghaste_field_pwa_theme_color' 		=> '#D5E0EB',
 			'imghaste_field_pwa_start_url' 		=> 0,
 			'start_url_amp'		=> 0,
 			'imghaste_field_pwa_offline_page' 		=> 0,
 			'imghaste_field_pwa_orientation'		=> 1,
 			'imghaste_field_pwa_display'			=> 1,
 			'is_static_manifest'=> 0,
 			'is_static_sw'		=> 0,
 			'disable_add_to_home'=> 0,
 		);

 		$options = get_option('imghaste_options', $defaults);

 		return $options;
 	}

	/**
 	* Asset function turn url to https
 	*
 	* @since   1.1.2
 	*
 	* @return  array
 	*/

 	public static function imghaste_httpsify( $url ) {
 		return str_replace( 'http://', 'https://', $url );
 	}


 }

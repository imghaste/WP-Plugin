<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function imghaste_get_options() {

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

// Check if any AMP plugin is installed

function imghaste_is_amp() {
	
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}

	// AMP for WordPress - https://wordpress.org/plugins/amp
	if ( is_plugin_active( 'amp/amp.php' ) ) {
		return defined( 'AMP_QUERY_VAR' ) ? AMP_QUERY_VAR . '/' : 'amp/';
	}

	// AMP for WP - https://wordpress.org/plugins/accelerated-mobile-pages/
	if ( is_plugin_active( 'accelerated-mobile-pages/accelerated-moblie-pages.php' ) ) {
		return defined( 'AMPFORWP_AMP_QUERY_VAR' ) ? AMPFORWP_AMP_QUERY_VAR . '/' : 'amp/';
	}

	// Better AMP - https://wordpress.org/plugins/better-amp/
	if ( is_plugin_active( 'better-amp/better-amp.php' ) ) {
		return 'amp/';
	}

	// AMP Supremacy - https://wordpress.org/plugins/amp-supremacy/
	if ( is_plugin_active( 'amp-supremacy/amp-supremacy.php' ) ) {
		return 'amp/';
	}

	// WP AMP - https://wordpress.org/plugins/wp-amp-ninja/
	if ( is_plugin_active( 'wp-amp-ninja/wp-amp-ninja.php' ) ) {
		return '?wpamp';
	}

	// tagDiv AMP - http://forum.tagdiv.com/tagdiv-amp/
	if ( is_plugin_active( 'td-amp/td-amp.php' ) ) {
		return defined( 'AMP_QUERY_VAR' ) ? AMP_QUERY_VAR . '/' : 'amp/';
	}

	return false;
}

// Return Start Page URL

function imghaste_get_start_url( $rel = false ) {
	
	// Get options
	$options = imghaste_get_options();
	
	// Start Page
	$start_url = get_permalink( $options['imghaste_field_pwa_start_url'] ) ? get_permalink( $options['imghaste_field_pwa_start_url'] ) : imghaste_get_bloginfo( 'sw' );
	
	// Force HTTPS
	$start_url = imghaste_httpsify( $start_url );
	
	// AMP URL
	if ( imghaste_is_amp() !== false && isset( $options['start_url_amp'] ) && $options['start_url_amp'] == 1 ) {
		$start_url = trailingslashit( $start_url ) . imghaste_is_amp();
	}
	
	// Relative URL for manifest
	if ( $rel === true ) {
		
		// Make start_url relative for manifest
		$start_url = ( parse_url( $start_url, PHP_URL_PATH ) == '' ) ? '.' : parse_url( $start_url, PHP_URL_PATH );
		
		return apply_filters( 'imghaste_manifest_start_url', $start_url );
	}
	
	return $start_url;
}

// Get offline page

function imghaste_get_offline_page() {
	
	// Get options
	$options = imghaste_get_options();

	if (isset($options['imghaste_field_pwa_offline_page'])) {
		$offline_page = get_permalink( $options['imghaste_field_pwa_offline_page'] ) ? imghaste_httpsify( get_permalink( $options['imghaste_field_pwa_offline_page'] ) ) : imghaste_httpsify( imghaste_get_bloginfo( 'sw' ) );

		return $offline_page;
	}
	
}


// Convert http URL to https

function imghaste_httpsify( $url ) {
	return str_replace( 'http://', 'https://', $url );
}



// Check if PWA is ready
// Check for HTTPS.
// Check if manifest is generated.

function imghaste_is_pwa_ready() {
	
	if ( 
		is_ssl() && 
		imghaste_file_exists( imghaste_manifest( 'src' ) ) && 
		imghaste_file_exists( imghaste_sw( 'src' ) ) 
	) {
		return apply_filters( 'imghaste_is_pwa_ready', true );
	}
	
	return false; 
}



// Check if file exists
// Not to be confused with file_exists PHP function. 
// In ImgHaste context, file exists if the response code is 200.

function imghaste_file_exists( $file ) {
	
	$response 		= wp_remote_head( $file, array( 'sslverify' => false ) );
	$response_code 	= wp_remote_retrieve_response_code( $response );
	
	if ( 200 === $response_code ) {
		return true;
	}
	
	return false;
}

// Check if manifest is static or dynamic

function imghaste_is_static( $file = 'manifest' ) {

	// Get options
	$options = imghaste_get_options();

	$options['is_static_sw'] = 0;

	switch ( $file ) {

		case 'sw':

		if ( $options['is_static_sw'] === 1 ) {
			return true;
		}

		return false;
		break;

		case 'manifest':
		default: 

		if ( $options['is_static_manifest'] === 1 ) {
			return true;
		}

		return false;
		break;
	}
}

// Returns WordPress URL v/s Site URL depending on the status of the file.  
// Static files are generated in the root directory of WordPress. So if static 
// files are used, the WordPress URL will be needed for many use cases, like
// offline page, start_url etc.

function imghaste_get_bloginfo( $file = 'sw' ) {
	
	if ( imghaste_is_static( $file ) ) {
		return get_bloginfo( 'wpurl' );
	}
	
	return get_bloginfo( 'url' );
}


// Returns the Manifest filename.

function imghaste_get_manifest_filename() {
	return 'imghaste-manifest' . imghaste_multisite_filename_postfix() . '.webmanifest';
}



// Manifest filename, absolute path and link
// For Multisite compatibility. Used to be constants defined in imghaste.php
// On a multisite, each sub-site needs a different manifest file.

function imghaste_manifest( $arg = 'src' ) {

	$manifest_filename = imghaste_get_manifest_filename();

	switch ( $arg ) {
		// TODO: Case `filename` can be deprecated in favor of @see imghaste_get_manifest_filename().
		// Name of Manifest file
		case 'filename':
		return $manifest_filename;
		break;

		case 'abs':
		$filepath = trailingslashit( ABSPATH ) . $manifest_filename;
		if(!file_exists($filepath)){
			$filepath = trailingslashit( ABSPATH ). $manifest_filename;
		}
		return $filepath;
		break;

		// Link to manifest
		case 'src':
		default:
		
			// Get options
		$options = imghaste_get_options();

		$options['is_static_manifest'] = 0;
			/**
			 * For static file, return site_url and network_site_url
			 * 
			 * Static files are generated in the root directory. 
			 * The site_url template tag retrieves the site url for the 
			 * current site (where the WordPress core files reside).
			 */
			if ( $options['is_static_manifest'] === 1 ) {
				return trailingslashit( network_site_url() ) . $manifest_filename;
			}
			
			// For dynamic files, return the home_url
			return home_url( '/' ) . $manifest_filename;
			
			break;
		}
	}


// Returns the Manifest template.

	function imghaste_manifest_template() {

	// Get options
		$options = imghaste_get_options();

		$manifest               = array();
		$manifest['name']       = $options['imghaste_field_pwa_appname'];
		$manifest['short_name'] = $options['imghaste_field_pwa_short_appname'];

	// Description
		if ( isset( $options['imghaste_field_pwa_description'] ) && ! empty( $options['imghaste_field_pwa_description'] ) ) {
			$manifest['description'] = $options['imghaste_field_pwa_description'];
		}

		$manifest['icons']            = imghaste_get_pwa_icons();
		$manifest['background_color'] = $options['imghaste_field_pwa_background_color'];
		$manifest['theme_color']      = $options['imghaste_field_pwa_theme_color'];
		$manifest['display']          = imghaste_get_display();
		$manifest['orientation']      = imghaste_get_orientation();
		$manifest['start_url']        = strlen( imghaste_get_start_url( true ) )>2?user_trailingslashit(imghaste_get_start_url( true )) : imghaste_get_start_url( true );
		$manifest['scope']            = strlen(imghaste_get_scope())>2? user_trailingslashit(imghaste_get_scope()) : imghaste_get_scope();

	/**
	 * Values that go in to Manifest JSON.
	 *
	 * The Web app manifest is a simple JSON file that tells the browser about your web application.
	 *
	 * @param array $manifest
	 */
	return apply_filters( 'imghaste_manifest', $manifest );
}

// Generate and write manifest into WordPress root folder

function imghaste_generate_manifest() {
	
	// Delete manifest if it exists.
	imghaste_delete_manifest();
	
	// Get options
	$options = imghaste_get_options();
	
	// Return true if dynamic file returns a 200 response.
	if ( imghaste_file_exists( home_url( '/' ) . imghaste_get_manifest_filename() ) && defined( 'WP_CACHE' ) && ! WP_CACHE ) {
		
		// set file status as dynamic file in database.
		$options['is_static_manifest'] = 0;
		
		// Write options back to database.
		update_option( 'imghaste_options', $options );
		
		return true;
	}
	
	// Write the manfiest to disk.
	if ( imghaste_put_contents( imghaste_manifest( 'abs' ),  json_encode(imghaste_manifest_template()) ) ) {
		
		// set file status as satic file in database.
		$options['is_static_manifest'] = 1;
		
		// Write options back to database.
		update_option( 'imghaste_options', $options );

		return true;
	}
	
	return false;
}


// Add manifest to header (wp_head)

function imghaste_add_manifest_to_wp_head() {
	
	$tags  = '<!-- Manifest added by ImgHaste -->' . PHP_EOL; 
	$tags .= '<link rel="manifest" href="'. parse_url( imghaste_manifest( 'src' ), PHP_URL_PATH ) . '">' . PHP_EOL;
	
	// theme-color meta tag 
	if ( apply_filters( 'imghaste_add_theme_color', true ) ) {
		
		// Get options
		$options = imghaste_get_options();
		$tags .= '<meta name="theme-color" content="'. $options['imghaste_field_pwa_theme_color'] .'">' . PHP_EOL;
	}
	
	$tags  = apply_filters( 'imghaste_wp_head_tags', $tags );
	
	$tags .= '<!-- / imghaste.com -->' . PHP_EOL; 
	
	echo $tags;
}
add_action( 'wp_head', 'imghaste_add_manifest_to_wp_head', 0 );


// Delete manifest

function imghaste_delete_manifest() {
	return imghaste_delete( imghaste_manifest( 'abs' ) );
}

// Get PWA Icons

function imghaste_get_pwa_icons() {
	
	// Get options
	$options = imghaste_get_options();	
	// Application icon
	$icons_array[] = array(
		'src' 	=> $options['imghaste_field_pwa_app_icon'],
							'sizes'	=> '192x192', // must be 192x192. Todo: use getimagesize($options['icon'])[0].'x'.getimagesize($options['icon'])[1] in the future
							'type'	=> 'image/png', // must be image/png. Todo: use getimagesize($options['icon'])['mime']
							//'purpose'=> 'any maskable', // any maskable to support adaptive icons
						);
	
	// Splash screen icon
	if ( @$options['imghaste_field_pwa_splash_screen_icon'] != '' ) {
		
		$icons_array[] = array(
			'src' 	=> $options['imghaste_field_pwa_splash_screen_icon'],
							'sizes'	=> '512x512', // must be 512x512.
							'type'	=> 'image/png', // must be image/png
						);
	}
	
	return $icons_array;
}

// Get navigation scope of PWA

function imghaste_get_scope() {
	return parse_url( trailingslashit( imghaste_get_bloginfo( 'sw' ) ), PHP_URL_PATH );
}


// Get orientation of PWA

function imghaste_get_orientation() {

	// Get options
	$options = imghaste_get_options();

	$orientation = isset( $options['imghaste_field_pwa_orientation'] ) ? $options['imghaste_field_pwa_orientation'] : 0;

	switch ( $orientation ) {

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

function imghaste_get_display() {

	// Get options
	$options = imghaste_get_options();

	$display = isset( $options['imghaste_field_pwa_display'] ) ? $options['imghaste_field_pwa_display'] : 1;

	switch ( $display ) {

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

// Initialize the WP filesystem

function imghaste_wp_filesystem_init() {
	
	global $wp_filesystem;
	
	if ( empty( $wp_filesystem ) ) {
		require_once( trailingslashit( ABSPATH ) . 'wp-admin/includes/file.php' );
		WP_Filesystem();
	}
}

// Write to a file using WP_Filesystem() functions

function imghaste_put_contents( $file, $content = null ) {
	
	// Return false if no filename is provided
	if ( empty( $file ) ) {
		return false;
	}
	
	// Initialize the WP filesystem
	imghaste_wp_filesystem_init();
	global $wp_filesystem;
	
	if( ! $wp_filesystem->put_contents( $file, $content, 0644) ) {
		return false;
	}
	
	return true;
}


// Read contents of a file using WP_Filesystem() functions

function imghaste_get_contents( $file, $array = false ) {
	
	// Return false if no filename is provided
	if ( empty( $file ) ) {
		return false;
	}
	
	// Initialize the WP filesystem
	imghaste_wp_filesystem_init();
	global $wp_filesystem;
	
	// Reads entire file into a string
	if ( $array == false ) {
		return $wp_filesystem->get_contents( $file );
	}
	
	// Reads entire file into an array
	return $wp_filesystem->get_contents_array( $file );
}

// Delete a file

function imghaste_delete( $file ) {
	
	// Return false if no filename is provided
	if ( empty( $file ) ) {
		return false;
	}
	
	// Initialize the WP filesystem
	imghaste_wp_filesystem_init();
	global $wp_filesystem;
	
	return $wp_filesystem->delete( $file );
}

// Multisite

// Filename postfix for multisites

function imghaste_multisite_filename_postfix() {
	
	// Return empty string if not a multisite
	if ( ! is_multisite() ) {
		return '';
	}
	
	return '-' . get_current_blog_id();
}

// Save activation status for current blog id
// For clean multisite uninstall. 
// Manifest deleted during deactivation. 
// Database options are cleaned during uninstall

function imghaste_multisite_activation_status( $status ) {
	
	// Only for multisites
	if ( ! is_multisite() || ! isset( $status ) ) {
		return;
	}
	
	// Get current list of sites where ImgHaste is activated.
	$imghaste_sites = get_site_option( 'imghaste_active_sites', array() );
	
	// Set the status for the current blog.
	$imghaste_sites[ get_current_blog_id() ] = $status;
	
	// Save it back to the database.
	update_site_option( 'imghaste_active_sites', $imghaste_sites );
}

// Handle multisite network deactivation

// Deletes manifest of all sub-sites.
// Sets the deactivation status for each site.

// Not used when wp_is_large_network() is true. Deleting that many files and db options will most likely time out. 
// This also this gives the user an option to decide if ImgHaste should handle this by changing the defenition of wp_is_large_network.
function imghaste_multisite_network_deactivator() {
	
	// Do not run on large networks
	if ( wp_is_large_network() ) {
		return;
	}
	
	// Retrieve the list of blog ids where ImgHaste is active. (saved with blog_id as $key and activation_status as $value)
	$imghaste_sites = get_site_option( 'imghaste_active_sites' );
	
	// Loop through each active site.
	foreach( $imghaste_sites as $blog_id => $actviation_status ) {
		
		// Switch to each blog
		switch_to_blog( $blog_id );

		// Delete manifest
		imghaste_delete_manifest();
		
		/**
		 * Delete ImgHaste version info for current blog.
		 * 
		 * This is required so that imghaste_upgrader() will run and create the manifest on next activation.
		 * Known edge case: Database upgrade that relies on the version number will fail if user deactivates and later activates after ImgHaste is updated.
		 */
		delete_option( 'imghaste_version' );

		// Save the de-activation status of current blog.
		imghaste_multisite_activation_status( false );
		
		// Return to main site
		restore_current_blog();
	}
}
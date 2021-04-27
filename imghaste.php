<?php

/**
 *
 * @link              https://www.imghaste.com/
 * @since             1.0.0
 * @package           Imghaste
 *
 * @wordpress-plugin
 * Plugin Name:       imghaste
 * Plugin URI:        imghaste
 * Description:       Get your images run with the speed of light all over the world.
 * Version:           1.1.1
 * Author:            IMGHaste
 * Author URI:        https://www.imghaste.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       imghaste
 * Domain Path:       /languages
 */

/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
	die;
}

/**
 * Current plugin version.
 */
define('IMGHASTE_VERSION', '1.1.1');

/**
 * Absolute path to the plugin directory. 
 */
if ( ! defined( 'IMGHASTE_PATH_ABS' ) ) {
	define( 'IMGHASTE_PATH_ABS'	, plugin_dir_path( __FILE__ ) ); 
}

/**
 * IMG Haste Plugin Path
 */
if ( ! defined( 'IMGHASTE_PATH_SRC' ) ) {
	define( 'IMGHASTE_PATH_SRC'	, plugin_dir_url( __FILE__ ) ); 
}

/**
 * Full path to the plugin file. 
 */
if ( ! defined( 'IMGHASTE_PLUGIN_FILE' ) ) {
	define( 'IMGHASTE_PLUGIN_FILE', __FILE__ ); 
}

/**
 * Caching Directory for SlimCSS
 */
define('SLIMCSS_CACHE_DIR', WP_CONTENT_DIR . "/cache/slimcss/");

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-imghaste-activator.php
 */
function activate_imghaste()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-imghaste-activator.php';
	Imghaste_Activator::activate();
}

/**
 * Redirect to Imghaste UI on plugin activation.
 *
 * Will redirect to Imghaste settings page when plugin is activated.
 * Will not redirect if multiple plugins are activated at the same time.
 * Will not redirect when activated network wide on multisite. Network admins know their way.
 */

function imghaste_activation_redirect( $plugin, $network_wide ) {
	
	// Return if not Imghaste or if plugin is activated network wide.
	if ( $plugin !== plugin_basename( IMGHASTE_PLUGIN_FILE ) || $network_wide === true ) {
		return false;
	}
	
	if ( ! class_exists( 'WP_Plugins_List_Table' ) ) {
		return false;
	}

	/**
	 * An instance of the WP_Plugins_List_Table class.
	 *
	 * @link https://core.trac.wordpress.org/browser/tags/4.9.8/src/wp-admin/plugins.php#L15
	 */
	$wp_list_table_instance = new WP_Plugins_List_Table();
	$current_action         = $wp_list_table_instance->current_action();

	// When only one plugin is activated, the current_action() method will return activate.
	if ( $current_action !== 'activate' ) {
		return false;
	}

	// Redirect to Imghaste settings page. 
	exit( wp_redirect( admin_url( 'admin.php?page=imghaste' ) ) );
}
add_action( 'activated_plugin', 'imghaste_activation_redirect', PHP_INT_MAX, 2 );


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-imghaste-deactivator.php
 */
function deactivate_imghaste()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-imghaste-deactivator.php';
	Imghaste_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_imghaste');
register_deactivation_hook(__FILE__, 'deactivate_imghaste');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'class-imghaste.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_imghaste()
{

	$plugin = new Imghaste();
	$plugin->run();

}

run_imghaste();

/**
 * Print direct link to plugin settings in plugins list in admin
 *
 */

function imghaste_settings_link( $links ) {

	return array_merge(
		array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=imghaste' ) . '">' . __( 'Settings', 'imghaste' ) . '</a>'
		),
		$links
	);
}
add_filter( 'plugin_action_links_' . plugin_basename( IMGHASTE_PLUGIN_FILE ), 'imghaste_settings_link' );


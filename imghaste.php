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
 * Version:           1.1.0
 * Author:            IMGHaste
 * Author URI:        https://www.imghaste.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       imghaste
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Current plugin version.
 */
define('IMGHASTE_VERSION', '1.1.0');


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

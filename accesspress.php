<?php

/**
 * AccessPress - A WordPress Plugin
 *
 * This is the main plugin file for the AccessPress WordPress plugin. It contains the plugin metadata and initializes the plugin by including necessary files and setting up activation and deactivation hooks.
 *
 * Plugin Name:       AccessPress
 * Plugin URI:        https://trilb.dev/collection/web-extension/wordpress/accesspress
 * Description:       AccessPress is a WordPress plugin.
 * Author:            MrTrilB
 * Author URI:        https://trilb.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       accesspress
 * Version:           1.0.0
 * Domain Path:       src/languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ACCESSPRESS_VERSION', '1.0.0' );
define( 'ACCESSPRESS_NAME', 'accesspress' );
define( 'ACCESSPRESS_DEFAULT_LANGUAGE', 'en_GB' );
define( 'ACCESSPRESS_FILE', __FILE__ );
define( 'ACCESSPRESS_DIR', plugin_dir_path( __FILE__ ) );
define( 'ACCESSPRESS_URL', plugin_dir_url( __FILE__ ) );
define( 'ACCESSPRESS_BASENAME', plugin_basename( __FILE__ ) );
define( 'ACCESSPRESS_ROOT', ACCESSPRESS_DIR );
define( 'ACCESSPRESS_ROOT_URL', ACCESSPRESS_URL );
define( 'ACCESSPRESS_API', ACCESSPRESS_DIR . 'src/API' );
define( 'ACCESSPRESS_ASSETS', ACCESSPRESS_DIR . 'src/Assets' );
define( 'ACCESSPRESS_ASSETS_URL', ACCESSPRESS_URL . 'src/Assets' );
define( 'ACCESSPRESS_ADMIN', ACCESSPRESS_DIR . 'src/Admin' );
define( 'ACCESSPRESS_ADMIN_URL', ACCESSPRESS_URL . 'src/Admin' );
define( 'ACCESSPRESS_LANGUAGES', ACCESSPRESS_DIR . 'src/languages' );
define( 'ACCESSPRESS_INCLUDES', ACCESSPRESS_DIR . 'src/Includes' );
define( 'ACCESSPRESS_CORE', ACCESSPRESS_INCLUDES . '/Core' );
define( 'ACCESSPRESS_SETTINGS', ACCESSPRESS_INCLUDES . '/Settings' );
define( 'ACCESSPRESS_PLUGINS', ACCESSPRESS_INCLUDES . '/Plugins' );
define( 'ACCESSPRESS_PLUGINS_URL', ACCESSPRESS_URL . 'src/Includes/Plugins' );

$accesspress_autoloader = ACCESSPRESS_DIR . 'vendor/autoload.php';
if ( is_readable( $accesspress_autoloader ) ) {
	require_once $accesspress_autoloader;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-accesspress-activator.php
 */
function activate_accesspress() {
	\AccessPress\Includes\Core\WP\Activator::activate();
}

register_activation_hook( __FILE__, 'activate_accesspress' );
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-accesspress-deactivator.php
 */
function deactivate_accesspress() {
	\AccessPress\Includes\Core\WP\Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_accesspress' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once ACCESSPRESS_DIR . 'src/AccessPress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_accesspress() {

	$plugin = new \AccessPress\AccessPress( ACCESSPRESS_FILE, ACCESSPRESS_NAME, ACCESSPRESS_VERSION );
	$plugin->run();

}
run_accesspress();





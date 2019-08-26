<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wooninjas.com
 * @since             1.0.0
 * @package           Woo_Ld_Customization
 *
 * @wordpress-plugin
 * Plugin Name:       LearnDash Customization By WooNinjas
 * Plugin URI:        https://wooninjas.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            WooNinjas
 * Author URI:        https://wooninjas.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-ld-customization
 * Domain Path:       /languages
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
define( 'WOO_LD_CUSTOMIZATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-ld-customization-activator.php
 */
function activate_woo_ld_customization() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-ld-customization-activator.php';
	Woo_Ld_Customization_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-ld-customization-deactivator.php
 */
function deactivate_woo_ld_customization() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-ld-customization-deactivator.php';
	Woo_Ld_Customization_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_ld_customization' );
register_deactivation_hook( __FILE__, 'deactivate_woo_ld_customization' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-ld-customization.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_ld_customization() {

	$plugin = new Woo_Ld_Customization();
	$plugin->run();

}
run_woo_ld_customization();

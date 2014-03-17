<?php
/**
 * @package   NerveTask
 * @author    Patrick Daly <patrick@developdaly.com>
 * @license   GPL-2.0+
 * @link      http://nervetask.com
 * @copyright 2014 NerveTask
 *
 * @wordpress-plugin
 * Plugin Name:       NerveTask
 * Plugin URI:        http://nervetask.com
 * Description:       Project management for WordPress.
 * Version:           0.1.0
 * Author:            Patrick Daly
 * Author URI:        http://developdaly.com
 * Text Domain:       nervetask-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nervetask/nervetask
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-nervetask.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/includes/class-task.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/includes/class-widgets.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/includes/shortcodes.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/includes/class-status-colors.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'NerveTask', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'NerveTask', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'NerveTask', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'NerveTask_Task', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'NerveTask_Status_Colors', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * If Ajax is needed within the dashboard, change the following conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-nervetask-admin.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/includes/class-settings.php' );

	add_action( 'plugins_loaded', array( 'NerveTask_Admin', 'get_instance' ) );
	add_action( 'plugins_loaded', array( 'NerveTask_Settings', 'get_instance' ) );

}

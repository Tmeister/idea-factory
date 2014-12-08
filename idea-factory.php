<?php
/**
 *
 * @package   Idea_Factory
 * @author    Nick Haskins <email@nickhaskins.com>
 * @license   GPL-2.0+
 * @link      http://nickhaskins.com
 * @copyright 2015 Your Mom
 *
 * Plugin Name:       Idea Factory
 * Plugin URI:        http://nickhaskins.com
 * Description:       Creates a user submission and voting system for site features
 * Version:           0.1
 * GitLab Plugin URL: https://gitlab.com/bearded-avenger/idea-factory
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Set some constants
define('IDEA_FACTORY_VERSION', '0.0.1');
define('IDEA_FACTORY_DIR', plugin_dir_path( __FILE__ ));
define('IDEA_FACTORY_URL', plugins_url( '', __FILE__ ));
/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once( plugin_dir_path( __FILE__ ) . 'public/class-idea-factory.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'Idea_Factory', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Idea_Factory', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Idea_Factory', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-idea-factory-admin.php' );
	add_action( 'plugins_loaded', array( 'Idea_Factory_Admin', 'get_instance' ) );

}

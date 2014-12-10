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
 * Plugin URI:        http://nickhaskins.com/idea-factory
 * Description:       Front-end user submission and voting system.
 * Version:           0.99
 * Github Plugin URL: https://github.com/bearded-avenger/idea-factory
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Set some constants
define('IDEA_FACTORY_VERSION', '0.99');
define('IDEA_FACTORY_DIR', plugin_dir_path( __FILE__ ));
define('IDEA_FACTORY_URL', plugins_url( '', __FILE__ ));

require_once( plugin_dir_path( __FILE__ ) . 'public/class-idea-factory.php' );


register_activation_hook( __FILE__, array( 'Idea_Factory', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Idea_Factory', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Idea_Factory', 'get_instance' ) );

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-idea-factory-admin.php' );
	add_action( 'plugins_loaded', array( 'Idea_Factory_Admin', 'get_instance' ) );

}

<?php
/**
 * Idea Factory
 *
 * @package   Idea_Factory
 * @author    Nick Haskins <email@nickhaskins.com>
 * @license   GPL-2.0+
 * @link      http://nickhaskins.com
 * @copyright 2015 Your Mom
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 *
 * @package Idea_Factory
 * @author  Nick Haskins <email@nickhaskins.com>
 */
class Idea_Factory {

	/**
	 * Unique identifier
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'idea-factory';

	/**
	 * Instance of this class.
	 *
	 * @since    0.0.1
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     0.0.1
	 */
	private function __construct() {

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		require_once(IDEA_FACTORY_DIR.'/includes/class.type.php');
		require_once(IDEA_FACTORY_DIR.'/includes/class.process-entry.php');
		require_once(IDEA_FACTORY_DIR.'/includes/class.process-vote.php');
		require_once(IDEA_FACTORY_DIR.'/includes/class.process-status.php');

		require_once(IDEA_FACTORY_DIR.'/public/includes/class.template-loader.php');
		require_once(IDEA_FACTORY_DIR.'/public/includes/class.assets.php');
		require_once(IDEA_FACTORY_DIR.'/public/includes/helpers.php');

		require_once(IDEA_FACTORY_DIR.'/public/includes/class.shortcodes.php');

		require_once(IDEA_FACTORY_DIR.'/includes/class.db.php');

		// Load plugin text domain
		add_action( 'init', 			array( $this, 'load_plugin_textdomain' ) );
		add_action('plugins_loaded', 	array($this,'upgrade'));
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    0.0.1
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.0.1
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    0.0.1
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    0.0.1
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    0.0.1
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    0.0.1
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Flush rewrite rules for custom post type archive on single activation
	 *
	 * @since    0.0.1
	 */
	private static function single_activate() {

		flush_rewrite_rules();

		global $wpdb;

		$table_name = $wpdb->prefix . 'idea_factory';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			postid bigint(20) NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			ip varchar(20) NOT NULL,
			UNIQUE KEY id (id)
		);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    0.0.1
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		$out = load_textdomain( $domain, trailingslashit( IDEA_FACTORY_DIR ). 'languages/' . $domain . '-' . $locale . '.mo' );
	}

	/**
	*
	*	Run on plugin upgrade
	*	@since 1.2
	*/
	function upgrade(){

		$version = get_option('idea_factory_version', true );

		if ( $version != IDEA_FACTORY_VERSION ) {

			self::upgrade_install_db();

		}
	}

	/**
	*
	*	Create public database tabes on upgrade
	*	@since 1.2
	*/
	function upgrade_install_db(){

		$table_name = $wpdb->prefix . 'idea_factory';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			postid bigint(20) NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			ip varchar(20) NOT NULL,
			UNIQUE KEY id (id)
		);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		update_option('idea_factory_version', $version );

	}
}






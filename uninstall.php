<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Idea_Factory
 * @author    Nick Haskins <email@nickhaskins.com>
 * @license   GPL-2.0+
 * @link      http://nickhaskins.com
 * @copyright 2015 Your Mom
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;
$table = $wpdb->prefix.'idea_factory';

// delete meta
delete_post_meta('_idea_votes');
delete_post_meta('_idea_total_votes');
delete_post_meta('_idea_status');

// drop db table
$wpdb->query("DROP TABLE IF EXISTS $table");

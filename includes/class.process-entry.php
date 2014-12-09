<?php

/*
*
*	Class responsible for processign the entry
*
*/
class ideaFactoryProcessEntry {

	function __construct(){

		add_action( 'wp_ajax_process_entry', 				array($this, 'process_entry' ));
		add_action( 'idea_factory_entry_submitted',			array($this,'send_mail'), 10, 2);
	}

	/**
	*
	*	Process the form submission
	*
	*/
	function process_entry(){

		$userid 		= isset( $_POST['user_id'] ) ? $_POST['user_id'] : null;
		$title 			= isset( $_POST['idea-title'] ) ? $_POST['idea-title'] : null;
		$desc 			= isset( $_POST['idea-description'] ) ? $_POST['idea-description'] : null;

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'process_entry' ) {

			// only run for logged in users
			if( !is_user_logged_in() )
				return;

			// ok security passes so let's process some data
			if ( wp_verify_nonce( $_POST['nonce'], 'if-entry-nonce' ) ) {

				// bail if we dont have rquired fields
				if ( empty( $title ) || empty( $desc ) ) {

					echo '<div class="error">Whoopsy! Looks like you forgot the Title and/or description.</div>';

				} else {

					// create an ideas post type
					$post_args = array(
					  	'post_title'    => wp_strip_all_tags( $title ),
					  	'post_content'  => idea_factory_media_filter( $desc ),
					  	'post_status'   => 'publish',
					  	'post_type'	  	=> 'ideas',
					  	'post_author'   => (int) $userid
					);
					$entry_id = wp_insert_post( $post_args );

					update_post_meta( $entry_id, '_idea_votes', 0 );

					do_action('idea_factory_entry_submitted', $entry_id, $userid );

					echo 'success';

				}

			}

		}

		exit(); // ajax
	}

	/**
	*
	*	Send email to the admin notifying of a new submission
	*
	*	@param $entry_id int postid object
	*	@param $userid int userid object
	*
	*/
	function send_mail( $entry_id, $userid ) {

		$user 		 	= get_userdata( $userid );
		$admin_email 	= get_bloginfo('admin_email');
		$entry       	= get_post( $entry_id );
		$mail_disabled 	= idea_factory_get_option('if_disable_mail','if_settings_advanced');

		$message = "Submitted by: ".$user->display_name.".\n\n";
		$message .= "Title: ".$entry->post_title."\n\n";
		$message .= "Description:\n";
		$message .= "".$entry->post_content."\n\n";
		$message .= "Manage ideas at link below\n";
		$message .= "".wp_login_url()."\n\n";

		if ( !$mail_disabled )
			wp_mail( $admin_email, 'New Idea Submission', $message );

	}

}
new ideaFactoryProcessEntry;
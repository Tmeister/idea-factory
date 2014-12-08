<?php

/*
*
*	Class responsible for processign the entry
*
*/
class ideaFactoryProcessEntry {

	function __construct(){

		add_action( 'wp_ajax_process_entry', 				array($this, 'process_entry' ));
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
					  'post_title'    => $title,
					  'post_content'  => $desc,
					  'post_status'   => 'publish',
					  'post_type'	  => 'ideas',
					  'post_author'   => $userid
					);
					$entry_id = wp_insert_post( $post_args );

					update_post_meta( $entry_id, '_idea_votes', 0 );

					echo 'success';

				}

			}

		}

		exit(); // ajax
	}


}
new ideaFactoryProcessEntry;
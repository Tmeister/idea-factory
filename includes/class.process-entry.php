<?php

/*
*
*	Class responsible for processign the entry
*
*/
class ideaFactoryProcessEntry {

	function __construct(){

		add_action( 'wp_ajax_process_entry', 				array($this, 'process_entry' ));
		add_action( 'wp_ajax_nopriv_process_entry', 		array($this, 'process_entry' ));
		add_action( 'idea_factory_entry_submitted',			array($this,'send_mail'), 10, 2);
	}

	/**
	*
	*	Process the form submission
	*
	*/
	function process_entry(){

		$public_can_vote = idea_factory_get_option('if_public_voting','if_settings_main');

		$title 			= isset( $_POST['idea-title'] ) ? $_POST['idea-title'] : null;
		$desc 			= isset( $_POST['idea-description'] ) ? $_POST['idea-description'] : null;

		$must_approve 	= 'on' == idea_factory_get_option('if_approve_ideas','if_settings_main') ? 'pending' : 'publish';

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'process_entry' ) {

			// only run for logged in users or if public is allowed
			if( !is_user_logged_in() && 'on' !== $public_can_vote )
				return;

			// ok security passes so let's process some data
			if ( wp_verify_nonce( $_POST['nonce'], 'if-entry-nonce' ) ) {

				// bail if we dont have rquired fields
				if ( empty( $title ) || empty( $desc ) ) {

					printf(('<div class="error">%s</div>'), __('Whoopsy! Looks like you forgot the Title and/or description.', 'idea-factory'));

				} else {

					if ( is_user_logged_in() ) {

						$userid = get_current_user_ID();

					} elseif ( !is_user_logged_in() && $public_can_vote ) {

						$userid = apply_filters('idea_factory_default_public_author', 1 );
					}

					// create an ideas post type
					$post_args = array(
					  	'post_title'    => wp_strip_all_tags( $title ),
					  	'post_content'  => idea_factory_media_filter( $desc ),
					  	'post_status'   => $must_approve,
					  	'post_type'	  	=> 'ideas',
					  	'post_author'   => (int) $userid
					);
					$entry_id = wp_insert_post( $post_args );

					update_post_meta( $entry_id, '_idea_votes', 0 );
					update_post_meta( $entry_id, '_idea_total_votes', 0 );

					do_action('idea_factory_entry_submitted', $entry_id, $userid );

					_e('Thanks for your entry!','idea-factory');
                                        if($must_approve == 'pending'){
                                            echo "<br/>";
                                            _e('You suggestion is awaiting moderation.','idea-factory');
                                        }

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

		$message = sprintf(__("Submitted by: %s", 'idea-factory'), $user->display_name) .".\n\n";
		$message .= __("Title:", 'idea-factory') . "\n";
		$message .= $entry->post_title."\n\n";
		$message .= __("Description:", 'idea-factory') . "\n";
		$message .= $entry->post_content."\n\n";
		$message .= __("Manage all ideas at", 'idea-factory') . "\n";
		$message .= admin_url('edit.php?post_type=ideas');

		if ( !isset($mail_disabled) || $mail_disabled == 'off' )
                    wp_mail( $admin_email, sprintf(__('New Idea Submission - %s', 'idea-factory'), $entry_id), $message );

	}

}
new ideaFactoryProcessEntry;
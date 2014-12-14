<?php

/**
*
*	Class responsible for processign the status of the idea
*
*	@since 1.1
*/
class ideaFactoryProcessStatus {

	function __construct(){

		add_action( 'idea_factory_vote_up', 				array($this, 'process_status' ), 10, 2);
		add_action( 'idea_factory_vote_down', 				array($this, 'process_status' ), 10, 2);
		add_action( 'idea_factory_status', 					array($this, 'mail_status' ), 10, 2);

	}

	/**
	*
	*	Process the status of an individual idea with an action fired when a user votes up or down
	*
	*	@param $postid int id of the post
	*	@param $userid int id of the user who voted
	*
	*/
	function process_status( $postid, $userid ) {

		// get threashold
		$threshold = idea_factory_get_option('if_threshold','if_settings_main');

		// bail if no user threshold set
		if ( empty( $threshold ) )
			return;

		// get total number of votes
		$total     = idea_factory_get_total_votes( $postid );

		// get total number of vote ups
		$votes     = idea_factory_get_votes( $postid );

		// if total votes are greater than the threshold
		if ( $total >= $threshold ) {

			// if up votes are passing
			if ( $votes >= $threshold ) {

				update_post_meta( $postid, '_idea_status', 'approved');

				do_action('idea_factory_status', 'approved', $postid );

			// up votes failed
			} else {

				update_post_meta( $postid, '_idea_status', 'declined');

				do_action('idea_factory_status', 'declined', $postid );

			}

		// not enough votes to calculate yet
		} else {

			update_post_meta( $postid, '_idea_status', 'open');

		}

	}

	/**
	*
	*	Send email to the admin notifying of a status change on an idea
	*
	*	@param $status string approved | declined
	*	@param $postid int postid object
	*
	*/
	function mail_status( $status, $postid ) {

		$admin_email 	= get_bloginfo('admin_email');
		$entry       	= get_post( $postid );
		$mail_disabled 	= idea_factory_get_option('if_disable_mail','if_settings_advanced');

		$message = "The status of ".$entry->post_title." has been updated to:\n";
		$message .= "".$status."\n\n";
		$message .= "Manage ideas at link below\n";
		$message .= "".wp_login_url()."\n\n";

		if ( !$mail_disabled )
			wp_mail( $admin_email, 'Idea '.$postid.' Approved ', $message );

	}

}
new ideaFactoryProcessStatus;
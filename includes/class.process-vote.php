<?php

/*
*
*	Class responsible for processign the vote
*
*/
class ideaFactoryProcessVote {
	function __construct(){

		add_action( 'wp_ajax_process_vote_up', 				array($this, 'process_vote_up' ));
		add_action( 'wp_ajax_process_vote_down', 			array($this, 'process_vote_down' ));

		add_action( 'wp_ajax_nopriv_process_vote_up', 				array($this, 'process_vote_up' ));
		add_action( 'wp_ajax_nopriv_process_vote_down', 			array($this, 'process_vote_down' ));
	}

	/**
	*
	*	Process the form submission
	*
	*/
	function process_vote_up(){

		check_ajax_referer('idea_factory','nonce');

		if ( isset( $_POST['post_id'] ) ) {

			$postid = $_POST['post_id'];

			// get vote statuses
			$has_public_voted = idea_factory_has_public_voted( $postid );
			$has_private_voted = idea_factory_has_private_voted( $postid );

			// get votes
			$votes 			=	get_post_meta( $postid, '_idea_votes', true );
			$total_votes 	=	get_post_meta( $postid, '_idea_total_votes', true );

			// public voting enabled
			$public_can_vote = idea_factory_get_option('if_public_voting','if_settings_main');


			if ( is_user_logged_in() ) {

				$userid = get_current_user_ID();

			} elseif ( !is_user_logged_in() && $public_can_vote ) {

				$userid = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
			}

			// if the public can vote and the user has already voted or they are logged in and have already voted then bail out
			if ( $public_can_vote && $has_public_voted || $has_private_voted ) {
				echo 'already-voted';
				die();
			}

			// increase votes
			update_post_meta( $postid, '_idea_votes', intval( $votes ) + 1 );
			update_post_meta( $postid, '_idea_total_votes', intval( $total_votes ) + 1 );

			if ( !is_user_logged_in() && $public_can_vote ) {

				$args = array( 'postid' => $postid );
		        idea_factory_add_public_vote( $args );

			} elseif ( is_user_logged_in() ) {

				// update user meta so they can't vote on this again
				update_user_meta( $userid, '_idea'.$postid.'_has_voted', true );

			}

			do_action('idea_factory_vote_up', $postid, $userid );

			echo 'success';

		}
		die();
	}

	/**
	*
	*	Process the form submission
	*
	*/
	function process_vote_down(){

		check_ajax_referer('idea_factory','nonce');

		if ( isset( $_POST['post_id'] ) ) {

			$postid = $_POST['post_id'];

			// get vote statuses
			$has_public_voted = idea_factory_has_public_voted( $postid );
			$has_private_voted = idea_factory_has_private_voted( $postid );

			// get votes
			$votes 			=	get_post_meta( $postid, '_idea_votes', true );
			$total_votes 	=	get_post_meta( $postid, '_idea_total_votes', true );

			// public voting enabled
			$public_can_vote = idea_factory_get_option('if_public_voting','if_settings_main');

			if ( is_user_logged_in() ) {

				$userid = get_current_user_ID();

			} elseif ( !is_user_logged_in() && $public_can_vote ) {

				$userid = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
			}


			// if the public can vote and the user has already voted or they are logged in and have already voted then bail out
			if ( $public_can_vote && $has_public_voted || $has_private_voted ) {
				echo 'already-voted';
				die();
			}

			// increase votes
			update_post_meta( $postid, '_idea_votes', intval( $votes ) - 1 );
			update_post_meta( $postid, '_idea_total_votes', intval( $total_votes ) + 1 );

			// update user meta so they can't vote on this again
			if ( !is_user_logged_in() && $public_can_vote ) {

				$args = array( 'postid' => $postid );
		        idea_factory_add_public_vote( $args );

			} elseif ( is_user_logged_in() ) {

				// update user meta so they can't vote on this again
				update_user_meta( $userid, '_idea'.$postid.'_has_voted', true );

			}


			do_action('idea_factory_vote_down', $postid, $userid );

			echo 'success';

		}
		die();
	}
}
new ideaFactoryProcessVote;
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

		if ( isset( $_POST['user_id'] ) && isset( $_POST['post_id'] ) ) {

			$postid = $_POST['post_id'];
			$userid = $_POST['user_id'];

			// get votes
			$votes 			=	get_post_meta( $postid, '_idea_votes', true );
			$total_votes 	=	get_post_meta( $postid, '_idea_total_votes', true );

			// public voting
			$public_can_vote = idea_factory_get_option('if_public_voting','if_settings_main');

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

		if ( isset( $_POST['user_id'] ) && isset( $_POST['post_id'] ) ) {

			$postid = $_POST['post_id'];
			$userid = $_POST['user_id'];

			// get votes
			$votes 			=	get_post_meta( $postid, '_idea_votes', true );
			$total_votes 	=	get_post_meta( $postid, '_idea_total_votes', true );

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
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
			$votes 			=	 get_post_meta( $postid, '_idea_votes', true );

			// increase votes
			update_post_meta( $postid, '_idea_votes', intval( $votes ) + 1 );

			// update user meta so they can't vote on this again
			update_user_meta( $userid, '_idea'.$postid.'_has_voted', true );

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
			$votes 			=	 get_post_meta( $postid, '_idea_votes', true );

			// increase votes
			update_post_meta( $postid, '_idea_votes', intval( $votes ) - 1 );

			// update user meta so they can't vote on this again
			update_user_meta( $userid, '_idea'.$postid.'_has_voted', true );

			echo 'success';

		}
		die();
	}
}
new ideaFactoryProcessVote;
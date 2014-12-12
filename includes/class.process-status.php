<?php

/*
*
*	Class responsible for processign the status of the idea
*
*/
class ideaFactoryProcessStatus {

	function __construct(){

		add_action( 'idea_factory_vote_up', 				array($this, 'process_status' ));
		add_action( 'idea_factory_vote_down', 				array($this, 'process_status' ));

	}

	function process_status( $postid, $userid ) {

		// get threashold
		$threshold = idea_factory_get_option('if_threshold','if_settings_main');

		if ( empty( $threshold ) )
			return;

		// get total number of votes
		$total     = idea_factory_get_total_votes( $postid );

		// get total number of vote ups
		$votes     = idea_factory_get_votes( $postid );

		if ( $total >= $threshold ) {

			if ( $votes >= $threshold ) {
				update_post_meta( $postid, '_idea_status', 'approved');
			} else {
				update_post_meta( $postid, '_idea_status', 'declined');
			}

		} else {
			update_post_meta( $postid, '_idea_status', 'open');
		}

	}

}
new ideaFactoryProcessStatus;
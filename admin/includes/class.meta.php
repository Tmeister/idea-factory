<?php

/**
*
*	Class responsible for adding a status metabox to single ideas so that admins can manulaly chnage the status
*
*	@since 1.1
*
*/
class ideaFactoryMeta {

	function __construct(){

		add_action( 'add_meta_boxes', 					array($this,'add_status_box') );
		add_action( 'save_post',						array($this,'save_status_box'), 10, 3 );
	}

	/**
	*
	*
	*	Add a status metabox if teh user has opted in for the threshold settings
	*
	*	@since 1.1
	*/
	function add_status_box(){

		// get threashold
		$threshold = idea_factory_get_option('if_threshold','if_settings_main');

		if ( $threshold ) {
			add_meta_box('idea_factory_status',__( 'Idea Status', 'idea-factory' ),array($this,'render_status_box'), 'ideas','side','core');
		}

	}

	/**
	* 	Render status metabox
	*
	* 	@param WP_Post $post The post object.
	*	@since 1.1
	*
	*/
	function render_status_box( $post ){

		wp_nonce_field( 'idea_factory_meta', 'idea_factory_nonce' );

		$status = get_post_meta( $post->ID, '_idea_status', true );

		?>
		<select name="idea_status">
	      	<option value="approved" <?php selected( $status, 'approved' ); ?>><?php _e('Approved','idea-factory');?></option>
	      	<option value="declined" <?php selected( $status, 'declined' ); ?>><?php _e('Declined','idea-factory');?></option>
	      	<option value="open" <?php selected( $status, 'open' ); ?>><?php _e('Open','idea-factory');?></option>
	    </select>
	    <?php
	}

	/**
	*
	* 	Save the status
	*
	* 	@param int $post_id The ID of the post being saved.
	*	@param post $post the post
	*	@since 1.1
	*
	*/
	function save_status_box( $post_id, $post, $update ) {

		if ( ! isset( $_POST['idea_factory_nonce'] ) )
			return $post_id;

		$nonce = $_POST['idea_factory_nonce'];

		if ( !wp_verify_nonce( $nonce, 'idea_factory_meta' ) || defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || 'ideas' != $post->post_type )
			return $post_id;

		$status 	 = isset( $_POST['idea_status'] ) ? $_POST['idea_status'] : false;

		update_post_meta( $post_id, '_idea_status', sanitize_text_field( trim( $status ) ) );


	}
}
new ideaFactoryMeta;
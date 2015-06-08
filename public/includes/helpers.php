<?php

/**
*
*	Get the status of an idea
*
*	@param $postid int id of the post to retrieve the status for
*	@return status
*	@since 1.1
*/
function idea_factory_get_status( $postid = 0 ) {

	if ( empty( $postid ) )
		return;

	$status = get_post_meta( $postid, '_idea_status', true );

	return !empty( $status ) ? $status : false;
}

/**
*
*	Get the total number of votes for a specific idea
*
*	@param $postid int id of the post to retrieve votes for
*	@return number of total votes
*	@since 1.1
*/
function idea_factory_get_total_votes( $postid = 0 ) {

	if ( empty( $postid ) )
		return;

	$total_votes = get_post_meta( $postid, '_idea_total_votes', true );

	return !empty( $total_votes ) ? $total_votes : false;
}

/**
*
*	Get the number of vote up votes for a specific idea
*
*	@param $postid int id of the post to retrieve votes for
*	@return number of vote ups
*	@since 1.0
*/
function idea_factory_get_votes( $postid = 0 ) {

	if ( empty( $postid ) )
		return;

	$votes = get_post_meta( $postid, '_idea_votes', true );

	return !empty( $votes ) ? $votes : false;
}

/**
*
*	Grab an optoin from our settings
*
*	@param $option string name of the option
*	@param $section string name of the section
*	@param $default string/int default option value
*	@return the option value
*	@since 1.0
*/
function idea_factory_get_option( $option, $section, $default = '' ) {

	if ( empty( $option ) )
		return;

    $options = get_option( $section );


    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return $default;
}

/**
*	Used on the front end to properly escape attributes where users have control over what input is entered
*	as well as through a callback upon saving in the backend
*
*	@since 1.0
*	@return a sanitized string
*/
function idea_factory_media_filter( $input = '' ) {

	// bail if no input
	if ( empty( $input ) )
		return;

	// setup our array of allowed content to pass
	$allowed_html = array(
		'a' 			=> array(
		    'href' 		=> array(),
		    'title' 	=> array(),
		    'rel'		=> array(),
		    'target'	=> array(),
		    'name' 		=> array()
		),
		'img'			=> array(
			'src' 		=> array(),
			'alt'		=> array(),
			'title'		=> array()
		),
		'p'				=> array(),
		'br' 			=> array(),
		'em' 			=> array(),
		'strong' 		=> array()
	);

	$out = wp_kses( $input, apply_filters('idea_factory_allowed_html', $allowed_html ) );

	return $out;
}

/**
*
*	Modify the post type archive to return results based on number of votes
*	@since 1.0
*
*/
add_action( 'pre_get_posts', 'idea_factory_archive_query');
function idea_factory_archive_query( $query ) {

	if ( is_admin() || ! $query->is_main_query() )
        return;

 	if ( is_post_type_archive( 'ideas' ) ) {
        $query->set( 'meta_key', '_idea_votes' );
        $query->set( 'orderby', 'meta_value_num' );
        $query->set( 'order', 'DESC' );
        return;
    }
}

/**
*
*	Determine if we're on the ideas post type and also account for their being no entries
*	as our post type archive still has to work regardless
*	@since 1.0
*	@return bool
*/
function idea_factory_is_archive(){

	global $post;

	$label 			= idea_factory_get_option('if_domain','if_settings_main','ideas');
	$url 			= isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING']) ? $_SERVER['REQUEST_URI'] : '';
	$is_empty_idea 	= $url ? substr($url,-6) == '/'.esc_attr( trim( $label ) ).' ' || substr($url,-7) == '/'.esc_attr( trim( $label ) ).'/' : null;

	if ( 'ideas' == get_post_type() || $is_empty_idea ):

		return true;

	else:

		return false;

	endif;

}

/**
*
*	Determines if the voting controls should be shown or not based on if the
*	user has voted, is logged in, and status is approved
*
*	@since 1.1
*	@param $postid int id of the actual idea
*	@return bool
*/
function idea_factory_is_voting_active( $postid = '' ) {


	$status      	= idea_factory_get_status( $postid );

	$public_can_vote = idea_factory_get_option('if_public_voting','if_settings_main');

	//if ( ( !idea_factory_has_private_voted( $postid ) && is_user_logged_in() || !idea_factory_has_public_voted( $postid ) && $public_can_vote ) && 'approved' !== $status ){

	if ( ( ( false == idea_factory_has_private_voted( $postid ) && is_user_logged_in()) || false == idea_factory_has_public_voted( $postid ) && $public_can_vote && !is_user_logged_in() ) && 'approved' !== $status ){

		return true;

	} else {

		return false;
	}
}

/**
* 	Adds a public vote entry into the databse
*
* 	@since    1.2
*	@return null
*/
function idea_factory_add_public_vote( $args = array() ) {

	$db = new ideaFactoryDB;

	$defaults = array(
		'postid' => get_the_ID(),
		'time'   => current_time('timestamp'),
		'ip'   	 => isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0,
	);

	$args = array_merge( $defaults, $args );

	$db->insert( $args );

}

/**
*
*	Has the private (logged in) user voted
*
*	@since 1.2
*	@param $postid int id of post to check
*	@param $userid id of user to check againts
*/
function idea_factory_has_private_voted( $postid = '', $userid = '' ) {

	if ( empty( $postid ) )
		return;

	if ( empty( $userid ) )
		$userid = get_current_user_ID();

	$has_voted 	= get_user_meta( $userid, '_idea'.absint( $postid ).'_has_voted', true);

	if ( $has_voted ) {

		return true;

	} else {

		return false;

	}
}

/**
*
*	Has the public user voted
*
*	@since 1.2
*	@param $postid int id of the post
*	@param $ip ip address of the public voter
*	@return bool
*/
function idea_factory_has_public_voted( $postid = '', $ip = '' ) {

	if ( empty( $postid ) )
		return;

	if ( empty( $ip ) )
		$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;


    global $wpdb;

    $table = $wpdb->base_prefix.'idea_factory';

   	$sql =  $wpdb->prepare('SELECT * FROM '.$table.' WHERE ip ="%s" AND postid ="%d"', $ip, $postid );

   	$result =  $wpdb->get_results( $sql );

	if ( $result ) {

		return true;

	} else {

		return false;

	}
}

/**
*	Determinees if public votes exist
*	@since 1.2
*	@return bool
*/
function idea_factory_has_public_votes(){

    global $wpdb;

    $table = $wpdb->base_prefix.'idea_factory';

   	$result =  $wpdb->get_results('SELECT * FROM '.$table.' ');

	if ( $result ) {

		return true;

	} else {

		return false;

	}
}

/**
*
*	The variables being localized
*	@param $max int max number of pages
*	@param $paged
*	@since 1.2
*/
function idea_factory_localized_args( $max = '', $paged = '' ){

	global $wp_query, $post;

	$args = array(
		'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
		'nonce'			=> wp_create_nonce('idea_factory'),
		'error_message' => apply_filters('idea_factory_error',__('Awww snap, something went wrong!','idea-factory')),
		'label'			=> apply_filters('idea_factory_loadmore_label',__('Load more ideas','idea-factory')),
		'label_loading' => apply_filters('idea_factory_loadmore_loading',__('Loading ideas...','idea-factory')),
		'thanks_voting' => apply_filters('idea_factory_thanks_voting',__('Thanks for voting!','idea-factory')),
		'already_voted' => apply_filters('idea_factory_already_voted',__('You have already voted!','idea-factory')),
		'startPage' 	=> $paged,
		'maxPages' 		=> $max,
		'nextLink' 		=> next_posts($max, false)
	);

	return apply_filters('idea_factory_localized_args', $args );

}

/**
*
*
*	ALL PLUGGABLE BELOW
*
*/

/**
*
*	The modal used to show the idea submission form
*	@since 1.1
*/
if ( !function_exists('idea_factory_submit_modal') ):

	function idea_factory_submit_modal(){

		$public_can_vote = idea_factory_get_option('if_public_voting','if_settings_main');

		$userid 		= $public_can_vote && !is_user_logged_in() ? 1 : get_current_user_ID();

		if ( is_user_logged_in() || $public_can_vote ): ?>

			<div class="fade idea-factory-modal" tabindex="-1">
				<div class="idea-factory-modal-dialog ">
				    <div class="idea-factory-modal-content">
						<button type="button" class="close" data-dismiss="idea-factory-modal"><span aria-hidden="true">&times;</span></button>

				    	<div class="idea-factory-modal-header">
				    		<h3 class="idea-factory-modal-title"><?php apply_filters('idea_factory_submit_idea_label', _e('Submit idea','idea-factory'));?></h3>
				    	</div>
				    	<div class="idea-factory-modal-body">

							<div id="idea-factory--entry--form-results"></div>
							<form id="idea-factory--entry--form" method="post" enctype="multipart/form-data">

								<?php do_action('idea_factory_inside_form_top');?>

								<label for="idea-title"><?php apply_filters('idea_factory_form_title', _e('Title','idea-factory'));?></label>
								<input id="idea-factory--entryform_title" type="text" name="idea-title" value="" placeholder="My Awesome Submission">

								<label for="idea-description"><?php apply_filters('idea_factory_form_description', _e('Description','idea-factory'));?></label>
								<textarea id="idea-factory--entryform_description" form="idea-factory--entry--form" name="idea-description" value="" placeholder="Make the description meaningful!"></textarea>

								<?php do_action('idea_factory_inside_form_bottom');?>

								<input type="hidden" name="action" value="process_entry">
								<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('if-entry-nonce'); ?>"/>

								<div class="idea-factory-modal-footer">
									<input class="idea-factory--button" type="submit" value="<?php apply_filters('idea_factory_submit_label', _e('Submit','idea-factory'));?>">
								</div>
							</form>

						</div>
					</div>
				</div>
			</div>

		<?php endif;
	}

endif;

/**
*
*	Header area showing intor message and button to click to open submission modal
*
*/
if ( !function_exists('idea_factory_submit_header') ):

	function idea_factory_submit_header(){

		$intro_message = idea_factory_get_option('if_welcome','if_settings_main',apply_filters('idea_factory_default_message', __('Submit and vote for new features!','idea-factory')));
		$public_can_vote = idea_factory_get_option('if_public_voting','if_settings_main');

		if ( is_user_logged_in() || $public_can_vote ): ?>

			<aside class="idea-factory--layout-submit">

				<div class="idea-factory--submit-left">

					<?php echo idea_factory_media_filter( $intro_message );?>

				</div>

				<div class="idea-factory--submit-right">

					<?php do_action('idea_factory_before_submit_button'); ?>

						<a href="#" data-toggle="idea-factory-modal" data-target=".idea-factory-modal" class="idea-factory--button idea-factory-trigger"><?php _e('Submit Idea','idea-factory');?></a>

					<?php do_action('idea_factory_after_submit_button'); ?>

				</div>

			</aside>

		<?php endif;
	}

endif;


/**
*	Draw teh actual voting controls
*	@since 1.1
*
*/
if ( !function_exists('idea_factory_vote_controls') ):

	function idea_factory_vote_controls( $postid = '' ) {

		if ( empty( $postid ) )
			$postid = get_the_ID();

		?>
			<a class="idea-factory vote-up" data-post-id="<?php echo (int) $postid;?>" href="#"></a>
			<a class="idea-factory vote-down" data-post-id="<?php echo (int) $postid;?>" href="#"></a>
		<?php
	}

endif;

/**
*	Draw teh voting status
*	@since 1.1
*
*/
if ( !function_exists('idea_factory_vote_status') ):

	function idea_factory_vote_status( $postid = '' ) {

		$status      	= idea_factory_get_status( $postid );
		$threshold 		= idea_factory_get_option('if_threshold','if_settings_main');


		if ( 'open' !== $status && false !== $status && !empty( $threshold ) ) { ?>
			<div class="idea-factory--status">
				<?php echo '<span class="idea-factory--status_'.sanitize_html_class( $status ).'">'.esc_attr( $status ).'</span>';?>
			</div>
		<?php }
	}

endif;








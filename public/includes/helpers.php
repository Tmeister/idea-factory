<?php

/**
*
*	Get the number of votes for a specific idea
*
*	@param $postid int id of the post to retrieve votes for
*	@return number of votes
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
*
*/
function idea_factory_is_archive(){

	$label 			= idea_factory_get_option('if_domain','if_settings_main','ideas');
	$url 			= isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING']) ? $_SERVER['REQUEST_URI'] : '';
	$is_empty_idea 	= $url ? substr($url,-6) == '/'.esc_attr( trim( $label ) ).' ' || substr($url,-7) == '/'.esc_attr( trim( $label ) ).'/' : null;

	if ( 'ideas' == get_post_type() || $is_empty_idea ):

		return true;

	else:

		return false;

	endif;

}
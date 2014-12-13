<?php

/**
*
*	Class responsible for adding columns into the edit posts screen in the ideas post type
*	used for showing admins the status of an idea
*	@since 1.1
*/
class ideaFactoryColumnMods {

	function __construct(){
    	add_filter('manage_ideas_posts_columns', 		array($this,'col_head'));
		add_action('manage_ideas_posts_custom_column', 	array($this,'col_content'), 10, 2);
	}

	/**
	*
	*	Log the columns
	*
	* 	@since    1.1
	*/
	function col_head( $item ) {

	    $item['idea_status'] = __('Idea Status','idea-factory');

	    return $item;
	}

	/**
	* Callback for col_head
	* Show the status of an idea
	*
	* @since    1.1
	*/
	function col_content( $column_name, $post_ID ) {

	    if ( 'idea_status' == $column_name ) {

	       	$status = get_post_meta( $post_ID,'_idea_status', true );

	        echo '<strong>'.$status.'</strong>';

	    }
	}

}
new ideaFactoryColumnMods;
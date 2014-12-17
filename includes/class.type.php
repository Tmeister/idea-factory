<?php

/**
*
*	Creates an "ideas" custom post type
*
*/

class ahIdeaType {

	public function __construct(){

       	add_action('init',array($this,'do_type'));
	}
	/**
	 	* Creates a post type
	 	*
	 	* @since    1.0.0
	*/
	function do_type() {

		$disable_archive = idea_factory_get_option('if_disable_archive','if_settings_advanced');

		$domain = 'on' == $disable_archive ? false : idea_factory_get_option('if_domain','if_settings_main','ideas');

		$labels = array(
			'name'                		=> _x( 'Ideas','idea-factory' ),
			'singular_name'       		=> _x( 'Idea','idea-factory' ),
			'menu_name'           		=> __( 'Ideas', 'idea-factory' ),
			'parent_item_colon'   		=> __( 'Parent Idea:', 'idea-factory' ),
			'all_items'           		=> __( 'All Ideas', 'idea-factory' ),
			'view_item'           		=> __( 'View Idea', 'idea-factory' ),
			'add_new_item'        		=> __( 'Add New Idea', 'idea-factory' ),
			'add_new'             		=> __( 'New Idea', 'idea-factory' ),
			'edit_item'           		=> __( 'Edit Idea', 'idea-factory' ),
			'update_item'         		=> __( 'Update Idea', 'idea-factory' ),
			'search_items'        		=> __( 'Search Ideas', 'idea-factory' ),
			'not_found'           		=> __( 'No Ideas found', 'idea-factory' ),
			'not_found_in_trash'  		=> __( 'No Ideas found in Trash', 'idea-factory' ),
		);
		$args = array(
			'label'               		=> __( 'Ideas', 'idea-factory' ),
			'description'         		=> __( 'Create votes', 'idea-factory' ),
			'labels'              		=> $labels,
			'supports'            		=> array( 'editor','title', 'comments', 'author'),
			'public'              		=> false,
			'menu_icon'					=> 'dashicons-lightbulb',
			'publicly_queryable'		=> true,
 			'show_ui' 					=> true,
			'query_var' 				=> true,
			'can_export' 				=> true,
			'has_archive'				=> $domain,
			'capability_type' 			=> 'post'
		);

		register_post_type( 'ideas', apply_filters('idea_factory_type_args', $args ) );

	}
}

new ahIdeaType;












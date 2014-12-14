<?php

/*
*
*	Class responsible for building the template redirect
*
*/
class ideaFactoryTemplateLoader {

	function __construct() {

		add_filter( 'template_include', array($this,'template_loader'));

	}

	/**
	*
	* @since version 1.0
	* @param $template - return based on view
	* @return page template based on view regardless if the post type doesnt even exist yet due to no posts
	*/
	function template_loader( $template ) {

		$disable_archive = idea_factory_get_option('if_disable_archive','if_settings_advanced');

	   	if ( idea_factory_is_archive() && 'on' !== $disable_archive ):

	    	if ( $overridden_template = locate_template( 'template-ideas.php', true ) ) {

			   $template = load_template( $overridden_template );

			} else {

			   	$template = IDEA_FACTORY_DIR.'templates/template-ideas.php';
			}

	    endif;

	    return $template;

	}
}
new ideaFactoryTemplateLoader;
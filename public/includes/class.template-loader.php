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
	function template_loader($template) {

		$url 			= isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING']) ? $_SERVER['REQUEST_URI'] : '';
		$is_empty_idea 	= $url ? substr($url,-6) == '/ideas' || substr($url,-7) == '/ideas/' : null;

	    if ( 'ideas' == get_post_type() || $is_empty_idea ):

			$template = IDEA_FACTORY_DIR.'templates/template-ideas.php';

	    endif;

	    return $template;

	}
}
new ideaFactoryTemplateLoader;
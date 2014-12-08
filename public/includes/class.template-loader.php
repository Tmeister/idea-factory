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
	* @return page template based on view
	*/
	function template_loader($template) {

	    if ( 'ideas' == get_post_type() ):

			$template = IDEA_FACTORY_DIR.'templates/template-ideas.php';

	    endif;

	    return $template;

	}
}
new ideaFactoryTemplateLoader;
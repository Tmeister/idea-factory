<?php

/*
*
*	Class responsible for building the template redirect
*
*/
class ideaFactoryAssetLoader {

	function __construct(){
		add_action('wp_enqueue_scripts', array($this,'scripts'));
	}

	function scripts(){

	    if ( 'ideas' == get_post_type() ):

	    	wp_enqueue_style('dashicons');
	    	wp_enqueue_style('idea-factory-css', IDEA_FACTORY_URL.'/public/assets/css/idea-factory.css', IDEA_FACTORY_VERSION, true );
			wp_enqueue_script('idea-factory-script', IDEA_FACTORY_URL.'/public/assets/js/idea-factory.js', array('jquery'), IDEA_FACTORY_VERSION, true);

			wp_localize_script('idea-factory-script', 'idea_factory', array(
				'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
				'nonce'	=> wp_create_nonce('idea_factory')
			));

		endif;
	}
}
new ideaFactoryAssetLoader;
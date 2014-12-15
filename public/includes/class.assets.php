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

		global $wp_query, $post;

		$disable_css    = idea_factory_get_option('if_disable_css','if_settings_advanced');

	 	$max 			=  $wp_query->max_num_pages;
	 	$paged 			= ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;

	    if ( idea_factory_is_archive() || has_shortcode( isset( $post->post_content ) ? $post->post_content : null, 'idea_factory') ):

	    	if ( 'on' !== $disable_css ) {
	    		wp_enqueue_style('dashicons');
	    		wp_enqueue_style('idea-factory-css', IDEA_FACTORY_URL.'/public/assets/css/idea-factory.css', IDEA_FACTORY_VERSION, true );
			}

			wp_enqueue_script('idea-factory-script', IDEA_FACTORY_URL.'/public/assets/js/idea-factory.js', array('jquery'), IDEA_FACTORY_VERSION, true);
			wp_localize_script('idea-factory-script', 'idea_factory', array(
				'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
				'nonce'			=> wp_create_nonce('idea_factory'),
				'label'			=> apply_filters('idea_factory_loadmore_label',__('Load more ideas','idea-factory')),
				'label_loading' => apply_filters('idea_factory_loadmore_loading',__('Loading ideas...','idea-factory')),
				'startPage' 	=> $paged,
	 			'maxPages' 		=> $max,
	 			'nextLink' 		=> next_posts($max, false)
			));

		endif;
	}
}
new ideaFactoryAssetLoader;
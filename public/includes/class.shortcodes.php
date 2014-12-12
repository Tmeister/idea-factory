<?php

/*
*
*	Class responsible for building teh various shortcodes used
*
*/
class ideaFactoryShortcodes {

	function __construct() {

		add_shortcode('idea_factory', array($this,'idea_factory_sc'));

	}

	/**
	*	Show teh votes and vote form within a shortcode
	* 	@since version 1.1
	*/
	function idea_factory_sc($atts, $content = null) {

		$defaults = array(
			'hide_submit'	=> 'off'
		);
		$atts = shortcode_atts( $defaults, $atts );

		ob_start();

		echo 'wassup';

		return ob_get_clean();

	}
}
new ideaFactoryShortcodes;
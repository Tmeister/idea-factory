<?php
/**
* creates setting tabs
*
* @since version 1.0
* @param null
* @return global settings
*/

require_once dirname( __FILE__ ) . '/class.settings-api.php';

if ( !class_exists('if_settings_api_wrap' ) ):
class if_settings_api_wrap {

    private $settings_api;

    const version = '1.0';

    function __construct() {

        $this->dir  		= plugin_dir_path( __FILE__ );
        $this->url  		= plugins_url( '', __FILE__ );
        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this,'submenu_page'));

    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

	function submenu_page() {
		add_submenu_page( 'edit.php?post_type=ideas', 'Settings', __('Settings','idea-factory'), 'manage_options', 'idea-factory-settings', array($this,'submenu_page_callback') );
	}

	function submenu_page_callback() {

		echo '<div class="wrap">';
			?><h2><?php _e('Idea Factory Settings','idea-factory');?></h2><?php

			$this->settings_api->show_navigation();
        	$this->settings_api->show_forms();

		echo '</div>';

	}

    function get_settings_sections() {
        $sections = array(
            array(
                'id' 	=> 'if_settings_main',
                'title' => __( 'Setup', 'idea-factory' )
            ),
           	array(
                'id' 	=> 'if_settings_design',
                'title' => __( 'Design', 'idea-factory' )
            ),
           	array(
                'id' 	=> 'if_settings_advanced',
                'title' => __( 'Advanced', 'idea-factory' )
            )
        );
        return $sections;
    }

    function get_settings_fields() {
        $settings_fields = array(
            'if_settings_main' => array(
            	array(
                    'name' 				=> 'if_domain',
                    'label' 			=> __( 'Naming Convention', 'idea-factory' ),
                    'desc' 				=> __( 'By default its called Ideas. You can rename this here. Flush permalinks after renaming by going to Settings-->Permalinks.', 'idea-factory' ),
                    'type' 				=> 'text',
                    'default' 			=> 'ideas',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name' 				=> 'if_welcome',
                    'label' 			=> __( 'Welcome Message', 'idea-factory' ),
                    'desc' 				=> __( 'Enter a message to display to users to vote. Some HTML ok.', 'idea-factory' ),
                    'type' 				=> 'textarea',
                    'default' 			=> 'Submit and vote for new features!',
                    'sanitize_callback' => 'idea_factory_media_filter'
                )
            ),
			'if_settings_design' 	=> array(
            	array(
                    'name' 				=> 'single_mast_height',
                    'label' 			=> __( 'Header Image Height', 'idea-factory' ),
                    'desc' 				=> __( 'Height of the masthead.', 'idea-factory' ),
                    'type'				=> 'text',
                    'default' 			=> 400,
                    'sanitize_callback' => ''
                )
            ),
            'if_settings_advanced' 	=> array(
            	array(
                    'name' 				=> 'if_disable_css',
                    'label' 			=> __( 'Disable Core CSS', 'idea-factory' ),
                    'desc' 				=> __( 'Disable the core css file from loading.', 'idea-factory' ),
                    'type'				=> 'checkbox',
                    'default' 			=> '',
                    'sanitize_callback' => ''
                ),
                 array(
                    'name' 				=> 'if_disable_mail',
                    'label' 			=> __( 'Disable Emails', 'idea-factory' ),
                    'desc' 				=> __( 'Disable the admin email notification of new submissions.', 'idea-factory' ),
                    'type'				=> 'checkbox',
                    'default' 			=> '',
                    'sanitize_callback' => ''
                )
            )
        );

        return $settings_fields;
    }
}
endif;

$settings = new if_settings_api_wrap();







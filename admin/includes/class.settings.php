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

        add_action( 'admin_init', 					array($this, 'admin_init') );
        add_action( 'admin_menu', 					array($this,'submenu_page'));
        add_action( 'admin_head', 					array($this, 'reset_votes'));
        add_action( 'wp_ajax_idea_factory_reset', 	array($this, 'idea_factory_reset' ));

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
		add_submenu_page( 'edit.php?post_type=ideas', 'Help', __('Help','idea-factory'), 'manage_options', 'idea-factory-docs', array($this,'docs_callback') );		
		add_submenu_page( 'edit.php?post_type=ideas', 'Reset', __('Reset','idea-factory'), 'manage_options', 'idea-factory-reset', array($this,'reset_callback') );
	}

	/**
	*
	*	Allow admins to reset the votes
	*
	*/
	function reset_callback(){

		echo '<div class="wrap">';

			?><h2><?php _e('Idea Factory Reset','idea-factory');?></h2>

			<label style="display:block;margin-top:20px;">Click the button below to reset the votes. Warning, there is no going back!</label>
			<a style="display:inline-block;margin-top:10px;" class="button" href="#" id="idea-factory-reset--votes">Reset Votes</a>

			<?php


		echo '</div>';


	}

	/**
	*
	*	Documentation page callback
	*
	*/
	function docs_callback(){
		echo '<div class="wrap">';

			?><h2 style="margin-bottom:0;"><?php _e('Idea Factory Documentation','idea-factory');?></h2>
			<hr>

			<h3 style="margin-bottom:0;">The Basics</h3> 
			<p style="margin-top:5px;">After you activate <em>Idea Factory</em>, it will automatically be available at yoursite.com/ideas. You can change this in the settings, and also deactivate the archive all together. You can additionally display the form and ideas via a shortcode as documented below.</p>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;">The Shortcode</h3> 
			<p style="margin-top:5px;">You can additionally display the form and ideas via a shortcode as documented below.</p>

			<code>[idea_factory hide_submit="off" hide_votes="off" hide_voting="off"]</code>

			<ul>
				<li><strong>Hide Submit</strong> - Set this to "on" to hide the submission button and form.</li>
				<li><strong>Hide Votes</strong> - Set this to "on" to hide the votes.</li>
				<li><strong>Hide Voting</strong> - Set this to "on" to hide the voting features.</li>
			</ul>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;">How Voting Works</h3> 
			<p style="margin-top:5px;">Voting is currently restricted to logged in users. Total votes are stored in the post meta table. Once a user votes, a flag is recorded in the user_meta table, preventing this user from being able to vote again on the same idea.</p>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;">Developers</h3> 
			<p style="margin-top:5px;">Full documentation of hooks, actions, filters, and helper functions are available on the GitHub wiki page located <a href="https://github.com/bearded-avenger/idea-factory/wiki">here</a>.</p>

			<?php


		echo '</div>';
	}

	/**
	*
	*	Handl the click event for resetting votes
	*
	*/
	function reset_votes() {

		$nonce = wp_create_nonce('idea-factory-reset');

		?>
			<!-- Reset Votes -->
			<script>
				jQuery(document).ready(function($){
				  	jQuery('#idea-factory-reset--votes').click(function(e){

				  		e.preventDefault();

				  		var data = {
				            action: 'idea_factory_reset',
				            security: '<?php echo $nonce;?>'
				        };

					  	jQuery.post(ajaxurl, data, function(response) {
					  		if( response ){
					        	alert(response);
					        	location.reload();
					  		}
					    });

				    });
				});
			</script>
		<?php 
	}

	/**
	*
	*	Process the votes reste
	*	@since 1.1
	*/
	function idea_factory_reset(){

		check_ajax_referer( 'idea-factory-reset', 'security' );

		$posts = get_posts( array('post_type' => 'ideas', 'posts_per_page' => -1 ) );

		if ( $posts ):

			foreach ( $posts as $post ) {

				$total_votes = get_post_meta( $post->ID, '_idea_total_votes', true );
				$votes 		 = get_post_meta( $post->ID, '_idea_votes', true );

				if ( !empty( $total_votes ) ) {
					update_post_meta( $post->ID, '_idea_total_votes', 0 );
				}

				if ( !empty( $votes ) ) {
					update_post_meta( $post->ID, '_idea_votes', 0 );
				}
			}

		endif;

		echo __('All reset!','idea-factory');

		exit;

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
                'id' 	=> 'if_settings_advanced',
                'title' => __( 'Advanced', 'idea-factory' )
            )
        );
        return $sections;
    }

    function get_settings_fields() {

		$domain 	= idea_factory_get_option('if_domain','if_settings_main','ideas');

        $settings_fields = array(
            'if_settings_main' => array(
            	array(
                    'name' 				=> 'if_domain',
                    'label' 			=> __( 'Naming Convention', 'idea-factory' ),
                    'desc' 				=> '<a href="'.get_post_type_archive_link( $domain ).'">'. __( 'Link to ideas page', 'idea-factory' ) .'</a> - ' . __( 'By default its called Ideas. You can rename this here. Flush permalinks after renaming by going to Settings-->Permalinks.', 'idea-factory' ),
                    'type' 				=> 'text',
                    'default' 			=> __('ideas','idea-factory'),
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name' 				=> 'if_welcome',
                    'label' 			=> __( 'Welcome Message', 'idea-factory' ),
                    'desc' 				=> __( 'Enter a message to display to users to vote. Some HTML ok.', 'idea-factory' ),
                    'type' 				=> 'textarea',
                    'default' 			=> __('Submit and vote for new features!', 'idea-factory'),
                    'sanitize_callback' => 'idea_factory_media_filter'
                ),
                array(
                    'name' 				=> 'if_approve_ideas',
                    'label' 			=> __( 'Require Idea Approval', 'idea-factory' ),
                    'desc' 				=> __( 'Check this box to enable newly submitted ideas to be put into a pending status instead of automatically publishing.', 'idea-factory' ),
                    'type'				=> 'checkbox',
                    'default' 			=> '',
                    'sanitize_callback' => 'idea_factory_sanitize_checkbox'
                ),
            	array(
                    'name' 				=> 'if_threshold',
                    'label' 			=> __( 'Voting Threshold', 'idea-factory' ),
                    'desc' 				=> __( 'Specify an optional number of votes that each idea must reach in order for its status to be automatically updated to "approved" , "declined", or "open."', 'idea-factory' ),
                    'type' 				=> 'text',
                    'default' 			=> __('','idea-factory'),
                    'sanitize_callback' => 'idea_factory_sanitize_int'
                )
            ),
            'if_settings_advanced' 	=> array(
            	array(
                    'name' 				=> 'if_disable_css',
                    'label' 			=> __( 'Disable Core CSS', 'idea-factory' ),
                    'desc' 				=> __( 'Disable the core css file from loading.', 'idea-factory' ),
                    'type'				=> 'checkbox',
                    'default' 			=> '',
                    'sanitize_callback' => 'idea_factory_sanitize_checkbox'
                ),
                 array(
                    'name' 				=> 'if_disable_mail',
                    'label' 			=> __( 'Disable Emails', 'idea-factory' ),
                    'desc' 				=> __( 'Disable the admin email notification of new submissions.', 'idea-factory' ),
                    'type'				=> 'checkbox',
                    'default' 			=> '',
                    'sanitize_callback' => 'idea_factory_sanitize_checkbox'
                )
            )
        );

        return $settings_fields;
    }

    /**
    *
    *	Sanitize checkbox input
    *
    */
    function idea_factory_sanitize_checkbox( $input ) {

		if ( $input ) {

			$output = '1';

		} else {

			$output = false;

		}

		return $output;
	}

	/**
	*
	*	Sanitize integers
	*
	*/
	function idea_factory_sanitize_int( $input ) {

		if ( $input ) {

			$output = absint( $input );

		} else {

			$output = false;

		}

		return $output;
	}
}
endif;

$settings = new if_settings_api_wrap();







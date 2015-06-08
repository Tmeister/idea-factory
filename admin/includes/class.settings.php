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

        add_action( 'admin_init', 						array($this, 'admin_init') );
        add_action( 'admin_menu', 						array($this,'submenu_page'));
        add_action( 'admin_head', 						array($this, 'reset_votes'));
        add_action( 'wp_ajax_idea_factory_reset', 		array($this, 'idea_factory_reset' ));
        add_action( 'wp_ajax_idea_factory_db_reset', 	array($this, 'idea_factory_db_reset' ));

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

			<label style="display:block;margin-top:20px;"><?php _e('Click the button below to reset the private (logged-in) votes. Warning, there is no going back!','idea-factory');?></label>
			<a style="background:#d9534f;border:none;box-shadow:none;color:white;display:inline-block;margin-top:10px;" class="button idea-factory-reset--votes" href="#"><?php _e('Reset Logged-in Votes','idea-factory');?></a>

			<?php if ( true == idea_factory_has_public_votes() ) { ?>
				<label style="display:block;margin-top:20px;"><?php _e('Click the button below to reset the public (logged-out) votes. Warning, there is no going back!','idea-factory');?></label>
				<a style="background:#d9534f;border:none;box-shadow:none;color:white;display:inline-block;margin-top:10px;" class="button idea-factory-reset--votes reset-db" href="#"><?php _e('Reset Public Votes','idea-factory');?></a>
			<?php }

		echo '</div>';


	}

	/**
	*
	*	Documentation page callback
	*
	*/
	function docs_callback(){

		$domain = idea_factory_get_option('if_domain','if_settings_main','ideas');

		echo '<div class="wrap">';

			?><h2 style="margin-bottom:0;"><?php _e('Idea Factory Documentation','idea-factory');?></h2>
			<hr>

			<h3 style="margin-bottom:0;"><?php _e('The Basics','idea-factory');?></h3>
			<p style="margin-top:5px;"><?php _e('After you activate <em>Idea Factory</em>, it will automatically be available at <a href="'.get_post_type_archive_link( $domain ).'" target="_blank">'.get_post_type_archive_link( $domain ).'</a>. You can rename this in the settings or deactivate it all together and use the shortcode instead. By default voting is limited to logged in users, however you can activate public voting that would work (in addition to) logged in voting.','idea-factory');?></p>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;"><?php _e('The Shortcode','idea-factory');?></h3>
			<p style="margin-top:5px;"><?php _e('You can additionally display the form and ideas via a shortcode as documented below.','idea-factory');?></p>

			<code>[idea_factory hide_submit="off" hide_votes="off" hide_voting="off"]</code>

			<ul>
				<li><strong><?php _e('Hide Submit','idea-factory');?></strong> - <?php _e('Set this to "on" to hide the submission button and form.','idea-factory');?></li>
				<li><strong><?php _e('Hide Votes','idea-factory');?></strong> - <?php _e('Set this to "on" to hide the votes.','idea-factory');?></li>
				<li><strong><?php _e('Hide Voting','idea-factory');?></strong> - <?php _e('Set this to "on" to hide the voting features.','idea-factory');?></li>
			</ul>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;"><?php _e('How Voting Works','idea-factory');?></h3>
			<p style="margin-top:5px;"><?php _e('Voting is available to logged in users, and logged out users (with the option enabled). Total votes are stored in the post meta table for (logged in users). Once a user votes, a flag is recorded in the user_meta table (logged in users), preventing this user from being able to vote again on the same idea.</br></br>In the case of public voting, voters IP addresses are recorded into a custom table. From there the logic works the same, only difference is where the data is stored.','idea-factory');?></p>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;"><?php _e('How the Threshold Works','idea-factory');?></h3>
			<p style="margin-top:5px;"><?php _e('The threshold allows individual ideas to automatically be assigned a status based on a grading formula. For example, if you set this threshold to 10, then when the total votes reaches 10 it will trigger the grading. A vote up, and vote down, both count. In the end, if the total votes is over 10, and the total up votes is over 10, it passes. If not, it fails. Otherwise, the status remains open.','idea-factory');?></p>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;"><?php _e('Reset','idea-factory');?></h3>
			<p style="margin-top:5px;"><?php _e('On your left you will see the Reset option. When you click into this menu, and you click the red Reset button, it will reset all the votes back to zero. There is no going back, so be sure this is what you want to do when you click that button.','idea-factory');?></p>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;"><?php _e('Developers','idea-factory');?></h3>
			<p style="margin-top:5px;"><?php _e('Full documentation of hooks, actions, filters, and helper functions are available on the GitHub wiki page located <a href="https://github.com/tmeister/idea-factory/wiki">here</a>','idea-factory');?>.</p>

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

		$screen = get_current_screen();

		if ( 'ideas_page_idea-factory-reset' == $screen->id ) {

			?>
				<!-- Reset Votes -->
				<script>
					jQuery(document).ready(function($){
						// reset post meta
					  	jQuery('.idea-factory-reset--votes').click(function(e){

					  		e.preventDefault();

					  		var data = {
					            action: $(this).hasClass('reset-db') ? 'idea_factory_db_reset' : 'idea_factory_reset',
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

		<?php }

	}

	/**
	*
	*	Process the votes reste
	*	@since 1.1
	*/
	function idea_factory_reset(){

		check_ajax_referer( 'idea-factory-reset', 'security' );

		if ( !current_user_can('manage_options') )
			exit;

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

		echo __('All logged-in votes reset!','idea-factory');

		exit;

	}

	/**
	*
	*	Process the votes database reset
	*	@since 1.1
	*/
	function idea_factory_db_reset(){

		check_ajax_referer( 'idea-factory-reset', 'security' );

		if ( !current_user_can('manage_options') )
			exit;

	    global $wpdb;

	    $table = $wpdb->base_prefix.'idea_factory';

	   	$delete = $wpdb->query('TRUNCATE TABLE '.$table.'');

		_e('All public votes reset!!','idea-factory');

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
                    'desc' 				=> '<a href="'.get_post_type_archive_link( $domain ).'">'. __( 'Link to ideas page', 'idea-factory' ) .'</a> - ' . __( 'By default its called Ideas. You can rename this here.', 'idea-factory' ),
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
                    'name' 				=> 'if_public_voting',
                    'label' 			=> __( 'Enable Public Voting', 'idea-factory' ),
                    'desc' 				=> __( 'Enable the public (non logged in users) to submit and vote on new ideas.', 'idea-factory' ),
                    'type'				=> 'checkbox',
                    'default' 			=> '',
                    'sanitize_callback' => 'idea_factory_sanitize_checkbox'
                ),
            	array(
                    'name' 				=> 'if_threshold',
                    'label' 			=> __( 'Voting Threshold', 'idea-factory' ),
                    'desc' 				=> __( 'Specify an optional number of votes that each idea must reach in order for its status to be automatically updated to "approved" , "declined", or "open."', 'idea-factory' ),
                    'type' 				=> 'text',
                    'default' 			=> '',
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
                ),
                array(
                    'name' 				=> 'if_disable_archive',
                    'label' 			=> __( 'Disable Archive', 'idea-factory' ),
                    'desc' 				=> __( 'Disable the automatic archive. This assumes you will be using the shortcode instead to show the ideas on a page that you specify.', 'idea-factory' ),
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







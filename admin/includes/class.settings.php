<?php

class ideaFactorySettings {

    private $options;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page() {

        add_submenu_page( 'edit.php?post_type=ideas', 'Settings', 'Settings', 'manage_options', 'settings', array($this,'create_admin_page') );
    }

    /**
     	* Options page callback
    */
    public function create_admin_page() {

        $this->options = get_option( 'idea_factory_options' );
        ?>
        <div class="wrap">
            <h2>Idea Factory Settings</h2>
            <form method="post" action="options.php">
            <?php
                settings_fields( 'if_option_group' );
                do_settings_sections( 'idea-factory-settings' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     	* Register and add settings
    */
    public function page_init() {

        register_setting('if_option_group','idea_factory_options',	array( $this, 'sanitize' ));
        add_settings_section('idea_factory','',						array( $this, 'print_section_info' ),'idea-factory-settings');
        add_settings_field('id_number','ID Number',					array( $this, 'id_number_callback' ),'idea-factory-settings','idea_factory');
        add_settings_field('title','Title',							array( $this, 'title_callback' ),'idea-factory-settings','idea_factory');
    }

    /**
     	* Sanitize each setting field as needed
     	*
     	* @param array $input Contains all settings fields as array keys
    */
    public function sanitize( $input ) {
        $new_input = array();
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        return $new_input;
    }

    /**
     	* Print the Section text
    */
    public function print_section_info() {
        print 'Enter your settings below:';
    }

    /**
     	* Get the settings option array and print one of its values
    */
    public function id_number_callback() {
        printf(
            '<input type="text" id="id_number" name="idea_factory_options[id_number]" value="%s" />',
            isset( $this->options['id_number'] ) ? esc_attr( $this->options['id_number']) : ''
        );
    }

    /**
     	* Get the settings option array and print one of its values
    */
    public function title_callback() {
        printf(
            '<input type="text" id="title" name="idea_factory_options[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }
}
new ideaFactorySettings;
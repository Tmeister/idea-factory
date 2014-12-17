<?php

/**
*
*	Main class responsible for logging public voting events
*	@since 1.2
*/

class ideaFactoryDB {

	private $table_name;
	private $db_version;

	function __construct() {

		global $wpdb;

		$this->table_name   = $wpdb->base_prefix . 'idea_factory';
		$this->db_version 	= IDEA_FACTORY_VERSION;


	}


	// insert events into db
	public function insert( $args = array() ) {

		global $wpdb;

		$defaults = array(
			'postid'	=>	'',
			'time'	=> '',
			'ip'	=>  ''
		);

		$args = wp_parse_args( $args, $defaults );

		$add = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$this->table_name} SET
					`postid`    = '%s',
					`time`    = '%s',
					`ip`    = '%s'
				;",
				absint( $args['postid'] ),
				date_i18n( 'Y-m-d H:i:s', $args['time'], true ),
				filter_var( $args['ip'], FILTER_VALIDATE_IP )
			)
		);

		do_action( 'idea_factory_public_vote', $args, $wpdb->insert_id );

		if( $add )
			return $wpdb->insert_id;
		return false;
	}

}
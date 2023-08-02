<?php
	function gpi_user_options_array() {
		GLOBAL $gpi_utils; 
		
		$ret = array();
		
		$ret['gp_database_name']							= array( 'type' => 'text', 'desc' => __( 'GlotPress DB Name', 'gp-integration'), 'setting' => htmlentities( $gpi_utils->get_option('gp_database_name') ) );
		$ret['gp_table_prefix'] 							= array( 'type' => 'text', 'desc' => __( 'GlotPress DB Prefix', 'gp-integration'), 'size' => 5, 'setting' => htmlentities( $gpi_utils->get_option('gp_table_prefix') ) );
		$ret['gp_path'] 									= array( 'type' => 'text', 'desc' => __( 'GlotPress Path', 'gp-integration'), 'size' => 60, 'setting' => htmlentities( $gpi_utils->get_option('gp_path') ) );
		$ret['gp_dir']										= array( 'type' => 'text', 'desc' => __( 'GlotPress Directory', 'gp-integration'), 'size' => 60, 'setting' => htmlentities( $gpi_utils->get_option('gp_dir') ) );

		return $ret;
	}

?>
<?php
	function gpi_user_options_array() {
		GLOBAL $gpi_utils; 
		
		$ret = array();
		
		$ret['gp_database_name']							= array( 'type' => 'text', 'desc' => 'GlotPress DB Name', 'setting' => htmlentities( $gpi_utils->get_option('gp_database_name') ) );
		$ret['gp_table_prefix'] 							= array( 'type' => 'text', 'desc' => 'GlotPress DB Prefix', 'size' => 5, 'setting' => htmlentities( $gpi_utils->get_option('gp_table_prefix') ) );
		$ret['gp_path'] 									= array( 'type' => 'text', 'desc' => 'GlotPress Path', 'size' => 60, 'setting' => htmlentities( $gpi_utils->get_option('gp_path') ) );
		$ret['gp_dir']										= array( 'type' => 'text', 'desc' => 'GlotPress Directory', 'size' => 60, 'setting' => htmlentities( $gpi_utils->get_option('gp_dir') ) );

		return $ret;
	}

?>
<?php
	function gpi_user_options_array() {
		$ret = array();
		
		$ret['gp_database_name']							= array( 'type' => 'text', 'desc' => 'GlotPress DB Name' );
		$ret['gp_table_prefix'] 							= array( 'type' => 'text', 'desc' => 'GlotPress DB Prefix', 'size' => 5 );
		$ret['gp_path'] 									= array( 'type' => 'text', 'desc' => 'GlotPress Path', 'size' => 60 );

		return $ret;
	}

?>
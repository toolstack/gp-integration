<?php
	if( is_admin() ) {
		// Store the new version information.
		update_option('gp_integration_plugin_version', GP_INTEGRATION_VERSION);

		if( $GPI_Installed == false ) {
		
			// If this is a first time install, we just need to setup the primary values in the tables.
			
			$gpi_utils->update_option('gp_database_name', DB_NAME );
			$gpi_utils->update_option('gp_table_prefix', 'gp_' );
			$gpi_utils->update_option('gp_path', plugin_dir_url( __FILE__ ) . 'gp' );
			$gpi_utils->update_option('gp_dir', ABSPATH . '/gp' );

		} else {

			// If this is an upgrade, we need to do anything.
			
		}
	}
?>
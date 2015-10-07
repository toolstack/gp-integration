<?php
/*
Plugin Name: GP Integration
Plugin URI: http://toolstack.com/gp-integration
Description: A fully integrated version of GlotPress for WordPress.
Version: 1.0
Author: Greg Ross
Author URI: http://toolstack.com
Text Domain: gp-integration
Domain Path: /languages/
License: GPL2
*/

	// These defines are used later for various reasons.
	define('GP_INTEGRATION_VERSION', '1.0');
	define('GP_INTEGRATON_REQUIRED_PHP_VERSION', '5.0' );

	include_once( 'ToolStack-WP-Utilities.class.php' );
	include_once( 'includes/gpi-settings.php' );

	GLOBAL $gpi_utils;
	
	// Create out global utilities object.  We might be tempted to load the user options now, but that's not possible as WordPress hasn't processed the login this early yet.
	$gpi_utils = new ToolStack_WP_Utilities_V2_5( 'gp_integration', __FILE__ );

	function gp_integration_php_after_plugin_row() {
		echo '<tr><th scope="row" class="check-column"></th><td class="plugin-title" colspan="10"><span style="padding: 3px; color: white; background-color: red; font-weight: bold">&nbsp;&nbsp;' . __('ERROR: GlotPress Integration has detected an unsupported version of PHP, GlotPress Integration will not function without PHP Version ', 'gp-integraiton') . GP_INTEGRATON_REQUIRED_PHP_VERSION . __(' or higher!', 'gp-integraiton') . '  ' . __('Your current PHP version is', 'gp-integraiton') . ' ' . phpversion() . '.&nbsp;&nbsp;</span></td></tr>';
	}
	
	// Check the PHP version, if we don't meet the minimum version to run WP Statistics return so we don't cause a critical error.
	if( !version_compare( phpversion(), GP_INTEGRATON_REQUIRED_PHP_VERSION, ">=" ) ) { 
		add_action('after_plugin_row_' . plugin_basename( __FILE__ ), 'gp_integration_php_after_plugin_row', 10, 2);
		return; 
	} 

	GLOBAL $wpdb;
	
	$gpdb = $wpdb;
	$gpi_remote_db = false;
	$gpi_database = $gpi_utils->get_option( 'gp_database_name' );
	
	if( $gpi_database != '' && $gpi_database != DB_NAME ) { $gpdb = new wpdb( DB_USER, DB_PASSWORD, $gpi_database, DB_HOST ); $gpi_remote_db = true; }
	
	// Check to see if we're installed and are the current version.
	$GPI_Installed = get_option('gp_integration_plugin_version');
	if( $GPI_Installed != GP_INTEGRATION_VERSION ) {	
		include_once( dirname( __FILE__ ) . '/gpi-install.php' );
	}

	// Add a settings link to the plugin list.
	function gp_integration_settings_links( $links, $file ) {
		array_unshift( $links, '<a href="' . admin_url( 'admin.php?page=gp-integration-settings' ) . '">' . __( 'Settings', 'gp-integraiton' ) . '</a>' );
		
		return $links;
	}
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'gp_integration_settings_links', 10, 2 );

	// Add a WordPress plugin page and rating links to the meta information to the plugin list.
	function gp_integration_add_meta_links($links, $file) {
		if( $file == plugin_basename(__FILE__) ) {
			$plugin_url = 'http://wordpress.org/plugins/gp-integration/';
			
			$links[] = '<a href="'. $plugin_url .'" target="_blank" title="'. __('Click here to visit the plugin on WordPress.org', 'gp-integraiton') .'">'. __('Visit WordPress.org page', 'gp-integraiton') .'</a>';
			
			$rate_url = 'http://wordpress.org/support/view/plugin-reviews/gp-integration?rate=5#postform';
			$links[] = '<a href="'. $rate_url .'" target="_blank" title="'. __('Click here to rate and review this plugin on WordPress.org', 'gp-integraiton') .'">'. __('Rate this plugin', 'gp-integraiton') .'</a>';
		}
		
		return $links;
	}
	add_filter('plugin_row_meta', 'gp_integration_add_meta_links', 10, 2);
	
	// This function adds the primary menu to WordPress.
	function gp_integration_menu() {
		GLOBAL $gpi_remote_db;
		
		// Add the top level menu.
		add_menu_page(__('GlotPress', 'gp-integraiton'), __('GlotPress', 'gp-integraiton'), 'read', __FILE__, 'gp_integration_main_page');

		// Add the sub items.
		add_submenu_page(__FILE__, __('Project Management', 'gp-integraiton'), __('Project Management', 'gp-integraiton'), 'manage_options', 'gpi_projects', 'gp_integration_projects_page');
		add_submenu_page(__FILE__, __('Translation Set Management', 'gp-integraiton'), __('Translation Set Management', 'gp-integraiton'), 'manage_options', 'gpi_translation_sets', 'gp_integration_translation_sets_page');
		if( $gpi_remote_db ) {
			add_submenu_page(__FILE__, __('Users', 'gp-integraiton'), __('Users', 'gp-integraiton'), 'manage_options', 'gpi_users', 'gp_integration_users_page');
		}
		add_submenu_page(__FILE__, __('Admin Users', 'gp-integraiton'), __('Admin Users', 'gp-integraiton'), 'manage_options', 'gpi_admin_users', 'gp_integration_admin_users_page');
		add_submenu_page(__FILE__, __('Settings', 'gp-integraiton'), __('Settings', 'gp-integraiton'), 'manage_options', 'gpi_settings', 'gp_integration_admin_page');
		
	}
	add_action('admin_menu', 'gp_integration_menu');

	// Load the translation code.
	function gp_integration_language() {
		load_plugin_textdomain('gp-integraiton', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
		__('GP Integration', 'gp-integraiton');
		__('A fully integrated version of GlotPress for WordPress.', 'gp-integraiton');
	}

	// Add translation action.
	add_action('plugins_loaded', 'gp_integration_language');
	
	function gp_integration_main_page() {
		echo gp_integration_shortcode(null);
	}

	function gp_integration_get_gp_table_prefix() {
		GLOBAL $gpi_utils;

		$table_prefix = $gpi_utils->get_option('gp_table_prefix');

		// if the table prefix hasn't been defined yet, set it to the default and save it.
		if( $table_prefix == '' ) { 
			$table_prefix = 'gp_'; 
			$gpi_utils->update_option('gp_table_prefix', $table_prefix ); 
		}
		
		return $table_prefix;
	}
	
	function gp_integration_get_gp_path() {
		GLOBAL $gpi_utils;

		$gp_path = $gpi_utils->get_option('gp_path');

		// if the table prefix hasn't been defined yet, set it to the default and save it.
		if( $gp_path == '' ) { 
			$gp_path = plugin_dir_url( __FILE__ ) . 'gp'; 
			$gpi_utils->update_option('gp_path', $gp_path );
		}
	
		return $gp_path;
	}

	function gp_integration_get_gp_dir() {
		GLOBAL $gpi_utils;

		$gp_dir = $gpi_utils->get_option('gp_dir');

		// if the dir hasn't been defined yet, set it to the default and save it.
		if( $gp_dir == '' ) { 
			$gp_dir = ABSPATH . 'gp'; 
			$gpi_utils->update_option('gp_dir', $gp_dir );
		}
	
		return $gp_dir;
	}

	function gp_integration_get_user_list() {
		GLOBAL $gpdb, $gpi_remote_db, $gpi_utils;
		
		$table_prefix = $gpi_utils->get_option('gp_table_prefix');
		
		if( $gpi_remote_db ) {
			$users = $gpdb->get_results( "SELECT * FROM {$table_prefix}users ORDER BY user_login ASC" );
		}
		else {
			$users = get_users('orderby=loginname');
		}
		
		return $users;
	}
	
	function gp_integration_admin_users_page() {
		GLOBAL $gpdb, $gpi_utils;
		
		$is_admin = current_user_can( 'manage_options' );

		if( !$is_admin ) {
			echo '<div class="update-nag"><p>' . __('You do not have permissions to this page!', 'gp-integraiton') . '</p></div>';
			return;
		}

		$table_prefix = gp_integration_get_gp_table_prefix();
		
		if( array_key_exists( 'add-admin', $_POST ) ) {
			if( $_POST['selected-user'] == 'select user' ) {
				echo '<div class="update-nag"><p>' . __('Please select a user to add!', 'gp-integraiton') . '</p></div>';
			}
			else {
				$sqlstring = $gpdb->prepare( 'INSERT INTO ' . $table_prefix . 'permissions (user_id, action) VALUES ( %d, %s);', $_POST['selected-user'], 'admin' );
				$gpdb->query( $sqlstring );
			}
		}
		else {
			if( is_array( $_POST ) ) {
				foreach( $_POST as $key => $value ) {
					if( substr( $key, 0, 13) == 'remove-admin-' ) {
						$user_id_to_remove = intval(str_replace( 'remove-admin-', '', $key ));
						
						if( $user_id_to_remove > 0 ) {
							$sqlstring = $gpdb->prepare( 'DELETE FROM ' . $table_prefix . 'permissions WHERE user_id=%d AND action=%s;', $user_id_to_remove, 'admin' );
							$gpdb->query( $sqlstring );
						}
						else {
							echo '<div class="update-nag"><p>' . __('Invalid user selected to remove!', 'gp-integraiton') . '</p></div>';
						}
						
					}
				}
			}
		}
		
		$admin_users = $gpdb->get_results("SELECT user_id FROM {$table_prefix}permissions WHERE action = 'admin'");
		$users = gp_integration_get_user_list();

		foreach( $users as $user ) {
			$users_array_by_id[$user->ID] = $user;
		}
		
		echo '<div class="wrap">' . "<br>";
		echo '	' . screen_icon('options-general') . "\n";
		echo '	<h2>' . __('Admin Users Management', 'gp-integraiton') . '</h2>' . "\n";
		echo '	<br>' . "\n";

		gp_integration_confirm_delete_javascript();
		
		echo "\n";
		echo '<form method="post">';
		echo '		<table class="widefat">' . "\n";
		echo '			<thead>' . "\n";
		echo '			<tr>';
		echo '				<th>' . __('User ID', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('E-Mail', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Action', 'gp-integraiton') . '</td>' . "\n";
		echo '			</tr>' . "\n";
		echo '			</thead>' . "\n";
		
		echo '			<tbody>' . "\n";
		
		$alternate = false;
		
		foreach( $admin_users as $user_id ) {
			$user_obj = $users_array_by_id[$user_id->user_id];
			$admin_users[] .= $user_obj->user_login;
				
			if( !$alternate ) { $alternate = true; $class = ' class="alternate"';} else { $alternate = false; $class = ''; }
			echo '			<tr' . $class . '>' . "\n";
			echo '				<td>' . esc_html( $user_obj->user_login ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $user_obj->user_email ) . '</td>' . "\n";
			echo '				<td><input type="submit" name="remove-admin-' . $user_id->user_id .'" value="' . __('Remove', 'gp-integraiton') . '" class="button-primary" onclick="return GPIntegrationConfirmAction(\'' . __('Are you sure you wish to remove this users admin privileges?', 'gp-integraiton'). '\')"></input></td>' . "\n";
			echo '			</tr>' . "\n";
		}
		
		if( !$alternate ) { $alternate = true; $class = ' class="alternate"';} else { $alternate = false; $class = ''; }
		echo '			<tr' . $class . '>' . "\n";
		echo '				<td>';

		echo '<select name="selected-user">';
		echo '<option value="select user" SELECTED> ' . __('Select user', 'gp-integraiton') . '</option>';

		foreach( $users as $user ) {

			if( !in_array( $user->user_login, $admin_users ) ) {
				echo '<option value="' . $user->ID . '">' . esc_html( $user->user_login ). '</option>';
			}
			
		}
		
		echo '</select>';
		
		echo '</td>' . "\n";

		echo '				<td></td>' . "\n";
		echo '				<td><input type="submit" name="add-admin" value="' . __('Add', 'gp-integraiton') . '" class="button-primary"></input></td>' . "\n";
		echo '			</tr>' . "\n";

		echo '			</tbody>' . "\n";
		echo '		</table>' . "\n";
		
		echo '</form>' . "\n";
		
		
		echo '</div>' . "<br>";
	}
	
	function gp_integration_users_page() {
		GLOBAL $gpdb, $gpi_utils;
		
		$is_admin = current_user_can( 'manage_options' );

		if( !$is_admin ) {
			echo '<div class="update-nag"><p>' . __('You do not have permissions to this page!', 'gp-integraiton') . '</p></div>';
			return;
		}

		$table_prefix = gp_integration_get_gp_table_prefix();
		
		if( array_key_exists( 'add-user', $_POST ) ) {
			if( array_key_exists( 'add_login_name', $_POST ) ) {
				$user_login = $_POST['add_login_name'];
				$user_nicename = $user_login;
				$display_name = $user_login;
				$user_email = '';
				$user_url = '';
				$user_registered = date("Y-m-d H:i:s");
				$user_status = 0;
				$passowrd = '';
				
				if( array_key_exists( 'add_nice_name', $_POST ) ) 		{ if( $_POST['add_nice_name'] != '' ) 		{ $user_nicename = $_POST['add_nice_name']; } }
				if( array_key_exists( 'add_display_name', $_POST ) ) 	{ if( $_POST['add_display_name'] != '' ) 	{ $display_name = $_POST['add_display_name']; } }
				if( array_key_exists( 'add_email', $_POST ) ) 			{ if( $_POST['add_email'] != '' ) 			{ $user_email = $_POST['add_email']; } }
				if( array_key_exists( 'add_url', $_POST ) ) 			{ if( $_POST['add_url'] != '' ) 			{ $user_url = $_POST['add_url']; } }
				if( array_key_exists( 'add_password', $_POST ) ) 		{ if( $_POST['add_password'] != '' ) 		{ $password = wp_hash_password( $_POST['add_password'] ); } }
				
				$sqlstring = $gpdb->prepare( 'INSERT INTO ' . $table_prefix . 'users (user_login, user_nicename, display_name, user_email, user_url, user_registered, user_status, user_pass) VALUES (%s, %s, %s, %s, %s, %s, %d, %s );', $user_login, $user_nicename, $display_name, $user_email, $user_url, $user_registered, $user_status, $password );
				$gpdb->query( $sqlstring );
				
			}
			else {
				echo '<div class="update-nag"><p>' . __('Please select a user to add!', 'gp-integraiton') . '</p></div>';
			}
		}
		else {
			if( is_array( $_POST ) ) {
				foreach( $_POST as $key => $value ) {
					if( substr( $key, 0, 12) == 'delete-user-' ) {
						$user_id_to_delete = intval(str_replace( 'delete-user-', '', $key ));

						if( $user_id_to_delete > 0 ) {
							$sqlstring = $gpdb->prepare( 'DELETE FROM ' . $table_prefix . 'users WHERE ID=%d;', $user_id_to_delete );
							$gpdb->query( $sqlstring );

							$sqlstring = $gpdb->prepare( 'DELETE FROM ' . $table_prefix . 'usermeta WHERE user_id=%d;', $user_id_to_delete );
							$gpdb->query( $sqlstring );
						}
						else {
							echo '<div class="update-nag"><p>' . __('Invalid user selected to remove!', 'gp-integraiton') . '</p></div>';
						}
						
					}

					if( substr( $key, 0, 9) == 'pw-reset-' ) {
						$user_id_to_reset = intval(str_replace( 'pw-reset-', '', $key ));

						if( $user_id_to_reset > 0 ) {
							if( array_key_exists( 'password-' . $user_id_to_reset, $_POST ) ) {
								$password = $_POST['password-' . $user_id_to_reset];
								if( $password != '' ) {
									$password = wp_hash_password( $password );
								
									$sqlstring = $gpdb->prepare( 'UPDATE ' . $table_prefix . 'users SET user_pass=%s WHERE ID=%d;', $password, $user_id_to_reset );
									//$gpdb->query( $sqlstring );
								}
							}
						}
						else {
							echo '<div class="update-nag"><p>' . __('Invalid user selected to remove!', 'gp-integraiton') . '</p></div>';
						}
						
					}

				}
			}
		}
		
		$users = gp_integration_get_user_list();

		echo '<div class="wrap">' . "<br>";
		echo '	' . screen_icon('options-general') . "\n";
		echo '	<h2>' . __('User Management', 'gp-integraiton') . '</h2>' . "\n";
		echo '	<br>' . "\n";

		gp_integration_confirm_delete_javascript();
		
		echo "\n";
		echo '<form method="post">';
		echo '		<table class="widefat">' . "\n";
		echo '			<thead>' . "\n";
		echo '			<tr>';
		echo '				<th>' . __('ID', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Login Name', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Nice Name', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Display Name', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('E-Mail', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('URL', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Registration Date', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Status', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Password', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Action', 'gp-integraiton') . '</td>' . "\n";
		echo '			</tr>' . "\n";
		echo '			</thead>' . "\n";
		
		echo '			<tbody>' . "\n";
		
		$alternate = false;
		
		foreach( $users as $user_obj ) {
			if( !$alternate ) { $alternate = true; $class = ' class="alternate"';} else { $alternate = false; $class = ''; }
			echo '			<tr' . $class . '>' . "\n";
			echo '				<td>' . esc_html( $user_obj->ID ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $user_obj->user_login ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $user_obj->user_nicename ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $user_obj->display_name ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $user_obj->user_email ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $user_obj->user_url ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $user_obj->user_registered ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $user_obj->user_status) . '</td>' . "\n";
			echo '				<td><input type="text" size="10" name="password-' . $user_obj->ID . '"></td>' . "\n";
			echo '				<td><input type="submit" name="pw-reset-' . $user_obj->ID . '" value="' . __('PW Reset', 'gp-integraiton') . '" class="button-primary"></input>&nbsp;&nbsp;<input type="submit" name="delete-user-' . $user_obj->ID .'" value="' . __('Delete', 'gp-integraiton') . '" class="button-primary" onclick="return GPIntegrationConfirmAction(\'' . __('Are you sure you wish to delete this user?  This cannot be undone!', 'gp-integraiton'). '\')"></input></td>' . "\n";
			echo '			</tr>' . "\n";
		}
		
		echo '			<tr' . $class . '>' . "\n";
		echo '				<td></td>' . "\n";
		echo '				<td><input type="text" size="10" name="add_login_name"></td>' . "\n";
		echo '				<td><input type="text" size="10" name="add_nice_name"></td>' . "\n";
		echo '				<td><input type="text" size="10" name="add_display_name"></td>' . "\n";
		echo '				<td><input type="text" size="20" name="add_email"></td>' . "\n";
		echo '				<td><input type="text" size="15" name="add_url"></td>' . "\n";
		echo '				<td></td>' . "\n";
		echo '				<td></td>' . "\n";
		echo '				<td><input type="text" size="10" name="add_password"></td>' . "\n";
		echo '				<td><input type="submit" name="add-user" value="' . __('Add', 'gp-integraiton') . '" class="button-primary"></input></td>' . "\n";
		echo '			</tr>' . "\n";

		echo '			</tbody>' . "\n";
		echo '		</table>' . "\n";
		
		echo '</form>' . "\n";
		
		
		echo '</div>' . "<br>";
	}

	function gp_integration_delete_translation_set( $set_id ) {
		GLOBAL $gpdb;

		if( $set_id < 1 ) { return; }
		
		$table_prefix = gp_integration_get_gp_table_prefix();
		
		// First remove it from the set list.
		$sqlstring = $gpdb->prepare( 'DELETE FROM ' . $table_prefix . 'translation_sets WHERE id=%d;', $set_id );
		$gpdb->query( $sqlstring );

		// Then remove all the translations associated with it.
		$sqlstring = $gpdb->prepare( 'DELETE FROM ' . $table_prefix . 'translations WHERE translation_set_id=%d;', $set_id );
		$gpdb->query( $sqlstring );
		
		// And finally the glossaries.
		$sqlstring = $gpdb->prepare( 'DELETE FROM ' . $table_prefix . 'glossaries WHERE translation_set_id=%d;', $set_id );
		$gpdb->query( $sqlstring );
		
	}
	
	function gp_integration_projects_page() {
		GLOBAL $gpdb, $gpi_utils;
		
		$is_admin = current_user_can( 'manage_options' );

		if( !$is_admin ) {
			echo '<div class="update-nag"><p>' . __('You do not have permissions to this page!', 'gp-integraiton') . '</p></div>';
			return;
		}

		$table_prefix = gp_integration_get_gp_table_prefix();
		
		if( is_array( $_POST ) ) {
			foreach( $_POST as $key => $value ) {
				if( substr( $key, 0, 15) == 'delete-project-' ) {
					$project_id_to_delete = intval(str_replace( 'delete-project-', '', $key ));
					
					if( $project_id_to_delete > 0 ) {
						// First delete the project from the projects table.
						$sqlstring = $gpdb->prepare( 'DELETE FROM ' . $table_prefix . 'projects WHERE id=%d;', $project_id_to_delete );
						$gpdb->query( $sqlstring );
						
						// Now the permissions.
						$sqlstring = $gpdb->prepare( 'DELETE FROM ' . $table_prefix . 'permissions WHERE object_id=%d;', $project_id_to_delete );
						$gpdb->query( $sqlstring );
						
						// Now the originals.
						$sqlstring = $gpdb->prepare( 'DELETE FROM ' . $table_prefix . 'originals WHERE project_id=%d;', $project_id_to_delete );
						$gpdb->query( $sqlstring );
						
						// Now the translation sets.
						$translation_sets = $gpdb->get_results($gpdb->prepare("SELECT id FROM {$table_prefix}translation_sets WHERE project_id=%d", $project_id_to_delete) );

						foreach( $translation_sets as $set ) {
							gp_integration_delete_translation_set( $set->id );
						}
						
						echo '<div class="updated"><p>' . __('Project deleted!', 'gp-integraiton') . '</p></div>';
					}
					else {
						echo '<div class="update-nag"><p>' . __('Invalid project selected to remove!', 'gp-integraiton') . '</p></div>';
					}
					
				}
			}
		}
		
		$projects = $gpdb->get_results("SELECT * FROM {$table_prefix}projects");
		
		echo '<div class="wrap">' . "<br>";
		echo '	' . screen_icon('options-general') . "\n";
		echo '	<h2>' . __('Project Management', 'gp-integraiton') . '</h2>' . "\n";
		echo '	<br>' . "\n";

		gp_integration_confirm_delete_javascript();
		
		echo "\n";
		echo '<form method="post">';
		echo '		<table class="widefat">' . "\n";
		echo '			<thead>' . "\n";
		echo '			<tr>';
		echo '				<th>' . __('ID', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Name', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Slug', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Path', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Description', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Parent', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Source URL', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Active', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Action', 'gp-integraiton') . '</td>' . "\n";
		echo '			</tr>' . "\n";
		echo '			</thead>' . "\n";
		
		echo '			<tbody>' . "\n";
		
		$alternate = false;
		
		foreach( $projects as $project ) {
			if( !$alternate ) { $alternate = true; $class = ' class="alternate"';} else { $alternate = false; $class = ''; }
			echo '			<tr' . $class . '>' . "\n";
			echo '				<td>' . esc_html( $project->id ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $project->name ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $project->slug ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $project->path ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $project->description ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $project->parent_project_id ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $project->source_url_template ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $project->active ) . '</td>' . "\n";
			echo '				<td><input type="submit" name="delete-project-' . $project->id .'" value="' . __('Delete', 'gp-integraiton') . '" class="button-primary" onclick="return GPIntegrationConfirmAction(\'' . __('Are you sure you wish to delete this project?  This action cannot be undone!', 'gp-integraiton'). '\')"></input></td>' . "\n";
			echo '			</tr>' . "\n";
		}
		
		echo '			</tbody>' . "\n";
		echo '		</table>' . "\n";
		
		echo '</form>' . "\n";
		
		
		echo '</div>' . "<br>";
	}

	function gp_integration_translation_sets_page() {
		GLOBAL $gpdb, $gpi_utils;
		
		$is_admin = current_user_can( 'manage_options' );

		if( !$is_admin ) {
			echo '<div class="update-nag"><p>' . __('You do not have permissions to this page!', 'gp-integraiton') . '</p></div>';
			return;
		}

		// Enqueue jQuery UI
		wp_enqueue_script( 'jquery-ui' );
		wp_enqueue_script( 'jquery-ui-dialog' );	
		
		$table_prefix = gp_integration_get_gp_table_prefix();
		
		if( is_array( $_POST ) ) {
			foreach( $_POST as $key => $value ) {
				if( substr( $key, 0, 23) == 'delete-translation-set-' ) {
					$set_id_to_delete = intval(str_replace( 'delete-translation-set-', '', $key ));
					
					if( $set_id_to_delete > 0 ) {
						gp_integration_delete_translation_set( $set_id_to_delete );
						echo '<div class="updated"><p>' . __('Translation set deleted!', 'gp-integraiton') . '</p></div>';
					}
					else {
						echo '<div class="update-nag"><p>' . __('Invalid project selected to remove!', 'gp-integraiton') . '</p></div>';
					}
					
				}
			}
		}
		
		$projects = $gpdb->get_results("SELECT * FROM {$table_prefix}projects");
		
		echo '<div class="wrap">' . "<br>";
		echo '	' . screen_icon('options-general') . "\n";
		echo '	<h2>' . __('Translation Set Management', 'gp-integraiton') . '</h2>' . "\n";
		echo '	<br>' . "\n";

		gp_integration_confirm_delete_javascript();
		
		echo "\n";
		echo '<form method="post">';
		
		$current_project_id = 0;
		if( array_key_exists( 'current-project-id', $_POST ) ) { 
			$current_project_id = intval( $_POST['current-project-id'] );
		}
		
		if( array_key_exists( 'selected-project', $_POST ) ) { 
			$selected_project = intval( $_POST['selected-project'] );
			if( $current_project_id != $selected_project && $selected_project > 0 ) {
				$current_project_id = $selected_project;
			}
		}
		
		if( $current_project_id == 0 ) {
			if( count( $projects ) == 1 )	{
				$current_project_id = $projects[0]->id;
			}
		}
		
		echo '<input type="hidden" value="' . $current_project_id . '" name="current-project-id" />';
		
		if( count( $projects ) > 1 ) {
			echo __('Select Project', 'gp-integraiton') . ': <select name="selected-project">';
			if( $current_project_id > 0 ) { $selected = ''; } else { $selected = ' SELECTED'; }
			echo '<option value="select project" SELECTED> ' . __('Select project', 'gp-integraiton') . '</option>';

			foreach( $projects as $project ) {

				if( $project->id == $current_project_id ) { $selected = ' SELECTED'; } else { $selected = ''; }
				echo '<option value="' . $project->id . '"' . $selected . '>' . esc_html( $project->name ). '</option>';
				
			}
			
			echo '</select>';

			echo '&nbsp;&nbsp;<input type="submit" name="select-project" value="' . __('Select', 'gp-integraiton') . '" class="button-primary"></input>' . "\n";
			echo '<br><br>' . "\n";
		}
		
		$translation_sets = $gpdb->get_results($gpdb->prepare("SELECT * FROM {$table_prefix}translation_sets WHERE project_id=%s",$current_project_id));
		
		echo '		<table class="widefat">' . "\n";
		echo '			<thead>' . "\n";
		echo '			<tr>';
		echo '				<th>' . __('ID', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Name', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Slug', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Locale', 'gp-integraiton') . '</td>' . "\n";
		echo '				<th>' . __('Action', 'gp-integraiton') . '</td>' . "\n";
		echo '			</tr>' . "\n";
		echo '			</thead>' . "\n";
		
		echo '			<tbody>' . "\n";
		
		$alternate = false;
		
		foreach( $translation_sets as $set ) {
			if( !$alternate ) { $alternate = true; $class = ' class="alternate"';} else { $alternate = false; $class = ''; }
			echo '			<tr' . $class . '>' . "\n";
			echo '				<td>' . esc_html( $set->id ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $set->name ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $set->slug ) . '</td>' . "\n";
			echo '				<td>' . esc_html( $set->locale ) . '</td>' . "\n";
			echo '				<td><input type="submit" name="delete-translation-set-' . $set->id .'" value="' . __('Delete', 'gp-integraiton') . '" class="button-primary" onclick="return GPIntegrationConfirmAction(\'' . __('Are you sure you wish to delete this translation set?  This action cannot be undone!', 'gp-integraiton'). '\')"></input></td>' . "\n";
			echo '			</tr>' . "\n";
		}
		
		echo '			</tbody>' . "\n";
		echo '		</table>' . "\n";
		
		echo '</form>' . "\n";
		
		
		echo '</div>' . "<br>";
	}

	// This function adds the menu icon to the top level menu.  WordPress 3.8 changed the style of the menu a bit and so a different css file is loaded.
	function gp_integration_menu_icon() {
	
		wp_enqueue_style('gpintegration-admin-css', plugin_dir_url(__FILE__) . 'css/admin.css', true, '1.0');
	}
	add_action('admin_head', 'gp_integration_menu_icon');
	
	add_shortcode( 'gp-integration', 'gp_integration_shortcode' );

	function gp_integration_shortcode( $atts ) {
		/*
			GP Integratoin shortcode has no parameters.
		*/
		
		$gp_path = gp_integration_get_gp_path();		
		
		$result = '<script type="text/javascript">// <![CDATA[' . "\n";
		$result .= 'jQuery(document).ready(function(){' . "\n";
		$result .= '	setInterval( function() { AdjustiFrameHeight( \'gp_int_glotpress_frame\', 200); }, 1000 );' . "\n";
		$result .= '});' . "\n";
		$result .= '' . "\n";
		$result .= 'function AdjustiFrameHeight(id,fudge)' . "\n";
		$result .= '      {' . "\n";
		$result .= '      var frame = document.getElementById(id);' . "\n";
		$result .= '      var content = jQuery(\'.embed-wrap\');' . "\n";
		$result .= '      var height = frame.contentDocument.body.offsetHeight + fudge;' . "\n";
		$result .= '      content.height( height );' . "\n";
		$result .= '      frame.height = height;' . "\n";
		$result .= '      }' . "\n";
		$result .= '// ]]></script>' . "\n";

		$result .= '<iframe id="gp_int_glotpress_frame" src="' . $gp_path . '" width="100%" height="150" frameborder="0" scrolling="no" onload="AdjustiFrameHeight(\'gp_int_glotpress_frame\',200);"></iframe>';
		
		return $result;
	}
	
	add_shortcode( 'gp-integration-link', 'gp_integration_link_shortcode' );

	function gp_integration_link_shortcode( $atts ) {
		return '<a href="' . gp_integration_get_gp_path() . '" target="_blank">GlotPress</a>';
	}
	function gp_integration_confirm_delete_javascript() {
?>
<script type="text/javascript">
	function GPIntegrationConfirmAction(message) {
		
		var agree = confirm(message);

		if(!agree)
			return false;
		
		return true;
	}
</script>
<?php
	}
	
	add_shortcode( 'gp-integration-translator-list', 'gp_integration_translator_list_shortcode' );
	
	function gp_integration_translator_list_shortcode( $atts ) {
		GLOBAL $gpdb;

		$table_prefix = gp_integration_get_gp_table_prefix();
		
		$project_id = '%';
		if( is_array( $atts ) ) {
			$projects = null;
			if( array_key_exists( 'projectname', $atts ) ) { 
				$projects = $gpdb->get_results("SELECT * FROM {$table_prefix}projects");
		
				foreach( $projects as $key => $value ) {
					if( $value->name == $atts['projectname'] ) { $project_id = $value->id; }
				}
			}
			
			if( array_key_exists( 'projectslug', $atts ) ) { 
				if( $projects == null ) {
					$projects = $gpdb->get_results("SELECT * FROM {$table_prefix}projects");
				}
			
				foreach( $projects as $key => $value ) {
					if( $value->slug == $atts['projectslug'] ) { $project_id = $value->id; }
				}
			}

			if( array_key_exists( 'projectid', $atts ) ) { 
				$project_id = (int)$atts['projectid']; 
			}
			
		}
		
		wp_enqueue_style( 'dashicons' ); 

		// Grab the locale definitions from GlotPress, if it doesn't exist then use the one included in GP Integration.
		$locale_file = gp_integration_get_gp_dir() . '/locales/locales.php';
		if( file_exists( $locale_file ) ) {
			include_once( gp_integration_get_gp_dir() . '/locales/locales.php' );
		} else {
			include_once( dirname( __FILE__ ) . 'includes/locales.php' );
		}
		
		// They're a call so let's create it.
		$gpl = new GP_Locales;
		
		// Setup some variables to use later.
		$return = '<style type="text/css">.gptl-twitter, .gptl-twitter:focus, .gptl-twitter:hover, .gptl-twitter:link, .gptl-twitter:visited, .gptl-twitter:active { color: #55acee; } .gptl-facebook, .gptl-facebook:focus, .gptl-facebook:hover, .gptl-facebook:link, .gptl-facebook:visited, .gptl-facebook:active { color: #3A5795; }</style><table style="border: 0px;">';
		$names = array();
        $links = array();
		
		// Grab all of the approvers from the GlotPress permissions table and join it to the WordPress users table so we can get display names later.
		$result = $gpdb->get_results( "SELECT * FROM {$table_prefix}permissions INNER JOIN `{$gpdb->users}` on `{$table_prefix}permissions`.`user_id` = `{$gpdb->users}`.`ID` WHERE `{$table_prefix}permissions`.`action`='approve' AND `{$table_prefix}permissions`.`object_id` LIKE '{$project_id}|%'" );

		// Loop through all the results from the database and create a list of locales with all their approvers assocaited with them.
		foreach( $result as $row ) {
			$details = explode( '|', $row->object_id );

			if( $details === FALSE || !isset( $details[1] ) ) { continue; }

			$current = $gpl->locales[$details[1]];
			
			$names[$current->english_name][] = $row->display_name;
            $links[$row->display_name] = $row->user_url;
		}

		// Sort the locale list.
		ksort( $names );
		
		// Loop through all the locales to do the output.
		foreach( $names as $key => $values ) {
			// Sort the approvers names alphabetically.
			ksort( $values );
			
			foreach( $values as $keynumber => $display_name ) {
				if( $links[$display_name] ) {
					$nice_link = parse_url( $links[$display_name], PHP_URL_HOST );
					$nice_link = str_ireplace( 'www.', '', $nice_link );
					$nice_link = strtolower( $nice_link );
					
					if( strstr( $display_name, $nice_link ) ) { 
						$nice_link = ''; 
					} else { 
						switch( $nice_link ) {
							case 'twitter.com':
								$nice_link = ' <span class="dashicons dashicons-twitter gptl-twitter"></span>';
								
								break;
							case 'facebook.com':
								$nice_link = ' <span class="dashicons dashicons-facebook gptl-facebook"></span>';
							
								break;
							default:
								$nice_link = ' (' . htmlentities( $nice_link ) . ')'; 
							
								break;
						} 
					}
					
					$values[$keynumber] = '<a href="' . htmlentities( $links[$display_name], ENT_QUOTES ) . '" target="_blank">' . htmlentities( $display_name, ENT_QUOTES ) . $nice_link . '</a>';
				} else {
					$values[$keynumber] = htmlentities( $display_name, ENT_QUOTES );
				}
			} 

			// Create the return string.
			$return .= "<tr><td style=\"text-align: right; border: 0px; background: transparent; white-space: nowrap;\">" . htmlentities( $key, ENT_QUOTES ) . ":</td><td style=\"border: 0px; background: transparent; padding-left:5px;\">" . implode( ', ', $values ) . "</td></tr>\r\n";
		}
		
		$return .= '</table>';
		
		// Return the value.
		return $return;
	}

	/*
	 	This function generates the settings page and handles the actions associated with it.
	 */
	function gp_integration_admin_page()
		{
		include( dirname( __FILE__ ) . '/includes/page.settings.php' );
		}
		
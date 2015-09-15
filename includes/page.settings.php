<?php
		global $gpdb, $gpi_utils;

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tabs');
		
		wp_register_style("jquery-ui-css", plugin_dir_url(__FILE__) . "../css/jquery-ui-1.10.4.custom.css");
		wp_enqueue_style("jquery-ui-css");
		wp_register_style("jquery-ui-tabs-css", plugin_dir_url(__FILE__) . "../css/jquery-ui-tabs.css");
		wp_enqueue_style("jquery-ui-tabs-css");


		$gpi_options = gpi_user_options_array();

		if( array_key_exists( 'gp-integration-options-save', $_POST ) ) {
			foreach( $gpi_options as $key => $option ) {
				if( array_key_exists( $key, $_POST ) ) {
					$setting = esc_html( stripslashes( $_POST[$key] ) );
					$gpi_utils->update_option($key, $setting );
				}
				else if( $option['type'] == 'bool' ) {
					$gpi_utils->update_option($key, false);
				}
			}

		$gpi_options = gpi_user_options_array();
		}

	?>

<div class="wrap">

<script type="text/javascript">jQuery(document).ready(function() { jQuery("#tabs").tabs(); jQuery("#tabs").tabs("option", "active",0);} );</script>
<h2><?php _e('GP Integration Options');?></h2>

	<div id="tabs">
		<ul>
			<li><a href="#fragment-0"><span><?php _e('Options');?></span></a></li>
			<li><a href="#fragment-1"><span><?php _e('About');?></span></a></li>
		</ul>

		<div id="fragment-0">
			<form method="post">
<?php
		echo $gpi_utils->generate_options_table( $gpi_options ); 
?>				
				<div class="submit"><input type="submit" class="button button-primary" name="gp-integration-options-save" value="<?php _e('Update Options') ?>" /></div>
			</form>
		</div>
	
		<div id="fragment-1">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<td scope="row" align="center"><img src="<?php echo plugins_url('gp-integration/images/logo-250.png'); ?>"></td>
					</tr>

					<tr valign="top">
						<td scope="row" align="center"><h2><?php echo sprintf(__('GP Integrationr V%s'), GP_INTEGRATION_VERSION); ?></h2></td>
					</tr>

					<tr valign="top">
						<td scope="row" align="center"><p>by <a href="https://toolstack.com">Greg Ross</a></p></td>
					</tr>

					<tr valign="top">
						<td scope="row" align="center"><hr /></td>
					</tr>

					<tr valign="top">
						<td scope="row" colspan="2"><h2><?php _e('Rate and Review at WordPress.org'); ?></h2></td>
					</tr>
					
					<tr valign="top">
						<td scope="row" colspan="2"><?php _e('Thanks for installing GP Integration, I encourage you to submit a ');?> <a href="http://wordpress.org/support/view/plugin-reviews/gp-integration" target="_blank"><?php _e('rating and review'); ?></a> <?php _e('over at WordPress.org.  Your feedback is greatly appreciated!');?></td>
					</tr>
					
					<tr valign="top">
						<td scope="row" colspan="2"><h2><?php _e('Support'); ?></h2></td>
					</tr>

					<tr valign="top">
						<td scope="row" colspan="2">
							<p><?php _e("Here are a few things to do submitting a support request:"); ?></p>

							<ul style="list-style-type: disc; list-style-position: inside; padding-left: 25px;">
								<li><?php echo sprintf( __('Have you read the %s?' ), '<a title="' . __('FAQs') . '" href="https://wordpress.org/plugins/gp-integration/faq/" target="_blank">' . __('FAQs'). '</a>');?></li>
								<li><?php echo sprintf( __('Have you search the %s for a similar issue?' ), '<a href="http://wordpress.org/support/plugin/gp-integration" target="_blank">' . __('support forum') . '</a>');?></li>
								<li><?php _e('Have you search the Internet for any error messages you are receiving?' );?></li>
								<li><?php _e('Make sure you have access to your PHP error logs.' );?></li>
							</ul>

							<p><?php _e('And a few things to double-check:' );?></p>

							<ul style="list-style-type: disc; list-style-position: inside; padding-left: 25px;">
								<li><?php _e('Have you double checked the plugin settings?' );?></li>
								<li><?php _e('Are you getting a blank or incomplete page displayed in your browser?  Did you view the source for the page and check for any fatal errors?' );?></li>
								<li><?php _e('Have you checked your PHP and web server error logs?' );?></li>
							</ul>

							<p><?php _e('Still not having any luck?' );?> <?php echo sprintf(__('Then please open a new thread on the %s.' ), '<a href="http://wordpress.org/support/plugin/gp-integration" target="_blank">' . __('WordPress.org support forum') . '</a>');?></p>
						</td>
					</tr>

				</tbody>
			</table>

		</div>
	</div>
</div>

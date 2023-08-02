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
<h2><?php esc_html_e('GP Integration Settings', 'gp-integration');?></h2>

	<div id="tabs">
		<ul>
			<li><a href="#fragment-0"><span><?php esc_html_e('Options', 'gp-integration');?></span></a></li>
			<li><a href="#fragment-1"><span><?php esc_html_e('About', 'gp-integration');?></span></a></li>
		</ul>

		<div id="fragment-0">
			<form method="post">
<?php
		echo $gpi_utils->generate_options_table( $gpi_options ); 
?>				
				<div class="submit"><input type="submit" class="button button-primary" name="gp-integration-options-save" value="<?php esc_attr_e('Update Options', 'gp-integration') ?>" /></div>
			</form>
		</div>
	
		<div id="fragment-1">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<td scope="row" align="center"><img src="<?php echo plugins_url('gp-integration/images/logo-250.png'); ?>"></td>
					</tr>

					<tr valign="top">
						<td scope="row" align="center"><h2><?php printf(esc_html__('GP Integration V%s', 'gp-integration'), GP_INTEGRATION_VERSION); ?></h2></td>
					</tr>

					<tr valign="top">
						<td scope="row" align="center"><p><?php printf(esc_html__('by %1$sGreg Ross%2$s', 'gp-integration'),'<a href="https://toolstack.com" target="_blank">','</a>'); ?></p></td>
					</tr>

					<tr valign="top">
						<td scope="row" align="center"><hr /></td>
					</tr>

					<tr valign="top">
						<td scope="row" colspan="2"><h2><?php esc_html_e('Rate and Review at WordPress.org', 'gp-integration'); ?></h2></td>
					</tr>
					
					<tr valign="top">
						<td scope="row" colspan="2"><?php printf(esc_html__('Thanks for installing GP Integration, I encourage you to submit a %1$srating and review%2$s over at WordPress.org. Your feedback is greatly appreciated!', 'gp-integration'),'<a href="http://wordpress.org/support/view/plugin-reviews/gp-integration" target="_blank">','</a>');?></td>
					</tr>
					
					<tr valign="top">
						<td scope="row" colspan="2"><h2><?php esc_html_e('Support', 'gp-integration'); ?></h2></td>
					</tr>

					<tr valign="top">
						<td scope="row" colspan="2">
							<p><?php esc_html_e("Here are a few things to do submitting a support request:", 'gp-integration'); ?></p>

							<ul style="list-style-type: disc; list-style-position: inside; padding-left: 25px;">
								<li><?php printf(esc_html__('Have you read the %1$sFAQs%2$s?', 'gp-integration'), '<a title="' . esc_attr__('FAQs', 'gp-integration') . '" href="https://wordpress.org/plugins/gp-integration/faq/" target="_blank">','</a>');?></li>
								<li><?php printf(esc_html__('Have you search the %1$ssupport forum%2$s for a similar issue?', 'gp-integration'),'<a href="http://wordpress.org/support/plugin/gp-integration" target="_blank">','</a>');?></li>
								<li><?php esc_html_e('Have you search the Internet for any error messages you are receiving?', 'gp-integration');?></li>
								<li><?php esc_html_e('Make sure you have access to your PHP error logs.', 'gp-integration');?></li>
							</ul>

							<p><?php esc_html_e('And a few things to double-check:', 'gp-integration');?></p>

							<ul style="list-style-type: disc; list-style-position: inside; padding-left: 25px;">
								<li><?php esc_html_e('Have you double checked the plugin settings?', 'gp-integration');?></li>
								<li><?php esc_html_e('Are you getting a blank or incomplete page displayed in your browser?  Did you view the source for the page and check for any fatal errors?', 'gp-integration');?></li>
								<li><?php esc_html_e('Have you checked your PHP and web server error logs?', 'gp-integration');?></li>
							</ul>

							<p><?php printf(esc_html__('Still not having any luck? Then please open a new thread on the %1$sWordPress.org support forum%2$s.', 'gp-integration'),'<a href="http://wordpress.org/support/plugin/gp-integration" target="_blank">','</a>');?> </p>
						</td>
					</tr>

				</tbody>
			</table>

		</div>
	</div>
</div>

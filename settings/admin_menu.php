<?php

function ceneo_plugin_admin_init() {
	
	wp_register_style('ceneo_admin' , CENEO_PLUGIN_URL . CENEO_ADMIN_CSS);
}

function ceneo_plugin_admin_menu() {
	
// Adding SubMenu Page.
	$page = add_submenu_page('options-general.php', __('Ceneo Plugin - Settings Page', 'Ceneo Plugin - Settings Page'), __('Ceneo Plugin', 'Ceneo Plugin'), 'administrator', __FILE__, 'ceneo_plugin_options');

    add_action('admin_print_styles-' . $page, 'ceneo_admin_plugin_add_style');
}

function ceneo_admin_plugin_add_style() {
	
	wp_enqueue_style('ceneo_admin');
}

function ceneo_plugin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	// Updating and validating data/POST Check.
	ceneo_update_data($_POST, get_option('ceneo_plugin_options'));
	
    // Importing global default options array.
	$ceneo_current_options = get_option('ceneo_plugin_options');
	
	?>

	<div id="ceneo-admin-container">
		<div class="metabox-holder">
			<div class="postbox">
				<!--  Open Form. -->
				<form id="ceneo_admin_form" name="ceneo_admin_form" action="" method="POST">
							
				<h3><?php _e('General Options Section');?></h3>
							
					<!-- BOF Left Box. -->
		     			<div id="ceneo-admin-leftcontent">
								<p>
								   <?php  _e('<p>Ustawienia ogólne Ceneo Plugin:</p>
										<dl>
											<dt><strong>Klucz Ceneo API:</strong></dt>
											<dd>Plugin posiada już domyślny klucz API. Jeśli posiadasz indywidulany klucz możesz tu z niego skorzystać.</dd>
										</dl>'); ?>
								</p>
							</div>
							<!-- EOF Left Box. -->
							
							<!-- BOF Right Box. -->
							<div id="ceneo-admin-rightcontent">
								<ul>
									<li>
										<label for="ceneo_api_key"><?php _e('API key:'); ?></label>
										<input type="text" id="ceneo_api_key" name="ceneo_api_key" value="<?php echo stripslashes($ceneo_current_options['ceneo_api_key']); ?>" size="50" />
									</li>
								</ul>
							</div>
							<!-- EOF Right Box. -->
							
							<div class="clearer"></div>
							
						</div><!-- EOF postbox. -->
					</div><!-- EOF metabox-holder. -->
				<input type="submit" name="submit" class="button-primary" value="<?php _e('Save Options'); ?>" />
			</form> <!--EOF Form. -->
	     </div> <!-- EOF srp_adm_container -->
	<?php 
	}
	
// Main function to update form option data.
function ceneo_update_data($data, $ceneo_current_options) {

	// Checking if form has been submitted.
	if (isset($_POST['submit'])) {
		
		// Remove the "submit" $_POST entry.
		unset($data['submit']);

		// Validating text fields.		
		foreach ($data as $k => $v) {
			
			// Assigning previous default value if field is empty. String break field excluded.
			if (empty($v)) {
				$data[$k] = $ceneo_current_options[$k];
			}
		}
	
		// Updating WP Option with new $_POST data.
		update_option('ceneo_plugin_options', $data);
		
		// Displaying "save settings" message.
		echo "<div id=\"message\" class=\"updated\"><p><strong>" . __('Settings Saved', CENEO_TRANSLATION_ID) . "</strong></p></div>";
	}
}
?>
<input type="hidden" name="post_id" id="post_id" value="<?php echo get_the_ID(); ?>">

<table class="form-table">
	<?php if($recipients_option_enabled): ?>
        <tr>
            <th><label for="recipients_enabled"><?php echo __("Advanced recipients", "infocob-crm-forms"); ?></label></th>
            <td>
                <input class="recipients_enabled" name="recipients_enabled" id="recipients_enabled" type="checkbox" value="1" <?php echo (isset($recipients_enabled) && $recipients_enabled) ? "checked" : ""; ?>>
            </td>
        </tr>
    <?php endif; ?>
    <?php if($espace_clients_enabled) { ?>
        <tr>
            <th><label for="type_formulaire"><?php echo __("Form type", "infocob-crm-forms"); ?></label></th>
            <td>
                <select name="type_formulaire" id="type_formulaire" class="full-width">
                    <option value="" <?php if(empty($type_formulaire)) echo "selected"; ?>><?php echo __("CRM Mobile", "infocob-crm-forms"); ?></option>
                    <option value="espace_clients" <?php if(!empty($type_formulaire) && strcasecmp($type_formulaire, "espace_clients") === 0) echo "selected"; ?>><?php echo __("Espace clients", "infocob-crm-forms"); ?></option>
                </select>
            </td>
        </tr>
        <?php if(strcasecmp($type_formulaire, "espace_clients") === 0) { ?>
            <tr>
                <th><label for="ec_module_telechargement"><?php echo __("Module download", "infocob-crm-forms"); ?></label></th>
                <td>
                    <select name="ec_module_telechargement" id="ec_module_telechargement" class="full-width">
                        <option value=""></option>
                        <?php if(isset($liste_modules_telechargement["modules"])) { ?>
                            <?php foreach($liste_modules_telechargement["modules"] as $module) { ?>
                                <option value="<?php echo isset($module['id']) ? $module['id'] : ""; ?>" <?php echo (isset($module['id']) && $module['id'] == $module_telechargement) ? "selected" : ""; ?>><?php echo isset($module['titre']) ? $module['titre'] : ""; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </td>
            </tr>
        
            <tr>
                <th><label><?php echo __("Connection fields", "infocob-crm-forms"); ?></label></th>
                <td>
                    <label for="ec_connection_fields[login]"><?php echo __("Login", "infocob-crm-forms"); ?></label>
                    <select name="ec_connection_fields[login]" id="ec_connection_fields[login]" class="full-width">
                        <?php foreach(($inputs_form ?? []) as $input) {
                            // groupe
	                        if(isset($input["champs"])) {
		                        foreach($input["champs"] as $sub_input) { ?>
                                    <option value="<?php echo $sub_input['nom'] ?? ""; ?>" <?php echo (isset($sub_input['nom']) && isset($ec_connection_fields["login"]) && $sub_input['nom'] == $ec_connection_fields["login"]) ? "selected" : ""; ?>><?php echo isset($sub_input['libelle']) ? $sub_input['libelle'] : ""; ?></option>
		                        <?php }
                            // simple
                            } else { ?>
                                <option value="<?php echo $input['nom'] ?? ""; ?>" <?php echo (isset($input['nom']) && isset($ec_connection_fields["login"]) && $input['nom'] == $ec_connection_fields["login"]) ? "selected" : ""; ?>><?php echo isset($input['libelle']) ? $input['libelle'] : ""; ?></option>
	                       <?php }
                        } ?>
                    </select>
                    <label for="ec_connection_fields[password]"><?php echo __("Password", "infocob-crm-forms"); ?></label>
                    <select name="ec_connection_fields[password]" id="ec_connection_fields[password]" class="full-width">
		                <?php foreach($inputs_form as $input) {
			                // groupe
			                if(isset($input["champs"])) {
				                foreach($input["champs"] as $sub_input) { ?>
                                    <option value="<?php echo $sub_input['nom'] ?? ""; ?>" <?php echo (isset($sub_input['nom']) && isset($ec_connection_fields["password"]) && $sub_input['nom'] == $ec_connection_fields["login"]) ? "selected" : ""; ?>><?php echo isset($sub_input['libelle']) ? $sub_input['libelle'] : ""; ?></option>
				                <?php }
				                // simple
			                } else { ?>
                                <option value="<?php echo $input['nom'] ?? ""; ?>" <?php echo (isset($input['nom']) && isset($ec_connection_fields["password"]) && $input['nom'] == $ec_connection_fields["password"]) ? "selected" : ""; ?>><?php echo isset($input['libelle']) ? $input['libelle'] : ""; ?></option>
			                <?php }
		                } ?>
                    </select>
                </td>
            </tr>
        <?php } ?>
    <?php } ?>
    <tr>
        <th><label for="mode_avance_enable"><?php echo __("Advanced mode", "infocob-crm-forms"); ?></label></th>
        <td>
            <input class="mode_avance_enable" name="mode_avance_enable" id="mode_avance_enable" type="checkbox" value="1" <?php echo (isset($mode_avance_enable) && $mode_avance_enable == "1") ? "checked" : ""; ?>>
        </td>
    </tr>
    <tr class="mode-avance-console hidden">
        <th><label for="mode_avance_console"></label></th>
        <td>
            <textarea name="mode_avance_console" id="mode_avance_console" placeholder="<?php _e("JSON configuration", "infocob-crm-forms"); ?>"></textarea>
        </td>
    </tr>
    <tr>
        <th><label for="shortcode_form"><?php echo __("Shortcode", "infocob-crm-forms"); ?></label></th>
        <td>
            <input class="infocob_crm_forms_copy" name="shortcode_form" id="shortcode_form" type="text" value="<?php echo sanitize_text_field($shortcode_form); ?>" readonly>
        </td>
    </tr>
	<?php if(!$espace_clients_enabled || strcasecmp($type_formulaire, "espace_clients") !== 0) { ?>
        <tr>
            <th><label for="redirect_page_submit"><?php echo __("Redirect after submission", "infocob-crm-forms"); ?></label></th>
            <td>
                <select name="redirect_page_submit" id="redirect_page_submit" class="full-width">
                    <option value=""></option>
                    <?php foreach($wp_pages_list as $page) { ?>
                        <option value="<?php echo isset($page->ID) ? $page->ID : ""; ?>" <?php echo (isset($page->ID) && $page->ID == $redirect_page_submit) ? "selected" : ""; ?>><?php echo isset($page->post_title) ? $page->post_title : ""; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
    <?php } ?>
    <tr>
        <th><label for="fullwidth"><?php echo __("Full width", "infocob-crm-forms"); ?></label></th>
        <td><input name="fullwidth" id="fullwidth" type="checkbox" value="1" <?php echo $fullwidth; ?>></td>
    </tr>
    <tr>
        <th><label for="btn_send"><?php echo __("Send button", "infocob-crm-forms"); ?></label></th>
        <td><input name="btn_send" id="btn_send" class="full-width" type="text" value="<?php echo $btn_send; ?>"></td>
    </tr>
	<?php if(!$espace_clients_enabled || strcasecmp($type_formulaire, "espace_clients") !== 0) { ?>
		<tr>
			<th><label for="disable_rgpd"><?php echo __("Disable GDPR", "infocob-crm-forms"); ?></label></th>
			<td><input name="disable_rgpd" id="disable_rgpd" type="checkbox" value="1" <?php echo $disable_rgpd; ?>></td>
		</tr>
        <tr>
            <th><label for="input_rgpd"><?php echo __("GDPR", "infocob-crm-forms"); ?></label></th>
            <td><textarea name="input_rgpd" id="input_rgpd" class="full-width"><?php echo $input_rgpd; ?></textarea></td>
        </tr>
    <?php } ?>
    <tr>
        <th><label for="max_file_size"><?php echo __("Maximal size (bytes)", "infocob-crm-forms"); ?></label></th>
        <td>
            <select name="max_file_size" id="max_file_size" class="full-width">
                <option value="1048576" <?php echo ($max_size == "1048576") ? "selected" : ""; ?>>1 Mo</option>
                <option value="2097152" <?php echo ($max_size == "2097152") ? "selected" : ""; ?>>2 Mo</option>
                <option value="3145728" <?php echo ($max_size == "3145728") ? "selected" : ""; ?>>3 Mo</option>
                <option value="4194304" <?php echo ($max_size == "4194304") ? "selected" : ""; ?>>4 Mo</option>
                <option value="5242880" <?php echo ($max_size == "5242880") ? "selected" : ""; ?>>5 Mo</option>
                <option value="10485760" <?php echo ($max_size == "10485760") ? "selected" : ""; ?>>10 Mo</option>
                <option value="26214400" <?php echo ($max_size == "26214400") ? "selected" : ""; ?>>25 Mo</option>
                <option value="52428800" <?php echo ($max_size == "52428800") ? "selected" : ""; ?>>50 Mo</option>
                <option value="104857600" <?php echo ($max_size == "104857600") ? "selected" : ""; ?>>100 Mo</option>
            </select>
        </td>
    </tr>
    <tr>
        <th><label for="columns_base"><?php echo __("Columns", "infocob-crm-forms"); ?></label></th>
        <td>
            <select name="columns_base" id="columns_base" class="full-width">
                <option value="4" <?php echo ($columns_base == "4") ? "selected" : ""; ?>>4</option>
                <option value="6" <?php echo ($columns_base == "6") ? "selected" : ""; ?>>6</option>
                <option value="12" <?php echo ($columns_base == "12") ? "selected" : ""; ?>>12</option>
            </select>
        </td>
    </tr>
</table>

<div id="inputs">
    <div id="inputsList" class="list-group col nested-sortable">
		
		<?php
			$i = 0;
			foreach($inputs_form as $input) { ?>
                
                <!-- Groupe -->
				<?php if(isset($input["champs"])) { ?>
                    <div data-id="<?php echo $i; ?>" class="list-group-item nested-1 nestedGroup draggable">
                        <div class='rowInputGroup'>
                            <input type='hidden' name="input[<?php echo $i; ?>][type]" value="groupe">
                            <div class='up-down-dashicons'>
                                <span class='dashicons dashicons-move handle'></span>
                            </div>
                            <div class='inputsGroup'>
                                <div>
                                    <label for='libelle'><?php _e("Label", "infocob-crm-forms"); ?></label>
                                    <input data-tippy-content="<?php _e("Label", "infocob-crm-forms"); ?>" type='text' name='input[<?php echo $i; ?>][libelle]' value="<?php echo isset($input["libelle"]) ? $input["libelle"] : ""; ?>" placeholder="<?php echo __("Label", "infocob-crm-forms"); ?>">
                                </div>
                                <div>
									<?php
										$input_col = isset($input["col"]) ? $input["col"] : "";
									?>
                                    <select data-tippy-content="<?php _e("Column(s)", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][col]" required>
										<?php for($num_col = 1; $num_col <= filter_var($columns_base, FILTER_VALIDATE_INT); $num_col ++) { ?>
                                            <option value="<?php echo $num_col; ?>" <?php echo ($input_col == $num_col) ? "selected" : ""; ?>><?php echo $num_col; ?></option>
										<?php } ?>
                                    </select>
                                </div>
                                <div class="input-flex">
                                    <label for='libelle'><?php _e("Display label", "infocob-crm-forms"); ?></label>
                                    <input data-tippy-content="<?php _e("Display label", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][display_libelle]" type="checkbox" value="1" <?php echo (isset($input["display_libelle"]) && $input["display_libelle"] == "1") ? "checked" : ""; ?>/>
                                </div>
                                <div>
                                    <button class="delInputGroup" type="button"><?php _e("Delete", "infocob-crm-forms"); ?></button>
                                </div>
                            </div>
                        </div>
                        <div class='draggableZoneSeparator'></div>
						
						<?php $x = 0; ?>
                        <!-- Champs groupe -->
						<?php foreach(($input["champs"] ?? []) as $sub_input) { ?>
                            
                            <div data-id="<?php echo $i; ?>" class="list-group-item nested-1 draggable">
                                <div class="rowInputField">
                                    <div class="up-down-dashicons">
                                        <span class="dashicons dashicons-move handle"></span>
                                    </div>
									<?php
										$input_type = isset($sub_input["type"]) ? $sub_input["type"] : "";
									?>
                                    <div class="input-flex">
                                        <select data-tippy-content="Type" class="input_type" name="input[<?php echo $i; ?>][champs][<?php echo $x; ?>][type]" required>
                                            <option value="text" <?php echo (strcasecmp($input_type, "text") === 0) ? "selected" : ""; ?>>Text</option>
                                            <option value="email" <?php echo (strcasecmp($input_type, "email") === 0) ? "selected" : ""; ?>>Email</option>
                                            <option value="number" <?php echo (strcasecmp($input_type, "number") === 0) ? "selected" : ""; ?>>Number</option>
                                            <option value="tel" <?php echo (strcasecmp($input_type, "tel") === 0) ? "selected" : ""; ?>>Tel</option>
                                            <option value="password" <?php echo (strcasecmp($input_type, "password") === 0) ? "selected" : ""; ?>>Password</option>
                                            <option value="textarea" <?php echo (strcasecmp($input_type, "textarea") === 0) ? "selected" : ""; ?>>Textarea</option>
                                            <option value="checkbox" <?php echo (strcasecmp($input_type, "checkbox") === 0) ? "selected" : ""; ?>>Checkbox</option>
                                            <option value="file" <?php echo (strcasecmp($input_type, "file") === 0) ? "selected" : ""; ?>>File</option>
                                            <option value="select" <?php echo (strcasecmp($input_type, "select") === 0) ? "selected" : ""; ?>>Select</option>
                                            <option value="date" <?php echo (strcasecmp($input_type, "date") === 0) ? "selected" : ""; ?>>Date</option>
                                            <option value="hidden" <?php echo (strcasecmp($input_type, "hidden") === 0) ? "selected" : ""; ?>>Hidden</option>
                                        </select>
										<?php echo !empty($sub_input["options"]) ? "<span class='dashicons dashicons-admin-generic option-dashicon'></span>" : ""; ?>
										<?php echo (strcasecmp($input_type, "number") === 0) ? "<span class='dashicons dashicons-admin-generic option-dashicon'></span>" : ""; ?>
	                                    <?php echo (strcasecmp($input_type, "checkbox") === 0) ? "<span class='dashicons dashicons-admin-generic option-dashicon'></span>" : ""; ?>
                                    </div>
                                    <div>
										<?php
											$input_col = isset($sub_input["col"]) ? $sub_input["col"] : "";
										?>
                                        <select data-tippy-content="Colonne(s)" name="input[<?php echo $i; ?>][champs][<?php echo $x; ?>][col]" required>
											<?php for($num_col = 1; $num_col <= filter_var($columns_base, FILTER_VALIDATE_INT); $num_col ++) { ?>
                                                <option value="<?php echo $num_col; ?>" <?php echo ($input_col == $num_col) ? "selected" : ""; ?>><?php echo $num_col; ?></option>
											<?php } ?>
                                        </select>
                                    </div>
                                    <div>
                                        <input data-tippy-content="<?php _e("Name", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][champs][<?php echo $x; ?>][nom]" type="text" value="<?php echo isset($sub_input["nom"]) ? $sub_input["nom"] : ""; ?>" placeholder="<?php echo __("Name", "infocob-crm-forms"); ?>" required />
                                    </div>
                                    <div>
                                        <input data-tippy-content="<?php _e("Label", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][champs][<?php echo $x; ?>][libelle]" type="text" value="<?php echo isset($sub_input["libelle"]) ? $sub_input["libelle"] : ""; ?>" placeholder="<?php echo __("Label", "infocob-crm-forms"); ?>" />
                                    </div>
                                    <div>
                                        <input data-tippy-content="<?php _e("Value", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][champs][<?php echo $x; ?>][valeur]" type="text" value="<?php echo isset($sub_input["valeur"]) ? $sub_input["valeur"] : ""; ?>" placeholder="<?php echo __("Value", "infocob-crm-forms"); ?>" />
                                    </div>
                                    <div>
                                        <input data-tippy-content="<?php _e("Post default", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][champs][<?php echo $x; ?>][defaut_post]" type="text" value="<?php echo isset($sub_input["defaut_post"]) ? $sub_input["defaut_post"] : ""; ?>" placeholder="<?php echo __("Post default", "infocob-crm-forms"); ?>" />
                                    </div>
                                    <div>
                                        <input data-tippy-content="<?php _e("Display label", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][champs][<?php echo $x; ?>][display_libelle]" type="checkbox" value="1" <?php echo (isset($sub_input["display_libelle"]) && $sub_input["display_libelle"] == "1") ? "checked" : ""; ?>/>
                                    </div>
                                    <div>
                                        <input data-tippy-content="<?php _e("Require", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][champs][<?php echo $x; ?>][required]" type="checkbox" value="1" <?php echo (isset($sub_input["required"]) && $sub_input["required"] == "1") ? "checked" : ""; ?>/>
                                    </div>
                                    <div class="input_search">
                                        <input data-tippy-content="<?php _e("Display search bar in select input", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][champs][<?php echo $x; ?>][search_select]" type="checkbox" value="1" <?php echo (isset($sub_input["search_select"]) && $sub_input["search_select"] == "1") ? "checked" : ""; ?>/>
                                    </div>
                                    <div class="input_multiple">
                                        <input data-tippy-content="<?php _e("Multiple", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][champs][<?php echo $x; ?>][multiple]" type="checkbox" value="1" <?php echo (isset($sub_input["multiple"]) && $sub_input["multiple"] == "1") ? "checked" : ""; ?>/>
                                    </div>
                                    <div data-tippy-content="<?php _e("Files", "infocob-crm-forms"); ?>" class="accept-file">
										<?php
											$accept = isset($sub_input["accept"]) ? $sub_input["accept"] : [];
										?>
                                        <select class="select-multiple" name="input[<?php echo $i; ?>][champs][<?php echo $x; ?>][accept][]" multiple>
                                            <option value="application/pdf" <?php echo in_array("application/pdf", $accept) ? "selected" : ""; ?>>PDF</option>
                                            <option value="image/jpeg" <?php echo in_array("image/jpeg", $accept) ? "selected" : ""; ?>>JPG</option>
                                            <option value="image/png" <?php echo in_array("image/png", $accept) ? "selected" : ""; ?>>PNG</option>
                                            <option value="application/zip" <?php echo in_array("application/zip", $accept) ? "selected" : ""; ?>>ZIP</option>
                                            <option value="text/plain" <?php echo in_array("text/plain", $accept) ? "selected" : ""; ?>>TXT</option>
                                            <option value="application/msword" <?php echo in_array("application/msword", $accept) ? "selected" : ""; ?>>DOC</option>
                                            <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document" <?php echo in_array("application/vnd.openxmlformats-officedocument.wordprocessingml.document", $accept) ? "selected" : ""; ?>>DOCX</option>
                                            <option value="application/step" <?php echo in_array("application/step", $accept) ? "selected" : ""; ?>>STEP</option>
                                            <option value="application/iges" <?php echo in_array("application/iges", $accept) ? "selected" : ""; ?>>IGES</option>
                                            <option value="application/acad" <?php echo in_array("application/acad", $accept) ? "selected" : ""; ?>>DWG</option>
                                            <option value="application/dxf" <?php echo in_array("application/dxf", $accept) ? "selected" : ""; ?>>DXF</option>
                                        </select>
                                    </div>
                                    <div>
                                        <button class="delInputRow" type="button"><span class="dashicons dashicons-trash"></span></button>
                                    </div>
                                </div>
                                
                                <div class="options hidden">
									<?php
										$opt = 0;
										if(!empty($sub_input["options"])) { ?>
                                            <table class="form-table inputs">
                                                <tr class='accordion-options'>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td colspan='7'>
                                                        <table class='form-table'>
                                                            <tbody>
                                                            <tr data-id='<?php echo $i; ?>'>
                                                                <td><?php _e("Placeholder :", "infocob-crm-forms"); ?></td>
                                                                <td colspan="3">
                                                                    <input data-tippy-content="<?php _e("Placeholder", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][champs][<?php echo $x; ?>][options][placeholder]' type='text' value="<?php echo isset($sub_input["options"]["placeholder"]) ? $sub_input["options"]["placeholder"] : ""; ?>">
                                                                </td>
	                                                            <?php if(($recipients_option_enabled ?? false) && ($recipients_enabled ?? false)): ?>
                                                                    <td>
                                                                        <input data-tippy-content="<?php _e("Enable recipients", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][champs][<?php echo $x; ?>][options][recipients_enabled]' type='checkbox' value="1" <?php echo (isset($sub_input["options"]["recipients_enabled"]) && $sub_input["options"]["recipients_enabled"] == "1") ? "checked" : ""; ?>>
                                                                    </td>
	                                                            <?php endif; ?>
                                                            </tr>
                                                            <tr data-id='<?php echo $i; ?>'>
                                                                <th class="sm"></th>
                                                                <th><?php _e("Label", "infocob-crm-forms"); ?></th>
                                                                <th><?php _e("Value", "infocob-crm-forms"); ?></th>
                                                                <th class='sm'><?php _e("Default", "infocob-crm-forms"); ?></th>
                                                                <th class='sm'></th>
                                                            </tr>
															<?php foreach($sub_input["options"] as $key => $options) { ?>
																<?php if(is_numeric($key)) : ?>
                                                                    <tr class="opt-row" data-id='<?php echo $i; ?>'>
                                                                        <td class="up-down-dashicons">
                                                                            <div>
                                                                                <span class="row-up-option dashicons dashicons-arrow-up-alt2"></span>
                                                                                <span class="row-down-option dashicons dashicons-arrow-down-alt2"></span>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <input data-tippy-content="<?php _e("Label", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][champs][<?php echo $x; ?>][options][<?php echo $opt; ?>][libelle]' type='text' value="<?php echo $options["libelle"]; ?>">
                                                                        </td>
                                                                        <td>
	                                                                    <?php if(($recipients_option_enabled ?? false) && ($recipients_enabled ?? false) && ($sub_input["options"]["recipients_enabled"] ?? false)): ?>
                                                                            <select name="input[<?php echo $i; ?>][champs][<?php echo $x; ?>][options][<?php echo $opt; ?>][recipients]" class="full-width">
			                                                                    <?php foreach($recipients as $recipient): ?>
				                                                                    <?php if($recipient instanceof WP_Post): ?>
                                                                                        <option value="<?php echo $recipient->ID ?>" <?php echo ($recipient->ID == ($options["recipients"] ?? false)) ? "selected" : ""; ?>><?php echo $recipient->post_title; ?></option>
				                                                                    <?php endif; ?>
			                                                                    <?php endforeach; ?>
                                                                            </select>
	                                                                    <?php else: ?>
                                                                            <input data-tippy-content="<?php _e("Value", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][champs][<?php echo $x; ?>][options][<?php echo $opt; ?>][valeur]' type='text' value="<?php echo $options["valeur"]; ?>">
	                                                                    <?php endif; ?>
                                                                        </td>
                                                                        <td>
                                                                            <input data-tippy-content="<?php _e("Default", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][champs][<?php echo $x; ?>][options][<?php echo $opt; ?>][selected]' type='checkbox' value="1" <?php echo (isset($options["selected"]) && $options["selected"] == "1") ? "checked" : ""; ?>>
                                                                        </td>
                                                                        <td>
                                                                            <button class='delOption' type='button'><?php _e("Delete", "infocob-crm-fprms"); ?></button>
                                                                        </td>
                                                                    </tr>
																	
																	<?php
																	$opt ++;
																endif;
															} ?>
                                                            </tbody>
                                                            <tfoot>
                                                            <tr>
                                                                <td colspan='4'>
                                                                    <button class='addOptionGroup' type='button'><?php _e("Add", "infocob-crm-forms"); ?></button>
                                                                </td>
                                                            </tr>
                                                            </tfoot>
                                                        </table>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </table>
										<?php } else if(strcasecmp($input_type, "number") === 0) { ?>
                                            <table class="form-table inputs">
                                                <tr class='accordion-options'>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td colspan='7'>
                                                        <table class='form-table'>
                                                            <tbody>
                                                                <tr data-id='<?php echo $i; ?>'>
                                                                    <td><?php _e("Min :", "infocob-crm-forms"); ?></td>
                                                                    <td>
                                                                        <input data-tippy-content="<?php _e("Min", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][champs][<?php echo $x; ?>][numbers][min]' type='number' step='any' value="<?php echo isset($sub_input["numbers"]["min"]) ? $sub_input["numbers"]["min"] : ""; ?>">
                                                                    </td>
                                                                </tr>
                                                                <tr data-id='<?php echo $i; ?>'>
                                                                    <td><?php _e("Max :", "infocob-crm-forms"); ?></td>
                                                                    <td>
                                                                        <input data-tippy-content="<?php _e("Max", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][champs][<?php echo $x; ?>][numbers][max]' type='number' step='any' value="<?php echo isset($sub_input["numbers"]["max"]) ? $sub_input["numbers"]["max"] : ""; ?>">
                                                                    </td>
                                                                </tr>
                                                                <tr data-id='<?php echo $i; ?>'>
                                                                    <td><?php _e("Step :", "infocob-crm-forms"); ?></td>
                                                                    <td>
                                                                        <input data-tippy-content="<?php _e("Step", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][champs][<?php echo $x; ?>][numbers][step]' type='number' step='any' value="<?php echo isset($sub_input["numbers"]["step"]) ? $sub_input["numbers"]["step"] : ""; ?>">
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </table>
										<?php } else if(strcasecmp($input_type, "checkbox") === 0) { ?>
                                            <table class="form-table inputs">
                                                <tr class='accordion-options'>
                                                    <td></td>
                                                    <td colspan='7'>
                                                        <table class='form-table'>
                                                            <tbody>
                                                                <tr data-id='<?php echo $i; ?>'>
                                                                    <td><?php _e("Invert value sent :", "infocob-crm-forms"); ?></td>
                                                                    <td>
                                                                        <input data-tippy-content="<?php _e("Invert value sent", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][champs][<?php echo $x; ?>][checkboxes][invert]' type='checkbox' <?php echo isset($sub_input["checkboxes"]["invert"]) ? "checked" : ""; ?>>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </table>
										<?php } ?>
                                </div>
                            </div>
							
							<?php $x ++; ?>
						<?php } ?>
                    
                    </div>
				<?php } else { ?>
                    <div data-id="<?php echo $i; ?>" class="list-group-item nested-1 draggable">
                        <div class="rowInputField">
                            <div class="up-down-dashicons">
                                <span class="dashicons dashicons-move handle"></span>
                            </div>
							<?php
								$input_type = isset($input["type"]) ? $input["type"] : "";
							?>
                            <div class="input-flex">
                                <select data-tippy-content="<?php _e("Type", "infocob-crm-forms"); ?>" class="input_type" name="input[<?php echo $i; ?>][type]" required>
                                    <option value="text" <?php echo (strcasecmp($input_type, "text") === 0) ? "selected" : ""; ?>>Text</option>
                                    <option value="email" <?php echo (strcasecmp($input_type, "email") === 0) ? "selected" : ""; ?>>Email</option>
                                    <option value="number" <?php echo (strcasecmp($input_type, "number") === 0) ? "selected" : ""; ?>>Number</option>
                                    <option value="tel" <?php echo (strcasecmp($input_type, "tel") === 0) ? "selected" : ""; ?>>Tel</option>
                                    <option value="password" <?php echo (strcasecmp($input_type, "password") === 0) ? "selected" : ""; ?>>Password</option>
                                    <option value="textarea" <?php echo (strcasecmp($input_type, "textarea") === 0) ? "selected" : ""; ?>>Textarea</option>
                                    <option value="checkbox" <?php echo (strcasecmp($input_type, "checkbox") === 0) ? "selected" : ""; ?>>Checkbox</option>
                                    <option value="file" <?php echo (strcasecmp($input_type, "file") === 0) ? "selected" : ""; ?>>File</option>
                                    <option value="select" <?php echo (strcasecmp($input_type, "select") === 0) ? "selected" : ""; ?>>Select</option>
                                    <option value="date" <?php echo (strcasecmp($input_type, "date") === 0) ? "selected" : ""; ?>>Date</option>
                                    <option value="hidden" <?php echo (strcasecmp($input_type, "hidden") === 0) ? "selected" : ""; ?>>Hidden</option>
                                </select>
								<?php echo !empty($input["options"]) ? "<span class='dashicons dashicons-admin-generic option-dashicon'></span>" : ""; ?>
	                            <?php echo (strcasecmp($input_type, "number") === 0) ? "<span class='dashicons dashicons-admin-generic option-dashicon'></span>" : ""; ?>
	                            <?php echo (strcasecmp($input_type, "checkbox") === 0) ? "<span class='dashicons dashicons-admin-generic option-dashicon'></span>" : ""; ?>
                            </div>
                            <div>
								<?php
									$input_col = isset($input["col"]) ? $input["col"] : "";
								?>
                                <select data-tippy-content="<?php _e("Column(s)", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][col]" required>
									<?php for($num_col = 1; $num_col <= filter_var($columns_base, FILTER_VALIDATE_INT); $num_col ++) { ?>
                                        <option value="<?php echo $num_col; ?>" <?php echo ($input_col == $num_col) ? "selected" : ""; ?>><?php echo $num_col; ?></option>
									<?php } ?>
                                </select>
                            </div>
                            <div>
                                <input data-tippy-content="<?php _e("Name", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][nom]" type="text" value="<?php echo isset($input["nom"]) ? $input["nom"] : ""; ?>" placeholder="<?php echo __("Name", "infocob-crm-forms"); ?>" required />
                            </div>
                            <div>
                                <input data-tippy-content="<?php _e("Label", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][libelle]" type="text" value="<?php echo isset($input["libelle"]) ? $input["libelle"] : ""; ?>" placeholder="<?php echo __("Label", "infocob-crm-forms"); ?>" />
                            </div>
                            <div>
                                <input data-tippy-content="<?php _e("Value", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][valeur]" type="text" value="<?php echo isset($input["valeur"]) ? $input["valeur"] : ""; ?>" placeholder="<?php echo __("Value", "infocob-crm-forms"); ?>" />
                            </div>
                            <div>
                                <input data-tippy-content="<?php _e("Post default", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][defaut_post]" type="text" value="<?php echo isset($input["defaut_post"]) ? $input["defaut_post"] : ""; ?>" placeholder="<?php echo __("Post default", "infocob-crm-forms"); ?>" />
                            </div>
                            <div>
                                <input data-tippy-content="<?php _e("Display label", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][display_libelle]" type="checkbox" value="1" <?php echo (isset($input["display_libelle"]) && $input["display_libelle"] == "1") ? "checked" : ""; ?>/>
                            </div>
                            <div>
                                <input data-tippy-content="<?php _e("Require", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][required]" type="checkbox" value="1" <?php echo (isset($input["required"]) && $input["required"] == "1") ? "checked" : ""; ?>/>
                            </div>
                            <div class="input_search">
                                <input data-tippy-content="<?php _e("Display search bar in select input", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][search_select]" type="checkbox" value="1" <?php echo (isset($input["search_select"]) && $input["search_select"] == "1") ? "checked" : ""; ?>/>
                            </div>
                            <div class="input_multiple">
                                <input data-tippy-content="<?php _e("Multiple", "infocob-crm-forms"); ?>" name="input[<?php echo $i; ?>][multiple]" type="checkbox" value="1" <?php echo (isset($input["multiple"]) && $input["multiple"] == "1") ? "checked" : ""; ?>/>
                            </div>
                            <div data-tippy-content="Fichiers" class="accept-file">
								<?php
									$accept = isset($input["accept"]) ? $input["accept"] : [];
								?>
                                <select class="select-multiple" name="input[<?php echo $i; ?>][accept][]" multiple>
                                    <option value="application/pdf" <?php echo in_array("application/pdf", $accept) ? "selected" : ""; ?>>PDF</option>
                                    <option value="image/jpeg" <?php echo in_array("image/jpeg", $accept) ? "selected" : ""; ?>>JPG</option>
                                    <option value="image/png" <?php echo in_array("image/png", $accept) ? "selected" : ""; ?>>PNG</option>
                                    <option value="application/zip" <?php echo in_array("application/zip", $accept) ? "selected" : ""; ?>>ZIP</option>
                                    <option value="text/plain" <?php echo in_array("text/plain", $accept) ? "selected" : ""; ?>>TXT</option>
                                    <option value="application/msword" <?php echo in_array("application/msword", $accept) ? "selected" : ""; ?>>DOC</option>
                                    <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document" <?php echo in_array("application/vnd.openxmlformats-officedocument.wordprocessingml.document", $accept) ? "selected" : ""; ?>>DOCX</option>
                                    <option value="application/step" <?php echo in_array("application/step", $accept) ? "selected" : ""; ?>>STEP</option>
                                    <option value="application/iges" <?php echo in_array("application/iges", $accept) ? "selected" : ""; ?>>IGES</option>
                                    <option value="application/acad" <?php echo in_array("application/acad", $accept) ? "selected" : ""; ?>>DWG</option>
                                    <option value="application/dxf" <?php echo in_array("application/dxf", $accept) ? "selected" : ""; ?>>DXF</option>
                                </select>
                            </div>
                            <div>
                                <button class="delInputRow" type="button"><span class="dashicons dashicons-trash"></span></button>
                            </div>
                        </div>
                        
                        <div class="options hidden">
							<?php
								$opt = 0;
								if(!empty($input["options"])) { ?>
                                    <table class="form-table inputs">
                                        <tr class='accordion-options'>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td colspan='7'>
                                                <table class='form-table'>
                                                    <tbody>
                                                    <tr data-id='<?php echo $i; ?>'>
                                                        <td><?php _e("Placeholder :", "infocob-crm-forms"); ?></td>
                                                        <td colspan="3">
                                                            <input data-tippy-content="<?php _e("Placeholder", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][options][placeholder]' type='text' value="<?php echo isset($input["options"]["placeholder"]) ? $input["options"]["placeholder"] : ""; ?>">
                                                        </td>
                                                        <?php if(($recipients_option_enabled ?? false) && ($recipients_enabled ?? false)): ?>
                                                            <td>
                                                                <input data-tippy-content="<?php _e("Enable recipients", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][options][recipients_enabled]' type='checkbox' value="1" <?php echo (isset($input["options"]["recipients_enabled"]) && $input["options"]["recipients_enabled"] == "1") ? "checked" : ""; ?>>
                                                            </td>
                                                        <?php endif; ?>
                                                    </tr>
                                                    <tr data-id='<?php echo $i; ?>'>
                                                        <th class="sm"></th>
                                                        <th><?php _e("Label", "infocob-crm-forms"); ?></th>
                                                        <th><?php _e("Value", "infocob-crm-forms"); ?></th>
                                                        <th class='sm'><?php _e("Default", "infocob-crm-forms"); ?></th>
                                                        <th class='sm'></th>
                                                    </tr>
													<?php foreach($input["options"] as $key => $options) { ?>
														<?php if(is_numeric($key)) : ?>
                                                            <tr class="opt-row" data-id='<?php echo $i; ?>'>
                                                                <td class="up-down-dashicons">
                                                                    <div>
                                                                        <span class="row-up-option dashicons dashicons-arrow-up-alt2"></span>
                                                                        <span class="row-down-option dashicons dashicons-arrow-down-alt2"></span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input data-tippy-content="<?php _e("Label", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][options][<?php echo $opt; ?>][libelle]' type='text' value="<?php echo $options["libelle"]; ?>">
                                                                </td>
                                                                <td>
                                                                    <?php if(($recipients_option_enabled ?? false) && ($recipients_enabled ?? false) && ($input["options"]["recipients_enabled"] ?? false)): ?>
                                                                        <select name="input[<?php echo $i; ?>][options][<?php echo $opt; ?>][recipients]" class="full-width">
		                                                                    <?php foreach($recipients as $recipient): ?>
			                                                                    <?php if($recipient instanceof WP_Post): ?>
                                                                                    <option value="<?php echo $recipient->ID ?>" <?php echo ($recipient->ID == ($options["recipients"] ?? false)) ? "selected" : ""; ?>><?php echo $recipient->post_title; ?></option>
			                                                                    <?php endif; ?>
		                                                                    <?php endforeach; ?>
                                                                        </select>
                                                                    <?php else: ?>
                                                                        <input data-tippy-content="<?php _e("Value", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][options][<?php echo $opt; ?>][valeur]' type='text' value="<?php echo $options["valeur"] ?? ""; ?>">
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <input data-tippy-content="<?php _e("Default", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][options][<?php echo $opt; ?>][selected]' type='checkbox' value="1" <?php echo (isset($options["selected"]) && $options["selected"] == "1") ? "checked" : ""; ?>>
                                                                </td>
                                                                <td>
                                                                    <button class='delOption' type='button'><?php _e("Delete", "infocob-crm-forms"); ?></button>
                                                                </td>
                                                            </tr>
															
															<?php
															$opt ++;
														endif;
													} ?>
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan='4'>
                                                            <button class='addOption' type='button'><?php _e("Add", "infocob-crm-forms"); ?></button>
                                                        </td>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </table>
                                <?php } else if(strcasecmp($input_type, "number") === 0) { ?>
                                    <table class="form-table inputs">
                                        <tr class='accordion-options'>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td colspan='7'>
                                                <table class='form-table'>
                                                    <tbody>
                                                        <tr data-id='<?php echo $i; ?>'>
                                                            <td><?php _e("Min :", "infocob-crm-forms"); ?></td>
                                                            <td>
                                                                <input data-tippy-content="<?php _e("Min", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][numbers][min]' type='number' step='any' value="<?php echo isset($input["numbers"]["min"]) ? $input["numbers"]["min"] : ""; ?>">
                                                            </td>
                                                        </tr>
                                                        <tr data-id='<?php echo $i; ?>'>
                                                            <td><?php _e("Max :", "infocob-crm-forms"); ?></td>
                                                            <td>
                                                                <input data-tippy-content="<?php _e("Max", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][numbers][max]' type='number' step='any' value="<?php echo isset($input["numbers"]["max"]) ? $input["numbers"]["max"] : ""; ?>">
                                                            </td>
                                                        </tr>
                                                        <tr data-id='<?php echo $i; ?>'>
                                                            <td><?php _e("Step :", "infocob-crm-forms"); ?></td>
                                                            <td>
                                                                <input data-tippy-content="<?php _e("Step", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][numbers][step]' type='number' step='any' value="<?php echo isset($input["numbers"]["step"]) ? $input["numbers"]["step"] : ""; ?>">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </table>
								<?php } else if(strcasecmp($input_type, "checkbox") === 0) { ?>
                                    <table class="form-table inputs">
                                        <tr class='accordion-options'>
                                            <td></td>
                                            <td colspan='7'>
                                                <table class='form-table'>
                                                    <tbody>
                                                        <tr data-id='<?php echo $i; ?>'>
                                                            <td><?php _e("Invert value sent :", "infocob-crm-forms"); ?></td>
                                                            <td>
                                                                <input data-tippy-content="<?php _e("Invert value sent", "infocob-crm-forms"); ?>" name='input[<?php echo $i; ?>][checkboxes][invert]' type='checkbox' <?php echo isset($input["checkboxes"]["invert"]) ? "checked" : ""; ?>>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </table>
								<?php } ?>
                        </div>
                    </div>
				<?php } ?>
				
				<?php
				$i ++;
			} ?>
		
		<?php if(empty($inputs_form)) { ?>
            <div data-id="0" class="list-group-item nested-1 draggable">
                <div class="rowInputField">
                    <div class="up-down-dashicons">
                        <span class="dashicons dashicons-move handle"></span>
                    </div>
                    <div class="input-flex">
                        <select data-tippy-content="<?php _e("Type", "infocob-crm-forms"); ?>" class="input_type" name="input[0][type]" required>
                            <option value="text">Text</option>
                            <option value="email">Email</option>
                            <option value="number">Number</option>
                            <option value="tel">Tel</option>
                            <option value="password">Password</option>
                            <option value="textarea">Textarea</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="file">File</option>
                            <option value="select">Select</option>
                            <option value="date">Date</option>
                            <option value="hidden">Hidden</option>
                        </select>
                    </div>
                    <div>
                        <select data-tippy-content="<?php _e("Column(s)", "infocob-crm-forms"); ?>" name="input[0][col]" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                    <div>
                        <input data-tippy-content="<?php _e("Name", "infocob-crm-forms"); ?>" name="input[0][nom]" type="text" value="" placeholder="<?php echo __("Name", "infocob-crm-forms"); ?>" required />
                    </div>
                    <div>
                        <input data-tippy-content="<?php _e("Label", "infocob-crm-forms"); ?>" name="input[0][libelle]" type="text" value="" placeholder="<?php echo __("Label", "infocob-crm-forms"); ?>" />
                    </div>
                    <div>
                        <input data-tippy-content="<?php _e("Value", "infocob-crm-forms"); ?>" name="input[0][valeur]" type="text" value="" placeholder="<?php echo __("Value", "infocob-crm-forms"); ?>" />
                    </div>
                    <div>
                        <input data-tippy-content="<?php _e("Post default", "infocob-crm-forms"); ?>" name="input[0][defaut_post]" type="text" value="" placeholder="<?php echo __("Post default", "infocob-crm-forms"); ?>" />
                    </div>
                    <div>
                        <input data-tippy-content="<?php _e("Display label", "infocob-crm-forms"); ?>" name="input[0][display_libelle]" type="checkbox" value="1" />
                    </div>
                    <div>
                        <input data-tippy-content="<?php _e("Require", "infocob-crm-forms"); ?>" name="input[0][required]" type="checkbox" value="1" />
                    </div>
                    <div class="input_search">
                        <input data-tippy-content="<?php _e("Display search bar in select input", "infocob-crm-forms"); ?>" name="input[0][search_select]" type="checkbox" value="1" />
                    </div>
                    <div class="input_multiple">
                        <input data-tippy-content="<?php _e("Multiple", "infocob-crm-forms"); ?>" name="input[0][multiple]" type="checkbox" value="1" />
                    </div>
                    <div data-tippy-content="<?php _e("Files", "infocob-crm-forms"); ?>" class="accept-file">
                        <select class="select-multiple" name="input[0][accept][]" multiple>
                            <option value="application/pdf">PDF</option>
                            <option value="image/jpeg">JPG</option>
                            <option value="image/png">PNG</option>
                            <option value="application/zip">ZIP</option>
                            <option value="text/plain">TXT</option>
                            <option value="application/msword">DOC</option>
                            <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document">DOCX</option>
                            <option value="application/step">STEP</option>
                            <option value="application/iges">IGES</option>
                            <option value="application/acad">DWG</option>
                            <option value="application/dxf">DXF</option>
                        </select>
                    </div>
                    <div>
                        <button class="delInputRow" type="button"><span class="dashicons dashicons-trash"></span></button>
                    </div>
                </div>
                <div class="options hidden"></div>
            </div>
		<?php } ?>
    
    </div>
    
    <div class="btnActions">
        <button class="addInputRow" type="button"><?php _e("Add field", "infocob-crm-forms"); ?></button>
        <button class="addInputGroup" type="button"><?php _e("Add group", "infocob-crm-forms"); ?></button>
    </div>
</div>


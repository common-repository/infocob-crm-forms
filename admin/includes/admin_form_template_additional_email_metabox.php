<?php
	
	use function Infocob\CrmForms\Admin\icf__;
	
	$nbAddEmail = !isset($nbAddEmail) ? 0 : $nbAddEmail;
?>
<h1><?php echo sprintf(__('Additional email n°%1$s', "infocob-crm-forms"), $nbAddEmail + 1); ?></h1>
<table class="form-table additional-email">
    <tr>
        <th>
            <label for="additional_email_enable_<?php echo $nbAddEmail; ?>"><?php echo __("Enable", "infocob-crm-forms"); ?></label>
        </th>
        <td>
            <input id="additional_email_enable_<?php echo $nbAddEmail; ?>" type='checkbox' name='additional_email[<?php echo $nbAddEmail; ?>][enable]' value="1" <?php echo (isset($additional_email[$nbAddEmail]["enable"]) && $additional_email[$nbAddEmail]["enable"] == "1") ? "checked" : ""; ?>>
        </td>
    </tr>
    <?php if(!empty($additional_email[$nbAddEmail]["enable"])) { ?>
        <tr>
            <th>
                <label for="additional_email_template_<?php echo $nbAddEmail; ?>"><?php echo __("Template", "infocob-crm-forms"); ?></label>
            </th>
            <td>
                <select name="additional_email[<?php echo $nbAddEmail; ?>][template]" id="additional_email_template_<?php echo $nbAddEmail; ?>" class="full-width" aria-describedby="info_additional_email_template_<?php echo $nbAddEmail; ?>">
                    <option value="defaut-infocob-crm-forms"><?php _e("Default (default)", "infocob-crm-forms"); ?></option>
                    <?php foreach(($additional_email_list_template ?? []) as $value) { ?>
                        <option value="<?php echo $value; ?>" <?php echo (strcasecmp($additional_email[$nbAddEmail]["template"], $value) === 0) ? "selected" : ""; ?>><?php echo $value; ?></option>
                    <?php } ?>
                </select>
                <p class="description" id="info_additional_email_template_<?php echo $nbAddEmail; ?>">
                    <?php _e("Variables availables : title, subtitle, color, logo, form (form data)", "infocob-crm-forms"); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th>
                <label for="additional_email_to_form_<?php echo $nbAddEmail; ?>"><?php echo __("To (from form)", "infocob-crm-forms"); ?></label>
            </th>
            <td>
                <table class="form-table">
                    <thead>
                    <tr>
                        <th>
                            <?php echo __("Email", "infocob-crm-forms"); ?>
                        </th>
                        <th>
                            <?php echo __("Firstname", "infocob-crm-forms"); ?>
                        </th>
                        <th>
                            <?php echo __("Lastname", "infocob-crm-forms"); ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="additional_email[<?php echo $nbAddEmail; ?>][field_to][0][email]" class="full-width" aria-describedby="info_additional_email_field_to_email_<?php echo $nbAddEmail; ?>">
                                    <option value=""></option>
                                    <?php foreach(($inputs_names_list["email"] ?? []) as $champ) { ?>
                                        <option value="<?php echo $champ["nom"] ?? ""; ?>" <?php echo (isset($additional_email[$nbAddEmail]["field_to"][0]["email"]) && strcasecmp($additional_email[$nbAddEmail]["field_to"][0]["email"], $champ["nom"]) === 0) ? "selected" : ""; ?>><?php echo $champ["libelle"] . " (" . $champ["nom"] . ")"; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <select name="additional_email[<?php echo $nbAddEmail; ?>][field_to][0][firstname]" class="full-width" aria-describedby="info_additional_email_field_to_firstname_<?php echo $nbAddEmail; ?>">
                                    <option value=""></option>
                                    <?php foreach(($inputs_names_list["text"] ?? []) as $champ) { ?>
                                        <option value="<?php echo $champ["nom"] ?? ""; ?>" <?php echo (isset($additional_email[$nbAddEmail]["field_to"][0]["firstname"]) && strcasecmp($additional_email[$nbAddEmail]["field_to"][0]["firstname"], $champ["nom"]) === 0) ? "selected" : ""; ?>><?php echo $champ["libelle"] . " (" . $champ["nom"] . ")"; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <select name="additional_email[<?php echo $nbAddEmail; ?>][field_to][0][lastname]" class="full-width" aria-describedby="info_additional_email_field_to_lastname_<?php echo $nbAddEmail; ?>">
                                    <option value=""></option>
                                    <?php foreach(($inputs_names_list["text"] ?? []) as $champ) { ?>
                                        <option value="<?php echo $champ["nom"] ?? ""; ?>" <?php echo (isset($additional_email[$nbAddEmail]["field_to"][0]["lastname"]) && strcasecmp($additional_email[$nbAddEmail]["field_to"][0]["lastname"], $champ["nom"]) === 0) ? "selected" : ""; ?>><?php echo $champ["libelle"] . " (" . $champ["nom"] . ")"; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <th>
                <label><?php echo __("Recipients", "infocob-crm-forms"); ?></label>
            </th>
            <td>
	            <?php if(($recipients_option_enabled ?? false) && ($recipients_enabled ?? false)): ?>
                    <select name="additional_email[<?php echo $nbAddEmail; ?>][recipients][]" class="full-width" multiple="multiple">
                        <?php foreach(($recipients ?? []) as $recipient): ?>
                            <?php if($recipient instanceof WP_Post): ?>
                                <option value="<?php echo $recipient->ID ?>" <?php echo (in_array($recipient->ID, $additional_email[ $nbAddEmail ]["recipients"] ?? [])) ? "selected" : ""; ?>><?php echo $recipient->post_title; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <table class="form-table destinataires additional-email">
                        <thead>
                        <tr>
                            <th>
                                <?php echo __("Email", "infocob-crm-forms"); ?>
                            </th>
                            <th>
                                <?php echo __("Fullname", "infocob-crm-forms"); ?>
                            </th>
                            <th class="sm"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $i = 0;
                            if(!empty($additional_email[$nbAddEmail]["to"])) {
                                foreach($additional_email[$nbAddEmail]["to"] as $email_to) { ?>
                                    <tr>
                                        <td>
                                            <input name="additional_email[<?php echo $nbAddEmail; ?>][to][<?php echo $i; ?>][email]" type="text" value="<?php echo $email_to["email"] ?? ""; ?>" placeholder="<?php echo __("Email", "infocob-crm-forms"); ?>" />
                                        </td>
                                        <td>
                                            <input name="additional_email[<?php echo $nbAddEmail; ?>][to][<?php echo $i; ?>][fullname]" type="text" value="<?php echo $email_to["fullname"] ?? ""; ?>" placeholder="<?php echo __("Firstname Lastname", "infocob-crm-forms"); ?>" />
                                        </td>
                                        <td>
                                            <button class="delAdditionalEmailTo" type="button"><?php _e("Delete", "infocob-crm-forms"); ?></button>
                                        </td>
                                    </tr>
                                    <?php $i ++;
                                }
                            } else if(empty($additional_email[$nbAddEmail]["to"])) { ?>
                                <tr>
                                    <td>
                                        <input name="additional_email[<?php echo $nbAddEmail; ?>][to][0][email]" type="text" value="<?php echo $additional_email[$nbAddEmail]["to"][0]["email"] ?? ""; ?>" placeholder="<?php echo __("Email", "infocob-crm-forms"); ?>" />
                                    </td>
                                    <td>
                                        <input name="additional_email[<?php echo $nbAddEmail; ?>][to][0][fullname]" type="text" value="<?php echo $additional_email[$nbAddEmail][0]["fullname"] ?? ""; ?>" placeholder="<?php echo __("Firstname Lastname", "infocob-crm-forms"); ?>" />
                                    </td>
                                    <td>
                                        <button class="delAdditionalEmailTo" type="button"><?php _e("Delete", "infocob-crm-forms"); ?></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <button class="addAdditionalEmailTo" type="button"><?php _e("Add", "infocob-crm-forms"); ?></button>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>
                <label for="additional_email_subject_<?php echo $nbAddEmail; ?>"><?php echo __("Subject", "infocob-crm-forms"); ?></label>
            </th>
            <td>
                <input class="full-width" id="additional_email_subject_<?php echo $nbAddEmail; ?>" type='text' name='additional_email[<?php echo $nbAddEmail; ?>][subject]' value='<?php echo esc_html($additional_email[$nbAddEmail]["subject"]); ?>'>
            </td>
        </tr>
        <tr>
            <th>
                <label for="additional_email_societe_<?php echo $nbAddEmail; ?>"><?php echo __("Company", "infocob-crm-forms"); ?></label>
            </th>
            <td>
                <input class="full-width" id="additional_email_societe_<?php echo $nbAddEmail; ?>" type='text' name='additional_email[<?php echo $nbAddEmail; ?>][societe]' value='<?php echo esc_html($additional_email[$nbAddEmail]["societe"]); ?>'>
            </td>
        </tr>
        <tr>
            <th>
                <label for="additional_email_title_<?php echo $nbAddEmail; ?>"><?php echo __("Title", "infocob-crm-forms"); ?></label>
            </th>
            <td>
                <input class="full-width" id="additional_email_title_<?php echo $nbAddEmail; ?>" type='text' name='additional_email[<?php echo $nbAddEmail; ?>][title]' value='<?php echo esc_html($additional_email[$nbAddEmail]["title"]); ?>'>
            </td>
        </tr>
        <tr>
            <th>
                <label for="additional_email_subtitle_<?php echo $nbAddEmail; ?>"><?php echo __("Subtitle", "infocob-crm-forms"); ?></label>
            </th>
            <td>
                <input class="full-width" id="additional_email_subtitle_<?php echo $nbAddEmail; ?>" type='text' name='additional_email[<?php echo $nbAddEmail; ?>][subtitle]' aria-describedby="info_additional_email_subtitle_<?php echo $nbAddEmail; ?>" value='<?php echo esc_html($additional_email[$nbAddEmail]["subtitle"]); ?>'>
                <p class="description" id="info_additional_email_subtitle_<?php echo $nbAddEmail; ?>">
                    <?php _e("By default it will be the current page name.", "infocob-crm-forms"); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th>
                <label for="additional_email_color_<?php echo $nbAddEmail; ?>" aria-describedby="info_additional_email_color_<?php echo $nbAddEmail; ?>"><?php echo __("Color", "infocob-crm-forms"); ?></label>
                <p class="description" id="info_additional_email_color_<?php echo $nbAddEmail; ?>">
                    <?php _e("Email header", "infocob-crm-forms"); ?>
                </p>
            </th>
            <td>
                <input name="additional_email[<?php echo $nbAddEmail; ?>][color]" type='text' class='color-field' value="<?php echo sanitize_text_field($additional_email[$nbAddEmail]["color"]); ?>">
            </td>
        </tr>
        <tr>
            <th>
                <label for="additional_email_color_text_title_<?php echo $nbAddEmail; ?>" aria-describedby="info_additional_email_color_text_title_<?php echo $nbAddEmail; ?>"><?php echo __("Color", "infocob-crm-forms"); ?></label>
                <p class="description" id="info_additional_email_color_text_title_<?php echo $nbAddEmail; ?>">
                    <?php _e("Email text", "infocob-crm-forms"); ?>
                </p>
            </th>
            <td>
                <input name="additional_email[<?php echo $nbAddEmail; ?>][color_text_title]" type='text' class='color-field' value="<?php echo sanitize_text_field($additional_email[$nbAddEmail]["color_text_title"]); ?>">
            </td>
        </tr>
        <tr>
            <th>
                <label for="additional_email_color_link_<?php echo $nbAddEmail; ?>" aria-describedby="info_additional_email_color_link_<?php echo $nbAddEmail; ?>"><?php echo __("Color", "infocob-crm-forms"); ?></label>
                <p class="description" id="info_additional_email_color_link_<?php echo $nbAddEmail; ?>">
                    <?php _e("Links", "infocob-crm-forms"); ?>
                </p>
            </th>
            <td>
                <input name="additional_email[<?php echo $nbAddEmail; ?>][color_link]" type='text' class='color-field' value="<?php echo sanitize_text_field($additional_email[$nbAddEmail]["color_link"]); ?>">
            </td>
        </tr>
        <tr>
            <th>
                <label for="additional_email_border_radius_<?php echo $nbAddEmail; ?>"><?php echo __("Border radius", "infocob-crm-forms"); ?></label>
            </th>
            <td>
                <input class="full-width" id="additional_email_border_radius_<?php echo $nbAddEmail; ?>" type='number' name='additional_email[<?php echo $nbAddEmail; ?>][border_radius]' aria-describedby="info_additional_email_border_radius_<?php echo $nbAddEmail; ?>" value='<?php echo esc_html($additional_email[$nbAddEmail]["border_radius"]); ?>'>
            </td>
        </tr>
        <tr>
            <th><?php _e("Logo", "infocob-crm-forms"); ?></th>
            <td class="logo_email">
                <div class='image-preview-wrapper'>
                    <img class='logo_preview' src='<?php echo wp_get_attachment_url($additional_email[$nbAddEmail]["logo"]["attachment_id"] ?? ""); ?>' width='100' height='100' style='max-height: 100px; width: 100px;'>
                </div>
                <div class="logo_actions">
                    <input type="button" class="button upload_logo" value="<?php _e('Upload logo', "infocob-crm-forms"); ?>" />
                    <input type='hidden' name='additional_email[<?php echo $nbAddEmail; ?>][logo][attachment_id]' class='logo_attachment_id' value='<?php echo $additional_email[$nbAddEmail]["logo"]["attachment_id"] ?? ""; ?>'>
                    <button class="remove_logo" type="button"><?php _e("Remove logo", "infocob-crm-forms"); ?></button>
                    <select name="additional_email[<?php echo $nbAddEmail; ?>][logo][size]">
                        <?php foreach(get_intermediate_image_sizes() as $size) { ?>
                            <option value="<?php echo $size; ?>" <?php echo (isset($additional_email[$nbAddEmail]["logo"]["size"]) && strcasecmp($additional_email[$nbAddEmail]["logo"]["size"], $size) === 0) ? "selected" : "" ?>><?php echo $size; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </td>
        </tr>
		<tr>
			<th>
				<label for="additional_email_no_original_attachements_<?php echo $nbAddEmail; ?>"><?php echo __("Don't include original attachments", "infocob-crm-forms"); ?></label>
			</th>
			<td>
				<input id="additional_email_no_original_attachements_<?php echo $nbAddEmail; ?>" type='checkbox' name='additional_email[<?php echo $nbAddEmail; ?>][no_original_attachements]' value="1" <?php echo (isset($additional_email[$nbAddEmail]["no_original_attachements"]) && $additional_email[$nbAddEmail]["no_original_attachements"] == "1") ? "checked" : ""; ?>>
			</td>
		</tr>
		<tr>
			<th><?php _e("Attachments", "infocob-crm-forms"); ?></th>
			<td class="attachments_email" data-id="<?php echo $nbAddEmail; ?>">
				<div class="attachments_actions">
					
					<div class="add">
						<input type="button" class="button upload_attachments" value="<?php _e('Upload attachments', "infocob-crm-forms"); ?>" />
					</div>
					
					<div class="inputs">
						<?php foreach($additional_email[$nbAddEmail]["attachments"] ?? [] as $attachment): ?>
							<div class="input">
								<figure class='image-preview-wrapper'>
									<img class='attachment_preview' src="<?php echo wp_get_attachment_url($attachment["attachment_id"] ?? ""); ?>" width='100' height='100' style='max-height: 100px; width: 100px;'/>
									<figcaption><?php echo get_the_title($attachment["attachment_id"] ?? ""); ?></figcaption>
								</figure>
								<input type='hidden' name='additional_email[<?php echo $nbAddEmail; ?>][attachments][][attachment_id]' class='attachments_attachment_id' value='<?php echo $attachment["attachment_id"] ?? ""; ?>'>
								<button class="remove_attachment" type="button"><?php _e("Remove attachments", "infocob-crm-forms"); ?></button>
							</div>
						<?php endforeach; ?>
					</div>
					
				</div>
			</td>
		</tr>
    <?php } ?>
</table>

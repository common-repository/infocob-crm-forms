<table class="form-table">
    <tr>
        <th>
            <label for="email_template"><?php echo __("Template", "infocob-crm-forms"); ?></label>
        </th>
        <td>
            <select name="email_template" id="email_template" class="full-width" aria-describedby="info_email_template">
                <option value="defaut-infocob-crm-forms"><?php _e("Default (default)", "infocob-crm-forms"); ?></option>
				<?php foreach($email_list_template as $value) { ?>
                    <option value="<?php echo $value; ?>" <?php echo (strcasecmp($email_template, $value) === 0) ? "selected" : ""; ?>><?php echo $value; ?></option>
				<?php } ?>
            </select>
            <p class="description" id="info_email_template">
				<?php _e("Variables availables : title, subtitle, color, logo, form (form data)", "infocob-crm-forms"); ?>
            </p>
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_from"><?php echo __("From", "infocob-crm-forms"); ?></label>
        </th>
        <td>
            <input class="full-width" id="email_from" type='text' name='email_from' value='<?php echo esc_html($email_from); ?>'>
        </td>
    </tr>
	<tr>
		<th>
			<label for="email_form_reply"><?php echo __("Reply (from form)", "infocob-crm-forms"); ?></label>
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
							<select name="email_form_reply[email]" class="full-width" aria-describedby="info_email_form_reply_email">
								<option value=""></option>
								<?php foreach(($inputs_names_list["email"] ?? []) as $champ) { ?>
									<option value="<?php echo $champ["nom"] ?? ""; ?>" <?php echo (isset($email_form_reply["email"]) && strcasecmp($email_form_reply["email"], $champ["nom"]) === 0) ? "selected" : ""; ?>><?php echo $champ["libelle"] . " (" . $champ["nom"] . ")"; ?></option>
								<?php } ?>
							</select>
						</td>
						<td>
							<select name="email_form_reply[firstname]" class="full-width" aria-describedby="info_email_form_reply_firstname">
								<option value=""></option>
								<?php foreach(($inputs_names_list["text"] ?? []) as $champ) { ?>
									<option value="<?php echo $champ["nom"] ?? ""; ?>" <?php echo (isset($email_form_reply["firstname"]) && strcasecmp($email_form_reply["firstname"], $champ["nom"]) === 0) ? "selected" : ""; ?>><?php echo $champ["libelle"] . " (" . $champ["nom"] . ")"; ?></option>
								<?php } ?>
							</select>
						</td>
						<td>
							<select name="email_form_reply[lastname]" class="full-width" aria-describedby="info_email_form_reply_lastname">
								<option value=""></option>
								<?php foreach(($inputs_names_list["text"] ?? []) as $champ) { ?>
									<option value="<?php echo $champ["nom"] ?? ""; ?>" <?php echo (isset($email_form_reply["lastname"]) && strcasecmp($email_form_reply["lastname"], $champ["nom"]) === 0) ? "selected" : ""; ?>><?php echo $champ["libelle"] . " (" . $champ["nom"] . ")"; ?></option>
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
                <select name="email_recipients[]" class="full-width" multiple="multiple">
					<?php foreach($recipients as $recipient): ?>
						<?php if($recipient instanceof WP_Post): ?>
                            <option value="<?php echo $recipient->ID ?>" <?php echo (in_array($recipient->ID, $recipients_selected ?? [])) ? "selected" : ""; ?>><?php echo $recipient->post_title; ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
                </select>
			<?php else: ?>
                <table class="form-table destinataires">
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
							foreach($emails_to as $email_to) { ?>
                                <tr>
                                    <td>
                                        <input name="emails_to[<?php echo $i; ?>][email]" id="email_<?php echo $i; ?>" type="text" value="<?php echo $email_to["email"] ?? ""; ?>" placeholder="<?php echo __("Email", "infocob-crm-forms"); ?>" required />
                                    </td>
                                    <td>
                                        <input name="emails_to[<?php echo $i; ?>][fullname]" id="fullname_<?php echo $i; ?>" type="text" value="<?php echo $email_to["fullname"] ?? ""; ?>" placeholder="<?php echo __("Firstname Lastname", "infocob-crm-forms"); ?>" />
                                    </td>
                                    <td>
                                        <button class="delEmailTo" type="button"><?php _e("Delete", "infocob-crm-forms"); ?></button>
                                    </td>
                                </tr>
								<?php $i ++;
							}
							if(empty($emails_to)) {
								?>
                                <tr>
                                    <td>
                                        <input name="emails_to[0][email]" id="email_0" type="text" value="<?php echo $email_to["email"] ?? ""; ?>" placeholder="<?php echo __("Email", "infocob-crm-forms"); ?>" required />
                                    </td>
                                    <td>
                                        <input name="emails_to[0][fullname]" id="fullname_0" type="text" value="<?php echo $email_to["fullname"] ?? ""; ?>" placeholder="<?php echo __("Firstname Lastname", "infocob-crm-forms"); ?>" />
                                    </td>
                                    <td>
                                        <button class="delEmailTo" type="button"><?php _e("Delete", "infocob-crm-forms"); ?></button>
                                    </td>
                                </tr>
							<?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                                <button class="addEmailTo" type="button"><?php _e("Add", "infocob-crm-forms"); ?></button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
			<?php endif; ?>
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_subject"><?php echo __("Subject", "infocob-crm-forms"); ?></label>
        </th>
        <td>
            <input class="full-width" id="email_subject" type='text' name='email_subject' value='<?php echo esc_html($email_subject); ?>'>
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_societe"><?php echo __("Company", "infocob-crm-forms"); ?></label>
        </th>
        <td>
            <input class="full-width" id="email_societe" type='text' name='email_societe' value='<?php echo esc_html($email_societe); ?>'>
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_title"><?php echo __("Title", "infocob-crm-forms"); ?></label>
        </th>
        <td>
            <input class="full-width" id="email_title" type='text' name='email_title' value='<?php echo esc_html($email_title); ?>'>
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_subtitle"><?php echo __("Subtitle", "infocob-crm-forms"); ?></label>
        </th>
        <td>
            <input class="full-width" id="email_subtitle" type='text' name='email_subtitle' aria-describedby="info_email_subtitle" value='<?php echo esc_html($email_subtitle); ?>'>
            <p class="description" id="info_email_subtitle">
				<?php _e("By default it will be the current page name.", "infocob-crm-forms"); ?>
            </p>
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_color" aria-describedby="info_email_color"><?php echo __("Color", "infocob-crm-forms"); ?></label>
            <p class="description" id="info_email_color">
				<?php _e("Email header", "infocob-crm-forms"); ?>
            </p>
        </th>
        <td>
            <input name="email_color" type='text' class='color-field' value="<?php echo sanitize_text_field($email_color); ?>">
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_color_text_title" aria-describedby="info_email_color_text_title"><?php echo __("Color", "infocob-crm-forms"); ?></label>
            <p class="description" id="info_email_color_text_title">
				<?php _e("Email text", "infocob-crm-forms"); ?>
            </p>
        </th>
        <td>
            <input name="email_color_text_title" type='text' class='color-field' value="<?php echo sanitize_text_field($email_color_text_title); ?>">
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_color_link" aria-describedby="info_email_color_link"><?php echo __("Color", "infocob-crm-forms"); ?></label>
            <p class="description" id="info_email_color_link">
				<?php _e("Links", "infocob-crm-forms"); ?>
            </p>
        </th>
        <td>
            <input name="email_color_link" type='text' class='color-field' value="<?php echo sanitize_text_field($email_color_link); ?>">
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_border_radius"><?php echo __("Border radius", "infocob-crm-forms"); ?></label>
        </th>
        <td>
            <input class="full-width" id="email_border_radius" type='number' name='email_border_radius' aria-describedby="info_email_border_radius" value='<?php echo esc_html($email_border_radius); ?>'>
        </td>
    </tr>
    <tr>
        <th><?php _e("Logo", "infocob-crm-forms"); ?></th>
        <td class="logo_email">
            <div class='image-preview-wrapper'>
                <img class='logo_preview' src='<?php echo wp_get_attachment_url($email_logo["attachment_id"] ?? ""); ?>' width='100' height='100' style='max-height: 100px; width: 100px;'>
            </div>
            <div class="logo_actions">
                <input type="button" class="button upload_logo" value="<?php _e('Upload logo', "infocob-crm-forms"); ?>" />
                <input type='hidden' name='email_logo[attachment_id]' class='logo_attachment_id' value='<?php echo $email_logo["attachment_id"] ?? ""; ?>'>
                <button class="remove_logo" type="button"><?php _e("Remove logo", "infocob-crm-forms"); ?></button>
                <select name="email_logo[size]">
					<?php foreach(get_intermediate_image_sizes() as $size) { ?>
                        <option value="<?php echo $size; ?>" <?php echo (isset($email_logo["size"]) && strcasecmp($email_logo["size"], $size) === 0) ? "selected" : "" ?>><?php echo $size; ?></option>
					<?php } ?>
                </select>
            </div>
        </td>
    </tr>
</table>

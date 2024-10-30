<input type="hidden" name="post_id" id="post_id" value="<?php echo get_the_ID(); ?>">

<table id="recipients-table" class="form-table">
    <thead>
        <tr>
            <th><?php _e("Lastname", "infocob-crm-forms"); ?></th>
            <th><?php _e("Firstname", "infocob-crm-forms"); ?></th>
            <th><?php _e("Email", "infocob-crm-forms"); ?></th>
            <th><?php _e("Carbon copy (cc)", "infocob-crm-forms"); ?></th>
            <th><?php _e("Blind carbon copy (bcc)", "infocob-crm-forms"); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($recipients as $index => $recipient): ?>
            <tr>
                <td>
                    <input type="text" name="recipients[ <?php echo $index; ?> ][lastname]" value="<?php echo $recipient["lastname"] ?? ""; ?>" placeholder="<?php _e("Lastname", "infocob-crm-forms"); ?>">
                </td>
                <td>
                    <input type="text" name="recipients[ <?php echo $index; ?> ][firstname]" value="<?php echo $recipient["firstname"] ?? ""; ?>" placeholder="<?php _e("Firstname", "infocob-crm-forms"); ?>">
                </td>
                <td>
                    <input type="email" name="recipients[ <?php echo $index; ?> ][email]" value="<?php echo $recipient["email"] ?? ""; ?>" placeholder="<?php _e("Email", "infocob-crm-forms"); ?>">
                </td>
                <td class="has-checkbox">
                    <input type="checkbox" name="recipients[ <?php echo $index; ?> ][cc]" <?php if($recipient["cc"] ?? false): ?>checked<?php endif; ?>>
                </td>
                <td class="has-checkbox">
                    <input type="checkbox" name="recipients[ <?php echo $index; ?> ][bcc]" <?php if($recipient["bcc"] ?? false): ?>checked<?php endif; ?>>
                </td>
                <td>
                    <button class="del-recipient" type="button"><?php _e("Delete", "infocob-crm-forms"); ?></button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6">
                <button id="add-recipient" type="button"><?php _e("Add", "infocob-crm-forms"); ?></button>
            </td>
        </tr>
    </tfoot>
</table>


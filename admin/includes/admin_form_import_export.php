<?php
	// don't load directly
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	require_once(ABSPATH . 'wp-includes/pluggable.php');
?>

<div class="wrap">
    <h1><?php _e("Import / Export - Infocob forms", "infocob-crm-forms"); ?></h1>
</div>

<form id="import_form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
	<?php wp_nonce_field("infocob_crm_forms_import", "nonce"); ?>
    <input name='action' type="hidden" value='infocob_crm_forms_import_action'>
    <table class="form-table">
        <tr>
            <th><label for="import_json_file"><?php echo __("Import", "infocob-crm-forms"); ?></label></th>
            <td>
                <input id="import_json_file" type="file" name="import_json_file" accept="application/json">
            </td>
            <td></td>
            <td>
                <input type="submit" value="<?php _e("Import", "infocob-crm-forms"); ?>">
            </td>
        </tr>
    </table>
</form>

<form id="export_form" method="post" action="">
    <a id="download_export_json" style="display:none"></a>
    <table class="form-table">
        <tr>
            <th><label for="form_export_id"><?php echo __("Export", "infocob-crm-forms"); ?></label></th>
            <td>
                <select name="form_export_id" id="form_export_id">
                    <option></option>
					<?php
						if($wp_query_ifb_crm_forms->have_posts()) {
							while($wp_query_ifb_crm_forms->have_posts()) {
								$wp_query_ifb_crm_forms->the_post();
								?>
                                <option value="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></option>
								<?php
							}
						}
					?>
                </select>
            </td>
            <td>
                <label for='export_config_crm'><?php _e("CRM", "infocob-crm-forms"); ?></label>
                <input id="export_config_crm" type='checkbox' name='export_config_crm'>
            </td>
            <td>
                <input type="submit" value="<?php _e("Export", "infocob-crm-forms"); ?>">
            </td>
        </tr>
    </table>
</form>

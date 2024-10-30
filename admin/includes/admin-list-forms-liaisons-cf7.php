<?php
	if(!defined('ABSPATH')) {
		exit;
	} // Exit if accessed directly
?>
<div class="box-plugin">
    <h3><?php _e("Form choice", "infocob-crm-forms"); ?></h3>
    <div class="select-form">
        <select name='listForm' size='1' onload='document.location=this.options[this.selectedIndex].value;' onchange='document.location=this.options[this.selectedIndex].value;'>
            <option value="<?php echo remove_query_arg((["post_id"] ?? ""), home_url() . $_SERVER['REQUEST_URI']); ?>"></option>
			<?php foreach(($forms ?? []) as $form) { ?>
				<?php
				$dataDbForm = \Infocob\CrmForms\Admin\Database::getFormCf7FromDb($form->id());
				$selected   = (!empty($_GET["post_id"]) && $_GET["post_id"] == $form->id()) ? "selected" : "";
				$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
				$option_link = add_query_arg(["post_id" => $form->id()], $actual_link);
				?>
                <option value='<?php echo $option_link; ?>' <?php echo $selected; ?>><?php echo $form->title() ?></option>
			<?php } ?>
        </select>
        <div class="select__arrow"></div>
    </div>
    <input class="inputPlugin" type='button' onclick='window.location.reload()' value='<?php _e("Refresh", "infocob-crm-forms"); ?>' /></h2>
</div>

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
			<?php
				if(($wp_query_ifb_crm_forms ?? false) && $wp_query_ifb_crm_forms->have_posts()) {
					while($wp_query_ifb_crm_forms->have_posts()) {
						$wp_query_ifb_crm_forms->the_post();
						
						$selected   = (!empty($_GET["post_id"]) && $_GET["post_id"] == get_the_ID()) ? "selected" : "";
						$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
						$option_link = add_query_arg(["post_id" => get_the_ID()], $actual_link);
						
						echo "<option value='" . $option_link . "' " . $selected . ">" . get_the_title() . "</option>";
					}
				}
            ?>
        </select>
        <div class="select__arrow"></div>
    </div>
    <input class="inputPlugin" type='button' onclick='window.location.reload()' value='<?php _e("Refresh", "infocob-crm-forms"); ?>' /></h2>
</div>

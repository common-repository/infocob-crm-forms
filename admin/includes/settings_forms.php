<form action="options.php" method="post">
	<?php
		settings_fields('infocob_crm_forms');
		do_settings_sections('infocob_crm_forms');
		submit_button();
	?>
</form>

<?php
	
	namespace Infocob\CrmForms\Admin;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Caps {
		public static $edit_settings = "icf_edit_settings";
		public static $edit_forms = "icf_edit_forms";
		public static $view_logs = "icf_view_logs";
	}

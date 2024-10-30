<?php
	
	namespace Infocob\CrmForms\Admin;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Upgrade_version_1 extends Upgrade_version {
		
		/*
		 * Move old logs folder to the new location
		 */
		public function upgrade() {
			$base_path = trim(ROOT_INFOCOB_CRM_FORMS_DIR_PATH, "/") . "/logs";
			if(file_exists($base_path)) {
				$new_base_path = Logger::getLogsFolder();
				if(file_exists($new_base_path)) {
					Tools::copyDirectory($base_path, $new_base_path."/logs");
					Tools::deleteDirectory($base_path);
				}
			}
		}
	}

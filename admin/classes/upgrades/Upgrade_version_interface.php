<?php
	
	namespace Infocob\CrmForms\Admin;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	interface Upgrade_version_interface {
		
		public function upgrade();
		
	}

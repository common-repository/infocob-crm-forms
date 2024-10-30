<?php
	
	namespace Infocob\CrmForms\Admin\WPCli;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Core_Command {
		
		public function __invoke($args, $assoc_args) {
			if(class_exists('\WP_CLI')) {
				if(!empty($args[0])) {
					if(strcasecmp($args[0], "settings") === 0) {
						unset($args[0]);
						$args = array_values($args);
						return new Settings_Command($args, $assoc_args);
						
					} else if(strcasecmp($args[0], "forms") === 0) {
						unset($args[0]);
						$args = array_values($args);
						return new Forms_Command($args, $assoc_args);
					}
				}
				
				static::usageMessage();
			}
			
			return false;
		}
		
		public static function usageMessage() {
			if(class_exists('\WP_CLI')) {
				\WP_CLI::log(
					"usage:  wp icf settings [<name>|list]\n\t" .
					"wp icf forms [list]"
				);
			}
		}
	}

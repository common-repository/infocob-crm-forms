<?php
	
	
	namespace Infocob\CrmForms\Admin;
	
	if(!defined('ABSPATH')) {
		exit;
	} // Exit if accessed directly
	
	class Polylang {
		protected static $translations = [];
		
		public static function addTranslations($translations) {
			if(empty(static::$translations)) {
				static::$translations = $translations;
			} else {
				foreach($translations as $k => $s) {
					if(isset(static::$translations[ $k ]) && static::$translations[ $k ] !== $s) {
						$i = 0;
						do {
							$kn = $k . "-" . $i;
							$i ++;
						} while(isset(static::$translations[ $kn ]));
						$k = $kn;
					}
					static::$translations[ $k ] = $s;
				}
			}
		}
		
		public static function registerStrings() {
			foreach(static::$translations as $name => $string) {
				pll_register_string($name, $string, 'infocob-crm-forms', true);
			}
		}
	}

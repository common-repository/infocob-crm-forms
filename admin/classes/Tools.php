<?php
	
	
	namespace Infocob\CrmForms\Admin;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Tools {
		
		public static function extractShortCode($string) {
			$string = preg_replace("/(\[\/?group.*])/mi", "", $string);
			preg_match_all("/(\[(?!\/|submit|recaptcha).*\])/mi", $string, $shortcode);
			
			return $shortcode[0];
		}
		
		public static function getShortCodeName($shortcode) {
			preg_match("/^(\[[^\ ]* ([^\ ]*))/mi", $shortcode, $newShortcode);
			$result = !empty($newShortcode[2]) ? rtrim($newShortcode[2], "]") : $shortcode;
			return $result;
		}
		
		public static function explodeShortName($string){
			$stringExplode = explode('"', $string);
			if(strstr($stringExplode[0], 'select')){
				return $stringExplode[0] . "]";
			} else {
				return $string;
			}
		}
		
		public static function sanitize_fields($data, $stripslashes = false) {
			if(is_string($data)) {
				$data = $stripslashes ? stripslashes(sanitize_text_field($data)) : sanitize_text_field($data);
				
			} else if(is_int($data)) {
				$data = (int) $data;
				
			} else if(is_bool($data)) {
				$data = (bool) $data;
				
			} else if(is_array($data)) {
				foreach($data as $key => &$value) {
					if(is_array($value)) {
						$value = static::sanitize_fields($value);
					} else {
						if(is_string($value)) {
							$value = $stripslashes ? stripslashes(sanitize_text_field($value)) : sanitize_text_field($value);
						} else if(is_int($value)) {
							$value = (int) $value;
						} else if(is_bool($value)) {
							$value = (bool) $value;
						}
					}
				}
			}
			
			return $data;
		}
		
		public static function infocob_register_data_db_filterStripSlashes(&$value) {
			if(is_null($value)) $value = "";
			$value = stripslashes($value); // PROBLEM BACKSLASH
		}
		
		public static function validate_google_recaptcha_v3($token) {
			// Google Recaptcha V3
			$options                = get_option('infocob_crm_forms_settings');
			$google_recaptcha_v3_secret_key = $options['google_recaptcha_v3']['secret_key'] ?? false;
			
			if (!isset($token) && $google_recaptcha_v3_secret_key) {
				return false;
			}
			$siteverify = 'https://www.google.com/recaptcha/api/siteverify';
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $siteverify . '?secret=' . $google_recaptcha_v3_secret_key . '&response=' . $token);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);
			curl_close($ch);
			
			$response = json_decode($response, true);
			
			return $response['success'] ?? false;
		}
		
		public static function validate_hCaptcha($token) {
			// hCaptcha
			$options                = get_option('infocob_crm_forms_settings');
			$hcaptcha_secret_key = $options['hcaptcha']['secret_key'] ?? false;
			
			if (!isset($token) && $hcaptcha_secret_key) {
				return false;
			}
			$siteverify = 'https://hcaptcha.com/siteverify';
			
			
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $siteverify);
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query([
				"response" => $token,
				"secret" => $hcaptcha_secret_key
			]));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//local, désactiver le https
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);
			curl_close ($ch);
			
			$response = json_decode($response, true);
			
			return $response['success'] ?? false;
		}
		
		public static function copyDirectory($source, $destination) {
			if (!is_dir($destination)) {
				mkdir($destination, 0755, true);
			}
			
			$dirContent = scandir($source);
			
			foreach ($dirContent as $item) {
				if ($item !== '.' && $item !== '..') {
					$sourcePath = $source . '/' . $item;
					$destinationPath = $destination . '/' . $item;
					
					if (is_dir($sourcePath)) {
						Tools::copyDirectory($sourcePath, $destinationPath);
					} else {
						copy($sourcePath, $destinationPath);
					}
				}
			}
		}
		
		public static function deleteDirectory($directory) {
			if (!is_dir($directory)) {
				return false;
			}
			
			$dirContent = scandir($directory);
			
			foreach ($dirContent as $item) {
				if ($item !== '.' && $item !== '..') {
					$itemPath = $directory . '/' . $item;
					
					if (is_dir($itemPath)) {
						Tools::deleteDirectory($itemPath);
					} else {
						unlink($itemPath);
					}
				}
			}
			
			return rmdir($directory);
		}
		
		public static function setFieldFromForm($string, $data) {
			preg_match_all("/({{\s?(\w+)\s?}})/mi", $string, $matches);
			if (!empty($matches) && !empty($matches[1])) {
				$stringsToReplace = $matches[1];
				foreach ($stringsToReplace as $index => $stringToReplace) {
					$field_name = !empty($matches[2][$index]) ? $matches[2][$index] : "";
					$field_value = "";
					
					if (is_array($data)) {
						$field_valid = isset($data[$field_name]);
					} else {
						$field_valid = false;
					}
					
					if ($field_valid) {
						$field_value = trim($data[$field_name]);
					}
					
					$string = str_replace($stringToReplace, $field_value, $string);
				}
			}
			return $string;
		}
	}

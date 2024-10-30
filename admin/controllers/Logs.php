<?php
	
	
	namespace Infocob\CrmForms\Admin;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Logs extends Controller {
		
		public function render() {
			$logs = Logger::getLogs();
			
			require_once plugin_dir_path(__FILE__) . '../includes/admin_form_logs.php';
		}
		
		public function wp_ajax_get_logs_file() {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			$level = sanitize_text_field($_GET["level"] ?? "mails");
			$filename = sanitize_text_field($_GET["filename"] ?? "");
			
			$logs = [];
			$path = Logger::getLogsFolder();
			$path = $path . "/logs/" . $level . "/" . $filename . ".log";
			if(!empty($filename) && file_exists($path)) {
				$handle = fopen($path, "r");
				if ($handle) {
					while (($line = fgets($handle)) !== false) {
						$logs[] = $line;
					}
					
					fclose($handle);
				}
			}
			
			if(check_ajax_referer('get_logs_forms_nonce', 'security', false) === 1) {
				wp_send_json_success($logs);
			} else {
				wp_send_json_error(__("Unable to retrieve data", "infocob-crm-forms"));
			}
		}
		
		public function wp_ajax_download_logs_file() {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			$filename = sanitize_text_field($_GET["filename"] ?? "");
			$path = Logger::getLogsFolder();
			$path = $path . "/logs/mails/eml/" . $filename;
			
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Description: File Transfer");
			header("Content-Type: application/octet-stream");
			header("Content-Transfer-Encoding: binary");
			
			if(!empty($filename) && file_exists($path)) {
				header('Content-Disposition: attachment; filename="'.basename($path).'"');
				header("Content-Length: ".filesize($path));
				
				if(check_ajax_referer('get_logs_forms_nonce', 'security', false) === 1) {
					readfile($path);
				}
			}
		}
		
	}

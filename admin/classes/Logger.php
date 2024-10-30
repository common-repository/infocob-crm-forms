<?php
	
	namespace Infocob\CrmForms\Admin;
	
	use Monolog\Formatter\LineFormatter;
	use Monolog\Formatter\JsonFormatter;
	use Monolog\Handler\StreamHandler;
	use Monolog\Logger as Monolog;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Logger {
		private static string $mails_path = "logs/mails";
		
		public static function getLogsFolder() {
			$dir = wp_upload_dir();
			$base_path = $dir["basedir"] ?? false;
			if(!empty($base_path)) {
				$base_path .= "/" . dirname(INFOCOB_CRM_FORMS_BASENAME);
				if (!file_exists($base_path)) mkdir($base_path, 0777, true);
				@chmod($base_path, 0777);
			}
			return $base_path;
		}
		
		/**
		 * @param string $message
		 * @param array  $vars
		 */
		public static function mail(string $message, array $vars = [], $eml_file = "") {
			$datetime = new DateTimeFr();
			$log_filename = $datetime->format("d-m-Y");
			$formatter = new JsonFormatter();
			
			$base_path = static::getLogsFolder();
			if(!empty($base_path)) {
				// create a log channel
				$log = new Monolog('mails');
				$stream_info = new StreamHandler($base_path . "/" . Logger::$mails_path . "/" . $log_filename . ".log", Monolog::INFO, true, 0777);
				$stream_info->setFormatter($formatter);
				$log->pushHandler($stream_info);
				
				if(!empty($eml_file) && !empty($vars["message_id"])) {
					if (!file_exists($base_path . "/" . Logger::$mails_path . "/eml/")) {
						mkdir($base_path . "/" . Logger::$mails_path . "/eml/", 0777, true);
					}
					
					if(file_put_contents($base_path . "/" . Logger::$mails_path . "/eml/" . $vars["message_id"] . ".eml", $eml_file)) {
						$vars["file"] = [
							"url" => $base_path . "/" . Logger::$mails_path . "/eml/" . $vars["message_id"] . ".eml",
							"path" => $base_path . "/" . Logger::$mails_path . "/eml/" . $vars["message_id"] . ".eml",
							"name" => $vars["message_id"] . ".eml"
						];
					}
				}
				
				$log->info($message, $vars);
				
				Logger::cleanLogs();
			}
		}
		
		/**
		 * Delete old log files
		 */
		public static function cleanLogs($force = false) {
			$base_path = static::getLogsFolder();
			if(!empty($base_path)) {
				
				if ($force) {
					$expiration_time = time(); // Clean now
				} else {
					$expiration_time = time() - (60 * 60 * 24 * 7 * 26); // 13 weeks (~ 3 months)
				}
				
				foreach (glob($base_path . "/" . Logger::$mails_path . "/*.log") as $filename) {
					if (filemtime($filename) < $expiration_time) {
						unlink($filename);
					}
				}
				
				foreach (glob($base_path . "/" . Logger::$mails_path . "/eml/*.eml") as $filename) {
					if (filemtime($filename) < $expiration_time) {
						unlink($filename);
					}
				}
			}
		}
		
		public static function getLogs() {
			$logs_files = [];
			
			$base_path = static::getLogsFolder();
			if(!empty($base_path)) {
				$base_path .= "/logs/";
				if (!file_exists($base_path)) mkdir($base_path, 0777, true);
				
				foreach (["mails"] as $level) {
					if (file_exists($base_path . $level)) {
						foreach (glob($base_path . $level . "/*.log") as $path) {
							$logs_files[$level][] = [
								"path"                 => $path,
								"filename"             => basename($path),
								"filename_without_ext" => pathinfo(basename($path), PATHINFO_FILENAME)
							];
						}
					}
					
					if (!empty($logs_files[$level])) {
						usort($logs_files[$level], function ($file_a, $file_b) {
							$updatetime_a = filemtime($file_a["path"]);
							$updatetime_b = filemtime($file_b["path"]);
							if ($updatetime_a == $updatetime_b) {
								return 0;
							}
							
							return ($updatetime_a > $updatetime_b) ? -1 : 1;
						});
					}
				}
			}
			
			return $logs_files;
		}
		
	}

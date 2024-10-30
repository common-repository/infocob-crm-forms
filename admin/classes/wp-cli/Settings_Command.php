<?php
	
	namespace Infocob\CrmForms\Admin\WPCli;
	
	use Infocob\CrmForms\Admin\Webservice;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Settings_Command {
		protected $isCmdValid = false;
		protected $export_format = "table";
		
		public function __construct($args, $assoc_args) {
			if(class_exists('\WP_CLI')) {
				if(!empty($assoc_args["format"])) {
					$this->export_format = $assoc_args["format"];
				}
				
				if(!empty($args[0])) {
					if(strcasecmp($args[0], "list") === 0) {
						$this->list();
						
					} else if(strcasecmp($args[0], "webservice") === 0) {
						$this->webservice();
						
					} else if(strcasecmp($args[0], "espaceclient") === 0) {
						$this->espace_client();
						
					} else if(strcasecmp($args[0], "smtp") === 0) {
						$this->smtp();
						
					} else if(strcasecmp($args[0], "gcaptcha") === 0) {
						$this->google_recaptcha_v3();
						
					} else if(strcasecmp($args[0], "hcaptcha") === 0) {
						$this->hCaptcha();
						
					} else if(strcasecmp($args[0], "recipients") === 0) {
						$this->recipients();
						
					} else if(strcasecmp($args[0], "select2js") === 0) {
						$this->select2_js();
						
					}
					
				} else {
					$this->list();
				}
				
				if(!$this->isCmdValid) \WP_CLI::error("Command unavailable !");
			}
		}
		
		private function list() {
			if(class_exists('\WP_CLI')) {
				$items = [
					[
						"name" => "webservice",
						"description" => "Webservice configuration (CRM)"
					],
					[
						"name" => "espaceclient",
						"description" => "Customers area configuration (auto-connection)"
					],
					[
						"name" => "smtp",
						"description" => "SMTP configuration"
					],
					[
						"name" => "gcaptcha",
						"description" => "Google Recaptcha configuration"
					],
					[
						"name" => "hcaptcha",
						"description" => "hCaptcha configuration"
					],
					[
						"name" => "recipients",
						"description" => "Information about the advanced recipients module"
					],
					[
						"name" => "select2js",
						"description" => "Information about the JS library"
					]
				];
				
				\WP_CLI\Utils\format_items($this->export_format, $items, ['name', 'description']);
				$this->isCmdValid = true;
			}
		}
		
		private function webservice() {
			if(class_exists('\WP_CLI')) {
				$options = get_option('infocob_crm_forms_settings');
				
				$domain = $options["api"]["domain"] ?? "";
				$api_key = !empty($options["api"]["key"]) ? "defined" : "undefined";
				$status = "inactive";
				if(!empty($domain)) {
					$webservice = new Webservice();
					$status = $webservice->test() ? "active" : "failed";
				}
				
				$items = [
					[
						"key" => "status",
						"value" => $status
					],
					[
						"key" => "domain",
						"value" => $domain
					],
					[
						"key" => "api_key",
						"value" => $api_key
					]
				];
				
				\WP_CLI\Utils\format_items($this->export_format, $items, ['key', 'value']);
				$this->isCmdValid = true;
			}
		}
		
		private function espace_client() {
			if(class_exists('\WP_CLI')) {
				$options = get_option('infocob_crm_forms_settings');
				
				$enabled = !empty($options["ec"]["enabled"]) ? "true" : "false";
				$domain = $options["ec"]["domain"] ?? "";
				$api_key = $options["ec"]["api_key"] ?? "";
				
				$items = [
					[
						"key" => "enabled",
						"value" => $enabled
					],
					[
						"key" => "domain",
						"value" => $domain
					],
					[
						"key" => "api_key",
						"value" => $api_key
					]
				];
				
				\WP_CLI\Utils\format_items($this->export_format, $items, ['key', 'value']);
				$this->isCmdValid = true;
			}
		}
		
		private function smtp() {
			if(class_exists('\WP_CLI')) {
				$options = get_option('infocob_crm_forms_settings');
				
				$enabled = !empty($options['smtp']['enabled']) ? "true" : "false";
				$host = $options['smtp']['host'] ?? "";
				$username = $options['smtp']['username'] ?? "";
				$password = !empty($options['smtp']['password']) ? "defined" : "undefined";
				$port = $options['smtp']['port'] ?? "";
				
				$items = [
					[
						"key" => "enabled",
						"value" => $enabled
					],
					[
						"key" => "host",
						"value" => $host
					],
					[
						"key" => "username",
						"value" => $username
					],
					[
						"key" => "password",
						"value" => $password
					],
					[
						"key" => "port",
						"value" => $port
					]
				];
				
				\WP_CLI\Utils\format_items($this->export_format, $items, ['key', 'value']);
				$this->isCmdValid = true;
			}
		}
		
		private function google_recaptcha_v3() {
			if(class_exists('\WP_CLI')) {
				$options = get_option('infocob_crm_forms_settings');
				
				$enabled = !empty($options['google_recaptcha_v3']['enabled']) ? "true" : "false";
				$client_key = $options['google_recaptcha_v3']['client_key'] ?? "";
				$secret_key = !empty($options['google_recaptcha_v3']['secret_key']) ? "defined" : "undefined";
				
				$items = [
					[
						"key" => "enabled",
						"value" => $enabled
					],
					[
						"key" => "client_key",
						"value" => $client_key
					],
					[
						"key" => "secret_key",
						"value" => $secret_key
					]
				];
				
				\WP_CLI\Utils\format_items($this->export_format, $items, ['key', 'value']);
				$this->isCmdValid = true;
			}
		}
		
		private function hCaptcha() {
			if(class_exists('\WP_CLI')) {
				$options = get_option('infocob_crm_forms_settings');
				
				$enabled = !empty($options['hcaptcha']['enabled']) ? "true" : "false";
				$client_key = $options['hcaptcha']['client_key'] ?? "";
				$secret_key = !empty($options['hcaptcha']['secret_key']) ? "defined" : "undefined";
				$size = !empty($options['hcaptcha']['size']) ? $options['hcaptcha']['size'] : "default";
				$theme = !empty($options['hcaptcha']['theme']) ? $options['hcaptcha']['theme'] : "auto";
				
				$items = [
					[
						"key" => "enabled",
						"value" => $enabled
					],
					[
						"key" => "client_key",
						"value" => $client_key
					],
					[
						"key" => "secret_key",
						"value" => $secret_key
					],
					[
						"key" => "size",
						"value" => $size
					],
					[
						"key" => "theme",
						"value" => $theme
					]
				];
				
				\WP_CLI\Utils\format_items($this->export_format, $items, ['key', 'value']);
				$this->isCmdValid = true;
			}
		}
		
		private function recipients() {
			if(class_exists('\WP_CLI')) {
				$options = get_option('infocob_crm_forms_settings');
				
				$enabled = !empty($options['recipients']['enabled']) ? "true" : "false";
				
				$items = [
					[
						"key" => "enabled",
						"value" => $enabled
					]
				];
				
				\WP_CLI\Utils\format_items($this->export_format, $items, ['key', 'value']);
				$this->isCmdValid = true;
			}
		}
		
		private function select2_js() {
			if(class_exists('\WP_CLI')) {
				$options = get_option('infocob_crm_forms_settings');
				
				$enabled = !empty($options["form_config"]["select2JS"]) ? "false" : "true";
				
				$items = [
					[
						"key" => "enabled",
						"value" => $enabled
					]
				];
				
				\WP_CLI\Utils\format_items($this->export_format, $items, ['key', 'value']);
				$this->isCmdValid = true;
			}
		}
		
	}

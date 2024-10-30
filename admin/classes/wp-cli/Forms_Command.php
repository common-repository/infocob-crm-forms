<?php
	
	namespace Infocob\CrmForms\Admin\WPCli;
	
	use Infocob\CrmForms\Admin\Database;
	use Infocob\CrmForms\Admin\Webservice;
	use WP_Query;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Forms_Command {
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
						
					}
					
				} else {
					$this->list();
				}
				
				if(!$this->isCmdValid) \WP_CLI::error("Command unavailable !");
			}
		}
		
		private function list() {
			if(class_exists('\WP_CLI')) {
				$wp_query_ifb_crm_forms = new WP_Query([
					'post_type' => 'ifb_crm_forms',
//					'post_status' => 'publish',
					'posts_per_page' => -1
				]);
				
				$items = [];
				if($wp_query_ifb_crm_forms->have_posts()) {
					$options = get_option('infocob_crm_forms_settings');
					
					$webservice_status = "inactive";
					$webservice_domain = $options["api"]["domain"] ?? "";
					if(!empty($webservice_domain)) {
						$webservice = new Webservice();
						$webservice_status = $webservice->test() ? "active" : "failed";
					}
					
					$gcaptcha_enabled = !empty($options['google_recaptcha_v3']['enabled']);
					$hcaptcha_enabled = !empty($options['hcaptcha']['enabled']);
					$captcha_enabled = ($gcaptcha_enabled || $hcaptcha_enabled) ? "true" : "false";
					
					while($wp_query_ifb_crm_forms->have_posts()) {
						$wp_query_ifb_crm_forms->the_post();
						
						$admin_form_edit_json = get_post_meta(get_the_ID(), 'infocob_crm_forms_admin_form_email_config', true);
						$admin_form_edit = json_decode($admin_form_edit_json, true);
						
						$email_from = $admin_form_edit["email_from"] ?? "";
						
						$emails_to = $admin_form_edit["emails_to"] ?? [];
						$emails_to_string = "";
						foreach($emails_to as $index => $email_to) {
							$emails_to_string .= ($email_to["email"] ?? "");
							if($index < count($emails_to)-1) $emails_to_string .= ';';
						}
						
						$email_form_reply = $admin_form_edit["email_form_reply"] ?? [];
						$email_form_reply_string = $email_from;
						if(!empty($email_form_reply["email"])) {
							$email_form_reply_string = "user";
						}
						
						$crm = "false";
						$infocob_form_config = Database::getFormIfbFromDb(get_the_ID());
						if(!empty($infocob_form_config) && is_array($infocob_form_config)) {
							if(!empty($infocob_form_config["tables"]) && is_array($infocob_form_config)) {
								foreach($infocob_form_config["tables"] as $table => $active) {
									if($active) {
										$crm = "true";
										break;
									}
								}
							}
						}
						
						$items[] = [
							"id" => get_the_ID(),
							"name" => get_the_title(),
							"status" => get_post_status(),
							"from" => $email_from,
							"to" => $emails_to_string,
							"reply" => $email_form_reply_string,
							"crm" => $crm,
							"webservice" => $webservice_status,
							"captcha" => $captcha_enabled
						];
					}
				}
				
				\WP_CLI\Utils\format_items($this->export_format, $items, ['name', 'status', 'from', 'to', 'reply', 'crm', 'webservice', 'captcha']);
				$this->isCmdValid = true;
			}
		}
		
	}

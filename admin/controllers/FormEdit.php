<?php
	
	namespace Infocob\CrmForms\Admin;
 
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FormEdit extends Controller {
		
		public $post_id;
		public $form = [];
		
		public function __construct() {
			global $post;
			$admin_form_edit_json = get_post_meta($post->ID, 'infocob_crm_forms_admin_form_config', true);
			$admin_form_edit = json_decode($admin_form_edit_json, true);
			$options = get_option('infocob_crm_forms_settings');
			$espace_clients_enabled = $options['ec']['enabled'] ?? false;
			$type_formulaire = !empty($admin_form_edit["type_formulaire"]) ? $admin_form_edit["type_formulaire"] : "";
   
			$success_webservice = false;
			$webservice = new Webservice();
            if(!empty(Webservice::$domain_client)) {
				$success_webservice = $webservice->test();
            }
			
			$success_espace_clients = false;
			if($espace_clients_enabled) {
				$espace_clients = new EspaceClients();
				$success_espace_clients = $espace_clients->test();
			}
			
			//get the current screen
			$screen = get_current_screen();
			
			if($screen->id === "ifb_crm_forms") {
				if(!$success_webservice && !empty(Webservice::$domain_client) && strcasecmp($type_formulaire, "crm-mobile") !== 0) {
					add_action('admin_notices', function() {
						?>
                        <div class="notice notice-error">
                            <p><?php _e('Warning, API connection failed !', 'infocob-crm-forms'); ?><br />
								<?php _e('Forms will not be links to Infocob !', 'infocob-crm-forms'); ?><br />
                                <a href="<?php menu_page_url('infocob-crm-forms-admin-settings-page'); ?>"><?php _e('Click here to change the connection parameters', 'infocob-crm-forms'); ?></a>
                            </p>
                        </div>
						<?php
					});
				}
				
				if($espace_clients_enabled && !$success_espace_clients && !empty(EspaceClients::$domain_client) && strcasecmp($type_formulaire, "espace_clients") === 0) {
					add_action('admin_notices', function() {
						?>
                        <div class="notice notice-error">
                            <p><?php _e('Warning, connection to customers area failed !', 'infocob-crm-forms'); ?><br />
								<?php _e('This form will not works !', 'infocob-crm-forms'); ?><br />
                                <a href="<?php menu_page_url('infocob-crm-forms-admin-settings-page'); ?>"><?php _e('Click here to change the connection parameters', 'infocob-crm-forms'); ?></a>
                            </p>
                        </div>
						<?php
					});
				}
			}
		}
		
		public function renderFormConfigMetabox() {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			global $post;
			
			// Nonce field to validate form request came from current site
			wp_nonce_field(basename(__FILE__), 'event_fields');
			
			$admin_form_edit_json = get_post_meta($post->ID, 'infocob_crm_forms_admin_form_config', true);
			$admin_form_edit = json_decode($admin_form_edit_json, true);
			
			$shortcode_form = !empty($admin_form_edit["shortcode_form"]) ? $admin_form_edit["shortcode_form"] : "[infocob-crm-forms id='" . $post->ID . "']";
			$inputs_form = !empty($admin_form_edit["input"]) ? $admin_form_edit["input"] : [];
			$fullwidth = !empty($admin_form_edit["fullwidth"]) ? "checked" : "";
			$email_supp_enable = !empty($admin_form_edit["email_supp_enable"]) ? "checked" : "";
			
			$recipients_enabled = !empty($admin_form_edit["recipients_enabled"]) ? "checked" : "";
			$recipients = $admin_form_edit["recipients"] ?? [];
			
			$btn_send = isset($admin_form_edit["btn_send"]) ? sanitize_text_field($admin_form_edit["btn_send"]) : "Envoyer";
			$max_size = isset($admin_form_edit["max_file_size"]) ? sanitize_text_field($admin_form_edit["max_file_size"]) : 2 * 1024 * 1024;
			$disable_rgpd = !empty($admin_form_edit["disable_rgpd"]) ? "checked" : "";
			$permalink_rgpd = !empty(get_option('wp_page_for_privacy_policy')) ? get_permalink(get_option('wp_page_for_privacy_policy')) : "#";
			$input_rgpd = isset($admin_form_edit["input_rgpd"]) ? wp_check_invalid_utf8($admin_form_edit["input_rgpd"]) : "J'ai pris connaissance et j'accepte <a href='" . $permalink_rgpd . "' target='_blank' class='main'>la politique de confidentialité</a> de " . get_bloginfo("name") . "";
			$columns_base = isset($admin_form_edit["columns_base"]) ? sanitize_text_field($admin_form_edit["columns_base"]) : 4;
			$redirect_page_submit = isset($admin_form_edit["redirect_page_submit"]) ? sanitize_text_field($admin_form_edit["redirect_page_submit"]) : "";
			
			$options = get_option('infocob_crm_forms_settings');
			$espace_clients_enabled = $options['ec']['enabled'] ?? false;
			
			$type_formulaire = !empty($admin_form_edit["type_formulaire"]) ? $admin_form_edit["type_formulaire"] : "";
			$module_telechargement = !empty($admin_form_edit["ec_module_telechargement"]) ? $admin_form_edit["ec_module_telechargement"] : "";
			$ec_connection_fields = !empty($admin_form_edit["ec_connection_fields"]) ? $admin_form_edit["ec_connection_fields"] : [];
			
			$wp_pages_list = !empty(get_pages()) ? get_pages() : [];
			$liste_modules_telechargement = [];
			if($espace_clients_enabled) {
				$espaceClients = new EspaceClients();
				$liste_modules_telechargement = $espaceClients->getModulesTelechargement();
			}
			
			$recipients_option_enabled = filter_var(($options['recipients']['enabled'] ?? false), FILTER_VALIDATE_BOOLEAN);
			$recipients = get_posts([
				"numberposts" => -1,
				"post_type" => "ifb_recipients"
			]);
			
			// Output the field
			include ROOT_INFOCOB_CRM_FORMS_DIR_PATH . "admin/includes/admin_form_config_metabox.php";
		}
		
		public function renderFormEmailMetabox() {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			global $post;
			
			// Nonce field to validate form request came from current site
			wp_nonce_field(basename(__FILE__), 'event_fields');
			
			$admin_form_edit_json = get_post_meta($post->ID, 'infocob_crm_forms_admin_form_email_config', true);
			$admin_form_edit = json_decode($admin_form_edit_json, true);
			
			$email_from = !empty($admin_form_edit["email_from"]) ? $admin_form_edit["email_from"] : get_bloginfo('admin_email'); //no-reply@dev.wordpress.local
			$email_subject = !empty($admin_form_edit["email_subject"]) ? $admin_form_edit["email_subject"] : get_bloginfo('name');
			$emails_to = !empty($admin_form_edit["emails_to"]) ? Tools::sanitize_fields($admin_form_edit["emails_to"]) : [
				[
					"email" => sanitize_email(get_bloginfo('admin_email')),
					"fullname" => "",
				]
			];
			
			$email_form_reply = !empty($admin_form_edit["email_form_reply"]) ? Tools::sanitize_fields($admin_form_edit["email_form_reply"]) : [
				"email" => "",
				"firstname" => "",
				"lastname" => "",
			];
			
			$email_title = !empty($admin_form_edit["email_title"]) ? $admin_form_edit["email_title"] : get_bloginfo("name") . " - Formulaire de contact";
			$email_color = !empty($admin_form_edit["email_color"]) ? $admin_form_edit["email_color"] : "#0271b8";
			$email_color_text_title = !empty($admin_form_edit["email_color_text_title"]) ? $admin_form_edit["email_color_text_title"] : "#ffffff";
			$email_color_link = !empty($admin_form_edit["email_color_link"]) ? $admin_form_edit["email_color_link"] : "#0271b8";
			$email_subtitle = !empty($admin_form_edit["email_subtitle"]) ? $admin_form_edit["email_subtitle"] : "";
			$email_societe = !empty($admin_form_edit["email_societe"]) ? $admin_form_edit["email_societe"] : get_bloginfo("name");
			$email_border_radius = !empty($admin_form_edit["email_border_radius"]) ? $admin_form_edit["email_border_radius"] : 0;
			
			$email_template = !empty($admin_form_edit["email_template"]) ? $admin_form_edit["email_template"] : "defaut-infocob-crm-forms";
			$email_list_template = $this->getEmailListTemplate();
			
			$inputs_names_list = $this->getNamesFieldsForm();
			
			$email_logo = !empty($admin_form_edit["email_logo"]) ? $admin_form_edit["email_logo"] : [];
			
			$recipients_selected = $admin_form_edit["email_recipients"] ?? [];
			$recipients = get_posts([
				"numberposts" => -1,
				"post_type" => "ifb_recipients"
			]);
			
			$admin_form_config_json = get_post_meta($post->ID, 'infocob_crm_forms_admin_form_config', true);
			$admin_form_config = json_decode($admin_form_config_json, true);
			$options = get_option('infocob_crm_forms_settings');
			
			$recipients_option_enabled = filter_var(($options['recipients']['enabled'] ?? false), FILTER_VALIDATE_BOOLEAN);
			$recipients_enabled = !empty($admin_form_config["recipients_enabled"]) ? "checked" : "";
			
			$espace_clients_enabled = $options['ec']['enabled'] ?? false;
			if(!$espace_clients_enabled || empty($admin_form_config["type_formulaire"]) || (!empty($admin_form_config["type_formulaire"]) && strcasecmp($admin_form_config["type_formulaire"], "espace_clients") !== 0)) {
				// Output the field
				include ROOT_INFOCOB_CRM_FORMS_DIR_PATH . "admin/includes/admin_form_template_email_metabox.php";
			}
		}
		
		public function renderFormAdditionalEmailMetabox() {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			global $post;
			
			$this->getNamesFieldsForm();
			
			// Nonce field to validate form request came from current site
			wp_nonce_field(basename(__FILE__), 'event_fields');
			
			$admin_form_edit_json = get_post_meta($post->ID, 'infocob_crm_forms_admin_form_additional_email_config', true);
			$additional_email = json_decode($admin_form_edit_json, true);
			
			$additional_email_list_template = $this->getEmailListTemplate();
			$inputs_names_list = $this->getNamesFieldsForm();
			$email_list_template = $this->getEmailListTemplate();
			
			$admin_form_config_json = get_post_meta($post->ID, 'infocob_crm_forms_admin_form_config', true);
			$admin_form_config = json_decode($admin_form_config_json, true);
			$options = get_option('infocob_crm_forms_settings');
			$espace_clients_enabled = $options['ec']['enabled'] ?? false;
			
			$options_additional_email = isset($options['additional_email_max_number']) ? (int)$options['additional_email_max_number'] : 1;
			
			$recipients_option_enabled = filter_var(($options['recipients']['enabled'] ?? false), FILTER_VALIDATE_BOOLEAN);
			$recipients_enabled = !empty($admin_form_config["recipients_enabled"]) ? "checked" : "";
			$recipients = get_posts([
				"numberposts" => -1,
				"post_type" => "ifb_recipients"
			]);
			
			if(!is_array($additional_email)) {
				$additional_email = [];
			}
			
			if(!$espace_clients_enabled || empty($admin_form_config["type_formulaire"]) || (!empty($admin_form_config["type_formulaire"]) && strcasecmp($admin_form_config["type_formulaire"], "espace_clients") !== 0)) {
				for($nbAddEmail = 0; $nbAddEmail < $options_additional_email; $nbAddEmail++) {
					$additional_email[$nbAddEmail]["enable"] = !empty($additional_email[$nbAddEmail]["enable"]) ? $additional_email[$nbAddEmail]["enable"] : false;
					$additional_email[$nbAddEmail]["from"] = !empty($additional_email[$nbAddEmail]["from"]) ? $additional_email[$nbAddEmail]["from"] : get_bloginfo('admin_email');
					$additional_email[$nbAddEmail]["subject"] = !empty($additional_email[$nbAddEmail]["subject"]) ? $additional_email[$nbAddEmail]["subject"] : get_bloginfo('name');
					$additional_email[$nbAddEmail]["to"] = !empty($additional_email[$nbAddEmail]["to"]) ? Tools::sanitize_fields($additional_email[$nbAddEmail]["to"]) : [];
					$additional_email[$nbAddEmail]["field_to"] = !empty($additional_email[$nbAddEmail]["field_to"]) ? Tools::sanitize_fields($additional_email[$nbAddEmail]["field_to"]) : [];
					
					$additional_email[$nbAddEmail]["title"] = !empty($additional_email[$nbAddEmail]["title"]) ? $additional_email[$nbAddEmail]["title"] : get_bloginfo("name") . " - Formulaire de contact";
					$additional_email[$nbAddEmail]["color"] = !empty($additional_email[$nbAddEmail]["color"]) ? $additional_email[$nbAddEmail]["color"] : "#0271b8";
					$additional_email[$nbAddEmail]["color_text_title"] = !empty($additional_email[$nbAddEmail]["color_text_title"]) ? $additional_email[$nbAddEmail]["color_text_title"] : "#ffffff";
					$additional_email[$nbAddEmail]["color_link"] = !empty($additional_email[$nbAddEmail]["color_link"]) ? $additional_email[$nbAddEmail]["color_link"] : "#0271b8";
					$additional_email[$nbAddEmail]["subtitle"] = !empty($additional_email[$nbAddEmail]["subtitle"]) ? $additional_email[$nbAddEmail]["subtitle"] : "";
					$additional_email[$nbAddEmail]["societe"] = !empty($additional_email[$nbAddEmail]["societe"]) ? $additional_email[$nbAddEmail]["societe"] : get_bloginfo("name");
					$additional_email[$nbAddEmail]["border_radius"] = !empty($additional_email[$nbAddEmail]["border_radius"]) ? $additional_email[$nbAddEmail]["border_radius"] : 0;
					
					$additional_email[$nbAddEmail]["template"] = !empty($additional_email[$nbAddEmail]["template"]) ? $additional_email[$nbAddEmail]["template"] : "defaut-infocob-crm-forms";
					
					$additional_email[$nbAddEmail]["logo"] = !empty($additional_email[$nbAddEmail]["logo"]) ? $additional_email[$nbAddEmail]["logo"] : [];
					
					$additional_email[$nbAddEmail]["recipients"] = $additional_email[$nbAddEmail]["recipients"] ?? [];
					
					$additional_email[$nbAddEmail]["no_original_attachements"] = !empty($additional_email[$nbAddEmail]["no_original_attachements"]) ? $additional_email[$nbAddEmail]["no_original_attachements"] : false;
					$additional_email[$nbAddEmail]["attachments"] = !empty($additional_email[$nbAddEmail]["attachments"]) ? $additional_email[$nbAddEmail]["attachments"] : [];
					
					include ROOT_INFOCOB_CRM_FORMS_DIR_PATH . "admin/includes/admin_form_template_additional_email_metabox.php";
				}
			}
		}
		
		public function getEmailListTemplate() {
			$templates = [];
			if(file_exists(get_stylesheet_directory() . "/infocob-crm-forms/mails/")) {
				$files = scandir(get_stylesheet_directory() . "/infocob-crm-forms/mails/");
				
				foreach($files as $file) {
					if(!in_array($file, [".", ".."])) {
						if(preg_match("/(\.twig)$/i", $file) && preg_match("/.*(?<!_text\.twig)$/i", $file)) {
							$file = preg_replace("/(\.twig)$/i", "", $file);
							$templates[] = $file;
						}
					}
				}
			}
			
			return $templates;
		}
		
		public function getNamesFieldsForm() {
			global $post;
			$admin_form_edit_json = get_post_meta($post->ID, 'infocob_crm_forms_admin_form_config', true);
			$admin_form_edit = json_decode($admin_form_edit_json, true);
			$inputs_form = !empty($admin_form_edit["input"]) ? $admin_form_edit["input"] : [];
			
			$names = [];
			foreach($inputs_form as $input_form) {
				if(!empty($input_form["champs"])) {
					foreach($input_form["champs"] as $champ) {
						if(strcasecmp($champ["type"], "email") === 0) {
							$names["email"][] = [
								"nom" => $champ["nom"] ?? "",
								"libelle" => $champ["libelle"] ?? "",
							];
						} else {
							$names["text"][] = [
								"nom" => $champ["nom"] ?? "",
								"libelle" => $champ["libelle"] ?? "",
							];
						}
					}
				} else {
					if(strcasecmp($input_form["type"], "email") === 0) {
						$names["email"][] = [
							"nom" => $input_form["nom"] ?? "",
							"libelle" => $input_form["libelle"] ?? "",
						];
					} else {
						$names["text"][] = [
							"nom" => $input_form["nom"] ?? "",
							"libelle" => $input_form["libelle"] ?? "",
						];
					}
				}
			}
			
			return $names;
		}
	}

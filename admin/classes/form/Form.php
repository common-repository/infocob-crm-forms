<?php
	
	namespace Infocob\CrmForms\Admin;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Form {
		protected $id = 1;
		protected $fieldsGroups = [];
		
		protected $destinataires = [];
		protected $reply = [];
		protected $expediteur = "";
		protected $objet = "";
		protected $btn_send_txt = "";
		protected $input_rgpd_txt = "";
		protected $columns_base = 4;
		
		protected $type_form = '';
		protected $ec_module_telechargement = '';
		protected $recipients_enabled = false;
		
		protected $full_width = true;
		protected $disable_rgpd = false;
		
		protected $lang = false;
		
		public function __construct(int $id) {
			$this->load($id);
			if(function_exists('pll_register_string')) {
				$this->lang = pll_current_language('locale');
			}
		}
		
		public function load(int $id) {
			$this->id = $id;
			
			$admin_form_edit_json = get_post_meta($this->id, 'infocob_crm_forms_admin_form_config', true);
			$form_config = json_decode($admin_form_edit_json, true);
			
			$admin_form_edit_json = get_post_meta($this->id, 'infocob_crm_forms_admin_form_email_config', true);
			$form_config_email = json_decode($admin_form_edit_json, true);
			
			$inputs = !empty($form_config["input"]) ? $form_config["input"] : [];
			
			$options = get_option('infocob_crm_forms_settings');
			$recipients_option_enabled = filter_var(($options['recipients']['enabled'] ?? false), FILTER_VALIDATE_BOOLEAN);
			$recipients_enabled = !empty($form_config["recipients_enabled"]);
			if($recipients_option_enabled && $recipients_enabled) {
				$this->setRecipientsEnabled(true);
			}
			
			if($this->isRecipientsEnabled()) {
				$destinataires = !empty($form_config_email["email_recipients"]) ? $form_config_email["email_recipients"] : [];
				foreach($destinataires as $destinataire_post_id) {
					$post_metas = get_post_meta($destinataire_post_id, 'infocob_crm_forms_admin_recipients_config', true);
					foreach($post_metas["recipients"] as $post_meta) {
						$formatDest[$post_meta["email"]] = [
							"firstname" => $post_meta["firstname"] ?? "",
							"lastname" => $post_meta["lastname"] ?? "",
							"cc" => $post_meta["cc"] ?? "",
							"bcc" => $post_meta["bcc"] ?? "",
						];
					}
					$this->addDestinataires($formatDest);
				}
			} else {
				$destinataires = !empty($form_config_email["emails_to"]) ? $form_config_email["emails_to"] : [];
				$formatDest = [];
				foreach($destinataires as $destinataire) {
					$formatDest[$destinataire["email"]] = $destinataire["fullname"];
				}
				
				$this->addDestinataires($formatDest);
			}
			
			$objet = !empty($form_config_email["email_subject"]) ? $form_config_email["email_subject"] : "";
			$this->objet = $objet;
			
			$this->expediteur = !empty($form_config_email["email_from"]) ? sanitize_email($form_config_email["email_from"]) : "";
			$this->reply = !empty($form_config_email["email_form_reply"]) ? $form_config_email["email_form_reply"] : [];
			$this->btn_send_txt = !empty($form_config["btn_send"]) ? sanitize_text_field($form_config["btn_send"]) : "";
			$this->input_rgpd_txt = !empty($form_config["input_rgpd"]) ? wp_check_invalid_utf8($form_config["input_rgpd"]) : "";
			$this->disable_rgpd = !empty($form_config["disable_rgpd"]);
			$this->columns_base = !empty($form_config["columns_base"]) ? $form_config["columns_base"] : 4;
			
			$this->type_form = !empty($form_config["type_formulaire"]) ? $form_config["type_formulaire"] : '';
			$this->ec_module_telechargement = !empty($form_config["ec_module_telechargement"]) ? $form_config["ec_module_telechargement"] : '';
			
			$this->loadFieldsFromArray($inputs);
		}
		
		protected function loadFieldsFromArray(array $tabFields) {
			foreach($tabFields as $f) {
				$f["columns_base"] = $this->columns_base;
				$field = Field::getInstanceFromType($f["type"]);
				
				if($field instanceof FieldSelect) {
					$field->setRecipients($this->isRecipientsEnabled());
				}
				
				$field->loadFromArray($f);
				$this->fieldsGroups[] = $field;
			}
		}
		
		/**
		 * @return Field|FieldGroup|boolean
		 */
		public function getChamp($key) {
			foreach($this->fieldsGroups as $c) {
				if($c->getNom() === $key) {
					return $c;
				}
			}
			
			return false;
		}
		
		/**
		 * @return Field[]|FieldGroup
		 */
		public function getFieldsGroups(): array {
			return $this->fieldsGroups;
		}
		
		/**
		 * @return int
		 */
		public function getId(): int {
			return $this->id;
		}
		
		/**
		 * @param array $destinataires
		 */
		public function addDestinataires(array $destinataires) {
			$this->destinataires = array_merge($this->destinataires, $destinataires);
		}
		
		/**
		 * @param array $destinataires
		 */
		public function setDestinataires(array $destinataires) {
			$this->destinataires = $destinataires;
		}
		
		/**
		 * @return array
		 */
		public function getDestinataires(): array {
			return $this->destinataires;
		}
		
		/**
		 * @return string
		 */
		public function getExpediteur(): string {
			return $this->expediteur;
		}
		
		/**
		 * @return array
		 */
		public function getReply(): array {
			return $this->reply;
		}
		
		/**
		 * @return string
		 */
		public function getObjet(): string {
			return $this->objet;
		}
		
		/**
		 * @return bool
		 */
		public function isRecipientsEnabled(): bool {
			return $this->recipients_enabled;
		}
		
		/**
		 * @param bool $recipients_enabled
		 */
		public function setRecipientsEnabled(bool $recipients_enabled) {
			$this->recipients_enabled = $recipients_enabled;
		}
		
		/**
		 * @return bool
		 */
		public function isFullWidth(): bool {
			return $this->full_width;
		}
		
		/**
		 * @param bool $full_width
		 */
		public function setFullWidth(bool $full_width): void {
			$this->full_width = $full_width;
		}
		
		public function isDisableRgpd(): bool {
			return $this->disable_rgpd;
		}
		
		public function setDisableRgpd(bool $disable_rgpd): void {
			$this->disable_rgpd = $disable_rgpd;
		}
		
		public function get() {
			$vars_inputs = [];
			$type = "crm-mobile";
			if(strcasecmp($this->type_form, "espace_clients") === 0) {
				$options = get_option('infocob_crm_forms_settings');
				$api_key = $options["ec"]["api_key"] ?? false;
				$domain = $options["ec"]["domain"] ?? false;
				$ec_enabled = $options["ec"]["enabled"] ?? false;
				
				$vars_inputs = [
					"id_module" => $this->ec_module_telechargement
				];
				
				if($api_key && $domain && $ec_enabled) {
					$type = "espace-clients";
				}
			}
			
			$form = $this->get_start_form($type, $vars_inputs);
			foreach($this->fieldsGroups as $group) {
				$form .= $group->get();
			}
			$form .= $this->get_end_form();
			
			return $form;
		}
		
		protected function get_start_form($type = "crm-mobile", $vars_inputs = []) {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			$fullwidth = $this->isFullWidth() ? "if-form-fullwidth" : "";
			$title = esc_attr(get_the_title());
			$id = $this->id;
			$target = ($type === "espace-clients") ? "_blank" : "";
			
			$polylang_input = "";
			if(function_exists('pll_register_string')) {
				$polylang_input = '<input type="hidden" name="polylang_lang" value="' . $this->lang . '" />';
			}
			
			$hidden_inputs = '';
			foreach($vars_inputs as $key => $value) {
				$hidden_inputs .= '<input type="hidden" name="infocob-crm-forms-' . $key . '" value="' . $value . '" />';
			}
			
			return '
            <form method="post" action="' . esc_url($this->getAdminUrl("admin-post.php")) . '" enctype="multipart/form-data" class="infocob-crm-forms if-form ' . $fullwidth . '" target="' . $target . '">
            <input type="hidden" name="recaptcha_token" id="recaptcha-token-' . $id . '">
            <input type="hidden" name="current_url" value="' . esc_attr(get_permalink()) . '" />
            <input type="hidden" name="page_form" value="' . $title . '" />
            ' . $polylang_input . '
            <input type="hidden" name="infocob-crm-forms-id" value="' . $id . '" />
            <input type="hidden" name="infocob-crm-forms-type" value="' . $type . '" />
            <input type="hidden" name="action" value="infocob-crm-forms-action_submit_form" />
            ' . wp_nonce_field('infocob-crm-forms-action_submit_' . $id, 'infocob-crm-forms_submit_form_nonce', true, false) . '
            ' . $hidden_inputs . '
            <div class="if-dadywinnie">
                <input type="text" name="winnie" value="" />
            </div>
            <div class="if-row">
			';
		}
		
		protected function get_end_form() {
			$svg_path = get_stylesheet_directory() . "/infocob-crm-forms/img/loader.svg";
			if(file_exists($svg_path)) {
				$svg = file_get_contents($svg_path);
			} else {
				$svg = file_get_contents(ROOT_INFOCOB_CRM_FORMS_DIR_PATH . "public/assets/img/loader.svg");
			}
			
			$input_rgpd = "";
			if(!$this->isDisableRgpd()) {
				$input_rgpd = '
			<label class="if-field col-12 if-field-slide-checkbox if-casenorobot">
			    ' . $this->input_rgpd_txt . '
			</label>';
			}
			
			return $input_rgpd . '
			<div class="col-12 if-field-12-' . esc_attr($this->columns_base) . ' h-captcha"></div>
			<div class="col-12 if-btn-submit">
				<button>' . $this->btn_send_txt . '</button>
				<span class="infocob-crm-forms-ajax-loader">
				    ' . $svg . '
                </span>
			</div>
			
			</div>
			</form>
			';
		}
		
		private function getAdminUrl($path = "") {
			$admin_url = admin_url($path);
			$admin_path = parse_url(admin_url(), PHP_URL_PATH) ?? false;
			
			$domain = $_SERVER['SERVER_NAME'] ?? "";
			$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			
			if(!empty($domain) && !empty($protocol) && !empty($admin_path)) {
				$admin_url = $protocol . $domain . $admin_path . $path;
			}
			return $admin_url;
		}
		
	}

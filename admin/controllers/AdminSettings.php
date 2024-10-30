<?php
	
	namespace Infocob\CrmForms\Admin;
 
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class AdminSettings extends Controller {
		protected $options;
		
		public function __construct() {
			$this->options = get_option('infocob_crm_forms_settings');
			
			/*
			 * Webservice
			 */
			add_settings_section(
				'infocob_crm_forms_api_section',
				__('API key', 'infocob-crm-forms'),
				[$this, 'apiKeySection'],
				'infocob_crm_forms'
			);
			
			add_settings_field(
				'domain',
				__('Domain', 'infocob-crm-forms'),
				[$this, 'apiDomainField'],
				'infocob_crm_forms',
				'infocob_crm_forms_api_section'
			);
			
			add_settings_field(
				'api_key',
				__('API key', 'infocob-crm-forms'),
				[$this, 'apiKeyField'],
				'infocob_crm_forms',
				'infocob_crm_forms_api_section'
			);
			
			add_settings_field(
				'breaking_change_update',
				__('I accept updates that contains breaking changes', 'infocob-crm-forms'),
				[$this, 'breakingChangeUpdate'],
				'infocob_crm_forms',
				'infocob_crm_forms_api_section'
			);
			
			add_settings_field(
				'additional_email_max_number',
				__('Define the maximum number of additional emails', 'infocob-crm-forms'),
				[$this, 'additionalEmailMaxNumber'],
				'infocob_crm_forms',
				'infocob_crm_forms_api_section'
			);
			
			add_settings_section(
				'infocob_crm_forms_ec_section',
				__('Customers area', 'infocob-crm-forms'),
				[$this, 'ecSection'],
				'infocob_crm_forms'
			);
			
			add_settings_field(
				'ec_enabled',
				__('Enabled', 'infocob-crm-forms'),
				[$this, 'ecEnabledField'],
				'infocob_crm_forms',
				'infocob_crm_forms_ec_section'
			);
			
			add_settings_field(
				'ec_domain',
				__('Domain', 'infocob-crm-forms'),
				[$this, 'ecDomainField'],
				'infocob_crm_forms',
				'infocob_crm_forms_ec_section'
			);
			
			add_settings_field(
				'ec_api_key',
				__('API key', 'infocob-crm-forms'),
				[$this, 'ecApiKeyField'],
				'infocob_crm_forms',
				'infocob_crm_forms_ec_section'
			);
			
			/*
			 * SMTP
			 * https://github.com/PHPMailer/PHPMailer#a-simple-example
			 */
			add_settings_section(
				'infocob_crm_forms_smtp_section',
				__('SMTP', 'infocob-crm-forms'),
				[$this, 'smtp_Section'],
				'infocob_crm_forms'
			);
			
			add_settings_field(
				'google_recpatcha_v3_enable',
				__('Enabled', 'infocob-crm-forms'),
				[$this, 'smtp_enabledField'],
				'infocob_crm_forms',
				'infocob_crm_forms_smtp_section'
			);
			
			add_settings_field(
				'smtp_host',
				__('Host', 'infocob-crm-forms'),
				[$this, 'smtp_HostField'],
				'infocob_crm_forms',
				'infocob_crm_forms_smtp_section'
			);
			
			add_settings_field(
				'smtp_username',
				__('Username', 'infocob-crm-forms'),
				[$this, 'smtp_UsernameField'],
				'infocob_crm_forms',
				'infocob_crm_forms_smtp_section'
			);
			
			add_settings_field(
				'smtp_password',
				__('Password', 'infocob-crm-forms'),
				[$this, 'smtp_PasswordField'],
				'infocob_crm_forms',
				'infocob_crm_forms_smtp_section'
			);
			
			add_settings_field(
				'smtp_port',
				__('Port', 'infocob-crm-forms'),
				[$this, 'smtp_PortField'],
				'infocob_crm_forms',
				'infocob_crm_forms_smtp_section'
			);
			
			/*
			 * Google recaptcha V3
			 */
			add_settings_section(
				'infocob_crm_forms_google_recaptcha_v3_section',
				__('Google Recaptcha V3', 'infocob-crm-forms'),
				[$this, 'googleRecpatchaV3_Section'],
				'infocob_crm_forms'
			);
			
			add_settings_field(
				'google_recpatcha_v3_enable',
				__('Enabled', 'infocob-crm-forms'),
				[$this, 'googleRecpatchaV3_enabledField'],
				'infocob_crm_forms',
				'infocob_crm_forms_google_recaptcha_v3_section'
			);
			
			add_settings_field(
				'client_key',
				__('Client key', 'infocob-crm-forms'),
				[$this, 'googleRecpatchaV3_ClientKeyField'],
				'infocob_crm_forms',
				'infocob_crm_forms_google_recaptcha_v3_section'
			);
			
			add_settings_field(
				'secret_key',
				__('Secret key', 'infocob-crm-forms'),
				[$this, 'googleRecpatchaV3_SecretKeyField'],
				'infocob_crm_forms',
				'infocob_crm_forms_google_recaptcha_v3_section'
			);
			
			/*
			 * HRecaptcha
			 */
			add_settings_section(
				'infocob_crm_forms_hcaptcha_section',
				__('hCaptcha', 'infocob-crm-forms'),
				[$this, 'hCaptcha_Section'],
				'infocob_crm_forms'
			);
			
			add_settings_field(
				'hcaptcha_enable',
				__('Enabled', 'infocob-crm-forms'),
				[$this, 'hCaptcha_enabledField'],
				'infocob_crm_forms',
				'infocob_crm_forms_hcaptcha_section'
			);
			
			add_settings_field(
				'hcatpcha_client_key',
				__('Client key', 'infocob-crm-forms'),
				[$this, 'hCaptcha_ClientKeyField'],
				'infocob_crm_forms',
				'infocob_crm_forms_hcaptcha_section'
			);
			
			add_settings_field(
				'hcatpcha_secret_key',
				__('Secret key', 'infocob-crm-forms'),
				[$this, 'hCaptcha_SecretKeyField'],
				'infocob_crm_forms',
				'infocob_crm_forms_hcaptcha_section'
			);
			
			add_settings_field(
				'hcatpcha_size',
				__('Size', 'infocob-crm-forms'),
				[$this, 'hCaptcha_SizeField'],
				'infocob_crm_forms',
				'infocob_crm_forms_hcaptcha_section'
			);
			
			add_settings_field(
				'hcatpcha_theme',
				__('Theme', 'infocob-crm-forms'),
				[$this, 'hCaptcha_ThemeField'],
				'infocob_crm_forms',
				'infocob_crm_forms_hcaptcha_section'
			);
			
			/*
			 * Destinataires
			 */
			add_settings_section(
				'infocob_crm_forms_recipients_section',
				__('Recipients', 'infocob-crm-forms'),
				[$this, 'recipients_Section'],
				'infocob_crm_forms'
			);
			
			add_settings_field(
				'recipients_enable',
				__('Enabled', 'infocob-crm-forms'),
				[$this, 'recipients_enabledField'],
				'infocob_crm_forms',
				'infocob_crm_forms_recipients_section'
			);
			
			/*
			 * Configuration formulaires
			 */
			add_settings_section(
				'infocob_crm_forms_form_config_section',
				__('Form', 'infocob-crm-forms'),
				[$this, 'formConfig_Section'],
				'infocob_crm_forms'
			);
			
			add_settings_field(
				'formConfig_select2JS',
				__('Disable select2 JS', 'infocob-crm-forms'),
				[$this, 'formConfig_select2JS'],
				'infocob_crm_forms',
				'infocob_crm_forms_form_config_section'
			);
		}
		
		public function testApiConnection() {
			$webservice = new Webservice();
			$success = $webservice->test();
			
			if($success) {
				add_action('infocob_crm_forms_settings_admin_notices', function() {
					?>
                    <div class="notice notice-success">
                        <p><?php _e('Webservice connection succeed !', 'infocob-crm-forms'); ?></p>
                    </div>
					<?php
				});
			} else {
				add_action('infocob_crm_forms_settings_admin_notices', function() {
					?>
                    <div class="notice notice-error">
                        <p><?php _e('Webservice connection failed !', 'infocob-crm-forms'); ?></p>
                    </div>
					<?php
				});
			}
			
			if(isset($this->options['ec']['enabled']) && $this->options['ec']['enabled']) {
				$espaceClients = new EspaceClients();
				$success = $espaceClients->test();
                
                if($success) {
                    add_action('infocob_crm_forms_settings_admin_notices', function() {
                        ?>
                        <div class="notice notice-success">
                            <p><?php _e('Connection to customers area succeed !', 'infocob-crm-forms'); ?></p>
                        </div>
                        <?php
                    });
                } else {
                    add_action('infocob_crm_forms_settings_admin_notices', function() {
                        ?>
                        <div class="notice notice-error">
                            <p><?php _e('Connection to customers area failed !', 'infocob-crm-forms'); ?></p>
                        </div>
                        <?php
                    });
                }
            }
			
			do_action('infocob_crm_forms_settings_admin_notices');
		}
		
		public function render() {
			if(!empty(Webservice::$domain_client)) {
				$this->testApiConnection();
			}
			
			require_once plugin_dir_path(__FILE__) . '../views/settings.php';
		}
		
		/*
		 * Webservice
		 */
		public function apiKeySection() {
			echo __('This settings enable the connection to the Infocob webservice', 'infocob-crm-forms');
		}
		
		public function apiKeyField() {
			?>
            <input id="apikey" type='text' name='infocob_crm_forms_settings[api][key]' value='<?php echo $this->options['api']['key'] ?? ""; ?>'>
			<?php
		}
		
		public function apiDomainField() {
			?>
            <input id="domain" type='text' name='infocob_crm_forms_settings[api][domain]' value='<?php echo $this->options['api']['domain'] ?? ""; ?>'>
			<?php
		}
		
		public function breakingChangeUpdate() {
			?>
            <input name="infocob_crm_forms_settings[breaking_change_update]" id="breaking_change_update" type="checkbox" value="1" <?php echo (isset($this->options["breaking_change_update"]) && $this->options["breaking_change_update"] == "1") ? "checked" : ""; ?>/>
			<?php
		}
		
		public function additionalEmailMaxNumber() {
			?>
            <input name="infocob_crm_forms_settings[additional_email_max_number]" id="additional_email_max_number" type="number" value="<?php echo isset($this->options["additional_email_max_number"]) ? $this->options["additional_email_max_number"] : "1"; ?>"/>
			<?php
		}
		
		/*
		 * Espace clients
		 */
		public function ecSection() {
			echo __('This settings enable the connection to the Infocob customers area', 'infocob-crm-forms');
		}
		
		public function ecEnabledField() {
			?>
            <input id="ec-enabled" type='checkbox' name='infocob_crm_forms_settings[ec][enabled]' value='1' <?php if(!empty($this->options['ec']['enabled'])) echo "checked"; ?>>
			<?php
		}
		
		public function ecApiKeyField() {
			?>
            <input id="ec-apikey" type='text' name='infocob_crm_forms_settings[ec][api_key]' value='<?php echo $this->options['ec']['api_key'] ?? ""; ?>'>
			<?php
		}
		
		public function ecDomainField() {
			?>
            <input id="ec-domain" type='text' name='infocob_crm_forms_settings[ec][domain]' value='<?php echo $this->options['ec']['domain'] ?? ""; ?>'>
			<?php
		}
		
		/*
		 * SMTP
		 */
		public function smtp_Section() {
			echo __('Manage SMTP', 'infocob-crm-forms');
		}
		
		public function smtp_enabledField() {
			?>
			<input id="smtp-enabled" type='checkbox' name='infocob_crm_forms_settings[smtp][enabled]' value='1' <?php if(!empty($this->options['smtp']['enabled'])) echo "checked"; ?>>
			<?php
		}
		
		public function smtp_HostField() {
			?>
			<input id="smtp-host" type='text' name='infocob_crm_forms_settings[smtp][host]' value='<?php echo $this->options['smtp']['host'] ?? ""; ?>'>
			<?php
		}
		
		public function smtp_UsernameField() {
			?>
			<input id="smtp-username" type='text' name='infocob_crm_forms_settings[smtp][username]' value='<?php echo $this->options['smtp']['username'] ?? ""; ?>'>
			<?php
		}
		
		public function smtp_PasswordField() {
			?>
			<input id="smtp-password" type='password' name='infocob_crm_forms_settings[smtp][password]' value='<?php echo $this->options['smtp']['password'] ?? ""; ?>'>
			<?php
		}
		
		public function smtp_PortField() {
			?>
			<input id="smtp-password" type='text' name='infocob_crm_forms_settings[smtp][port]' value='<?php echo $this->options['smtp']['port'] ?? "465"; ?>'>
			<?php
		}
		
		/*
		 * Google recaptcha V3
		 */
		public function googleRecpatchaV3_Section() {
			echo __('Manage Google Recaptcha V3', 'infocob-crm-forms');
		}
		
		public function googleRecpatchaV3_enabledField() {
			?>
            <input id="google_recpatcha_v3-enabled" type='checkbox' name='infocob_crm_forms_settings[google_recaptcha_v3][enabled]' value='1' <?php if(!empty($this->options['google_recaptcha_v3']['enabled'])) echo "checked"; ?>>
			<?php
		}
		
		public function googleRecpatchaV3_ClientKeyField() {
			?>
            <input id="google_recpatcha_v3-client_key" type='text' name='infocob_crm_forms_settings[google_recaptcha_v3][client_key]' value='<?php echo $this->options['google_recaptcha_v3']['client_key'] ?? ""; ?>'>
			<?php
		}
		
		public function googleRecpatchaV3_SecretKeyField() {
			?>
            <input id="google_recpatcha_v3-secret_key" type='text' name='infocob_crm_forms_settings[google_recaptcha_v3][secret_key]' value='<?php echo $this->options['google_recaptcha_v3']['secret_key'] ?? ""; ?>'>
			<?php
		}
		
		/*
		 * hCaptcha
		 */
		public function hCaptcha_Section() {
			echo __('Manage hCaptcha', 'infocob-crm-forms');
			echo "<br/>";
			echo "<span style='color: red'>" . __('If invisible hCatpcha is used, don\'t forget to include the Privacy Policy and Terms of Service links', 'infocob-crm-forms') . "</span>";
			echo "<br/>";
			echo "<a href='https://docs.hcaptcha.com/faq/#do-i-need-to-display-anything-on-the-page-when-using-hcaptcha-in-invisible-mode' target='_blank' rel='noopener'>https://docs.hcaptcha.com/faq/#do-i-need-to-display-anything-on-the-page-when-using-hcaptcha-in-invisible-mode</a>";
		}
		
		public function hCaptcha_enabledField() {
			?>
			<input id="hcaptcha-enabled" type='checkbox' name='infocob_crm_forms_settings[hcaptcha][enabled]' value='1' <?php if(!empty($this->options['hcaptcha']['enabled'])) echo "checked"; ?>>
			<?php
		}
		
		public function hCaptcha_ClientKeyField() {
			?>
			<input id="hcaptcha-client_key" type='text' name='infocob_crm_forms_settings[hcaptcha][client_key]' value='<?php echo $this->options['hcaptcha']['client_key'] ?? ""; ?>'>
			<?php
		}
		
		public function hCaptcha_SecretKeyField() {
			?>
			<input id="hcaptcha-secret_key" type='text' name='infocob_crm_forms_settings[hcaptcha][secret_key]' value='<?php echo $this->options['hcaptcha']['secret_key'] ?? ""; ?>'>
			<?php
		}
		
		public function hCaptcha_SizeField() {
			?>
			<select id="hcaptcha-size" name="infocob_crm_forms_settings[hcaptcha][size]">
				<option value="" <?php echo (($this->options['hcaptcha']['size'] ?? "") === "") ? "selected" : ""; ?>><?php _e('Default', 'infocob-crm-forms'); ?></option>
				<option value="compact" <?php echo (($this->options['hcaptcha']['size'] ?? "") === "compact") ? "selected" : ""; ?>><?php _e('Compact', 'infocob-crm-forms'); ?></option>
				<option value="invisible" <?php echo (($this->options['hcaptcha']['size'] ?? "") === "invisible") ? "selected" : ""; ?>><?php _e('Invisible', 'infocob-crm-forms'); ?></option>
			</select>
			<?php
		}
		
		public function hCaptcha_ThemeField() {
			?>
			<select id="hcaptcha-theme" name="infocob_crm_forms_settings[hcaptcha][theme]">
				<option value="" <?php echo (($this->options['hcaptcha']['theme'] ?? "") === "") ? "selected" : ""; ?>><?php _e('Auto', 'infocob-crm-forms'); ?></option>
				<option value="light" <?php echo (($this->options['hcaptcha']['theme'] ?? "") === "light") ? "selected" : ""; ?>><?php _e('Light', 'infocob-crm-forms'); ?></option>
				<option value="dark" <?php echo (($this->options['hcaptcha']['theme'] ?? "") === "dark") ? "selected" : ""; ?>><?php _e('Dark', 'infocob-crm-forms'); ?></option>
			</select>
			<?php
		}
		
		/*
		 * Destinataires
		 */
		public function recipients_Section() {
			echo __('Manage recipients', 'infocob-crm-forms');
		}
		
		public function recipients_enabledField() {
			?>
            <input id="recipients-enabled" type='checkbox' name='infocob_crm_forms_settings[recipients][enabled]' value='1' <?php if(!empty($this->options['recipients']['enabled'])) echo "checked"; ?>>
			<?php
		}
		
		/*
		 * Formulaires
		 */
		public function formConfig_Section() {
			echo __('Manage global form configurations', 'infocob-crm-forms');
		}
		
		public function formConfig_select2JS() {
			?>
            <input id="select2JS-disabled" type='checkbox' name='infocob_crm_forms_settings[form_config][select2JS]' value='1' <?php if(!empty($this->options['form_config']['select2JS'])) echo "checked"; ?>>
			<?php
		}
	}

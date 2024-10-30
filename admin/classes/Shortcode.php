<?php
	
	
	namespace Infocob\CrmForms\Admin;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Shortcode {
		protected static $post_id;
		
		public static function addForm($atts = [], $content = null, $tag = '') {
			/*
			 * Load scripts and styles only if shortcode call
			 */
			global $infocob_assets_version;
			//Scripts
			// Libs
			if (!wp_script_is("infocob_crm_forms_select2_js")) {
				wp_register_script('infocob_crm_forms_select2_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/select2/js/select2.full.min.js', [], $infocob_assets_version);
			}
			
			// Google Recaptcha V3
			$options = get_option('infocob_crm_forms_settings');
			$google_recaptcha_v3_enabled = $options['google_recaptcha_v3']['enabled'] ?? false;
			$google_recaptcha_v3_client_key = $options['google_recaptcha_v3']['client_key'] ?? false;
			
			if ($google_recaptcha_v3_enabled && $google_recaptcha_v3_client_key && !wp_script_is("infocob_crm_forms_google_recaptcha_v3_js")) {
				wp_register_script('infocob_crm_forms_google_recaptcha_v3_js', "https://www.google.com/recaptcha/api.js?render=" . $google_recaptcha_v3_client_key . "");
				wp_enqueue_script('infocob_crm_forms_google_recaptcha_v3_js');
			}
			
			// hCaptcha
			$options = get_option('infocob_crm_forms_settings');
			$hcaptcha_enabled = $options['hcaptcha']['enabled'] ?? false;
			$hcaptcha_client_key = $options['hcaptcha']['client_key'] ?? false;
			$hcaptcha_size = $options['hcaptcha']['size'] ?? "";
			$hcaptcha_theme = $options['hcaptcha']['theme'] ?? "";
			
			if ($hcaptcha_enabled && $hcaptcha_client_key && !wp_script_is("infocob_crm_forms_hcaptcha_js")) {
				$lang = substr(get_bloginfo('language'), 0, 2);
				if (function_exists("pll_current_language")) {
					$lang = pll_current_language("slug");
				}
				
				wp_register_script('infocob_crm_forms_hcaptcha_js', "https://js.hcaptcha.com/1/api.js?hl=" . $lang . "&render=explicit");
				wp_enqueue_script('infocob_crm_forms_hcaptcha_js');
			}
			
			if (!wp_script_is("infocob_crm_forms_main_js")) {
				wp_register_script('infocob_crm_forms_main_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'public/assets/js/main.js', [
					'jquery',
					'infocob_crm_forms_select2_js',
					'wp-i18n'
				], $infocob_assets_version);
				
				wp_enqueue_script('infocob_crm_forms_main_js');
				wp_localize_script('infocob_crm_forms_main_js', "icf_form", [
					// Google Recaptcha V3
					'google_recaptcha_v3_enabled'    => $google_recaptcha_v3_enabled,
					'google_recaptcha_v3_client_key' => $google_recaptcha_v3_client_key,
					// hCaptcha
					'hcaptcha_enabled'               => $hcaptcha_enabled,
					'hcaptcha_client_key'            => $hcaptcha_client_key,
					'hcaptcha_size'                  => $hcaptcha_size,
					'hcaptcha_theme'                 => $hcaptcha_theme,
				]);
			}
			
			
			//Styles
			// Libs
			if (!wp_style_is("infocob_crm_forms_select2_css")) {
				wp_register_style('infocob_crm_forms_select2_css', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/select2/css/select2.min.css', [], $infocob_assets_version);
				wp_enqueue_style('infocob_crm_forms_select2_css');
			}
			
			if (!wp_style_is("infocob_crm_forms_main_css")) {
				wp_register_style('infocob_crm_forms_main_css', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'public/assets/css/main.css', [], $infocob_assets_version);
				wp_enqueue_style('infocob_crm_forms_main_css');
			}
			
			// normalize attribute keys, lowercase
			$atts = array_change_key_case((array)$atts, CASE_LOWER);
			// override default attributes with user attributes
			$infocob_atts = shortcode_atts([
				'id' => ''
			], $atts, $tag);
			
			$post_id = !empty($infocob_atts['id']) ? sanitize_text_field($infocob_atts['id']) : false;
			
			if ($post_id) {
				$admin_form_edit_json = get_post_meta($post_id, 'infocob_crm_forms_admin_form_config', true);
				$admin_form_edit = json_decode($admin_form_edit_json, true);
				$fullwidth = !empty($admin_form_edit["fullwidth"]) ? true : false;
				$disable_rgpd = !empty($admin_form_edit["disable_rgpd"]) ? true : false;
				
				$form = new Form($post_id);
				$form->setFullWidth($fullwidth);
				$form->setDisableRgpd($disable_rgpd);
				$o = $form->get();
			}
			
			return $o;
		}
		
		public function replaceShortcodes($html_form) {
			$html_form = preg_replace_callback('/\[(?:text|email|submit|groupements) .*\]/im', function ($shortcode) {
				if (!empty($shortcode[0])) {
					return do_shortcode($shortcode[0]);
				} else {
					return "";
				}
			}, $html_form);
			
			return $html_form;
		}
	}

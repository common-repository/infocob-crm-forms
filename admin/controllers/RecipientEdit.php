<?php
	
	
	namespace Infocob\CrmForms\Admin;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class RecipientEdit {
		
		public $post_id;
		public $recipient = [];
		
		
		public function renderRecipientConfig() {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			global $post;
			
			// Nonce field to validate form request came from current site
			wp_nonce_field(basename(__FILE__), 'event_fields');
			
			$post_metas = get_post_meta($post->ID, 'infocob_crm_forms_admin_recipients_config', true);
			
			$recipients = isset($post_metas["recipients"]) ? $post_metas["recipients"] : [];
			
			// Output the field
			include ROOT_INFOCOB_CRM_FORMS_DIR_PATH . "admin/includes/admin_recipients_config_metabox.php";
		}
		
	}

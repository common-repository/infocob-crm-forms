<?php
	
	
	namespace Infocob\CrmForms\Admin;
	
	use WPCF7_ContactForm;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Database {
		
		public static function getContact7Form() {
			global $wpdb;
			
			$table_name = $wpdb->prefix . 'postmeta';
			$response   = $wpdb->get_results('SELECT post_id, meta_key, meta_value FROM ' . sanitize_text_field($table_name), ARRAY_A);
			
			$formsID = array();
			foreach($response as $key => $value) {
				if($value["meta_key"] == "_form") {
					$formsID[] = sanitize_text_field($value["post_id"]);
				}
			}
			
			$data = array();
			foreach($formsID as $key => $value) {
				$data[] = WPCF7_ContactForm::get_instance(sanitize_text_field($value))->get_current();
			}
			
			return $data;
		}
		
		public function infocob_db_install() {
			global $wpdb;
			
			if($wpdb->get_var("SHOW TABLES LIKE 'infocob_ws'") == "infocob_ws") {
				$data = $wpdb->get_row("SELECT * FROM INFOCOB_WS");
				if(!empty($data->apikey) && !empty($data->url)) {
					$url    = sanitize_text_field($data->url);
					$apikey = sanitize_text_field($data->apikey);
					
					$options                  = [];
					$options["api"]["key"]    = $apikey;
					$options["api"]["domain"] = rtrim($url, '/');
					preg_match('/^(?:https:\/\/)?(.+)$/i', $options["api"]["domain"], $matches);
					if(!empty($matches[1])) {
						$options["api"]["domain"] = $matches[1];
					}
					
					update_option("infocob_crm_forms_settings", $options);
					
					$sql = "DROP TABLE INFOCOB_WS;";
					$wpdb->query($sql);
				}
			}
		}
		
		public function infocob_db_install_form() {
			global $wpdb;
			
			$charset_collate = $wpdb->get_charset_collate();
			$table_name      = 'infocob_form_cf7';
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			if($wpdb->get_var("SHOW TABLES LIKE 'infocob_form'") == "infocob_form") {
				$sql = "RENAME TABLE infocob_form TO $table_name;";
				$wpdb->query($sql);
			} else {
				$sql = "CREATE TABLE $table_name (
			        id integer NOT NULL AUTO_INCREMENT,
			        idPostContactForm integer NOT NULL,
			        fieldAssoc text,
			        pivot text,
			        maj text,
			        fichiersLies text,
			        cloudFichiers text,
			        tables text,
			        sendmail integer,
			        PRIMARY KEY  (id)
			    ) $charset_collate;";
				dbDelta($sql);
			}
			
			$table_name = 'infocob_form_ifb';
			
			$sql = "CREATE TABLE $table_name (
		        id integer NOT NULL AUTO_INCREMENT,
		        idPostContactForm integer NOT NULL,
		        fieldAssoc text,
		        pivot text,
		        maj text,
		        fichiersLies text,
		        cloudFichiers text,
		        tables text,
		        sendmail integer,
		        PRIMARY KEY  (id)
		    ) $charset_collate;";
			
			dbDelta($sql);
		}
		
		public function infocob_update_process() {
			global $infocob_db_version;
			$installed_db_version = get_option("infocob_db_version");
			
			if(empty($installed_db_version)) {
				add_option('infocob_db_version', $infocob_db_version);
			}
			
			if($installed_db_version != $infocob_db_version) {
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				
				$this->infocob_db_install();
				$this->infocob_db_install_form();
				
				update_option('infocob_db_version', $infocob_db_version);
			}
			
			load_plugin_textdomain('infocob-crm-forms', false, basename(dirname(__FILE__)) . '/languages/');
		}
		
		public static function getFormCf7FromDb($idForm) {
			global $wpdb;
			$table_name = 'infocob_form_cf7';
			
			$select = $wpdb->get_row("SELECT * FROM $table_name WHERE idPostContactForm = $idForm", ARRAY_A);
			
			if(is_null($select)) {
				return null;
			}
			
			// ########################################
			// 				Fix ut8 char
			// ########################################
			
			$select["fieldAssoc"] = htmlspecialchars_decode($select["fieldAssoc"], ENT_NOQUOTES);
			
			// ########################################
			// ########################################
			
			$id                = $select["id"];
			$fichiersLiesJson  = !empty($select["fichiersLies"]) ? $select["fichiersLies"] : "";
			$cloudFichiersJson = !empty($select["cloudFichiers"]) ? $select["cloudFichiers"] : "";
			$idPostContactForm = json_decode($select["idPostContactForm"], true);
			$fieldAssoc        = json_decode($select["fieldAssoc"], true);
			$pivot             = json_decode($select["pivot"], true);
			$maj               = json_decode($select["maj"], true);
			$fichiersLies      = json_decode($fichiersLiesJson, true);
			$cloudFichiers     = json_decode($cloudFichiersJson, true);
			$tables            = json_decode($select["tables"], true);
			
			$fieldAssoc = stripslashes_deep($fieldAssoc);
			
			return array(
				"id"                => $id,
				"idPostContactForm" => $idPostContactForm,
				"fieldAssoc"        => $fieldAssoc,
				"pivot"             => $pivot,
				"maj"               => $maj,
				"fichiersLies"      => $fichiersLies,
				"cloudFichiers"     => $cloudFichiers,
				"tables"            => $tables,
				"sendmail"          => $select['sendmail']
			);
		}
		
		public static function getFormIfbFromDb($idForm) {
			global $wpdb;
			$table_name = 'infocob_form_ifb';
			
			$select = $wpdb->get_row("SELECT * FROM $table_name WHERE idPostContactForm = $idForm", ARRAY_A);
			
			if(is_null($select)) {
				return null;
			}
			
			// ########################################
			// 				Fix ut8 char
			// ########################################
			
			$select["fieldAssoc"] = htmlspecialchars_decode($select["fieldAssoc"], ENT_NOQUOTES);
			
			// ########################################
			// ########################################
			
			$id                = $select["id"];
			$fichiersLiesJson  = !empty($select["fichiersLies"]) ? $select["fichiersLies"] : "";
			$cloudFichiersJson = !empty($select["cloudFichiers"]) ? $select["cloudFichiers"] : "";
			$idPostContactForm = json_decode($select["idPostContactForm"], true);
			$fieldAssoc        = json_decode($select["fieldAssoc"], true);
			$pivot             = json_decode($select["pivot"], true);
			$maj               = json_decode($select["maj"], true);
			$fichiersLies      = json_decode($fichiersLiesJson, true);
			$cloudFichiers     = json_decode($cloudFichiersJson, true);
			$tables            = json_decode($select["tables"], true);
			
			$fieldAssoc = stripslashes_deep($fieldAssoc);
			
			return array(
				"id"                => $id,
				"idPostContactForm" => $idPostContactForm,
				"fieldAssoc"        => $fieldAssoc,
				"pivot"             => $pivot,
				"maj"               => $maj,
				"fichiersLies"      => $fichiersLies,
				"cloudFichiers"     => $cloudFichiers,
				"tables"            => $tables,
				"sendmail"          => $select['sendmail']
			);
		}
		
		public static function deleteFormIfb($idForm) {
			global $wpdb;
			$table_name = 'infocob_form_ifb';
			
			$where = array(
				"idPostContactForm" => sanitize_text_field($idForm)
			);
			
			$delete = $wpdb->delete(sanitize_text_field($table_name), $where);
			
			return ($delete !== false) ? true : false;
		}
		
		public static function deleteFormCf7($idForm) {
			global $wpdb;
			$table_name = 'infocob_form_cf7';
			
			$where = array(
				"idPostContactForm" => sanitize_text_field($idForm)
			);
			
			$delete = $wpdb->delete(sanitize_text_field($table_name), $where);
			
			return ($delete !== false) ? true : false;
		}
		
	}

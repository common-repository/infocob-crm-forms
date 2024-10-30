<?php
	
	namespace Infocob\CrmForms\Admin;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	class Webservice {
		public static $domain_client = "";
		protected static $api_key = "";
		
		/**
		 * Webservice constructor.
		 */
		public function __construct() {
			$options = get_option('infocob_crm_forms_settings');
			
			static::$domain_client = !empty($options["api"]["domain"]) ? $options["api"]["domain"] : "";
			static::$api_key       = !empty($options["api"]["key"]) ? $options["api"]["key"] : "";
		}
		
		public function test() {
			$response = wp_remote_request("https://" . static::$domain_client . "/api/", array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			if(!empty($response)) {
				$response = json_decode(wp_remote_retrieve_body($response), true);
				if(!empty($response) && isset($response["success"])) {
					return $response["success"];
				}
			}
			
			return false;
		}
		
		public static function requestGetAjaxAPI($url) {
			new Webservice();
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $url, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key,
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
			
		}
		
		public static function requestPostAjaxAPI($url, $data) {
			new Webservice();
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $url, array(
				'method'    => 'POST',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key,
					"Content-Type"  => "application/x-www-form-urlencoded"
				),
				'body'      => $data,
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
			
		}
		
		public static function requestPutAjaxAPI($url, $data) {
			new Webservice();
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $url, array(
				'method'    => 'PUT',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key,
					"Content-Type"  => "application/x-www-form-urlencoded"
				),
				'body'      => $data,
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getRequiredFields($table = null, $field = null, $hack = false) {
			$url = "dictionnaire";
			$url .= (!is_null($table)) ? "/" . $table : "";
			$url .= (!is_null($field)) ? "/" . $field : "";
			
			$correspondances_hack = [
				"contactfiche"       => "contact",
				"interlocuteurfiche" => "interlocuteur",
				"produitfiche"       => "produit",
			];
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $url, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key,
					"Content-Type"  => "application/x-www-form-urlencoded"
				),
				'sslverify' => false
			));
			
			$arrayResponse = json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
			
			if(isset($arrayResponse["success"]) && $arrayResponse["success"] == false
			   && !$hack) {
				return $this->getRequiredFields($correspondances_hack[ $table ], $field, true);
			}
			
			if($hack) {
				$correspondances_hack = array_flip($correspondances_hack);
				$prefix               = Dico::PREFIX[ $correspondances_hack[ $table ] ];
			} else {
				$prefix = Dico::PREFIX[ $table ];
			}
			
			$datas = [];
			foreach($arrayResponse["result"] as $field) {
				if(preg_match('/^' . $prefix . '.+$/i', $field["DI_CHAMP"]) === 1 && $field["DI_REQUIRED"] === 'T') {
					$datas[ $field["DI_CHAMP"] ] = [
						"DI_DISPLAYLABEL" => $field["DI_DISPLAYLABEL"],
						"DI_CHAMP"        => $field["DI_CHAMP"]
					];
				}
			}
			ksort($datas);
			
			return $datas;
		}
		
		public function getVendeurs($v_code = null) {
			$table_name = "vendeur";
			
			$url = (is_null($v_code)) ? $table_name : $table_name . "/" . sanitize_text_field($v_code);
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $url, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getTypesAction($lta_code = null) {
			$table_name = "listetypeaction";
			
			$url = (is_null($lta_code)) ? $table_name : $table_name . "/" . sanitize_text_field($lta_code);
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $url, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getTicketStatus() {
			$table_name = "listeticketstatus";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getTicketTypes() {
			$table_name = "listetickettype";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getTicketCategories() {
			$table_name = "listeticketcategorie";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getTicketFrequences() {
			$table_name = "listeticketfrequence";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getTicketPlateformes() {
			$table_name = "listeticketplateforme";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getTicketPriorites() {
			$table_name = "listeticketpriorite";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getTicketSeverites() {
			$table_name = "listeticketseverite";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getTicketSources() {
			$table_name = "listeticketsource";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getTicketVersions() {
			$table_name = "listeticketversion";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getTicketModules() {
			$table_name = "listeticketmodule";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getContatEtats() {
			$table_name = "listecontratetat";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getContatTypes() {
			$table_name = "listecontrattype";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getContatPeriodicites() {
			$table_name = "listecontratperiodicite";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getContatFacturations() {
			$table_name = "listecontratfacturation";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getContatModesReconduction() {
			$table_name = "listecontratmodereconduction";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getGroupements($gr_code = null) {
			$table_name = "groupe";
			
			$url = (is_null($gr_code)) ? $table_name : $table_name . "/" . sanitize_text_field($gr_code);
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $url, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false
			));
			
			return json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
		}
		
		public function getTableLibelle() {
			$table_name = "dictionnaire";
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $table_name, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key
				),
				'sslverify' => false,
				'timeout' => 120
			));
			
			$results = json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
			
			$tables = array_map('strtolower', Dico::TABLES);
			
			$tablesLibelles = [];
			if(isset($results['success']) && $results['success']) {
				$correspondances_hack = [
					"contact"       => "contactfiche",
					"interlocuteur" => "interlocuteurfiche",
					"produit"       => "produitfiche",
				];
				foreach($results['result'] as $index => $values) {
					if(isset($correspondances_hack[ strtolower($values["DI_CHAMP"]) ])) {
						$values['DI_CHAMP'] = $correspondances_hack[ strtolower($values["DI_CHAMP"]) ];
					}
					
					if(in_array(strtolower($values['DI_CHAMP']), $tables)) {
						$tablesLibelles[ $values['DI_CHAMP'] ] = $values['DI_DISPLAYLABEL'];
					}
				}
			}
			
			return $tablesLibelles;
		}
		
		public function getDataTable($table_name = null, $hack = false) {
			if(!$hack && (is_null($table_name) || !in_array($table_name, Dico::TABLES))) {
				return "";
			}
			
			$correspondances_hack = [
				"contactfiche"       => "contact",
				"interlocuteurfiche" => "interlocuteur",
				"produitfiche"       => "produit",
			];
			
			$url = "dictionnaire/" . $table_name;
			
			$response = wp_remote_request("https://" . static::$domain_client . "/api/" . $url, array(
				'method'    => 'GET',
				'headers'   => array(
					"Authorization" => "Bearer " . static::$api_key,
					"Content-Type"  => "application/x-www-form-urlencoded"
				),
				'sslverify' => false
			));
			
			$arrayResponse = json_decode(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', wp_remote_retrieve_body($response)), true);
			
			if(isset($arrayResponse["success"]) && $arrayResponse["success"] == false
			   && !$hack) {
				return $this->getDataTable($correspondances_hack[ $table_name ], true);
			}
			
			if($hack) {
				$correspondances_hack = array_flip($correspondances_hack);
				$prefix               = Dico::PREFIX[ $correspondances_hack[ $table_name ] ];
			} else {
				$prefix = Dico::PREFIX[ $table_name ];
			}
			
			$datas = [];
			foreach($arrayResponse["result"] as $field) {
				if(preg_match('/^' . $prefix . '.+$/i', $field["DI_CHAMP"]) === 1) {
					$datas[ $field["DI_CHAMP"] ] = $field["DI_DISPLAYLABEL"];
				}
			}
			ksort($datas);
			
			return $datas;
		}
	}

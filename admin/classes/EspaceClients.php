<?php


	namespace Infocob\CrmForms\Admin;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	class EspaceClients {
		public static $domain_client = "";
		protected static $api_key = "";
		
		protected static $_espace_client_login;
		protected static $_espace_client_password;

		/**
		 * Webservice constructor.
		 */
		public function __construct() {
			$options = get_option('infocob_crm_forms_settings');

			static::$domain_client = !empty($options["ec"]["domain"]) ? $options["ec"]["domain"] : "";
			static::$api_key       = !empty($options["ec"]["api_key"]) ? $options["ec"]["api_key"] : "";
			
			$this->getInformationConnexion();
		}
		
		protected function getInformationConnexion() {
			$ch = curl_init();
			
			//change version pour être sur la version de démo
			curl_setopt($ch, CURLOPT_URL, self::$domain_client . "/index.php?controller=connexion-externe&parametres_connexion=1");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			//local, désactiver le https
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			
			$server_output = curl_exec($ch);
			
			curl_close($ch);
			
			if ($server_output !== false) {
				$data = json_decode($server_output);
				if (isset($data->success) && $data->success) {
					self::$_espace_client_login = $data->champ_login;
					self::$_espace_client_password = $data->champ_password;
				}
			}
		}
		
		public function test() {
			$jsonWebToken = new JsonWebToken(static::$api_key);
			$token = $jsonWebToken->encode([
				"alg" => "md5",
				"scope" => "private",
			], [
				"secret" => EspaceClients::$api_key,
				"request" => "test-connexion",
				"exp" => time() + 60,
				"iat" => time()
			]);
			
			$response = wp_remote_request("https://" . static::$domain_client . "/index.php?controller=get-info-config&token=".$token, array(
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
		
		public function getModulesTelechargement() {
			$jsonWebToken = new JsonWebToken(static::$api_key);
			$token = $jsonWebToken->encode([
				"alg" => "md5",
				"scope" => "private",
			], [
				"secret" => EspaceClients::$api_key,
				"request" => "modules-telechargements",
				"exp" => time() + 60,
				"iat" => time()
			]);
			
			$response = wp_remote_request("https://" . static::$domain_client . "/index.php?controller=get-info-config&token=".$token, array(
				'method'    => 'GET',
				'sslverify' => false
			));
			
			if(!empty($response)) {
				$response = json_decode(wp_remote_retrieve_body($response), true);
				if(!empty($response) && !empty($response["success"])) {
					return $response;
				}
			}
		}
		
		public function auth($login, $password, $id_module) {
			$jsonWebToken = new JsonWebToken(static::$api_key);
			$token = $jsonWebToken->encode([
				"alg" => "md5",
				"scope" => "user",
			], [
				"secret" => EspaceClients::$api_key,
				"request" => "get-lien-module-telechargements",
				"login" => $login,
				"password" => $password,
				"id_module" => $id_module,
				"exp" => time() + 60,
				"iat" => time()
			]);
			
			header('location: https://' . static::$domain_client . '/index.php?controller=connexion-auto&token='.$token);
			die();
		}

	}

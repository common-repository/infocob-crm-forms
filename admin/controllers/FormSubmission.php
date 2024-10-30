<?php
	
	
	namespace Infocob\CrmForms\Admin;
	
	use WPCF7_ContactForm;
	use WPCF7_Submission;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FormSubmission extends Controller {
		
		public static $polylang_lang = "";
		
		public function __construct() {
			if(!empty($_POST["polylang_lang"])) {
				static::$polylang_lang = $_POST["polylang_lang"];
			}
		}
		
		public function process_cf7() {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			if(InfocobCrmForms::check_contact_form_7_is_activated()) {
				$submission = WPCF7_Submission::get_instance();
				if($submission) {
					$formData     = $submission->get_posted_data();
					$uploadsFiles = $submission->uploaded_files();
					
					$customFormData = $formData;
					
					if(!empty($customFormData["_wpcf7"])) {
						$contactFormId = $customFormData["_wpcf7"];
					} else {
						$contact_form_full_properies = $submission->get_contact_form();
						if(!empty($contact_form_full_properies)) {
							$contactFormId = $contact_form_full_properies->id();
						}
					}
					
					unset($customFormData["_wpcf7"],
						$customFormData["_wpcf7_version"],
						$customFormData["_wpcf7_locale"],
						$customFormData["_wpcf7_unit_tag"],
						$customFormData["_wpcf7_container_post"]);
					
					$customFormData["contactformId"] = $contactFormId;
					
					$webservice = new Webservice();
					$success    = $webservice->test();
					if($success) {
						$this->register_data_db_cf7($customFormData, $uploadsFiles);
					}
				}
			}
		}
		
		public function process_ifb() {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			/*
			 * Formulaire espace clients
			 */
			$post_id = !empty($_POST["infocob-crm-forms-id"]) ? sanitize_text_field($_POST["infocob-crm-forms-id"]) : false;
			if($post_id && isset($_POST['infocob-crm-forms_submit_form_nonce']) && isset($_POST["infocob-crm-forms-type"]) && strcasecmp($_POST["infocob-crm-forms-type"], "espace-clients") === 0) {
				if(wp_verify_nonce(sanitize_text_field($_POST['infocob-crm-forms_submit_form_nonce']), 'infocob-crm-forms-action_submit_' . $post_id)) {
					$admin_form_edit_json = get_post_meta($post_id, 'infocob_crm_forms_admin_form_config', true);
					$form_config          = json_decode($admin_form_edit_json, true);
					$login_field          = (!empty($form_config["ec_connection_fields"]) && $form_config["ec_connection_fields"]["login"]) ? $form_config["ec_connection_fields"]["login"] : '';
					$password_field       = (!empty($form_config["ec_connection_fields"]) && $form_config["ec_connection_fields"]["password"]) ? $form_config["ec_connection_fields"]["password"] : '';
					
					$login     = $_POST[ $login_field ] ?? false;
					$password  = $_POST[ $password_field ] ?? false;
					$id_module = $_POST["infocob-crm-forms-id_module"] ?? false;
					
					if($login && $password && $id_module) {
						$espaceClients = new EspaceClients();
						$espaceClients->auth($login, $password, $id_module);
					}
					
					die();
				}
			}
			
			/*
			 * Formulaire classique
			 */
			$post_id = !empty($_POST["infocob-crm-forms-id"]) ? sanitize_text_field($_POST["infocob-crm-forms-id"]) : false;
			if($post_id && isset($_POST['infocob-crm-forms_submit_form_nonce']) && isset($_POST["infocob-crm-forms-type"]) && strcasecmp($_POST["infocob-crm-forms-type"], "crm-mobile") === 0) {
				if(wp_verify_nonce(sanitize_text_field($_POST['infocob-crm-forms_submit_form_nonce']), 'infocob-crm-forms-action_submit_' . $post_id)) {
					$_POST = apply_filters('infocob_forms_before_data', $_POST);
					$_FILES = apply_filters('infocob_forms_before_files', $_FILES);
					
					$uploadsFiles   = $_FILES;
					$customFormData = Tools::sanitize_fields($_POST, true);
					
					do_action('infocob_forms_before', [
						"files"     => $uploadsFiles,
						"form-data" => $customFormData,
					]);
					
					$current_url = !empty($_POST["current_url"]) ? esc_url($_POST["current_url"]) : home_url();
					
					$form       = new Form($post_id);
					$formSender = new FormSenderIfb($current_url);
					$formSender->setForm($form);
					
					$customFormData = array_merge((array) $customFormData, $formSender->extractDatasFromPost(true));
					
					add_action("infocob-crm-forms_process_form_ifb", function($vars) use ($customFormData, $uploadsFiles) {
						$webservice = new Webservice();
						$success    = $webservice->test();
						if($success) {
							$this->register_data_db_ifb($customFormData, $uploadsFiles);
						}
						
						do_action('infocob_forms_after', [
							"webservice" => $success,
							"files"      => $uploadsFiles,
							"form-data"  => $customFormData,
						]);
						
						$url = !empty($vars["url"]) ? esc_url($vars["url"]) : home_url();
						header("Location: " . $url);
						die();
					});
					
					$formSender->process();
				}
			}
		}
		
		public function register_data_db_ifb($formData, $uploadsFiles) {
			$formData = Tools::sanitize_fields($formData);
			
			$infocob_form = Database::getFormIfbFromDb($formData["infocob-crm-forms-id"]);
			
			$arrayData  = array();
			$arrayPivot = array();
			$arrayMaj   = array();
			
			if(!empty($infocob_form)) {
				
				foreach($formData as &$data) {
					if(is_array($data)) {
						$data = $data[0] ?? "";
					}
				}
				
				// CREATE ARRAY FOR CONTACT DATA
				
				$arrayData['contact']  = $this->getDataTables('contact', $infocob_form, $formData);
				$arrayPivot['contact'] = $this->getPivotTables('contact', $infocob_form, $formData);
				$arrayMaj['contact']   = $this->getMajTables('contact', $infocob_form, $formData);
				
				$arrayData['inventaire-contact']         = $this->getDataTables('inventaire-contact', $infocob_form, $formData);
				$arrayPivot['inventaire-contact']        = $this->getPivotTables('inventaire-contact', $infocob_form, $formData);
				$arrayMaj['inventaire-contact']          = $this->getMajTables('inventaire-contact', $infocob_form, $formData);
				
				// CREATE ARRAY FOR INTERLOCUTEUR DATA
				
				$arrayData['interlocuteur']  = $this->getDataTables('interlocuteur', $infocob_form, $formData);
				$arrayPivot['interlocuteur'] = $this->getPivotTables('interlocuteur', $infocob_form, $formData);
				$arrayMaj['interlocuteur']   = $this->getMajTables('interlocuteur', $infocob_form, $formData);
				
				$arrayData['inventaire-interlocuteur']         = $this->getDataTables('inventaire-interlocuteur', $infocob_form, $formData);
				$arrayPivot['inventaire-interlocuteur']        = $this->getPivotTables('inventaire-interlocuteur', $infocob_form, $formData);
				$arrayMaj['inventaire-interlocuteur']          = $this->getMajTables('inventaire-interlocuteur', $infocob_form, $formData);
				
				// CREATE ARRAY FOR AFFAIRE DATA
				
				$arrayData['affaire']         = $this->getDataTables('affaire', $infocob_form, $formData);
				$arrayPivot['affaire']        = $this->getPivotTables('affaire', $infocob_form, $formData);
				$arrayMaj['affaire']          = $this->getMajTables('affaire', $infocob_form, $formData);
				$arrayFichiersLies['affaire'] = $this->getFichiersLiesTables('affaire', $infocob_form, $formData);
				
				$arrayData['inventaire-affaire']         = $this->getDataTables('inventaire-affaire', $infocob_form, $formData);
				$arrayPivot['inventaire-affaire']        = $this->getPivotTables('inventaire-affaire', $infocob_form, $formData);
				$arrayMaj['inventaire-affaire']          = $this->getMajTables('inventaire-affaire', $infocob_form, $formData);
				
				// CREATE ARRAY FOR PRODUIT DATA
				
				$arrayData['produit']         = $this->getDataTables('produit', $infocob_form, $formData);
				$arrayPivot['produit']        = $this->getPivotTables('produit', $infocob_form, $formData);
				$arrayMaj['produit']          = $this->getMajTables('produit', $infocob_form, $formData);
				$arrayFichiersLies['produit'] = $this->getFichiersLiesTables('produit', $infocob_form, $formData);
				
				$arrayData['inventaire-produit']         = $this->getDataTables('inventaire-produit', $infocob_form, $formData);
				$arrayPivot['inventaire-produit']        = $this->getPivotTables('inventaire-produit', $infocob_form, $formData);
				$arrayMaj['inventaire-produit']          = $this->getMajTables('inventaire-produit', $infocob_form, $formData);
				
				// CREATE ARRAY FOR TICKET DATA
				
				$arrayData['ticket']         = $this->getDataTables('ticket', $infocob_form, $formData);
				$arrayPivot['ticket']        = $this->getPivotTables('ticket', $infocob_form, $formData);
				$arrayMaj['ticket']          = $this->getMajTables('ticket', $infocob_form, $formData);
				$arrayFichiersLies['ticket'] = $this->getFichiersLiesTables('ticket', $infocob_form, $formData);
				
				$arrayData['inventaire-ticket']         = $this->getDataTables('inventaire-ticket', $infocob_form, $formData);
				$arrayPivot['inventaire-ticket']        = $this->getPivotTables('inventaire-ticket', $infocob_form, $formData);
				$arrayMaj['inventaire-ticket']          = $this->getMajTables('inventaire-ticket', $infocob_form, $formData);
				
				// CREATE ARRAY FOR CONTRAT DATA
				
				$arrayData['contrat']         = $this->getDataTables('contrat', $infocob_form, $formData);
				$arrayPivot['contrat']        = $this->getPivotTables('contrat', $infocob_form, $formData);
				$arrayMaj['contrat']          = $this->getMajTables('contrat', $infocob_form, $formData);
				$arrayFichiersLies['contrat'] = $this->getFichiersLiesTables('contrat', $infocob_form, $formData);
				
				$arrayData['inventaire-contrat']         = $this->getDataTables('inventaire-contrat', $infocob_form, $formData);
				$arrayPivot['inventaire-contrat']        = $this->getPivotTables('inventaire-contrat', $infocob_form, $formData);
				$arrayMaj['inventaire-contrat']          = $this->getMajTables('inventaire-contrat', $infocob_form, $formData);
				
				// CREATE ARRAY FOR HISTORIQUE DATA
				
				$arrayData['historique']  = $this->getDataTables('historique', $infocob_form, $formData);
				$arrayPivot['historique'] = $this->getPivotTables('historique', $infocob_form, $formData);
				$arrayMaj['historique']   = $this->getMajTables('historique', $infocob_form, $formData);
				
				$arrayData['inventaire-historique']         = $this->getDataTables('inventaire-historique', $infocob_form, $formData);
				$arrayPivot['inventaire-historique']        = $this->getPivotTables('inventaire-historique', $infocob_form, $formData);
				$arrayMaj['inventaire-historique']          = $this->getMajTables('inventaire-historique', $infocob_form, $formData);
				
				// CREATE ARRAY FOR ACTION DATA
				
				$arrayData['action']         = $this->getDataTables('action', $infocob_form, $formData);
				$arrayPivot['action']        = $this->getPivotTables('action', $infocob_form, $formData);
				$arrayMaj['action']          = $this->getMajTables('action', $infocob_form, $formData);
				$arrayFichiersLies['action'] = $this->getFichiersLiesTables('action', $infocob_form, $formData);
				
				$arrayData['inventaire-action']         = $this->getDataTables('inventaire-action', $infocob_form, $formData);
				$arrayPivot['inventaire-action']        = $this->getPivotTables('inventaire-action', $infocob_form, $formData);
				$arrayMaj['inventaire-action']          = $this->getMajTables('inventaire-action', $infocob_form, $formData);
				
			}
			
			//array_walk_recursive($arrayData, "infocob_filter");
			array_walk_recursive($arrayData, [
				"Infocob\CrmForms\Admin\Tools",
				"infocob_register_data_db_filterStripSlashes"
			]);
			//array_walk_recursive($arrayData, "escapeSQL");
			
			//array_walk_recursive($arrayPivot, "infocob_filter");
			array_walk_recursive($arrayPivot, [
				"Infocob\CrmForms\Admin\Tools",
				"infocob_register_data_db_filterStripSlashes"
			]);
			//array_walk_recursive($arrayPivot, "escapeSQL");
			
			//array_walk_recursive($arrayMaj, "infocob_filter");
			array_walk_recursive($arrayMaj, [
				"Infocob\CrmForms\Admin\Tools",
				"infocob_register_data_db_filterStripSlashes"
			]);
			//array_walk_recursive($arrayMaj, "escapeSQL");
			
			// CONTACT
			$contact = null;
			
			if(isset($infocob_form['tables']['contact']) && $infocob_form['tables']['contact'] == "true") {
				$resContact = null;
				if(!empty($arrayPivot['contact']) && $arrayPivot['contact'] != "") {
					$resContact = Webservice::requestGetAjaxAPI("contactfiche?" . $this->buildRequest($arrayPivot['contact']));
					if(!empty($arrayMaj['contact']) && $arrayMaj['contact'] != "") {
						if($resContact['success']) {
							$contact_id = $resContact['result'][0]['C_CODE'];
							$contact    = Webservice::requestPutAjaxAPI("contactfiche/" . $contact_id, $this->buildRequest($arrayMaj['contact']));
						} else {
							$contact = Webservice::requestPostAjaxAPI("contactfiche", $this->buildRequest($arrayData['contact']));
						}
					} else {
						if(!$resContact["success"]) {
							$contact = Webservice::requestPostAjaxAPI("contactfiche", $this->buildRequest($arrayData['contact']));
						} else {
							$contact = [
								"success" => true,
								"result"  => [
									"key" => $resContact['result'][0]['C_CODE']
								]
							];
						}
					}
				} else {
					$contact = Webservice::requestPostAjaxAPI("contactfiche", $this->buildRequest($arrayData['contact']));
				}
				
				if(!$resContact) {
					$resContact = Webservice::requestGetAjaxAPI("contactfiche?" . $this->buildRequest($arrayPivot['contact']));
				}
			}
			
			// INTERLOCUTEUR
			if($contact !== null && isset($contact['result']['key']) && (isset($infocob_form['tables']['interlocuteur']) && $infocob_form["tables"]["interlocuteur"] == "true")) {
				$arrayData['interlocuteur']['I_CODECONTACT'] = $contact['result']['key'];
			}
			$interlocuteur = null;
			
			if(isset($infocob_form['tables']['interlocuteur']) && $infocob_form['tables']['interlocuteur'] == "true") {
				if(!empty($arrayPivot['interlocuteur']) && $arrayPivot['interlocuteur'] != "") {
					$resInterlocuteur = Webservice::requestGetAjaxAPI("interlocuteurfiche?" . $this->buildRequest($arrayPivot['interlocuteur']));
					if(!empty($arrayMaj['interlocuteur']) && $arrayMaj['interlocuteur'] != "") {
						if($resInterlocuteur['success']) {
							$interlocuteur_id = $resInterlocuteur['result'][0]['I_CODE'];
							$interlocuteur    = Webservice::requestPutAjaxAPI("interlocuteurfiche/" . $interlocuteur_id, $this->buildRequest($arrayMaj['interlocuteur']));
						} else {
							$interlocuteur = Webservice::requestPostAjaxAPI("interlocuteurfiche", $this->buildRequest($arrayData['interlocuteur']));
						}
					} else {
						if(!$resInterlocuteur["success"]) {
							$interlocuteur = Webservice::requestPostAjaxAPI("interlocuteurfiche", $this->buildRequest($arrayData['interlocuteur']));
						} else {
							$interlocuteur = [
								"success" => true,
								"result"  => [
									"key" => $resInterlocuteur['result'][0]['I_CODE']
								]
							];
						}
					}
				} else {
					$interlocuteur = Webservice::requestPostAjaxAPI("interlocuteurfiche", $this->buildRequest($arrayData['interlocuteur']));
				}
			}
			
			// AFFAIRE
			$affaire = null;
			
			if(isset($infocob_form['tables']['affaire']) && $infocob_form['tables']['affaire'] == "true") {
				if(!empty($arrayPivot['affaire']) && $arrayPivot['affaire'] != "") {
					$resAffaire = Webservice::requestGetAjaxAPI("affaire?" . $this->buildRequest($arrayPivot['affaire']));
					if(!empty($arrayMaj['affaire']) && $arrayMaj['affaire'] != "") {
						if($resAffaire['success']) {
							$affaire_id = $resAffaire['result'][0]['AF_CODE'];
							$affaire    = Webservice::requestPutAjaxAPI("affaire/" . $affaire_id, $this->buildRequest($arrayMaj['affaire']));
						} else {
							$affaire = Webservice::requestPostAjaxAPI("affaire", $this->buildRequest($arrayData['affaire']));
						}
					} else {
						if(!$resAffaire["success"]) {
							$affaire = Webservice::requestPostAjaxAPI("affaire", $this->buildRequest($arrayData['affaire']));
						} else {
							$affaire = [
								"success" => true,
								"result"  => [
									"key" => $resAffaire['result'][0]['AF_CODE']
								]
							];
						}
					}
				} else {
					$affaire = Webservice::requestPostAjaxAPI("affaire", $this->buildRequest($arrayData['affaire']));
				}
			}
			
			// PRODUIT
			$produit = null;
			
			if(isset($infocob_form['tables']['produit']) && $infocob_form['tables']['produit'] == "true") {
				if(!empty($arrayPivot['produit']) && $arrayPivot['produit'] != "") {
					$resProduit = Webservice::requestGetAjaxAPI("produitfiche?" . $this->buildRequest($arrayPivot['produit']));
					if(!empty($arrayMaj['produit']) && $arrayMaj['produit'] != "") {
						if($resProduit['success']) {
							$produit_id = $resProduit['result'][0]['P_CODE'];
							$produit    = Webservice::requestPutAjaxAPI("produitfiche/" . $produit_id, $this->buildRequest($arrayMaj['produit']));
						} else {
							$produit = Webservice::requestPostAjaxAPI("produitfiche", $this->buildRequest($arrayData['produit']));
						}
					} else {
						if(!$resProduit["success"]) {
							$produit = Webservice::requestPostAjaxAPI("produitfiche", $this->buildRequest($arrayData['produit']));
						} else {
							$produit = [
								"success" => true,
								"result"  => [
									"key" => $resProduit['result'][0]['P_CODE']
								]
							];
						}
					}
				} else {
					$produit = Webservice::requestPostAjaxAPI("produitfiche", $this->buildRequest($arrayData['produit']));
				}
			}
			
			// TICKET
			$ticket = null;
			
			if(isset($infocob_form['tables']['ticket']) && $infocob_form['tables']['ticket'] == "true") {
				if(!empty($arrayPivot['ticket']) && $arrayPivot['ticket'] != "") {
					$resTicket = Webservice::requestGetAjaxAPI("ticket?" . $this->buildRequest($arrayPivot['ticket']));
					if(!empty($arrayMaj['ticket']) && $arrayMaj['ticket'] != "") {
						if($resTicket['success']) {
							$ticket_id = $resTicket['result'][0]['TI_CODE'];
							$ticket    = Webservice::requestPutAjaxAPI("ticket/" . $ticket_id, $this->buildRequest($arrayMaj['ticket']));
						} else {
							$ticket = Webservice::requestPostAjaxAPI("ticket", $this->buildRequest($arrayData['ticket']));
						}
					} else {
						if(!$resTicket["success"]) {
							$ticket = Webservice::requestPostAjaxAPI("ticket", $this->buildRequest($arrayData['ticket']));
						} else {
							$ticket = [
								"success" => true,
								"result"  => [
									"key" => $resTicket['result'][0]['TI_CODE']
								]
							];
						}
					}
				} else {
					$ticket = Webservice::requestPostAjaxAPI("ticket", $this->buildRequest($arrayData['ticket']));
				}
			}
			
			// ADD Destinataires
			
			if(!empty($ticket) && $ticket['success'] && !empty($infocob_form['fieldAssoc']['ticket']['type_ticket']['destinataires'])) {
				$ticketCode       = $ticket['result']['key'];
				$field            = [];
				$field['TI_CODE'] = $ticketCode;
				$queryFields      = $this->buildRequest($field);
				
				$second_destinataires = $infocob_form['fieldAssoc']['ticket']['type_ticket']['destinataires'];
				
				foreach($second_destinataires as $keyDest => $valueDest) {
					$queryFields .= "&DESTINATAIRES[]=" . urlencode($valueDest);
				}
				
				Webservice::requestPutAjaxAPI("ticket/" . $ticketCode, $queryFields);
			}
			
			// CONTRAT
			$contrat = null;
			
			if(isset($infocob_form['tables']['contrat']) && $infocob_form['tables']['contrat'] == "true") {
				if(!empty($arrayPivot['contrat']) && $arrayPivot['contrat'] != "") {
					$resContrat = Webservice::requestGetAjaxAPI("contrat?" . $this->buildRequest($arrayPivot['contrat']));
					if(!empty($arrayMaj['contrat']) && $arrayMaj['contrat'] != "") {
						if($resContrat['success']) {
							$contrat_id = $resContrat['result'][0]['CT_CODE'];
							$contrat    = Webservice::requestPutAjaxAPI("contrat/" . $contrat_id, $this->buildRequest($arrayMaj['contrat']));
						} else {
							$contrat = Webservice::requestPostAjaxAPI("contrat", $this->buildRequest($arrayData['contrat']));
						}
					} else {
						if(!$resContrat["success"]) {
							$contrat = Webservice::requestPostAjaxAPI("contrat", $this->buildRequest($arrayData['contrat']));
						} else {
							$contrat = [
								"success" => true,
								"result"  => [
									"key" => $resContrat['result'][0]['CT_CODE']
								]
							];
						}
					}
				} else {
					$contrat = Webservice::requestPostAjaxAPI("contrat", $this->buildRequest($arrayData['contrat']));
				}
			}
			
			// ACTION
			
			// Récupération ID CONTACT
			
			if(!empty($contact['success']) && $contact['success'] && $infocob_form["tables"]["action"] == "true") {
				$arrayData['action']['AC_CODECONTACT']  = $contact['result']['key'];
				$arrayPivot['action']['AC_CODECONTACT'] = $contact['result']['key'];
				$arrayMaj['action']['AC_CODECONTACT']   = $contact['result']['key'];
			}
			
			if(!empty($contact["success"]) && $contact["success"]) {
				/*
				 * Inventaire
				 */
				if(isset($infocob_form['tables']['inventaire-contact']) && $infocob_form['tables']['inventaire-contact'] == "true") {
					$arrayData['inventaire-contact'] = array_merge([
						"IP_TYPEPARENT" => 102,
						"IP_CODEPRODUIT" => $contact['result']['key']
					], $arrayData["inventaire-contact"]);
					
					$arrayPivot['inventaire-contact'] = array_merge([
						"IP_TYPEPARENT" => 102,
						"IP_CODEPRODUIT" => $contact['result']['key']
					], $arrayPivot["inventaire-contact"]);
					
					if(!empty($arrayPivot['inventaire-contact']) && $arrayPivot['inventaire-contact'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-contact']));
						if(!empty($arrayMaj['inventaire-contact']) && $arrayMaj['inventaire-contact'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-contact']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-contact']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-contact']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-contact']));
					}
				}
			}
			
			// Récupération ID INTERLOCUTEUR
			
			if(!empty($interlocuteur['success']) && $interlocuteur['success'] && $infocob_form["tables"]["action"] == "true") {
				$arrayData['action']['AC_INTERLOCUTEURCONTACT']  = $interlocuteur['result']['key'];
				$arrayPivot['action']['AC_INTERLOCUTEURCONTACT'] = $interlocuteur['result']['key'];
				$arrayMaj['action']['AC_INTERLOCUTEURCONTACT']   = $interlocuteur['result']['key'];
			}
			
			if(!empty($interlocuteur["success"]) && $interlocuteur["success"]) {
				/*
				 * Inventaire
				 */
				if(isset($infocob_form['tables']['inventaire-interlocuteur']) && $infocob_form['tables']['inventaire-interlocuteur'] == "true") {
					$arrayData['inventaire-interlocuteur'] = array_merge([
						"IP_TYPEPARENT" => 108,
						"IP_CODEPRODUIT" => $interlocuteur['result']['key']
					], $arrayData["inventaire-interlocuteur"]);
					
					$arrayPivot['inventaire-interlocuteur'] = array_merge([
						"IP_TYPEPARENT" => 108,
						"IP_CODEPRODUIT" => $interlocuteur['result']['key']
					], $arrayPivot["inventaire-interlocuteur"]);
					
					if(!empty($arrayPivot['inventaire-interlocuteur']) && $arrayPivot['inventaire-interlocuteur'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-interlocuteur']));
						if(!empty($arrayMaj['inventaire-interlocuteur']) && $arrayMaj['inventaire-interlocuteur'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-interlocuteur']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-interlocuteur']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-interlocuteur']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-interlocuteur']));
					}
				}
			}
			
			// Récupération ID AFFAIRE
			
			if(!empty($affaire['success']) && $affaire['success'] && $infocob_form["tables"]["action"] == "true") {
				$arrayData['action']['AC_AFFAIRE']  = $affaire['result']['key'];
				$arrayPivot['action']['AC_AFFAIRE'] = $affaire['result']['key'];
				$arrayMaj['action']['AC_AFFAIRE']   = $affaire['result']['key'];
			}
			
			if(!empty($affaire["success"]) && $affaire["success"]) {
				/*
				 * Inventaire
				 */
				if(isset($infocob_form['tables']['inventaire-affaire']) && $infocob_form['tables']['inventaire-affaire'] == "true") {
					$arrayData['inventaire-affaire'] = array_merge([
						"IP_TYPEPARENT" => 2,
						"IP_CODEPRODUIT" => $affaire['result']['key']
					], $arrayData["inventaire-affaire"]);
					
					$arrayPivot['inventaire-affaire'] = array_merge([
						"IP_TYPEPARENT" => 2,
						"IP_CODEPRODUIT" => $affaire['result']['key']
					], $arrayPivot["inventaire-affaire"]);
					
					if(!empty($arrayPivot['inventaire-affaire']) && $arrayPivot['inventaire-affaire'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-affaire']));
						if(!empty($arrayMaj['inventaire-affaire']) && $arrayMaj['inventaire-affaire'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-affaire']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-affaire']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-affaire']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-affaire']));
					}
				}
			}
			
			// Récupération ID PRODUIT
			
			if(!empty($produit['success']) && $produit['success'] && $infocob_form["tables"]["action"] == "true") {
				$arrayData['action']['AC_INFOLIBRE1']  = $produit['result']['key'];
				$arrayPivot['action']['AC_INFOLIBRE1'] = $produit['result']['key'];
				$arrayMaj['action']['AC_INFOLIBRE1']   = $produit['result']['key'];
			}
			
			if(!empty($produit["success"]) && $produit["success"]) {
				/*
				 * Inventaire
				 */
				if(isset($infocob_form['tables']['inventaire-produit']) && $infocob_form['tables']['inventaire-produit'] == "true") {
					$arrayData['inventaire-produit'] = array_merge([
						"IP_TYPEPARENT" => 0,
						"IP_CODEPRODUIT" => $produit['result']['key']
					], $arrayData["inventaire-produit"]);
					
					$arrayPivot['inventaire-produit'] = array_merge([
						"IP_TYPEPARENT" => 0,
						"IP_CODEPRODUIT" => $produit['result']['key']
					], $arrayPivot["inventaire-produit"]);
					
					if(!empty($arrayPivot['inventaire-produit']) && $arrayPivot['inventaire-produit'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-produit']));
						if(!empty($arrayMaj['inventaire-produit']) && $arrayMaj['inventaire-produit'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-produit']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-produit']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-produit']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-produit']));
					}
				}
			}
			
			// Récupération ID TICKET
			
			if(!empty($ticket['success']) && $ticket['success'] && $infocob_form["tables"]["ticket"] == "true") {
				$arrayData['action']['AC_TICKET']  = $ticket['result']['key'];
				$arrayPivot['action']['AC_TICKET'] = $ticket['result']['key'];
				$arrayMaj['action']['AC_TICKET']   = $ticket['result']['key'];
			}
			
			if(!empty($ticket["success"]) && $ticket["success"]) {
				/*
				 * Inventaire
				 */
				if(isset($infocob_form['tables']['inventaire-ticket']) && $infocob_form['tables']['inventaire-ticket'] == "true") {
					$arrayData['inventaire-ticket'] = array_merge([
						"IP_TYPEPARENT" => 118,
						"IP_CODEPRODUIT" => $ticket['result']['key']
					], $arrayData["inventaire-ticket"]);
					
					$arrayPivot['inventaire-ticket'] = array_merge([
						"IP_TYPEPARENT" => 118,
						"IP_CODEPRODUIT" => $ticket['result']['key']
					], $arrayPivot["inventaire-ticket"]);
					
					if(!empty($arrayPivot['inventaire-ticket']) && $arrayPivot['inventaire-ticket'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-ticket']));
						if(!empty($arrayMaj['inventaire-ticket']) && $arrayMaj['inventaire-ticket'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-ticket']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-ticket']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-ticket']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-ticket']));
					}
				}
			}
			
			// Récupération ID CONTRAT
			
			if(!empty($contrat['success']) && $contrat['success'] && $infocob_form["tables"]["contrat"] == "true") {
				$arrayData['action']['AC_CODECONTRAT']  = $contrat['result']['key'];
				$arrayPivot['action']['AC_CODECONTRAT'] = $contrat['result']['key'];
				$arrayMaj['action']['AC_CODECONTRAT']   = $contrat['result']['key'];
			}
			
			if(!empty($contrat["success"]) && $contrat["success"]) {
				/*
				 * Inventaire
				 */
				if(isset($infocob_form['tables']['inventaire-contrat']) && $infocob_form['tables']['inventaire-contrat'] == "true") {
					$arrayData['inventaire-contrat'] = array_merge([
						"IP_TYPEPARENT" => 120,
						"IP_CODEPRODUIT" => $contrat['result']['key']
					], $arrayData["inventaire-contrat"]);
					
					$arrayPivot['inventaire-contrat'] = array_merge([
						"IP_TYPEPARENT" => 120,
						"IP_CODEPRODUIT" => $contrat['result']['key']
					], $arrayPivot["inventaire-contrat"]);
					
					if(!empty($arrayPivot['inventaire-contrat']) && $arrayPivot['inventaire-contrat'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-contrat']));
						if(!empty($arrayMaj['inventaire-contrat']) && $arrayMaj['inventaire-contrat'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-contrat']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-contrat']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-contrat']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-contrat']));
					}
				}
			}
			
			if(!empty($formData["infocob-crm-forms-id"])) {
				
				$form_object = new Form($formData["infocob-crm-forms-id"]);
				$formSender  = new FormSenderIfb(false);
				$formSender->setForm($form_object);
				$formSender->extractDatasFromPost();
				$tpl              = $formSender->getTemplateEmail();
				$ac_detail_format = $tpl->text();
				
				$ac_detail_format = html_entity_decode($ac_detail_format, ENT_QUOTES | ENT_HTML5);
				
				$arrayData['action']['AC_DETAIL'] = $ac_detail_format;
			}
			
			// ADD ACTION TO CRM
			
			$action = null;
			
			// !isset($infocob_form['tables']['action']) ==> compatibilite old install
			if(((isset($infocob_form['tables']['action']) && $infocob_form['tables']['action'] == "true") || !isset($infocob_form['tables']['action'])) && !empty($infocob_form)) {
				$action = Webservice::requestPostAjaxAPI("actions", $this->buildRequest($arrayData['action']));
			}
			
			// ADD Autres Destinataires
			
			if(($action['success'] ?? false) && !empty($infocob_form['fieldAssoc']['action']['type_action']['autres_destinataires'])) {
				$actionCode       = $action['result']['key'];
				$field            = [];
				$field['AC_CODE'] = $actionCode;
				$queryFields      = $this->buildRequest($field);
				
				$second_autres_destinataires = $infocob_form['fieldAssoc']['action']['type_action']['autres_destinataires'];
				
				foreach($second_autres_destinataires as $keyDest => $valueDest) {
					$queryFields .= "&AUTRES_DESTINATAIRES[]=" . urlencode($valueDest);
				}
				
				Webservice::requestPutAjaxAPI("actions/" . $actionCode, $queryFields);
			}
			
			/*
			 * Inventaire
			 */
			if($action['success'] ?? false) {
				if(isset($infocob_form['tables']['inventaire-action']) && $infocob_form['tables']['inventaire-action'] == "true") {
					$arrayData['inventaire-action'] = array_merge([
						"IP_TYPEPARENT" => 100,
						"IP_CODEPRODUIT" => $action['result']['key']
					], $arrayData["inventaire-action"]);
					
					$arrayPivot['inventaire-action'] = array_merge([
						"IP_TYPEPARENT" => 100,
						"IP_CODEPRODUIT" => $action['result']['key']
					], $arrayPivot["inventaire-action"]);
					
					if(!empty($arrayPivot['inventaire-action']) && $arrayPivot['inventaire-action'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-action']));
						if(!empty($arrayMaj['inventaire-action']) && $arrayMaj['inventaire-action'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-action']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-action']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-action']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-action']));
					}
				}
			}
			
			// ADD Fichiers lies
			
			if(!empty($action['success'])) {
				$actionCode = $action['result']['key'];
				
				$filesToUploads = [];
				if(!empty($arrayFichiersLies['action']["fichiersLies"]) || !empty($arrayFichiersLies['action']["cloudFichiers"])) {
					
					foreach($arrayFichiersLies['action']["fichiersLies"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ]) && !empty($uploadsFiles[ $shortcode ]["size"])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									if(!empty($uploadsFiles[ $shortcode ]["tmp_name"][ $i ])) {
										$filesToUploads["FICHIERS_LIES"][] = [
											"filename" => basename($uploadsFiles[$shortcode]["name"][$i]),
											"file"     => base64_encode(file_get_contents($uploadsFiles[$shortcode]["tmp_name"][$i]))
										];
									}
								}
							} else {
								if(!empty($uploadsFiles[ $shortcode ]["tmp_name"])) {
									$filesToUploads["FICHIERS_LIES"][] = [
										"filename" => basename($uploadsFiles[$shortcode]["name"]),
										"file"     => base64_encode(file_get_contents($uploadsFiles[$shortcode]["tmp_name"]))
									];
								}
							}
						}
					}
					
					foreach($arrayFichiersLies['action']["cloudFichiers"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ]) && !empty($uploadsFiles[ $shortcode ]["size"])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									if(!empty($uploadsFiles[ $shortcode ]["tmp_name"][ $i ])) {
										Webservice::requestPostAjaxAPI("cloudfichier", [
											"file_data"   => base64_encode(file_get_contents($uploadsFiles[$shortcode]["tmp_name"][$i])),
											"file_name"   => basename($uploadsFiles[$shortcode]["name"][$i]),
											"code_module" => $actionCode,
											"module"      => "action"
										]);
									}
								}
							} else {
								if(!empty($uploadsFiles[ $shortcode ]["tmp_name"])) {
									Webservice::requestPostAjaxAPI("cloudfichier", [
										"file_data"   => base64_encode(file_get_contents($uploadsFiles[$shortcode]["tmp_name"])),
										"file_name"   => basename($uploadsFiles[$shortcode]["name"]),
										"code_module" => $actionCode,
										"module"      => "action"
									]);
								}
							}
						}
					}
					
					Webservice::requestPutAjaxAPI("actions/" . $actionCode, $filesToUploads);
				}
			}
			
			if(!empty($affaire['success'])) {
				$affaireCode = $affaire['result']['key'];
				
				$filesToUploads = [];
				if(!empty($arrayFichiersLies['affaire']["fichiersLies"]) || !empty($arrayFichiersLies['affaire']["cloudFichiers"])) {
					
					foreach($arrayFichiersLies['affaire']["fichiersLies"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									$filesToUploads["FICHIERS_LIES"][] = [
										"filename" => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ]))
									];
								}
							} else {
								$filesToUploads["FICHIERS_LIES"][] = [
									"filename" => basename($uploadsFiles[ $shortcode ]["name"]),
									"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"]))
								];
							}
						}
					}
					
					foreach($arrayFichiersLies['affaire']["cloudFichiers"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									Webservice::requestPostAjaxAPI("cloudfichier", [
										"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ])),
										"file_name"   => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"code_module" => $affaireCode,
										"module"      => "affaire"
									]);
								}
							} else {
								Webservice::requestPostAjaxAPI("cloudfichier", [
									"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"])),
									"file_name"   => basename($uploadsFiles[ $shortcode ]["name"]),
									"code_module" => $affaireCode,
									"module"      => "affaire"
								]);
							}
						}
					}
					
					Webservice::requestPutAjaxAPI("affaire/" . $affaireCode, $filesToUploads);
				}
			}
			
			if(!empty($produit['success'])) {
				$produitCode = $produit['result']['key'];
				
				$filesToUploads = [];
				if(!empty($arrayFichiersLies['produit']["fichiersLies"]) || !empty($arrayFichiersLies['produit']["cloudFichiers"])) {
					
					foreach($arrayFichiersLies['produit']["fichiersLies"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									$filesToUploads["FICHIERS_LIES"][] = [
										"filename" => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ]))
									];
								}
							} else {
								$filesToUploads["FICHIERS_LIES"][] = [
									"filename" => basename($uploadsFiles[ $shortcode ]["name"]),
									"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"]))
								];
							}
						}
					}
					
					foreach($arrayFichiersLies['produit']["cloudFichiers"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									Webservice::requestPostAjaxAPI("cloudfichier", [
										"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ])),
										"file_name"   => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"code_module" => $produitCode,
										"module"      => "produit"
									]);
								}
							} else {
								Webservice::requestPostAjaxAPI("cloudfichier", [
									"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"])),
									"file_name"   => basename($uploadsFiles[ $shortcode ]["name"]),
									"code_module" => $produitCode,
									"module"      => "produit"
								]);
							}
						}
					}
					
					Webservice::requestPutAjaxAPI("produitfiche/" . $produitCode, $filesToUploads);
				}
			}
			
			if(!empty($ticket['success'])) {
				$ticketCode = $ticket['result']['key'];
				
				$filesToUploads = [];
				if(!empty($arrayFichiersLies['ticket']["fichiersLies"]) || !empty($arrayFichiersLies['ticket']["cloudFichiers"])) {
					
					foreach($arrayFichiersLies['ticket']["fichiersLies"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									$filesToUploads["FICHIERS_LIES"][] = [
										"filename" => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ]))
									];
								}
							} else {
								$filesToUploads["FICHIERS_LIES"][] = [
									"filename" => basename($uploadsFiles[ $shortcode ]["name"]),
									"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"]))
								];
							}
						}
					}
					
					foreach($arrayFichiersLies['ticket']["cloudFichiers"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									Webservice::requestPostAjaxAPI("cloudfichier", [
										"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ])),
										"file_name"   => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"code_module" => $ticketCode,
										"module"      => "ticket"
									]);
								}
							} else {
								Webservice::requestPostAjaxAPI("cloudfichier", [
									"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"])),
									"file_name"   => basename($uploadsFiles[ $shortcode ]["name"]),
									"code_module" => $ticketCode,
									"module"      => "ticket"
								]);
							}
						}
					}
					
					Webservice::requestPutAjaxAPI("ticket/" . $ticketCode, $filesToUploads);
				}
			}
			
			if(!empty($contrat['success'])) {
				$contratCode = $contrat['result']['key'];
				
				$filesToUploads = [];
				if(!empty($arrayFichiersLies['contrat']["fichiersLies"]) || !empty($arrayFichiersLies['contrat']["cloudFichiers"])) {
					
					foreach($arrayFichiersLies['contrat']["fichiersLies"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									$filesToUploads["FICHIERS_LIES"][] = [
										"filename" => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ]))
									];
								}
							} else {
								$filesToUploads["FICHIERS_LIES"][] = [
									"filename" => basename($uploadsFiles[ $shortcode ]["name"]),
									"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"]))
								];
							}
						}
					}
					
					foreach($arrayFichiersLies['contrat']["cloudFichiers"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									Webservice::requestPostAjaxAPI("cloudfichier", [
										"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ])),
										"file_name"   => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"code_module" => $contratCode,
										"module"      => "contrat"
									]);
								}
							} else {
								Webservice::requestPostAjaxAPI("cloudfichier", [
									"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"])),
									"file_name"   => basename($uploadsFiles[ $shortcode ]["name"]),
									"code_module" => $contratCode,
									"module"      => "contrat"
								]);
							}
						}
					}
					
					Webservice::requestPutAjaxAPI("contrat/" . $contratCode, $filesToUploads);
				}
			}
			
			do_action("infocob-crm-forms-after-infocob", [
				"ac_code" => $action["result"]["key"] ?? false,
				"c_code" => $contact["result"]["key"] ?? false,
				"i_code" => $interlocuteur["result"]["key"] ?? false,
				"p_code" => $produit["result"]["key"] ?? false,
				"af_code" => $affaire["result"]["key"] ?? false,
				"ct_code" => $contrat["result"]["key"] ?? false,
				"ti_code" => $ticket["result"]["key"] ?? false,
				"form-data" => $formData,
				"files" => $uploadsFiles,
			]);
		}
		
		public function register_data_db_cf7($formData, $uploadsFiles) {
			$formData = Tools::sanitize_fields($formData);
			
			$infocob_form = Database::getFormCf7FromDb($formData["contactformId"]);
			if(empty($infocob_form)) {
				return null;
			}
			
			$cf7 = WPCF7_ContactForm::get_instance($infocob_form["idPostContactForm"]);
			
			$arrayData  = array();
			$arrayPivot = array();
			$arrayMaj   = array();
			
			if(!empty($infocob_form)) {
				
				foreach($formData as &$data) {
					if(is_array($data)) {
						$data = $data[0] ?? "";
					}
				}
				
				// CREATE ARRAY FOR CONTACT DATA
				
				$arrayData['contact']  = $this->getDataTables('contact', $infocob_form, $formData);
				$arrayPivot['contact'] = $this->getPivotTables('contact', $infocob_form, $formData);
				$arrayMaj['contact']   = $this->getMajTables('contact', $infocob_form, $formData);
				
				$arrayData['inventaire-contact']         = $this->getDataTables('inventaire-contact', $infocob_form, $formData);
				$arrayPivot['inventaire-contact']        = $this->getPivotTables('inventaire-contact', $infocob_form, $formData);
				$arrayMaj['inventaire-contact']          = $this->getMajTables('inventaire-contact', $infocob_form, $formData);
				
				// CREATE ARRAY FOR INTERLOCUTEUR DATA
				
				$arrayData['interlocuteur']  = $this->getDataTables('interlocuteur', $infocob_form, $formData);
				$arrayPivot['interlocuteur'] = $this->getPivotTables('interlocuteur', $infocob_form, $formData);
				$arrayMaj['interlocuteur']   = $this->getMajTables('interlocuteur', $infocob_form, $formData);
				
				// CREATE ARRAY FOR AFFAIRE DATA
				
				$arrayData['affaire']         = $this->getDataTables('affaire', $infocob_form, $formData);
				$arrayPivot['affaire']        = $this->getPivotTables('affaire', $infocob_form, $formData);
				$arrayMaj['affaire']          = $this->getMajTables('affaire', $infocob_form, $formData);
				$arrayFichiersLies['affaire'] = $this->getFichiersLiesTables('affaire', $infocob_form, $formData);
				
				$arrayData['inventaire-affaire']         = $this->getDataTables('inventaire-affaire', $infocob_form, $formData);
				$arrayPivot['inventaire-affaire']        = $this->getPivotTables('inventaire-affaire', $infocob_form, $formData);
				$arrayMaj['inventaire-affaire']          = $this->getMajTables('inventaire-affaire', $infocob_form, $formData);
				
				// CREATE ARRAY FOR PRODUIT DATA
				
				$arrayData['produit']         = $this->getDataTables('produit', $infocob_form, $formData);
				$arrayPivot['produit']        = $this->getPivotTables('produit', $infocob_form, $formData);
				$arrayMaj['produit']          = $this->getMajTables('produit', $infocob_form, $formData);
				$arrayFichiersLies['produit'] = $this->getFichiersLiesTables('produit', $infocob_form, $formData);
				
				$arrayData['inventaire-produit']         = $this->getDataTables('inventaire-produit', $infocob_form, $formData);
				$arrayPivot['inventaire-produit']        = $this->getPivotTables('inventaire-produit', $infocob_form, $formData);
				$arrayMaj['inventaire-produit']          = $this->getMajTables('inventaire-produit', $infocob_form, $formData);
				
				// CREATE ARRAY FOR TICKET DATA
				
				$arrayData['ticket']         = $this->getDataTables('ticket', $infocob_form, $formData);
				$arrayPivot['ticket']        = $this->getPivotTables('ticket', $infocob_form, $formData);
				$arrayMaj['ticket']          = $this->getMajTables('ticket', $infocob_form, $formData);
				$arrayFichiersLies['ticket'] = $this->getFichiersLiesTables('ticket', $infocob_form, $formData);
				
				$arrayData['inventaire-ticket']         = $this->getDataTables('inventaire-ticket', $infocob_form, $formData);
				$arrayPivot['inventaire-ticket']        = $this->getPivotTables('inventaire-ticket', $infocob_form, $formData);
				$arrayMaj['inventaire-ticket']          = $this->getMajTables('inventaire-ticket', $infocob_form, $formData);
				
				// CREATE ARRAY FOR CONTRAT DATA
				
				$arrayData['contrat']         = $this->getDataTables('contrat', $infocob_form, $formData);
				$arrayPivot['contrat']        = $this->getPivotTables('contrat', $infocob_form, $formData);
				$arrayMaj['contrat']          = $this->getMajTables('contrat', $infocob_form, $formData);
				$arrayFichiersLies['contrat'] = $this->getFichiersLiesTables('contrat', $infocob_form, $formData);
				
				$arrayData['inventaire-contrat']         = $this->getDataTables('inventaire-contrat', $infocob_form, $formData);
				$arrayPivot['inventaire-contrat']        = $this->getPivotTables('inventaire-contrat', $infocob_form, $formData);
				$arrayMaj['inventaire-contrat']          = $this->getMajTables('inventaire-contrat', $infocob_form, $formData);
				
				// CREATE ARRAY FOR HISTORIQUE DATA
				
				$arrayData['historique']  = $this->getDataTables('historique', $infocob_form, $formData);
				$arrayPivot['historique'] = $this->getPivotTables('historique', $infocob_form, $formData);
				$arrayMaj['historique']   = $this->getMajTables('historique', $infocob_form, $formData);
				
				$arrayData['inventaire-historique']         = $this->getDataTables('inventaire-historique', $infocob_form, $formData);
				$arrayPivot['inventaire-historique']        = $this->getPivotTables('inventaire-historique', $infocob_form, $formData);
				$arrayMaj['inventaire-historique']          = $this->getMajTables('inventaire-historique', $infocob_form, $formData);
				
				// CREATE ARRAY FOR ACTION DATA
				
				$arrayData['action']         = $this->getDataTables('action', $infocob_form, $formData);
				$arrayPivot['action']        = $this->getPivotTables('action', $infocob_form, $formData);
				$arrayMaj['action']          = $this->getMajTables('action', $infocob_form, $formData);
				$arrayFichiersLies['action'] = $this->getFichiersLiesTables('action', $infocob_form, $formData);
				
				$arrayData['inventaire-action']         = $this->getDataTables('inventaire-action', $infocob_form, $formData);
				$arrayPivot['inventaire-action']        = $this->getPivotTables('inventaire-action', $infocob_form, $formData);
				$arrayMaj['inventaire-action']          = $this->getMajTables('inventaire-action', $infocob_form, $formData);
				
			}
			
			//array_walk_recursive($arrayData, "infocob_filter");
			array_walk_recursive($arrayData, [
				"Infocob\CrmForms\Admin\Tools",
				"infocob_register_data_db_filterStripSlashes"
			]);
			//array_walk_recursive($arrayData, "escapeSQL");
			
			//array_walk_recursive($arrayPivot, "infocob_filter");
			array_walk_recursive($arrayPivot, [
				"Infocob\CrmForms\Admin\Tools",
				"infocob_register_data_db_filterStripSlashes"
			]);
			//array_walk_recursive($arrayPivot, "escapeSQL");
			
			//array_walk_recursive($arrayMaj, "infocob_filter");
			array_walk_recursive($arrayMaj, [
				"Infocob\CrmForms\Admin\Tools",
				"infocob_register_data_db_filterStripSlashes"
			]);
			//array_walk_recursive($arrayMaj, "escapeSQL");
			
			// CONTACT
			$contact = null;
			
			if(isset($infocob_form['tables']['contact']) && $infocob_form['tables']['contact'] == "true") {
				$resContact = null;
				if(!empty($arrayPivot['contact']) && $arrayPivot['contact'] != "") {
					$resContact = Webservice::requestGetAjaxAPI("contactfiche?" . $this->buildRequest($arrayPivot['contact']));
					if(!empty($arrayMaj['contact']) && $arrayMaj['contact'] != "") {
						if($resContact['success']) {
							$contact_id = $resContact['result'][0]['C_CODE'];
							$contact    = Webservice::requestPutAjaxAPI("contactfiche/" . $contact_id, $this->buildRequest($arrayMaj['contact']));
						} else {
							$contact = Webservice::requestPostAjaxAPI("contactfiche", $this->buildRequest($arrayData['contact']));
						}
					} else {
						if(!$resContact["success"]) {
							$contact = Webservice::requestPostAjaxAPI("contactfiche", $this->buildRequest($arrayData['contact']));
						} else {
							$contact = [
								"success" => true,
								"result"  => [
									"key" => $resContact['result'][0]['C_CODE']
								]
							];
						}
					}
				} else {
					$contact = Webservice::requestPostAjaxAPI("contactfiche", $this->buildRequest($arrayData['contact']));
				}
				
				if(!$resContact) {
					$resContact = Webservice::requestGetAjaxAPI("contactfiche?" . $this->buildRequest($arrayPivot['contact']));
				}
			}
			
			// INTERLOCUTEUR
			if($contact !== null && (isset($infocob_form['tables']['interlocuteur']) && $infocob_form["tables"]["interlocuteur"] == "true")) {
				$arrayData['interlocuteur']['I_CODECONTACT'] = $contact['result']['key'];
			}
			$interlocuteur = null;
			
			if(isset($infocob_form['tables']['interlocuteur']) && $infocob_form['tables']['interlocuteur'] == "true") {
				if(!empty($arrayPivot['interlocuteur']) && $arrayPivot['interlocuteur'] != "") {
					$resInterlocuteur = Webservice::requestGetAjaxAPI("interlocuteurfiche?" . $this->buildRequest($arrayPivot['interlocuteur']));
					if(!empty($arrayMaj['interlocuteur']) && $arrayMaj['interlocuteur'] != "") {
						if($resInterlocuteur['success']) {
							$interlocuteur_id = $resInterlocuteur['result'][0]['I_CODE'];
							$interlocuteur    = Webservice::requestPutAjaxAPI("interlocuteurfiche/" . $interlocuteur_id, $this->buildRequest($arrayMaj['interlocuteur']));
						} else {
							$interlocuteur = Webservice::requestPostAjaxAPI("interlocuteurfiche", $this->buildRequest($arrayData['interlocuteur']));
						}
					} else {
						if(!$resInterlocuteur["success"]) {
							$interlocuteur = Webservice::requestPostAjaxAPI("interlocuteurfiche", $this->buildRequest($arrayData['interlocuteur']));
						} else {
							$interlocuteur = [
								"success" => true,
								"result"  => [
									"key" => $resInterlocuteur['result'][0]['I_CODE']
								]
							];
						}
					}
				} else {
					$interlocuteur = Webservice::requestPostAjaxAPI("interlocuteurfiche", $this->buildRequest($arrayData['interlocuteur']));
				}
			}
			
			// AFFAIRE
			$affaire = null;
			
			if(isset($infocob_form['tables']['affaire']) && $infocob_form['tables']['affaire'] == "true") {
				if(!empty($arrayPivot['affaire']) && $arrayPivot['affaire'] != "") {
					$resAffaire = Webservice::requestGetAjaxAPI("affaire?" . $this->buildRequest($arrayPivot['affaire']));
					if(!empty($arrayMaj['affaire']) && $arrayMaj['affaire'] != "") {
						if($resAffaire['success']) {
							$affaire_id = $resAffaire['result'][0]['AF_CODE'];
							$affaire    = Webservice::requestPutAjaxAPI("affaire/" . $affaire_id, $this->buildRequest($arrayMaj['affaire']));
						} else {
							$affaire = Webservice::requestPostAjaxAPI("affaire", $this->buildRequest($arrayData['affaire']));
						}
					} else {
						if(!$resAffaire["success"]) {
							$affaire = Webservice::requestPostAjaxAPI("affaire", $this->buildRequest($arrayData['affaire']));
						} else {
							$affaire = [
								"success" => true,
								"result"  => [
									"key" => $resAffaire['result'][0]['AF_CODE']
								]
							];
						}
					}
				} else {
					$affaire = Webservice::requestPostAjaxAPI("affaire", $this->buildRequest($arrayData['affaire']));
				}
			}
			
			// PRODUIT
			$produit = null;
			
			if(isset($infocob_form['tables']['produit']) && $infocob_form['tables']['produit'] == "true") {
				if(!empty($arrayPivot['produit']) && $arrayPivot['produit'] != "") {
					$resProduit = Webservice::requestGetAjaxAPI("produitfiche?" . $this->buildRequest($arrayPivot['produit']));
					if(!empty($arrayMaj['produit']) && $arrayMaj['produit'] != "") {
						if($resProduit['success']) {
							$produit_id = $resProduit['result'][0]['P_CODE'];
							$produit    = Webservice::requestPutAjaxAPI("produitfiche/" . $produit_id, $this->buildRequest($arrayMaj['produit']));
						} else {
							$produit = Webservice::requestPostAjaxAPI("produitfiche", $this->buildRequest($arrayData['produit']));
						}
					} else {
						if(!$resProduit["success"]) {
							$produit = Webservice::requestPostAjaxAPI("produitfiche", $this->buildRequest($arrayData['produit']));
						} else {
							$produit = [
								"success" => true,
								"result"  => [
									"key" => $resProduit['result'][0]['P_CODE']
								]
							];
						}
					}
				} else {
					$produit = Webservice::requestPostAjaxAPI("produitfiche", $this->buildRequest($arrayData['produit']));
				}
			}
			
			// TICKET
			$ticket = null;
			
			if(isset($infocob_form['tables']['ticket']) && $infocob_form['tables']['ticket'] == "true") {
				if(!empty($arrayPivot['ticket']) && $arrayPivot['ticket'] != "") {
					$resTicket = Webservice::requestGetAjaxAPI("ticket?" . $this->buildRequest($arrayPivot['ticket']));
					if(!empty($arrayMaj['ticket']) && $arrayMaj['ticket'] != "") {
						if($resTicket['success']) {
							$ticket_id = $resTicket['result'][0]['TI_CODE'];
							$ticket    = Webservice::requestPutAjaxAPI("ticket/" . $ticket_id, $this->buildRequest($arrayMaj['ticket']));
						} else {
							$ticket = Webservice::requestPostAjaxAPI("ticket", $this->buildRequest($arrayData['ticket']));
						}
					} else {
						if(!$resTicket["success"]) {
							$ticket = Webservice::requestPostAjaxAPI("ticket", $this->buildRequest($arrayData['ticket']));
						} else {
							$ticket = [
								"success" => true,
								"result"  => [
									"key" => $resTicket['result'][0]['TI_CODE']
								]
							];
						}
					}
				} else {
					$ticket = Webservice::requestPostAjaxAPI("ticket", $this->buildRequest($arrayData['ticket']));
				}
			}
			
			// ADD Destinataires
			
			if(!empty($ticket) && $ticket['success'] && !empty($infocob_form['fieldAssoc']['ticket']['type_ticket']['destinataires'])) {
				$ticketCode       = $ticket['result']['key'];
				$field            = [];
				$field['TI_CODE'] = $ticketCode;
				$queryFields      = $this->buildRequest($field);
				
				$second_destinataires = $infocob_form['fieldAssoc']['ticket']['type_ticket']['destinataires'];
				
				foreach($second_destinataires as $keyDest => $valueDest) {
					$queryFields .= "&DESTINATAIRES[]=" . urlencode($valueDest);
				}
				
				Webservice::requestPutAjaxAPI("ticket/" . $ticketCode, $queryFields);
			}
			
			// CONTRAT
			$contrat = null;
			
			if(isset($infocob_form['tables']['contrat']) && $infocob_form['tables']['contrat'] == "true") {
				if(!empty($arrayPivot['contrat']) && $arrayPivot['contrat'] != "") {
					$resContrat = Webservice::requestGetAjaxAPI("contrat?" . $this->buildRequest($arrayPivot['contrat']));
					if(!empty($arrayMaj['contrat']) && $arrayMaj['contrat'] != "") {
						if($resContrat['success']) {
							$contrat_id = $resContrat['result'][0]['CT_CODE'];
							$contrat    = Webservice::requestPutAjaxAPI("contrat/" . $contrat_id, $this->buildRequest($arrayMaj['contrat']));
						} else {
							$contrat = Webservice::requestPostAjaxAPI("contrat", $this->buildRequest($arrayData['contrat']));
						}
					} else {
						if(!$resContrat["success"]) {
							$contrat = Webservice::requestPostAjaxAPI("contrat", $this->buildRequest($arrayData['contrat']));
						} else {
							$contrat = [
								"success" => true,
								"result"  => [
									"key" => $resContrat['result'][0]['CT_CODE']
								]
							];
						}
					}
				} else {
					$contrat = Webservice::requestPostAjaxAPI("contrat", $this->buildRequest($arrayData['contrat']));
				}
			}
			
			// ACTION
			
			// Récupération ID CONTACT
			
			if(!empty($contact['success']) && $contact['success'] && $infocob_form["tables"]["action"] == "true") {
				$arrayData['action']['AC_CODECONTACT']  = $contact['result']['key'];
				$arrayPivot['action']['AC_CODECONTACT'] = $contact['result']['key'];
				$arrayMaj['action']['AC_CODECONTACT']   = $contact['result']['key'];
				
			}
			
			if(!empty($contact["success"]) && $contact["success"]) {
				/*
				 * Inventaire
				 */
				if(isset($infocob_form['tables']['inventaire-contact']) && $infocob_form['tables']['inventaire-contact'] == "true") {
					$arrayData['inventaire-contact'] = array_merge([
						"IP_TYPEPARENT" => 102,
						"IP_CODEPRODUIT" => $contact['result']['key']
					], $arrayData["inventaire-contact"]);
					
					$arrayPivot['inventaire-contact'] = array_merge([
						"IP_TYPEPARENT" => 102,
						"IP_CODEPRODUIT" => $contact['result']['key']
					], $arrayPivot["inventaire-contact"]);
					
					if(!empty($arrayPivot['inventaire-contact']) && $arrayPivot['inventaire-contact'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-contact']));
						if(!empty($arrayMaj['inventaire-contact']) && $arrayMaj['inventaire-contact'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-contact']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-contact']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-contact']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-contact']));
					}
				}
			}
			
			// Récupération ID INTERLOCUTEUR
			
			if(!empty($interlocuteur['success']) && $interlocuteur['success'] && $infocob_form["tables"]["action"] == "true") {
				$arrayData['action']['AC_INTERLOCUTEURCONTACT']  = $interlocuteur['result']['key'];
				$arrayPivot['action']['AC_INTERLOCUTEURCONTACT'] = $interlocuteur['result']['key'];
				$arrayMaj['action']['AC_INTERLOCUTEURCONTACT']   = $interlocuteur['result']['key'];
			}
			
			if(!empty($interlocuteur["success"]) && $interlocuteur["success"]) {
				/*
				 * Inventaire
				 */
				if(isset($infocob_form['tables']['inventaire-interlocuteur']) && $infocob_form['tables']['inventaire-interlocuteur'] == "true") {
					$arrayData['inventaire-interlocuteur'] = array_merge([
						"IP_TYPEPARENT" => 108,
						"IP_CODEPRODUIT" => $interlocuteur['result']['key']
					], $arrayData["inventaire-interlocuteur"]);
					
					$arrayPivot['inventaire-interlocuteur'] = array_merge([
						"IP_TYPEPARENT" => 108,
						"IP_CODEPRODUIT" => $interlocuteur['result']['key']
					], $arrayPivot["inventaire-interlocuteur"]);
					
					if(!empty($arrayPivot['inventaire-interlocuteur']) && $arrayPivot['inventaire-interlocuteur'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-interlocuteur']));
						if(!empty($arrayMaj['inventaire-interlocuteur']) && $arrayMaj['inventaire-interlocuteur'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-interlocuteur']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-interlocuteur']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-interlocuteur']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-interlocuteur']));
					}
				}
			}
			
			// Récupération ID AFFAIRE
			
			if(!empty($affaire['success']) && $affaire['success'] && $infocob_form["tables"]["action"] == "true") {
				$arrayData['action']['AC_AFFAIRE']  = $affaire['result']['key'];
				$arrayPivot['action']['AC_AFFAIRE'] = $affaire['result']['key'];
				$arrayMaj['action']['AC_AFFAIRE']   = $affaire['result']['key'];
			}
			
			if(!empty($affaire["success"]) && $affaire["success"]) {
				/*
				 * Inventaire
				 */
				if(isset($infocob_form['tables']['inventaire-affaire']) && $infocob_form['tables']['inventaire-affaire'] == "true") {
					$arrayData['inventaire-affaire'] = array_merge([
						"IP_TYPEPARENT" => 2,
						"IP_CODEPRODUIT" => $affaire['result']['key']
					], $arrayData["inventaire-affaire"]);
					
					$arrayPivot['inventaire-affaire'] = array_merge([
						"IP_TYPEPARENT" => 2,
						"IP_CODEPRODUIT" => $affaire['result']['key']
					], $arrayPivot["inventaire-affaire"]);
					
					if(!empty($arrayPivot['inventaire-affaire']) && $arrayPivot['inventaire-affaire'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-affaire']));
						if(!empty($arrayMaj['inventaire-affaire']) && $arrayMaj['inventaire-affaire'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-affaire']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-affaire']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-affaire']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-affaire']));
					}
				}
			}
			
			// Récupération ID PRODUIT
			
			if(!empty($produit['success']) && $produit['success'] && $infocob_form["tables"]["action"] == "true") {
				$arrayData['action']['AC_INFOLIBRE1']  = $produit['result']['key'];
				$arrayPivot['action']['AC_INFOLIBRE1'] = $produit['result']['key'];
				$arrayMaj['action']['AC_INFOLIBRE1']   = $produit['result']['key'];
			}
			
			if(!empty($produit["success"]) && $produit["success"]) {
				/*
				 * Inventaire
				 */
				if(isset($infocob_form['tables']['inventaire-produit']) && $infocob_form['tables']['inventaire-produit'] == "true") {
					$arrayData['inventaire-produit'] = array_merge([
						"IP_TYPEPARENT" => 0,
						"IP_CODEPRODUIT" => $produit['result']['key']
					], $arrayData["inventaire-produit"]);
					
					$arrayPivot['inventaire-produit'] = array_merge([
						"IP_TYPEPARENT" => 0,
						"IP_CODEPRODUIT" => $produit['result']['key']
					], $arrayPivot["inventaire-produit"]);
					
					if(!empty($arrayPivot['inventaire-produit']) && $arrayPivot['inventaire-produit'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-produit']));
						if(!empty($arrayMaj['inventaire-produit']) && $arrayMaj['inventaire-produit'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-produit']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-produit']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-produit']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-produit']));
					}
				}
			}
			
			// Récupération ID TICKET
			
			if(!empty($ticket['success']) && $ticket['success'] && $infocob_form["tables"]["ticket"] == "true") {
				$arrayData['action']['AC_TICKET']  = $ticket['result']['key'];
				$arrayPivot['action']['AC_TICKET'] = $ticket['result']['key'];
				$arrayMaj['action']['AC_TICKET']   = $ticket['result']['key'];
			}
			
			if(!empty($ticket["success"]) && $ticket["success"]) {
				/*
				 * Inventaire
				 */
				if(isset($infocob_form['tables']['inventaire-ticket']) && $infocob_form['tables']['inventaire-ticket'] == "true") {
					$arrayData['inventaire-ticket'] = array_merge([
						"IP_TYPEPARENT" => 118,
						"IP_CODEPRODUIT" => $ticket['result']['key']
					], $arrayData["inventaire-ticket"]);
					
					$arrayPivot['inventaire-ticket'] = array_merge([
						"IP_TYPEPARENT" => 118,
						"IP_CODEPRODUIT" => $ticket['result']['key']
					], $arrayPivot["inventaire-ticket"]);
					
					if(!empty($arrayPivot['inventaire-ticket']) && $arrayPivot['inventaire-ticket'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-ticket']));
						if(!empty($arrayMaj['inventaire-ticket']) && $arrayMaj['inventaire-ticket'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-ticket']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-ticket']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-ticket']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-ticket']));
					}
				}
			}
			
			// Récupération ID CONTRAT
			
			if(!empty($contrat['success']) && $contrat['success'] && $infocob_form["tables"]["contrat"] == "true") {
				$arrayData['action']['AC_CODECONTRAT']  = $contrat['result']['key'];
				$arrayPivot['action']['AC_CODECONTRAT'] = $contrat['result']['key'];
				$arrayMaj['action']['AC_CODECONTRAT']   = $contrat['result']['key'];
			}
			
			if(!empty($contrat["success"]) && $contrat["success"]) {
				/*
				 * Inventaire
				 */
				if(isset($infocob_form['tables']['inventaire-contrat']) && $infocob_form['tables']['inventaire-contrat'] == "true") {
					$arrayData['inventaire-contrat'] = array_merge([
						"IP_TYPEPARENT" => 120,
						"IP_CODEPRODUIT" => $contrat['result']['key']
					], $arrayData["inventaire-contrat"]);
					
					$arrayPivot['inventaire-contrat'] = array_merge([
						"IP_TYPEPARENT" => 120,
						"IP_CODEPRODUIT" => $contrat['result']['key']
					], $arrayPivot["inventaire-contrat"]);
					
					if(!empty($arrayPivot['inventaire-contrat']) && $arrayPivot['inventaire-contrat'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-contrat']));
						if(!empty($arrayMaj['inventaire-contrat']) && $arrayMaj['inventaire-contrat'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-contrat']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-contrat']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-contrat']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-contrat']));
					}
				}
			}
			
			// GESTION MAIL => DETAIL
			
			if(!empty($infocob_form["idPostContactForm"])) {
				$contact7Mail = WPCF7_ContactForm::get_instance($infocob_form["idPostContactForm"])->prop("mail");
				
				preg_match_all("/\[(.*?)\]/mi", $contact7Mail["body"], $shortcodesMail);
				
				array_walk_recursive($formData, [
					"Infocob\CrmForms\Admin\Tools",
					"infocob_register_data_db_filterStripSlashes"
				]);
				
				foreach($shortcodesMail[0] as $key => $value) {
					foreach($formData as $keyPost => $valuePost) {
						if($value == "[" . $keyPost . "]") {
							$shortcodesValues[ "[" . $keyPost . "]" ] = $valuePost;
						}
					}
				}
				
				foreach($shortcodesValues as $shortCodeIndex => $shortCodeValue) {
					if(is_array($shortCodeValue) || is_object($shortCodeValue)) {
						
						$i = 0;
						foreach($shortCodeValue as $value) {
							if($i === 0) {
								$shortcodesValues[ $shortCodeIndex ] = "";
							}
							
							if($i >= count($shortCodeValue) - 1) {
								$shortcodesValues[ $shortCodeIndex ] .= $value;
							} else {
								$shortcodesValues[ $shortCodeIndex ] .= $value . ", ";
							}
							$i ++;
						}
					}
				}
				
				$contact7MailReplaced = str_replace(array_keys($shortcodesValues), array_values($shortcodesValues), $contact7Mail["body"]);
				
				$contact7MailReplaced_strip = strip_tags($contact7MailReplaced, '<b>');
				$contact7MailReplaced_array = preg_split('/[\n\r]/i', $contact7MailReplaced_strip);
				
				$contact7MailReplaced_format = "";
				$lineBreakNumber             = 1;
				
				foreach($contact7MailReplaced_array as $line) {
					$trueLine = preg_replace('/[\x00-\x1F\x7F]/u', '', trim($line));
					
					// {\rtf1\ansi this word is \b bold \b0 }
					
					/*echo("\n");
					var_export($trueLine);
					echo("\n");*/
					
					if($trueLine != "") {
						$trueLine = preg_replace('/(?<=<b>)(.*)(?=<\/b>)/i', " \b " . $trueLine . " \b0 ", $trueLine);
						$trueLine = preg_replace('/(<b>|<\/b>)/i', '', $trueLine);
						
						$i       = 0;
						$newLine = '';
						while($i < $lineBreakNumber) {
							$newLine .= ' \par ';
							$i ++;
						}
						$contact7MailReplaced_format .= $newLine . $trueLine;
						$lineBreakNumber             = 1;
					} else {
						if($lineBreakNumber <= 2) {
							$lineBreakNumber ++;
						}
					}
				}
				
				$contact7MailReplaced_format = '{\rtf1\ansi ' . trim($contact7MailReplaced_format) . ' }';
				
				$arrayData['action']['AC_DETAIL'] = $contact7MailReplaced_format;
			}
			
			// ADD ACTION TO CRM
			
			$action = null;
			
			// !isset($infocob_form['tables']['action']) ==> compatibilite old install
			if(((isset($infocob_form['tables']['action']) && $infocob_form['tables']['action'] == "true") || !isset($infocob_form['tables']['action'])) && !empty($infocob_form)) {
				$action = Webservice::requestPostAjaxAPI("actions", $this->buildRequest($arrayData['action']));
			}
			
			// ADD Autres Destinataires
			
			if(!empty($action['success']) && !empty($infocob_form['fieldAssoc']['action']['type_action']['autres_destinataires'])) {
				$actionCode       = $action['result']['key'];
				$field            = [];
				$field['AC_CODE'] = $actionCode;
				$queryFields      = $this->buildRequest($field);
				
				$second_autres_destinataires = $infocob_form['fieldAssoc']['action']['type_action']['autres_destinataires'];
				
				foreach($second_autres_destinataires as $keyDest => $valueDest) {
					$queryFields .= "&autres_destinataires[]=" . urlencode($valueDest);
				}
				
				Webservice::requestPutAjaxAPI("actions/" . $actionCode, $queryFields);
			}
			
			/*
			 * Inventaire
			 */
			if($action['success'] ?? false) {
				if(isset($infocob_form['tables']['inventaire-action']) && $infocob_form['tables']['inventaire-action'] == "true") {
					$arrayData['inventaire-action'] = array_merge([
						"IP_TYPEPARENT" => 100,
						"IP_CODEPRODUIT" => $action['result']['key']
					], $arrayData["inventaire-action"]);
					
					$arrayPivot['inventaire-action'] = array_merge([
						"IP_TYPEPARENT" => 100,
						"IP_CODEPRODUIT" => $action['result']['key']
					], $arrayPivot["inventaire-action"]);
					
					if(!empty($arrayPivot['inventaire-action']) && $arrayPivot['inventaire-action'] != "") {
						$responseInventaire = Webservice::requestGetAjaxAPI("inventaireproduit?" . $this->buildRequest($arrayPivot['inventaire-action']));
						if(!empty($arrayMaj['inventaire-action']) && $arrayMaj['inventaire-action'] != "") {
							if($responseInventaire['success']) {
								$inventaire_id = $responseInventaire['result'][0]['IP_CODE'];
								Webservice::requestPutAjaxAPI("inventaireproduit/" . $inventaire_id, $this->buildRequest($arrayMaj['inventaire-action']));
							} else {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-action']));
							}
						} else {
							if(!$responseInventaire["success"]) {
								Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-action']));
							}
						}
					} else {
						Webservice::requestPostAjaxAPI("inventaireproduit", $this->buildRequest($arrayData['inventaire-action']));
					}
				}
			}
			
			// ADD Fichiers lies
			
			if(!empty($action['success'])) {
				$actionCode = $action['result']['key'];
				
				$filesToUploads = [];
				if(!empty($arrayFichiersLies['action']["fichiersLies"]) || !empty($arrayFichiersLies['action']["cloudFichiers"])) {
					
					foreach($arrayFichiersLies['action']["fichiersLies"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									$filesToUploads["FICHIERS_LIES"][] = [
										"filename" => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ]))
									];
								}
							} else {
								$filesToUploads["FICHIERS_LIES"][] = [
									"filename" => basename($uploadsFiles[ $shortcode ]["name"]),
									"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"]))
								];
							}
						}
					}
					
					foreach($arrayFichiersLies['action']["cloudFichiers"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									Webservice::requestPostAjaxAPI("cloudfichier", [
										"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ])),
										"file_name"   => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"code_module" => $actionCode,
										"module"      => "action"
									]);
								}
							} else {
								Webservice::requestPostAjaxAPI("cloudfichier", [
									"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"])),
									"file_name"   => basename($uploadsFiles[ $shortcode ]["name"]),
									"code_module" => $actionCode,
									"module"      => "action"
								]);
							}
						}
					}
					
					Webservice::requestPutAjaxAPI("actions/" . $actionCode, $filesToUploads);
				}
			}
			
			if(!empty($affaire['success'])) {
				$affaireCode = $affaire['result']['key'];
				
				$filesToUploads = [];
				if(!empty($arrayFichiersLies['affaire']["fichiersLies"]) || !empty($arrayFichiersLies['affaire']["cloudFichiers"])) {
					
					foreach($arrayFichiersLies['affaire']["fichiersLies"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									$filesToUploads["FICHIERS_LIES"][] = [
										"filename" => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ]))
									];
								}
							} else {
								$filesToUploads["FICHIERS_LIES"][] = [
									"filename" => basename($uploadsFiles[ $shortcode ]["name"]),
									"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"]))
								];
							}
						}
					}
					
					foreach($arrayFichiersLies['affaire']["cloudFichiers"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									Webservice::requestPostAjaxAPI("cloudfichier", [
										"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ])),
										"file_name"   => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"code_module" => $affaireCode,
										"module"      => "affaire"
									]);
								}
							} else {
								Webservice::requestPostAjaxAPI("cloudfichier", [
									"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"])),
									"file_name"   => basename($uploadsFiles[ $shortcode ]["name"]),
									"code_module" => $affaireCode,
									"module"      => "affaire"
								]);
							}
						}
					}
					
					Webservice::requestPutAjaxAPI("affaire/" . $affaireCode, $filesToUploads);
				}
			}
			
			if(!empty($produit['success'])) {
				$produitCode = $produit['result']['key'];
				
				$filesToUploads = [];
				if(!empty($arrayFichiersLies['produit']["fichiersLies"]) || !empty($arrayFichiersLies['produit']["cloudFichiers"])) {
					
					foreach($arrayFichiersLies['produit']["fichiersLies"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									$filesToUploads["FICHIERS_LIES"][] = [
										"filename" => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ]))
									];
								}
							} else {
								$filesToUploads["FICHIERS_LIES"][] = [
									"filename" => basename($uploadsFiles[ $shortcode ]["name"]),
									"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"]))
								];
							}
						}
					}
					
					foreach($arrayFichiersLies['produit']["cloudFichiers"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									Webservice::requestPostAjaxAPI("cloudfichier", [
										"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ])),
										"file_name"   => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"code_module" => $produitCode,
										"module"      => "produit"
									]);
								}
							} else {
								Webservice::requestPostAjaxAPI("cloudfichier", [
									"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"])),
									"file_name"   => basename($uploadsFiles[ $shortcode ]["name"]),
									"code_module" => $produitCode,
									"module"      => "produit"
								]);
							}
						}
					}
					
					Webservice::requestPutAjaxAPI("produitfiche/" . $produitCode, $filesToUploads);
				}
			}
			
			if(!empty($ticket['success'])) {
				$ticketCode = $ticket['result']['key'];
				
				$filesToUploads = [];
				if(!empty($arrayFichiersLies['ticket']["fichiersLies"]) || !empty($arrayFichiersLies['ticket']["cloudFichiers"])) {
					
					foreach($arrayFichiersLies['ticket']["fichiersLies"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									$filesToUploads["FICHIERS_LIES"][] = [
										"filename" => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ]))
									];
								}
							} else {
								$filesToUploads["FICHIERS_LIES"][] = [
									"filename" => basename($uploadsFiles[ $shortcode ]["name"]),
									"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"]))
								];
							}
						}
					}
					
					foreach($arrayFichiersLies['ticket']["cloudFichiers"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									Webservice::requestPostAjaxAPI("cloudfichier", [
										"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ])),
										"file_name"   => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"code_module" => $ticketCode,
										"module"      => "ticket"
									]);
								}
							} else {
								Webservice::requestPostAjaxAPI("cloudfichier", [
									"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"])),
									"file_name"   => basename($uploadsFiles[ $shortcode ]["name"]),
									"code_module" => $ticketCode,
									"module"      => "ticket"
								]);
							}
						}
					}
					
					Webservice::requestPutAjaxAPI("ticket/" . $ticketCode, $filesToUploads);
				}
			}
			
			if(!empty($contrat['success'])) {
				$contratCode = $contrat['result']['key'];
				
				$filesToUploads = [];
				if(!empty($arrayFichiersLies['contrat']["fichiersLies"]) || !empty($arrayFichiersLies['contrat']["cloudFichiers"])) {
					
					foreach($arrayFichiersLies['contrat']["fichiersLies"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									$filesToUploads["FICHIERS_LIES"][] = [
										"filename" => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ]))
									];
								}
							} else {
								$filesToUploads["FICHIERS_LIES"][] = [
									"filename" => basename($uploadsFiles[ $shortcode ]["name"]),
									"file"     => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"]))
								];
							}
						}
					}
					
					foreach($arrayFichiersLies['contrat']["cloudFichiers"] as $shortcode) {
						if(!empty($uploadsFiles[ $shortcode ])) {
							if(isset($uploadsFiles[ $shortcode ]["size"]) && is_array($uploadsFiles[ $shortcode ]["size"])) {
								$nb_files = count($uploadsFiles[ $shortcode ]["size"]);
								for($i = 0; $i < $nb_files; $i ++) {
									Webservice::requestPostAjaxAPI("cloudfichier", [
										"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"][ $i ])),
										"file_name"   => basename($uploadsFiles[ $shortcode ]["name"][ $i ]),
										"code_module" => $contratCode,
										"module"      => "contrat"
									]);
								}
							} else {
								Webservice::requestPostAjaxAPI("cloudfichier", [
									"file_data"   => base64_encode(file_get_contents($uploadsFiles[ $shortcode ]["tmp_name"])),
									"file_name"   => basename($uploadsFiles[ $shortcode ]["name"]),
									"code_module" => $contratCode,
									"module"      => "contrat"
								]);
							}
						}
					}
					
					Webservice::requestPutAjaxAPI("contrat/" . $contratCode, $filesToUploads);
				}
			}
			
			do_action("infocob-crm-forms-after-infocob", [
				"ac_code" => $action["result"]["key"] ?? false,
				"c_code" => $contact["result"]["key"] ?? false,
				"i_code" => $interlocuteur["result"]["key"] ?? false,
				"p_code" => $produit["result"]["key"] ?? false,
				"af_code" => $affaire["result"]["key"] ?? false,
				"ct_code" => $contrat["result"]["key"] ?? false,
				"ti_code" => $ticket["result"]["key"] ?? false,
				"form-data" => $formData,
				"files" => $uploadsFiles,
			]);
			
			/*
			 * Allow others plugins to get this values
			 */
			$contact_form_id        = !empty($formData["contactformId"]) ? $formData["contactformId"] : false;
			$infocob_tracking_token = !empty($formData["infocob_tracking_token"]) ? $formData["infocob_tracking_token"] : false;
			$selectedGroupements    = !empty($formData["infocob_tracking_groupements"]) ? $formData["infocob_tracking_groupements"] : false;
			if($contact_form_id && $infocob_tracking_token && $selectedGroupements) {
				$c_code = !empty($contact["result"]["key"]) ? $contact["result"]["key"] : false;
				$i_code = !empty($interlocuteur["result"]["key"]) ? $interlocuteur["result"]["key"] : false;
				
				do_action('infocob_forms_after_submit_form', [
					"CONTACT_FORM_ID"              => $contact_form_id,
					"INFOCOB_TRACKING_TOKEN"       => $infocob_tracking_token,
					"INFOCOB_TRACKING_GROUPEMENTS" => $selectedGroupements,
					"C_CODE"                       => $c_code,
					"I_CODE"                       => $i_code,
				]);
			}
		}
		
		public function buildRequest($param) {
			if(is_object($param) || is_array($param)) {
				return http_build_query($param);
			} else {
				return "";
			}
		}
		
		public function matchUrl($url) {
			preg_match("/^(.+[^\/])/mi", $url, $matchUrl);
			
			return $matchUrl[0];
		}
		
		public function getDataTables($table, $infocob_form, $formData) {
			date_default_timezone_set("Europe/Paris");
			
			if($table == 'action') {
				$arrayData['AC_DUREEREELLEESTIME'] = "0.001388";
				
				foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataDb => $valueDataDb) {
					//if($keyDataDb != 'infocob_fichiers_lies') {
					foreach($formData as $keyFormData => $valueFormData) {
						if(!empty($valueFormData) && $valueFormData != "") {
							if($keyDataDb == $keyFormData) {
								if(strcasecmp($valueDataDb, "AC_TYPEACTION") === 0 ||
								   strcasecmp($valueDataDb, "AC_SOUSTYPEACTION") === 0 ||
								   strcasecmp($valueDataDb, "AC_SOUSSOUSTYPEACTION") === 0) {
									if(!empty($valueFormData) && $valueFormData != "") {
										$types = explode(".", $valueFormData);
										if(!empty($types) && strcasecmp($valueDataDb, "AC_TYPEACTION") === 0) {
											$arrayData['AC_TYPEACTION'] = $types[0] ?? "";
										}
										if(!empty($types) && (strcasecmp($valueDataDb, "AC_TYPEACTION") === 0 || strcasecmp($valueDataDb, "AC_SOUSTYPEACTION") === 0)) {
											$type = $types[1] ?? "";
											if(strcasecmp($valueDataDb, "AC_SOUSTYPEACTION") === 0) {
												$type = $types[0] ?? "";
											}
											$arrayData['AC_SOUSTYPEACTION'] = $type;
										}
										if(!empty($types) && (strcasecmp($valueDataDb, "AC_TYPEACTION") === 0 || strcasecmp($valueDataDb, "AC_SOUSTYPEACTION") === 0 || strcasecmp($valueDataDb, "AC_SOUSSOUSTYPEACTION") === 0)) {
											$type = $types[2] ?? "";
											if(strcasecmp($valueDataDb, "AC_SOUSTYPEACTION") === 0) {
												$type = $types[1] ?? "";
											} else if(strcasecmp($valueDataDb, "AC_SOUSSOUSTYPEACTION") === 0) {
												$type = $types[0] ?? "";
											}
											$arrayData['AC_SOUSSOUSTYPEACTION'] = $type;
										}
									}
								} else if(strcasecmp($valueDataDb, "AC_DUREEREELLEESTIME") === 0) {
									$arrayData['AC_DUREEREELLEESTIME'] = $valueFormData * 24 * 60; // conversion jour en minute
								} else {
									$arrayData[ $valueDataDb ] = $valueFormData;
								}
							}
						}
					}
					//}
					/*if($keyDataDb == 'infocob_fichiers_lies') {
						$arrayData['FICHIERS_LIES'] = $valueDataDb;
					}*/
					if($keyDataDb == 'type_action') {
						foreach($valueDataDb as $key => $value) {
							if($key == "type" && (
									empty($arrayData['AC_TYPEACTION']) &&
									empty($arrayData['AC_SOUSTYPEACTION']) &&
									empty($arrayData['AC_SOUSSOUSTYPEACTION'])
								)) {
								if(!empty($value) && $value != "") {
									$types = explode(".", $value);
									if(isset($types[0])) {
										$arrayData['AC_TYPEACTION'] = $types[0];
									}
									if(isset($types[1])) {
										$arrayData['AC_SOUSTYPEACTION'] = $types[1];
									}
									if(isset($types[2])) {
										$arrayData['AC_SOUSSOUSTYPEACTION'] = $types[2];
									}
								}
							} else if($key == "destinataires") {
								if(!empty($value) && $value != "") {
									$arrayData['AC_CODEINTERLOCUTEUR_DEST'] = $value;
								}
							}
						}
					}
					if($keyDataDb == "alarme") {
						$arrayData["AC_DATEDEBUT"] = date("Y-m-d H:i:s");
					}
					if($keyDataDb == 'moreData') {
						foreach($valueDataDb as $key => $value) {
						    if(strcasecmp($value['champ'], "AC_DUREEREELLEESTIME") === 0) {
								$arrayData[$value['champ']] = $value['value'] * 24 * 60; // conversion jour en minute
							} else {
							    $arrayData[ $value['champ'] ] = $value['value'];
						    }
						}
					}
				}
				
				$arrayData['AC_DATE_PREVU']        = date("Y-m-d H:i:s");
				$arrayData['AC_DATE_SAISIE']       = date("Y-m-d H:i:s");
				$arrayData['AC_DATECREATION']      = date("Y-m-d H:i:s");
				$date_fin                          = date("Y-m-d H:i:s");
				$date_fin                          = date("Y-m-d H:i:s", strtotime("$date_fin + 2 min"));
				$arrayData['AC_DATEFIN']           = $date_fin;
				
			} else if($table == 'ticket') {
				
				if(isset($infocob_form['fieldAssoc'][ $table ])) {
					foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataDb => $valueDataDb) {
						
						foreach($formData as $keyFormData => $valueFormData) {
							if(!empty($valueFormData) && $valueFormData != "") {
								if($keyDataDb == $keyFormData) {
									$arrayData[ $valueDataDb ] = $valueFormData;
								}
							}
						}
						
						if($keyDataDb == 'type_ticket') {
							foreach($valueDataDb as $key => $value) {
								if($key == "module") {
									if(!empty($value) && $value != "") {
										$types = explode(".", $value);
										if(isset($types[0])) {
											$arrayData['TI_MODULE'] = $types[0];
										}
										if(isset($types[1])) {
											$arrayData['TI_SOUSMODULE'] = $types[1];
										}
										if(isset($types[2])) {
											$arrayData['TI_SOUSSOUSMODULE'] = $types[2];
										}
									}
								} else if($key == "type") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_TYPE'] = $value;
									}
								} else if($key == "categorie") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_CATEGORIE'] = $value;
									}
								} else if($key == "frequence") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_FREQUENCE'] = $value;
									}
								} else if($key == "plateforme") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_PLATEFORME'] = $value;
									}
								} else if($key == "priorite") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_PRIORITE'] = $value;
									}
								} else if($key == "severite") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_SEVERITE'] = $value;
									}
								} else if($key == "source") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_SOURCE'] = $value;
									}
								} else if($key == "version") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_VERSION'] = $value;
									}
								}
							}
						}
						if($keyDataDb == "alarme") {
							$arrayData["TI_DATEALERT"] = date("Y-m-d H:i:s");
						}
						if($keyDataDb == 'moreData') {
							foreach($valueDataDb as $key => $value) {
								$arrayData[ $value['champ'] ] = $value['value'];
							}
						}
					}
				}
				
				$arrayData['TI_DATEOUVERTURE'] = date("Y-m-d H:i:s");
				$arrayData['TI_DATECREATION']  = date("Y-m-d H:i:s");
				$arrayData['TI_DATEMODIF']     = date("Y-m-d H:i:s");
				
			} else if($table == 'contrat') {
				
				if(isset($infocob_form['fieldAssoc'][ $table ])) {
					foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataDb => $valueDataDb) {
						
						foreach($formData as $keyFormData => $valueFormData) {
							if(!empty($valueFormData) && $valueFormData != "") {
								if($keyDataDb == $keyFormData) {
									$arrayData[ $valueDataDb ] = $valueFormData;
								}
							}
						}
						
						if($keyDataDb == 'type_contrat') {
							foreach($valueDataDb as $key => $value) {
								if($key == "etat") {
									if(!empty($value) && $value != "") {
										$arrayData['CT_ETATCONTRAT'] = $value;
									}
								} else if($key == "type") {
									if(!empty($value) && $value != "") {
										$arrayData['CT_TYPE'] = $value;
									}
								} else if($key == "periodicite") {
									if(!empty($value) && $value != "") {
										$arrayData['CT_PERIODICITE'] = $value;
									}
								} else if($key == "facturation") {
									if(!empty($value) && $value != "") {
										$arrayData['CT_FACTURATION'] = $value;
									}
								} else if($key == "mode") {
									if(!empty($value) && $value != "") {
										$arrayData['CT_MODERECONDUCTION'] = $value;
									}
								}
							}
						}
						if($keyDataDb == 'moreData') {
							foreach($valueDataDb as $key => $value) {
								$arrayData[ $value['champ'] ] = $value['value'];
							}
						}
					}
				}
				
				$arrayData['CT_DATEOUVERTURE'] = date("Y-m-d H:i:s");
				$arrayData['CT_DATECREATION']  = date("Y-m-d H:i:s");
				$arrayData['CT_DATEMODIF']     = date("Y-m-d H:i:s");
				
			} else {
				
				if(isset($infocob_form['tables'][ $table ]) && $infocob_form['tables'][ $table ] == "true") {
					foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataDb => $valueDataDb) {
						foreach($formData as $keyFormData => $valueFormData) {
							if(!empty($valueFormData) && $valueFormData != "") {
								if($keyDataDb == $keyFormData) {
									if(is_array($valueFormData) && count($valueFormData) === 1) {
										$valueFormData = $valueFormData[0];
									}
									$arrayData[ $valueDataDb ] = $valueFormData;
								}
							}
						}
						if($keyDataDb == 'moreData') {
							foreach($valueDataDb as $key => $value) {
								$arrayData[ $value['champ'] ] = $value['value'];
							}
						}
					}
				} else {
					$arrayData = [];
				}
				
			}
			
			return $arrayData;
		}
		
		public function getPivotTables($table, $infocob_form, $formData) {
			date_default_timezone_set("Europe/Paris");
			
			if($table == 'action') {
				
				if(isset($infocob_form['pivot'][ $table ]) && !empty($infocob_form['pivot'][ $table ])) {
					foreach($infocob_form['pivot'][ $table ] as $keyPivotTable => $valuePivotTable) {
						foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataTable => $valuedDataTable) {
							if($valuePivotTable == $keyDataTable && !empty($formData[ $valuePivotTable ])) {
								$arrayData[ $valuedDataTable ] = $formData[ $valuePivotTable ];
							}
						}
					}
				} else {
					$arrayData = [];
				}
				
				if(isset($infocob_form['fieldAssoc'][ $table ])) {
					foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataTable => $valuedDataTable) {
						if($keyDataTable == "type_action") {
							foreach($valuedDataTable as $keyTypeAction => $valueTypeAction) {
								if($keyTypeAction == "type") {
									if(!empty($valueTypeAction) && $valueTypeAction != "") {
										$arrayData['AC_TYPEACTION'] = $valueTypeAction;
									}
								} else if($keyTypeAction == "destinataires") {
									if(!empty($valueTypeAction) && $valueTypeAction != "") {
										$arrayData['AC_CODEINTERLOCUTEUR_DEST'] = $valueTypeAction;
									}
								}
							}
						}
					}
				}
				
			} else {
				if(isset($infocob_form['tables'][ $table ]) && $infocob_form['tables'][ $table ] == "true") {
					if(isset($infocob_form['pivot'][ $table ]) && !empty($infocob_form['pivot'][ $table ])) {
						foreach($infocob_form['pivot'][ $table ] as $keyPivotTable => $valuePivotTable) {
							foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataTable => $valuedDataTable) {
								if($valuePivotTable == $keyDataTable && !empty($formData[ $valuePivotTable ])) {
									if(is_array($formData[ $valuePivotTable ]) && count($formData[ $valuePivotTable ]) === 1) {
										$formData[ $valuePivotTable ] = $formData[ $valuePivotTable ][0];
									}
									$arrayData[ $valuedDataTable ] = $formData[ $valuePivotTable ];
								}
							}
						}
					} else {
						$arrayData = [];
					}
				} else {
					$arrayData = [];
				}
			}
			
			if(isset($infocob_form['fieldAssoc'][ $table ]['moreData'])) {
				foreach($infocob_form['fieldAssoc'][ $table ]['moreData'] as $key => $value) {
					if(isset($value['pivot'])) {
						$arrayData[ $value['champ'] ] = $value['value'];
					}
				}
			}
			
			return $arrayData;
			
		}
		
		public function getMajTables($table, $infocob_form, $formData) {
			date_default_timezone_set("Europe/Paris");
			
			if($table == 'action') {
				$arrayData['AC_DUREEREELLEESTIME'] = "0.001388";
				
				if(isset($infocob_form['maj'][ $table ]) && !empty($infocob_form['maj'][ $table ])) {
					foreach($infocob_form['maj'][ $table ] as $keyPivotTable => $valuePivotTable) {
						foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataTable => $valuedDataTable) {
							if($valuePivotTable == $keyDataTable) {
								if(strcasecmp($valuedDataTable, "AC_DUREEREELLEESTIME") === 0) {
									$arrayData['AC_DUREEREELLEESTIME'] = $formData[ $valuePivotTable ] * 24 * 60; // conversion jour en minute
								} else {
									$arrayData[ $valuedDataTable ] = $formData[ $valuePivotTable ];
								}
							}
						}
					}
				} else {
					$arrayData = [];
				}
				
				if(isset($infocob_form['fieldAssoc'][ $table ])) {
					foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataTable => $valuedDataTable) {
						if($keyDataTable == "type_action") {
							foreach($valuedDataTable as $keyTypeAction => $valueTypeAction) {
								if($keyTypeAction == "type") {
									if(!empty($valueTypeAction) && $valueTypeAction != "") {
										$arrayData['AC_TYPEACTION'] = $valueTypeAction;
									}
								} else if($keyTypeAction == "destinataires") {
									if(!empty($valueTypeAction) && $valueTypeAction != "") {
										$arrayData['AC_CODEINTERLOCUTEUR_DEST'] = $valueTypeAction;
									}
								}
							}
						}
					}
				}
				
				$arrayData['AC_DATE_PREVU']        = date("Y-m-d H:i:s");
				$arrayData['AC_DATE_SAISIE']       = date("Y-m-d H:i:s");
				$arrayData['AC_DATECREATION']      = date("Y-m-d H:i:s");
				$date_fin                          = date("Y-m-d H:i:s");
				$arrayData['AC_DATEFIN']           = date("Y-m-d H:i:s", strtotime("$date_fin + 2 min"));
				
			} else if($table == 'ticket') {
				if(isset($infocob_form['maj'][ $table ]) && !empty($infocob_form['maj'][ $table ])) {
					foreach($infocob_form['maj'][ $table ] as $keyPivotTable => $valuePivotTable) {
						foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataTable => $valuedDataTable) {
							if($valuePivotTable == $keyDataTable) {
								$arrayData[ $valuedDataTable ] = $formData[ $valuePivotTable ];
							}
						}
					}
				} else {
					$arrayData = [];
				}
				
				if(isset($infocob_form['fieldAssoc'][ $table ])) {
					foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataTable => $valuedDataTable) {
						if($keyDataTable == "type_ticket") {
							foreach($valuedDataTable as $key => $value) {
								if($key == "module") {
									if(!empty($value) && $value != "") {
										$types = explode(".", $value);
										if(isset($types[0])) {
											$arrayData['TI_MODULE'] = $types[0];
										}
										if(isset($types[1])) {
											$arrayData['TI_SOUSMODULE'] = $types[1];
										}
										if(isset($types[2])) {
											$arrayData['TI_SOUSSOUSMODULE'] = $types[2];
										}
									}
								} else if($key == "type") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_TYPE'] = $value;
									}
								} else if($key == "categorie") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_CATEGORIE'] = $value;
									}
								} else if($key == "frequence") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_FREQUENCE'] = $value;
									}
								} else if($key == "plateforme") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_PLATEFORME'] = $value;
									}
								} else if($key == "priorite") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_PRIORITE'] = $value;
									}
								} else if($key == "severite") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_SEVERITE'] = $value;
									}
								} else if($key == "source") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_SOURCE'] = $value;
									}
								} else if($key == "version") {
									if(!empty($value) && $value != "") {
										$arrayData['TI_VERSION'] = $value;
									}
								}
							}
						}
					}
				}
				
				$arrayData['TI_DATEOUVERTURE'] = date("Y-m-d H:i:s");
				$arrayData['TI_DATECREATION']  = date("Y-m-d H:i:s");
				$arrayData['TI_DATEMODIF']     = date("Y-m-d H:i:s");
				
			} else if($table == 'contrat') {
				if(isset($infocob_form['maj'][ $table ]) && !empty($infocob_form['maj'][ $table ])) {
					foreach($infocob_form['maj'][ $table ] as $keyPivotTable => $valuePivotTable) {
						foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataTable => $valuedDataTable) {
							if($valuePivotTable == $keyDataTable) {
								$arrayData[ $valuedDataTable ] = $formData[ $valuePivotTable ];
							}
						}
					}
				} else {
					$arrayData = [];
				}
				
				if(isset($infocob_form['fieldAssoc'][ $table ])) {
					foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataTable => $valuedDataTable) {
						if($keyDataTable == "type_contrat") {
							foreach($valuedDataTable as $key => $value) {
								if($key == "etat") {
									if(!empty($value) && $value != "") {
										$arrayData['CT_ETATCONTRAT'] = $value;
									}
								} else if($key == "type") {
									if(!empty($value) && $value != "") {
										$arrayData['CT_TYPE'] = $value;
									}
								} else if($key == "periodicite") {
									if(!empty($value) && $value != "") {
										$arrayData['CT_PERIODICITE'] = $value;
									}
								} else if($key == "facturation") {
									if(!empty($value) && $value != "") {
										$arrayData['CT_FACTURATION'] = $value;
									}
								} else if($key == "mode") {
									if(!empty($value) && $value != "") {
										$arrayData['CT_MODERECONDUCTION'] = $value;
									}
								}
							}
						}
					}
				}
				
				$arrayData['CT_DATEOUVERTURE'] = date("Y-m-d H:i:s");
				$arrayData['CT_DATECREATION']  = date("Y-m-d H:i:s");
				$arrayData['CT_DATEMODIF']     = date("Y-m-d H:i:s");
				
			} else {
				if(isset($infocob_form['tables'][ $table ]) && $infocob_form['tables'][ $table ] == "true") {
					if(isset($infocob_form['maj'][ $table ]) && !empty($infocob_form['maj'][ $table ])) {
						foreach($infocob_form['maj'][ $table ] as $keyPivotTable => $valuePivotTable) {
							foreach($infocob_form['fieldAssoc'][ $table ] as $keyDataTable => $valuedDataTable) {
								if($valuePivotTable == $keyDataTable) {
									if(is_array($formData[ $valuePivotTable ]) && count($formData[ $valuePivotTable ]) === 1) {
										$formData[ $valuePivotTable ] = $formData[ $valuePivotTable ][0];
									}
									$arrayData[ $valuedDataTable ] = $formData[ $valuePivotTable ];
								}
							}
						}
					} else {
						$arrayData = [];
					}
				} else {
					$arrayData = [];
				}
			}
			
			if(isset($infocob_form['fieldAssoc'][ $table ]['moreData'])) {
				foreach($infocob_form['fieldAssoc'][ $table ]['moreData'] as $key => $value) {
					if(isset($value['maj'])) {
						$arrayData[ $value['champ'] ] = $value['value'];
					}
				}
			}
			
			
			return $arrayData;
			
		}
		
		public function getFichiersLiesTables($table, $infocob_form, $formData) {
			$arrayData = [];
			if((isset($infocob_form['fichiersLies'][ $table ]) && !empty($infocob_form['fichiersLies'][ $table ]))
			   || (isset($infocob_form['cloudFichiers'][ $table ]) && !empty($infocob_form['cloudFichiers'][ $table ]))) {
				return [
					"cloudFichiers" => $infocob_form["cloudFichiers"][ $table ] ?? [],
					"fichiersLies"  => $infocob_form['fichiersLies'][ $table ] ?? [],
				];
				//return $infocob_form['fichiersLies'][ $table ];
			}
			
			return $arrayData;
		}
		
		public static function skipMailCf7($idPostContact7, $skipMail = "true") {
			if($skipMail == "true") {
				$contact_form        = WPCF7_ContactForm::get_instance($idPostContact7);
				$additional_settings = "skip_mail: on";
				$contact_form->set_properties(array('additional_settings' => $additional_settings));
				$contact_form->save();
			} else {
				$contact_form        = WPCF7_ContactForm::get_instance($idPostContact7);
				$additional_settings = "";
				$contact_form->set_properties(array('additional_settings' => $additional_settings));
				$contact_form->save();
			}
		}
	}

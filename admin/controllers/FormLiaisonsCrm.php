<?php
	
	namespace Infocob\CrmForms\Admin;
	
	use WP_Query;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FormLiaisonsCrm {
		
		public function render() {
			echo "<div class='wrap'>";
			echo "<h1>" . __("CRM links managment", "infocob-crm-forms") . "</h1>";
			
			$tab = !empty($_GET["tab"]) ? esc_attr($_GET["tab"]) : "ifb";
			
			$this->renderTabs($tab);
			
			if(strcasecmp($tab, 'cf7') === 0 && InfocobCrmForms::check_contact_form_7_is_activated()) {
				$this->renderCf7();
			} else if(strcasecmp($tab, 'ifb') === 0 || empty($tab)) {
				$this->renderIfb();
			}
			
			echo "</div>";
		}
		
		public function renderTabs($currentTab = 'cf7') {
			$tabs = array(
				'ifb' => "Infocob"
			);
			
			if(InfocobCrmForms::check_contact_form_7_is_activated()) {
				$tabs["cf7"] = "Contact form 7";
			}
			
			$link = get_admin_url(null, 'edit.php');
			
			$html = '<h2 class="nav-tab-wrapper">';
			foreach($tabs as $tab => $name) {
				$link = add_query_arg([
					"post_type" => "ifb_crm_forms",
					"page" => "infocob-crm-forms-admin-liaisons-crm-page",
					"tab" => $tab
				], $link);
				$class = (strcasecmp($tab, $currentTab) === 0) ? 'nav-tab-active' : '';
				$html .= '<a class="nav-tab ' . $class . '" href="' . $link . '">' . $name . '</a>';
			}
			$html .= '</h2>';
			echo $html;
		}
		
		public function renderIfb() {
			$wp_query_ifb_crm_forms = new WP_Query([
				'post_type' => 'ifb_crm_forms',
				'post_status' => 'publish',
				'posts_per_page' => -1
			]);
			
			require_once plugin_dir_path(__FILE__) . '../includes/admin-list-forms-liaisons-ifb.php';
			
			$post_id = !empty($_GET["post_id"]) ? esc_attr($_GET["post_id"]) : "";
			
			if(!empty($post_id)) {
				$dataDbForm = Database::getFormIfbFromDb(sanitize_text_field($post_id));
				
				$admin_form_edit_json = get_post_meta($post_id, 'infocob_crm_forms_admin_form_config', true);
				$form_config = json_decode($admin_form_edit_json, true);
				
				$form_inputs = !empty($form_config["input"]) ? $form_config["input"] : [];
				
				$shortcodes = [];
				$shortcodesFichiersLies = [];
				foreach($form_inputs as $form_input) {
					if(strcasecmp($form_input["type"], 'select') !== 0 || empty($form_input["multiple"])) {
						if(strcasecmp($form_input["type"], 'groupe') === 0 && isset($form_input["champs"])) {
							foreach($form_input["champs"] as $sub_input) {
								if(strcasecmp($sub_input["type"], 'file') === 0) {
									$shortcodesFichiersLies[] = $sub_input["nom"];
								} else {
									$shortcodes[] = $sub_input["nom"];
								}
							}
						} else {
							if(strcasecmp($form_input["type"], 'file') === 0) {
								$shortcodesFichiersLies[] = $form_input["nom"];
							} else {
								$shortcodes[] = $form_input["nom"];
							}
						}
					}
				}
				
				$ws = new Webservice();
				
				// Traitement pour action
				$dataActions = $ws->getDataTable("actions");
				$requireFieldsActions = $ws->getRequiredFields("actions");
				
				$typesaction = $ws->getTypesAction();
				$vendeurs = $ws->getVendeurs();
				$groupements = $ws->getGroupements();
				// Fin traitement action
				
				// Traitement Contact
				$dataContact = $ws->getDataTable("contactfiche");
				$requireFieldsContact = $ws->getRequiredFields("contactfiche");
				// Fin traitement contact
				
				// Traitement Interlocuteur
				$dataInterlocuteur = $ws->getDataTable("interlocuteurfiche");
				$requireFieldsInterlocuteur = $ws->getRequiredFields("interlocuteurfiche");
				// Fin traitement interlocuteur
				
				// Traitement Affaire
				$dataAffaire = $ws->getDataTable("affaire");
				$requireFieldsAffaire = $ws->getRequiredFields("affaire");
				// Fin traitement Affaire
				
				// Traitement Produit
				$dataProduit = $ws->getDataTable("produitfiche");
				$requireFieldsProduit = $ws->getRequiredFields("produitfiche");
				// Fin traitement Produit
				
				// Traitement Ticket
				$dataTicket = $ws->getDataTable("ticket");
				$requireFieldsTicket = $ws->getRequiredFields("ticket");
				
				$ticketStatus = $ws->getTicketStatus();
				$ticketTypes = $ws->getTicketTypes();
				$ticketCategories = $ws->getTicketCategories();
				$ticketFrequences = $ws->getTicketFrequences();
				$ticketPlateformes = $ws->getTicketPlateformes();
				$ticketPriorites = $ws->getTicketPriorites();
				$ticketSeverites = $ws->getTicketSeverites();
				$ticketSources = $ws->getTicketSources();
				$ticketVersions = $ws->getTicketVersions();
				$ticketModules = $ws->getTicketModules();
				// Fin traitement Ticket
				
				// Traitement Contrat
				$dataContrat = $ws->getDataTable("contrat");
				$requireFieldsContrat = $ws->getRequiredFields("contrat");
				
				$contratEtats = $ws->getContatEtats();
				$contratTypes = $ws->getContatTypes();
				$contratPeriodicites = $ws->getContatPeriodicites();
				$contratFacturations = $ws->getContatFacturations();
				$contratModesReconduction = $ws->getContatModesReconduction();
				// Fin traitement Contrat
				
				// Traitement Historique
				$dataHistorique = $ws->getDataTable("historique");
				$requireFieldsHistorique = $ws->getRequiredFields("historique");
				// Fin traitement Historique
				
				// Traitement pour inventaires
				$dataInventaires = $ws->getDataTable("inventaireproduit");
				$requireFieldsInventaires = $ws->getRequiredFields("inventaireproduit");
				
				// Traitement Labels tables
				$libellesTables = array_change_key_case($ws->getTableLibelle(), CASE_UPPER);
				// FIN traitement labels
				
				require_once plugin_dir_path(__FILE__) . '../includes/admin-forms-liaisons-ifb.php';
			}
		}
		
		public function renderCf7() {
			$forms = Database::getContact7Form();
			require_once plugin_dir_path(__FILE__) . '../includes/admin-list-forms-liaisons-cf7.php';
			
			$post_id = !empty($_GET["post_id"]) ? esc_attr($_GET["post_id"]) : "";
			
			if(!empty($post_id)) {
				$dataDbForm = Database::getFormCf7FromDb(sanitize_text_field($post_id));
				$dataForm = get_post_meta(sanitize_text_field($post_id));
				
				// Récupération shortcode
				foreach($dataForm as $key => $value) {
					if($key == '_form') {
						$shortcodes = Tools::extractShortCode($value[0]);
					}
				}
				
				$shortcodesFichiersLies = preg_grep("/^(\[.*file\ .+\]$)/mi", $shortcodes);
				$shortcodes = array_diff($shortcodes, $shortcodesFichiersLies);
				
				$ws = new Webservice();
				
				// Traitement pour action
				$dataActions = $ws->getDataTable("actions");
				$requireFieldsActions = $ws->getRequiredFields("actions");
				
				$typesaction = $ws->getTypesAction();
				$vendeurs = $ws->getVendeurs();
				$groupements = $ws->getGroupements();
				// Fin traitement action
				
				// Traitement Contact
				$dataContact = $ws->getDataTable("contactfiche");
				$requireFieldsContact = $ws->getRequiredFields("contactfiche");
				// Fin traitement contact
				
				// Traitement Interlocuteur
				$dataInterlocuteur = $ws->getDataTable("interlocuteurfiche");
				$requireFieldsInterlocuteur = $ws->getRequiredFields("interlocuteurfiche");
				// Fin traitement interlocuteur
				
				// Traitement Affaire
				$dataAffaire = $ws->getDataTable("affaire");
				$requireFieldsAffaire = $ws->getRequiredFields("affaire");
				// Fin traitement Affaire
				
				// Traitement Produit
				$dataProduit = $ws->getDataTable("produitfiche");
				$requireFieldsProduit = $ws->getRequiredFields("produitfiche");
				// Fin traitement Produit
				
				// Traitement Ticket
				$dataTicket = $ws->getDataTable("ticket");
				$requireFieldsTicket = $ws->getRequiredFields("ticket");
				
				$ticketStatus = $ws->getTicketStatus();
				$ticketTypes = $ws->getTicketTypes();
				$ticketCategories = $ws->getTicketCategories();
				$ticketFrequences = $ws->getTicketFrequences();
				$ticketPlateformes = $ws->getTicketPlateformes();
				$ticketPriorites = $ws->getTicketPriorites();
				$ticketSeverites = $ws->getTicketSeverites();
				$ticketSources = $ws->getTicketSources();
				$ticketVersions = $ws->getTicketVersions();
				$ticketModules = $ws->getTicketModules();
				// Fin traitement Ticket
				
				// Traitement Contrat
				$dataContrat = $ws->getDataTable("contrat");
				$requireFieldsContrat = $ws->getRequiredFields("contrat");
				
				$contratEtats = $ws->getContatEtats();
				$contratTypes = $ws->getContatTypes();
				$contratPeriodicites = $ws->getContatPeriodicites();
				$contratFacturations = $ws->getContatFacturations();
				$contratModesReconduction = $ws->getContatModesReconduction();
				// Fin traitement Contrat
				
				// Traitement Historique
				$dataHistorique = $ws->getDataTable("historique");
				$requireFieldsHistorique = $ws->getRequiredFields("historique");
				// Fin traitement Historique
				
				// Traitement pour inventaires
				$dataInventaires = $ws->getDataTable("inventaireproduit");
				$requireFieldsInventaires = $ws->getRequiredFields("inventaireproduit");
				
				// Traitement Labels tables
				$libellesTables = array_change_key_case($ws->getTableLibelle(), CASE_UPPER);
				// FIN traitement labels
				
				require_once plugin_dir_path(__FILE__) . '../includes/admin-forms-liaisons-cf7.php';
			}
		}
		
		public function infocob_filter(&$value) {
			// #####################################
			// #####################################
			
			// ########################################
			// 				Fix ut8 char
			// ########################################
			
			$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
			if(is_string($value)) {
				$value = sanitize_text_field($value);
			} else if(is_int($value)) {
				$value = (int)$value;
			} else if(is_bool($value)) {
				$value = (bool)$value;
			}
			/*$value = stripslashes_deep($value);
			$value = htmlentities($value, "UTF-8");*/
		}
		
		public function saveLiaisons() {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			if(!empty($_POST) && check_admin_referer('infocob_save_form_liaisons_nonce', 'nonce') && current_user_can(Caps::$edit_forms)) {
				// Sanitize $_POST
				$_POST = Tools::sanitize_fields($_POST);
				
				$form_type = $_POST["form_type"];
				$idPostContact = (int)$_POST["post_id"];
				$data = array();
				$pivot = array();
				$maj = array();
				$fichiersLies = array();
				$cloudFichiers = array();
				$tables = array();
				$sendMail = $_POST['sendmail'];
				
				// Create array for DATA of form
				if(isset($_POST['data'])) {
					foreach($_POST['data'] as $key => $value) {
						if($key == 'action') {
							foreach($_POST['data'][$key] as $form_action => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_action] = $value;
								}
							}
						} elseif($key == 'inventaire-action') {
							foreach($_POST['data'][$key] as $form_action => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_action] = $value;
								}
							}
						} elseif($key == 'contact') {
							foreach($_POST['data'][$key] as $form_contact => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_contact] = $value;
								}
							}
						} elseif($key == 'inventaire-contact') {
							foreach($_POST['data'][$key] as $form_contact => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_contact] = $value;
								}
							}
						} elseif($key == 'interlocuteur') {
							foreach($_POST['data'][$key] as $form_interlocuteur => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_interlocuteur] = $value;
								}
							}
						} elseif($key == 'inventaire-interlocuteur') {
							foreach($_POST['data'][$key] as $form_interlocuteur => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_interlocuteur] = $value;
								}
							}
						} elseif($key == 'affaire') {
							foreach($_POST['data'][$key] as $form_affaire => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_affaire] = $value;
								}
							}
						} elseif($key == 'inventaire-affaire') {
							foreach($_POST['data'][$key] as $form_affaire => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_affaire] = $value;
								}
							}
						} elseif($key == 'produit') {
							foreach($_POST['data'][$key] as $form_produit => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_produit] = $value;
								}
							}
						} elseif($key == 'inventaire-produit') {
							foreach($_POST['data'][$key] as $form_produit => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_produit] = $value;
								}
							}
						} elseif($key == 'ticket') {
							foreach($_POST['data'][$key] as $form_ticket => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_ticket] = $value;
								}
							}
						} elseif($key == 'inventaire-ticket') {
							foreach($_POST['data'][$key] as $form_ticket => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_ticket] = $value;
								}
							}
						} elseif($key == 'contrat') {
							foreach($_POST['data'][$key] as $form_contrat => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_contrat] = $value;
								}
							}
						} elseif($key == 'inventaire-contrat') {
							foreach($_POST['data'][$key] as $form_contrat => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_contrat] = $value;
								}
							}
						} elseif($key == 'historique') {
							foreach($_POST['data'][$key] as $form_historique => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_historique] = $value;
								}
							}
						} elseif($key == 'inventaire-historique') {
							foreach($_POST['data'][$key] as $form_historique => $value) {
								if($value != null || $value != "") {
									$data[$key][$form_historique] = $value;
								}
							}
						}
					}
				}
				
				//Create array for PIVOT of form
				if(isset($_POST['pivot'])) {
					foreach($_POST['pivot'] as $key => $value) {
						if($key == 'action') {
							foreach($_POST['pivot'][$key] as $pivot_action => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_action] = $value;
								}
							}
						} elseif($key == 'inventaire-action') {
							foreach($_POST['pivot'][$key] as $pivot_action => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_action] = $value;
								}
							}
						} elseif($key == 'contact') {
							foreach($_POST['pivot'][$key] as $pivot_contact => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_contact] = $value;
								}
							}
						} elseif($key == 'inventaire-contact') {
							foreach($_POST['pivot'][$key] as $pivot_contact => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_contact] = $value;
								}
							}
						} elseif($key == 'interlocuteur') {
							foreach($_POST['pivot'][$key] as $pivot_interlocuteur => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_interlocuteur] = $value;
								}
							}
						} elseif($key == 'inventaire-interlocuteur') {
							foreach($_POST['pivot'][$key] as $pivot_interlocuteur => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_interlocuteur] = $value;
								}
							}
						} elseif($key == 'affaire') {
							foreach($_POST['pivot'][$key] as $pivot_affaire => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_affaire] = $value;
								}
							}
						} elseif($key == 'inventaire-affaire') {
							foreach($_POST['pivot'][$key] as $pivot_affaire => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_affaire] = $value;
								}
							}
						} elseif($key == 'produit') {
							foreach($_POST['pivot'][$key] as $pivot_produit => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_produit] = $value;
								}
							}
						} elseif($key == 'inventaire-produit') {
							foreach($_POST['pivot'][$key] as $pivot_produit => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_produit] = $value;
								}
							}
						} elseif($key == 'ticket') {
							foreach($_POST['pivot'][$key] as $pivot_ticket => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_ticket] = $value;
								}
							}
						} elseif($key == 'inventaire-ticket') {
							foreach($_POST['pivot'][$key] as $pivot_ticket => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_ticket] = $value;
								}
							}
						} elseif($key == 'contrat') {
							foreach($_POST['pivot'][$key] as $pivot_contrat => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_contrat] = $value;
								}
							}
						} elseif($key == 'inventaire-contrat') {
							foreach($_POST['pivot'][$key] as $pivot_contrat => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_contrat] = $value;
								}
							}
						} elseif($key == 'historique') {
							foreach($_POST['pivot'][$key] as $pivot_historique => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_historique] = $value;
								}
							}
						} elseif($key == 'inventaire-historique') {
							foreach($_POST['pivot'][$key] as $pivot_historique => $value) {
								if($value != null || $value != "") {
									$pivot[$key][$pivot_historique] = $value;
								}
							}
						}
					}
				}
				
				//Create array for MAJ of form
				if(isset($_POST['maj'])) {
					foreach($_POST['maj'] as $key => $value) {
						if($key == 'action') {
							foreach($_POST['maj'][$key] as $maj_action => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_action] = $value;
								}
							}
						} elseif($key == 'inventaire-action') {
							foreach($_POST['maj'][$key] as $maj_action => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_action] = $value;
								}
							}
						} elseif($key == 'contact') {
							foreach($_POST['maj'][$key] as $maj_contact => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_contact] = $value;
								}
							}
						} elseif($key == 'inventaire-contact') {
							foreach($_POST['maj'][$key] as $maj_contact => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_contact] = $value;
								}
							}
						} elseif($key == 'interlocuteur') {
							foreach($_POST['maj'][$key] as $maj_interlocuteur => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_interlocuteur] = $value;
								}
							}
						} elseif($key == 'inventaire-interlocuteur') {
							foreach($_POST['maj'][$key] as $maj_interlocuteur => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_interlocuteur] = $value;
								}
							}
						} elseif($key == 'affaire') {
							foreach($_POST['maj'][$key] as $maj_affaire => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_affaire] = $value;
								}
							}
						} elseif($key == 'inventaire-affaire') {
							foreach($_POST['maj'][$key] as $maj_affaire => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_affaire] = $value;
								}
							}
						} elseif($key == 'produit') {
							foreach($_POST['maj'][$key] as $maj_produit => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_produit] = $value;
								}
							}
						} elseif($key == 'inventaire-produit') {
							foreach($_POST['maj'][$key] as $maj_produit => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_produit] = $value;
								}
							}
						} elseif($key == 'ticket') {
							foreach($_POST['maj'][$key] as $maj_ticket => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_ticket] = $value;
								}
							}
						} elseif($key == 'inventaire-ticket') {
							foreach($_POST['maj'][$key] as $maj_ticket => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_ticket] = $value;
								}
							}
						} elseif($key == 'contrat') {
							foreach($_POST['maj'][$key] as $maj_contrat => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_contrat] = $value;
								}
							}
						} elseif($key == 'inventaire-contrat') {
							foreach($_POST['maj'][$key] as $maj_contrat => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_contrat] = $value;
								}
							}
						} elseif($key == 'historique') {
							foreach($_POST['maj'][$key] as $maj_historique => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_historique] = $value;
								}
							}
						} elseif($key == 'inventaire-historique') {
							foreach($_POST['maj'][$key] as $maj_historique => $value) {
								if($value != null || $value != "") {
									$maj[$key][$maj_historique] = $value;
								}
							}
						}
					}
				}
				
				//Create array for FichiersLies of form
				if(isset($_POST['fichiersLies'])) {
					foreach($_POST['fichiersLies'] as $key => $value) {
						if($key == 'action') {
							foreach($_POST['fichiersLies'][$key] as $fichiersLies_action => $value) {
								if($value != null || $value != "") {
									$fichiersLies[$key][$fichiersLies_action] = $value;
								}
							}
						} elseif($key == 'affaire') {
							foreach($_POST['fichiersLies'][$key] as $fichiersLies_affaire => $value) {
								if($value != null || $value != "") {
									$fichiersLies[$key][$fichiersLies_affaire] = $value;
								}
							}
						} elseif($key == 'produit') {
							foreach($_POST['fichiersLies'][$key] as $fichiersLies_produit => $value) {
								if($value != null || $value != "") {
									$fichiersLies[$key][$fichiersLies_produit] = $value;
								}
							}
						} elseif($key == 'ticket') {
							foreach($_POST['fichiersLies'][$key] as $fichiersLies_ticket => $value) {
								if($value != null || $value != "") {
									$fichiersLies[$key][$fichiersLies_ticket] = $value;
								}
							}
						} elseif($key == 'contrat') {
							foreach($_POST['fichiersLies'][$key] as $fichiersLies_contrat => $value) {
								if($value != null || $value != "") {
									$fichiersLies[$key][$fichiersLies_contrat] = $value;
								}
							}
						}
					}
				}
				
				//Create array for CloudFichiers of form
				if(isset($_POST['cloudFichiers'])) {
					foreach($_POST['cloudFichiers'] as $key => $value) {
						if($key == 'action') {
							foreach($_POST['cloudFichiers'][$key] as $cloudFichiers_action => $value) {
								if($value != null || $value != "") {
									$cloudFichiers[$key][$cloudFichiers_action] = $value;
								}
							}
						} elseif($key == 'affaire') {
							foreach($_POST['cloudFichiers'][$key] as $cloudFichiers_affaire => $value) {
								if($value != null || $value != "") {
									$cloudFichiers[$key][$cloudFichiers_affaire] = $value;
								}
							}
						} elseif($key == 'produit') {
							foreach($_POST['cloudFichiers'][$key] as $cloudFichiers_produit => $value) {
								if($value != null || $value != "") {
									$cloudFichiers[$key][$cloudFichiers_produit] = $value;
								}
							}
						} elseif($key == 'ticket') {
							foreach($_POST['cloudFichiers'][$key] as $cloudFichiers_ticket => $value) {
								if($value != null || $value != "") {
									$cloudFichiers[$key][$cloudFichiers_ticket] = $value;
								}
							}
						} elseif($key == 'contrat') {
							foreach($_POST['cloudFichiers'][$key] as $cloudFichiers_contrat => $value) {
								if($value != null || $value != "") {
									$cloudFichiers[$key][$cloudFichiers_contrat] = $value;
								}
							}
						}
					}
				}
				
				//Create array for TABLES of form
				
				foreach($_POST as $key => $value) {
					if($key == 'tables_action') {
						$tables['action'] = $value;
					}
					if($key == 'tables_inventaire_action') {
						$tables['inventaire-action'] = ($value == "on") ? "true" : "false";
					} else if($key == 'tables_contact') {
						$tables['contact'] = $value;
					}
					if($key == 'tables_inventaire_contact') {
						$tables['inventaire-contact'] = ($value == "on") ? "true" : "false";
					} else if($key == 'tables_interlocuteur') {
						$tables['interlocuteur'] = $value;
					} else if($key == 'tables_inventaire_interlocuteur') {
						$tables['inventaire-interlocuteur'] = ($value == "on") ? "true" : "false";
					} else if($key == 'tables_affaire') {
						$tables['affaire'] = $value;
					} else if($key == 'tables_inventaire_affaire') {
						$tables['inventaire-affaire'] = ($value == "on") ? "true" : "false";
					} else if($key == 'tables_produit') {
						$tables['produit'] = $value;
					} else if($key == 'tables_inventaire_produit') {
						$tables['inventaire-produit'] = ($value == "on") ? "true" : "false";
					} else if($key == 'tables_ticket') {
						$tables['ticket'] = $value;
					} else if($key == 'tables_inventaire_ticket') {
						$tables['inventaire-ticket'] = ($value == "on") ? "true" : "false";
					} else if($key == 'tables_contrat') {
						$tables['contrat'] = $value;
					} else if($key == 'tables_inventaire_contrat') {
						$tables['inventaire-contrat'] = ($value == "on") ? "true" : "false";
					} else if($key == 'tables_historique') {
						$tables['historique'] = $value;
					} else if($key == 'tables_inventaire_historique') {
						$tables['inventaire-historique'] = ($value == "on") ? "true" : "false";
					}
				}
				
				// #####################################
				// 		SET FORM TO NOT SEND EMAIL
				// #####################################
				
				if(strcasecmp($form_type, 'cf7') === 0) {
					FormSubmission::skipMailCf7($idPostContact, ($sendMail == "true") ? false : true);
				}
				
				array_walk_recursive($data, [$this, "infocob_filter"]);
				array_walk_recursive($pivot, [$this, "infocob_filter"]);
				array_walk_recursive($maj, [$this, "infocob_filter"]);
				array_walk_recursive($fichiersLies, [$this, "infocob_filter"]);
				array_walk_recursive($cloudFichiers, [$this, "infocob_filter"]);
				array_walk_recursive($tables, [$this, "infocob_filter"]);
				
				// ########################################
				// ########################################
				
				$dataJson = wp_json_encode($data);
				$pivotJson = wp_json_encode($pivot);
				$majJson = wp_json_encode($maj);
				$fichiersLiesJson = wp_json_encode($fichiersLies);
				$cloudFichiersJson = wp_json_encode($cloudFichiers);
				$tablesJson = wp_json_encode($tables);
				
				/*
				*	Insert in DB
				*/
				global $wpdb;
				if(strcasecmp($form_type, 'cf7') === 0) {
					$table_name = 'infocob_form_cf7';
				} else if(strcasecmp($form_type, 'ifb') === 0) {
					$table_name = 'infocob_form_ifb';
				}
				
				$select = $wpdb->get_row("SELECT idPostContactForm FROM $table_name WHERE idPostContactForm = $idPostContact");
				
				if($sendMail == "true") {
					$sendMail = true;
				} elseif($sendMail == "false") {
					$sendMail = false;
				}
				
				if(is_null($select)) {
					$wpdb->insert(sanitize_text_field($table_name), array(
						"idPostContactForm" => $idPostContact,
						"fieldAssoc" => $dataJson,
						"pivot" => $pivotJson,
						"maj" => $majJson,
						"fichiersLies" => $fichiersLiesJson,
						"cloudFichiers" => $cloudFichiersJson,
						"tables" => $tablesJson,
						"sendmail" => (boolean)$sendMail
					), array(
						"%d",
						"%s",
						"%s",
						"%s",
						"%s",
						"%s",
						"%s",
						"%d"
					));
				} else {
					$wpdb->update(sanitize_text_field($table_name), array(
						"idPostContactForm" => $idPostContact,
						"fieldAssoc" => $dataJson,
						"pivot" => $pivotJson,
						"maj" => $majJson,
						"fichiersLies" => $fichiersLiesJson,
						"cloudFichiers" => $cloudFichiersJson,
						"tables" => $tablesJson,
						"sendmail" => (boolean)$sendMail
					), array(
						"idPostContactForm" => $idPostContact
					), array(
						"%d",
						"%s",
						"%s",
						"%s",
						"%s",
						"%s",
						"%s",
						"%d"
					), array(
						"%d"
					));
				}
				
				wp_redirect(wp_get_referer());
			}
		}
		
		function delete_data_form_liaisons() {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			if(!empty($_POST) && check_admin_referer('delete_data_form_liaisons_nonce', 'nonce') && current_user_can(Caps::$edit_forms)) {
				
				if(!$_POST["postId"]) {
					die();
				}
				
				$delete = false;
				if(!empty($_POST["form_type"])) {
					$form_type = sanitize_text_field($_POST["form_type"]);
					if(strcasecmp($form_type, "cf7") === 0) {
						$delete = Database::deleteFormCf7(sanitize_text_field($_POST["postId"]));
					} else if(strcasecmp($form_type, "ifb") === 0) {
						$delete = Database::deleteFormIfb(sanitize_text_field($_POST["postId"]));
					}
				}
				
				if($delete !== false) {
					_e("The data has been deleted", "infocob-crm-forms");
				} else {
					_e("An error occured while removing the data", "infocob-crm-forms");
				}
			}
			
			die();
		}
	}

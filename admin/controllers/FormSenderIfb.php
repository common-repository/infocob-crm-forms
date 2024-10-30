<?php
	
	namespace Infocob\CrmForms\Admin;
	
	use PHPMailer\PHPMailer\PHPMailer;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FormSenderIfb {
		
		/**
		 * @var Form
		 */
		protected $form;
		protected $_donnees_formulaires = [];
		protected $redirect_url = "";
		protected $max_file_size = 2 * 1024 * 1024;
		protected $send_mail = true;
		protected $email_template = "defaut";
		protected $display_popup_message = true;
		
		protected static $session_key = "infocob_crm_forms";
		protected static $is_session_vars_initied = false;
		protected static $is_message_sent = null;
		protected static $return_message = "";
		protected static $submit_form = false;
		
		protected $title;
		protected $subtitle;
		protected $color;
		protected $color_text_title;
		protected $color_link;
		protected $logo;
		protected $societe;
		protected $border_radius;
		
		public function __construct($redirect_url) {
			$this->redirect_url = $redirect_url;
		}
		
		/**
		 * @param Form $form
		 */
		public function setForm(Form $form): void {
			$this->form = $form;
			
			$admin_form_edit_json = get_post_meta($this->form->getId(), 'infocob_crm_forms_admin_form_config', true);
			$admin_form_edit = json_decode($admin_form_edit_json, true);
			
			$admin_form_edit_email_json = get_post_meta($this->form->getId(), 'infocob_crm_forms_admin_form_email_config', true);
			$admin_form_edit_email = json_decode($admin_form_edit_email_json, true);
			
			if(!empty($admin_form_edit["max_file_size"])) {
				$this->max_file_size = $admin_form_edit["max_file_size"];
			}
			
			if(!empty($admin_form_edit["redirect_page_submit"]) && !empty(get_permalink($admin_form_edit["redirect_page_submit"]))) {
				$this->display_popup_message = false;
				$this->redirect_url = get_permalink($admin_form_edit["redirect_page_submit"]);
			}
			
			if(!empty($admin_form_edit_email["email_title"])) {
				$this->title = $admin_form_edit_email["email_title"];
			} else {
				$this->title = get_bloginfo("name") . " - Formulaire de contact";
			}
			
			if(!empty($admin_form_edit_email["email_subtitle"])) {
				$this->subtitle = $admin_form_edit_email["email_subtitle"];
			}
			
			if(!empty($admin_form_edit_email["email_societe"])) {
				$this->societe = $admin_form_edit_email["email_societe"];
			} else {
				$this->societe = get_bloginfo("name");
			}
			
			if(!empty($admin_form_edit_email["email_border_radius"])) {
				$this->border_radius = $admin_form_edit_email["email_border_radius"];
			} else {
				$this->border_radius = 0;
			}
			
			if(!empty($admin_form_edit_email["email_color"])) {
				$this->color = $admin_form_edit_email["email_color"];
			} else {
				$this->color = "#0271b8";
			}
			
			if(!empty($admin_form_edit_email["email_color_text_title"])) {
				$this->color_text_title = $admin_form_edit_email["email_color_text_title"];
			} else {
				$this->color_text_title = "#ffffff";
			}
			
			if(!empty($admin_form_edit_email["email_color_link"])) {
				$this->color_link = $admin_form_edit_email["email_color_link"];
			} else {
				$this->color_link = "#0271b8";
			}
			
			if(!empty($admin_form_edit_email["email_logo"])) {
				$attachment_id = $admin_form_edit_email["email_logo"]["attachment_id"] ?? false;
				$size = $admin_form_edit_email["email_logo"]["size"] ?? "";
				if($attachment_id) {
					$image = wp_get_attachment_image_src($attachment_id, $size);
					$this->logo = $image[0] ?? "";
				}
			} else {
				$this->logo = "";
			}
			
			if(!empty($admin_form_edit_email["email_template"])) {
				$this->email_template = $admin_form_edit_email["email_template"];
			} else {
				$this->email_template = "defaut-infocob-crm-forms";
			}
			
			$dataForm = Database::getFormIfbFromDb($this->form->getId());
			if((!empty($dataForm["sendmail"]) && $dataForm["sendmail"]) || empty($dataForm)) {
				$this->send_mail = true;
			} else {
				$this->send_mail = false;
			}
		}
		
		public function process() {
			if(!empty($_POST["infocob-crm-forms-id"])) {
				$this->processFormulaire();
			}
		}
		
		protected function registerAndRedirect($form_sent = false, $message = "") {
			if($this->display_popup_message || !$form_sent) {
				$_SESSION[static::$session_key . "_is_message_sent"] = $form_sent;
				$_SESSION[static::$session_key . "_message_form_sent"] = $message;
				$_SESSION[static::$session_key . "_submit_form"] = $this->form->getId();
			}
			
			if($form_sent) {
				do_action("infocob-crm-forms_process_form_ifb", ["url" => $this->redirect_url]);
			} else {
				$current_url = !empty($_POST["current_url"]) ? esc_url($_POST["current_url"]) : home_url();
				
				do_action('infocob_forms_after');
				header("Location: " . $current_url);
				die();
			}
		}
		
		public function processFormulaire() {
			if(!empty($_POST["infocob-crm-forms-id"])) {
				if(!$this->form->isDisableRgpd()) {
					if (!isset($_POST['accept-rgpd'])) {
						$this->registerAndRedirect(false, icf_translate_string("Politique de confidentialité non accepté.", FormSubmission::$polylang_lang));
					}
				}
				
				$recaptcha_valid = true;
				
				// Google Recaptcha V3
				$options = get_option('infocob_crm_forms_settings');
				$google_recaptcha_v3_enabled = $options['google_recaptcha_v3']['enabled'] ?? false;
				if($google_recaptcha_v3_enabled) {
					$recaptcha_valid = Tools::validate_google_recaptcha_v3($_POST["recaptcha_token"] ?? false);
				}
				
				// hCaptcha
				$options = get_option('infocob_crm_forms_settings');
				$hcaptcha_enabled = $options['hcaptcha']['enabled'] ?? false;
				if($hcaptcha_enabled) {
					$recaptcha_valid = Tools::validate_hCaptcha($_POST["h-captcha-response"] ?? false);
				}
				
				if(isset($_POST['winnie']) && $_POST['winnie'] == "" && $recaptcha_valid) {
					//-- gestion des données
					$this->extractDatasFromPost();
					$this->checkDatasRequired();
					
					$tpl = $this->getTemplateEmail();
					
					do_action('infocob-crm-forms-sending', [
						"files" => $_FILES,
						"form-data" => $_POST,
					]);
					
					if($this->send_mail) {
						if($this->sendMail($tpl)) {
							$admin_form_edit_email_json = get_post_meta($this->form->getId(), 'infocob_crm_forms_admin_form_additional_email_config', true);
							$additional_email_config = json_decode($admin_form_edit_email_json, true);
							
							if(is_array($additional_email_config)) {
								$options = get_option('infocob_crm_forms_settings');
								$options_additional_email = isset($options['additional_email_max_number']) ? abs((int)$options['additional_email_max_number']) : 1;
								for($i = 0; $i < $options_additional_email; $i++) {
									$additionalEmail = new AdditionalEmail($additional_email_config[$i]);
									$additionalEmail->setForm($this->form);
									$additionalEmail->sendAdditionalEmail();
								}
							}
							
							$this->registerAndRedirect(true, icf_translate_string("Merci, votre message a bien été envoyé.", FormSubmission::$polylang_lang));
							
							return true;
						} else {
							$this->registerAndRedirect(false, icf_translate_string("Une erreur est survenue lors de l'envoi du message. Merci de réessayer plus tard.", FormSubmission::$polylang_lang));
						}
					} else {
						$this->registerAndRedirect(true, icf_translate_string("Merci, votre message a bien été envoyé.", FormSubmission::$polylang_lang));
						
						return true;
					}
				} else {
					$this->registerAndRedirect(false, icf_translate_string("Anti-robot non validé.", FormSubmission::$polylang_lang));
				}
			}
			
			return false;
		}
		
		public function extractDatasFromPost($return = false) {
			$this->_donnees_formulaires = [];
			$champs = $this->form->getFieldsGroups();
			
			foreach($champs as $champ) {
				if($champ->getType() == "groupe") {
					foreach($champ->getChamps() as $sub_champ) {
						
						if($sub_champ->getType() == "select" && $sub_champ->isRecipients()) {
							if($this->form->isRecipientsEnabled()) {
								$destinataires_post_id = $this->getValue($sub_champ);
								
								if(!empty($destinataires_post_id)) {
									$formatDest = [];
									
									if($sub_champ->isMultiple()) {
										foreach($destinataires_post_id as $post_id) {
											$post_metas = get_post_meta($post_id, 'infocob_crm_forms_admin_recipients_config', true);
											foreach($post_metas["recipients"] as $post_meta) {
												$formatDest[$post_meta["email"]] = [
													"firstname" => $post_meta["firstname"] ?? "",
													"lastname" => $post_meta["lastname"] ?? "",
													"cc" => $post_meta["cc"] ?? "",
													"bcc" => $post_meta["bcc"] ?? "",
												];
											}
										}
										
										$this->form->addDestinataires($formatDest);
									} else {
										$post_metas = get_post_meta($destinataires_post_id, 'infocob_crm_forms_admin_recipients_config', true);
										foreach($post_metas["recipients"] as $post_meta) {
											$formatDest[$post_meta["email"]] = [
												"firstname" => $post_meta["firstname"] ?? "",
												"lastname" => $post_meta["lastname"] ?? "",
												"cc" => $post_meta["cc"] ?? "",
												"bcc" => $post_meta["bcc"] ?? "",
											];
										}
										$this->form->addDestinataires($formatDest);
									}
								}
							}
						}
						
						if($champ->getType() !== "textarea") {
							$this->_donnees_formulaires[$sub_champ->getNom()] = Tools::sanitize_fields($this->getValue($sub_champ), true);
						} else {
							$this->_donnees_formulaires[$sub_champ->getNom()] = $this->getValue($sub_champ);
						}
					}
				} else {
					
					if($champ->getType() == "select" && $champ->isRecipients()) {
						if($this->form->isRecipientsEnabled()) {
							$destinataires_post_id = $this->getValue($champ);
							
							if(!empty($destinataires_post_id)) {
								$formatDest = [];
								
								if($champ->isMultiple()) {
									foreach($destinataires_post_id as $post_id) {
										$post_metas = get_post_meta($post_id, 'infocob_crm_forms_admin_recipients_config', true);
										foreach($post_metas["recipients"] as $post_meta) {
											$formatDest[$post_meta["email"]] = [
												"firstname" => $post_meta["firstname"] ?? "",
												"lastname" => $post_meta["lastname"] ?? "",
												"cc" => $post_meta["cc"] ?? "",
												"bcc" => $post_meta["bcc"] ?? "",
											];
										}
									}
									
									$this->form->addDestinataires($formatDest);
								} else {
									$post_metas = get_post_meta($destinataires_post_id, 'infocob_crm_forms_admin_recipients_config', true);
									foreach($post_metas["recipients"] as $post_meta) {
										$formatDest[$post_meta["email"]] = [
											"firstname" => $post_meta["firstname"] ?? "",
											"lastname" => $post_meta["lastname"] ?? "",
											"cc" => $post_meta["cc"] ?? "",
											"bcc" => $post_meta["bcc"] ?? "",
										];
									}
									$this->form->addDestinataires($formatDest);
								}
							}
						}
					}
					
					if($champ->getType() !== "textarea") {
						$this->_donnees_formulaires[$champ->getNom()] = Tools::sanitize_fields($this->getValue($champ), true);
					} else {
						$this->_donnees_formulaires[$champ->getNom()] = $this->getValue($champ);
					}
				}
			}
			$this->_donnees_formulaires["page_form"] = isset($_POST["page_form"]) ? $_POST["page_form"] : "";
			
			if($return) {
				return $this->_donnees_formulaires;
			}
		}
		
		protected function getValue(Field $champ) {
			$value = isset($_POST[$champ->getNom()]) ? $_POST[$champ->getNom()] : "";
			if($champ->getType() === "checkbox") {
				if($champ->isInvert()) {
					$value = isset($_POST[$champ->getNom()]) ? "NON" : "OUI";
				} else {
					$value = isset($_POST[$champ->getNom()]) ? "OUI" : "NON";
				}
			}
			
			if(isset($_POST[$champ->getNom()]) && $_POST[$champ->getNom()] === "" && !empty($champ->getDefautPost())) {
				$value = $champ->getDefautPost();
			}
			
			return $value;
		}
		
		protected function checkDatasRequired() {
			$champs = $this->form->getFieldsGroups();
			
			foreach($champs as $champ) {
				if($champ->getType() == "groupe") {
					foreach($champ->getChamps() as $sub_champ) {
						if($sub_champ->isRequired() && empty($this->_donnees_formulaires[$sub_champ->getNom()]) && $sub_champ->getType() != "file") {
							$this->registerAndRedirect(false, icf_translate_string("Tous les champs obligatoires doivent être complétés.", FormSubmission::$polylang_lang));
						}
					}
				} else {
					if($champ->isRequired() && empty($this->_donnees_formulaires[$champ->getNom()]) && $champ->getType() != "file") {
						$this->registerAndRedirect(false, icf_translate_string("Tous les champs obligatoires doivent être complétés.", FormSubmission::$polylang_lang));
					}
				}
			}
		}
		
		public function getTemplateEmail() {
			$tpl = new TemplateMail($this->email_template, $this->form, $this->_donnees_formulaires);
			$tpl->setTitle($this->title);
			$tpl->setSubtitle($this->subtitle);
			$tpl->setColor($this->color);
			$tpl->setColorTextTitle($this->color_text_title);
			$tpl->setColorLink($this->color_link);
			$tpl->setSociete($this->societe);
			$tpl->setBorderRadius($this->border_radius);
			$tpl->setLogo($this->logo);
			
			return $tpl;
		}
		
		protected function sendMail($tpl) {
			$this->includePHPMailer();
			
			$mail = new PHPMailer(true);
			
			$user_reply = false;
			if(!empty($this->form->getReply()["email"])) {
				$email = $this->_donnees_formulaires[$this->form->getReply()["email"]] ?? "";
				$nom = "";
				$prenom = "";
				
				$form_nom = $this->form->getReply()["lastname"] ?? "";
				$form_prenom = $this->form->getReply()["firstname"] ?? "";
				
				if(!empty($form_nom)) {
					$nom = $this->_donnees_formulaires[$form_nom] ?? "";
				}
				if(!empty($form_prenom)) {
					$prenom = $this->_donnees_formulaires[$form_prenom] ?? "";
				}
				if(!empty($email)) {
					$mail->AddReplyTo($email, htmlspecialchars_decode(trim($prenom . " " . $nom)));
					$user_reply = true;
				}
			}
			
			if(!$user_reply) {
				$mail->AddReplyTo($this->form->getExpediteur(), htmlspecialchars_decode(get_bloginfo("name")));
			}
			$mail->SetFrom($this->form->getExpediteur(), htmlspecialchars_decode(get_bloginfo("name")));
			
			$mail->setLanguage('fr');
			$mail->CharSet = 'UTF-8';
			
			try {
				// SMTP
				$options = get_option('infocob_crm_forms_settings');
				$smtp_enabled = $options['smtp']['enabled'] ?? false;
				if($smtp_enabled) {
					$smtp_host = $options['smtp']['host'] ?? "";
					$smtp_username = $options['smtp']['username'] ?? "";
					$smtp_password = $options['smtp']['password'] ?? "";
					$smtp_port = $options['smtp']['port'] ?? "";
					
					//$mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
					$mail->isSMTP();          //Send using SMTP
					$mail->Host = $smtp_host; //Set the SMTP server to send through
					if(!empty($smtp_username)) {
						$mail->SMTPAuth = true;           //Enable SMTP authentication
						$mail->Username = $smtp_username; //SMTP username
						$mail->Password = $smtp_password; //SMTP password
					}
					$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
					$mail->Port = $smtp_port;                        //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
				}
				
				$destinataires = $this->form->getDestinataires();
				$destinataires = apply_filters('infocob-crm-forms_destinataires', $destinataires, $this->form->isRecipientsEnabled(), $this->_donnees_formulaires);
				
				if(empty($destinataires)) {
					$this->registerAndRedirect(false, icf_translate_string("Aucun destinataire disponible.", FormSubmission::$polylang_lang));
				}
				
				if($this->form->isRecipientsEnabled()) {
					foreach($destinataires as $email => $destinataire) {
						$firstname = $destinataire["firstname"] ?? "";
						$lastname = $destinataire["lastname"] ?? "";
						$fullname = mb_convert_encoding(trim($firstname . " " . $lastname), 'ISO-8859-1', 'UTF-8');
						
						if(filter_var($destinataire["cc"], FILTER_VALIDATE_BOOLEAN)) {
							$mail->addCC($email, $fullname);
						} else if(filter_var($destinataire["bcc"], FILTER_VALIDATE_BOOLEAN)) {
							$mail->addBCC($email, $fullname);
						} else {
							$mail->AddAddress($email, $fullname);
						}
					}
				} else {
					$i = 0;
					foreach($destinataires as $emailAdd => $nom) {
						if(!$i) {
							$mail->AddAddress($emailAdd, mb_convert_encoding($nom, 'ISO-8859-1', 'UTF-8'));
						} else {
							$mail->addCC($emailAdd, mb_convert_encoding($nom, 'ISO-8859-1', 'UTF-8'));
						}
						$i++;
					}
				}
				
				if(INFOCOB_CRM_FORMS_COPY_EMAIL && !$smtp_enabled) {
					$mail->addBCC("formulaires@infocob-solutions.com", icf_translate_string("Copie Infocob", FormSubmission::$polylang_lang));
				}
				
				$mail->Subject = Tools::setFieldFromForm($this->form->getObjet(), $this->_donnees_formulaires);
				
				if(!empty($_FILES)) {
					$fileMaxSize = $this->max_file_size;
					
					$files_added = [];
					foreach($_FILES as $key => $file) {
						if(!empty($file["size"])) {
							foreach($this->form->getFieldsGroups() as $champ) {
								if($champ->getType() == "groupe") {
									foreach($champ->getChamps() as $sub_champ) {
										if($sub_champ && $sub_champ->getType() === "file" && isset($_FILES[$sub_champ->getNom()]["size"])) {
											if($sub_champ->isMultiple() && is_array($_FILES[$sub_champ->getNom()]["size"])) {
												$nb_files = count($_FILES[$sub_champ->getNom()]["size"]);
												for($i = 0; $i < $nb_files; $i++) {
													if($_FILES[$sub_champ->getNom()]['error'][$i] == UPLOAD_ERR_OK && $_FILES[$sub_champ->getNom()]['size'][$i] < $fileMaxSize) {
														$finfo = finfo_open(FILEINFO_MIME_TYPE);
														$mimetype = finfo_file($finfo, $_FILES[$sub_champ->getNom()]['tmp_name'][$i]);
														
														if(in_array($mimetype, $sub_champ->getAccept()) || empty($sub_champ->getAccept())) {
															if(!in_array($_FILES[$sub_champ->getNom()]['tmp_name'][$i], $files_added)) {
																$mail->AddAttachment(
																	$_FILES[$sub_champ->getNom()]['tmp_name'][$i], $_FILES[$sub_champ->getNom()]['name'][$i]
																);
																
																$files_added[] = $_FILES[$sub_champ->getNom()]['tmp_name'][$i];
															}
														} else {
															$this->registerAndRedirect(false, icf_translate_string("Le type de fichier est incorrect et n'a pas pu être envoyé", FormSubmission::$polylang_lang));
														}
													} else if($_FILES[$sub_champ->getNom()]['error'][$i] != UPLOAD_ERR_NO_FILE) {
														$fileMaxSizeMo = (int)$this->max_file_size / 1024 / 1024;
														$this->registerAndRedirect(false, sprintf(icf_translate_string('Le fichier doit être inférieur à %1$sMo et n\'a pas pu être envoyé.', FormSubmission::$polylang_lang), $fileMaxSizeMo));
													}
												}
											} else {
												if($_FILES[$sub_champ->getNom()]['error'] == UPLOAD_ERR_OK && $_FILES[$sub_champ->getNom()]['size'] < $fileMaxSize) {
													$finfo = finfo_open(FILEINFO_MIME_TYPE);
													$mimetype = finfo_file($finfo, $_FILES[$sub_champ->getNom()]['tmp_name']);
													
													if(in_array($mimetype, $sub_champ->getAccept()) || empty($sub_champ->getAccept())) {
														if(!in_array($_FILES[$sub_champ->getNom()]['tmp_name'], $files_added)) {
															$mail->AddAttachment(
																$_FILES[$sub_champ->getNom()]['tmp_name'], $_FILES[$sub_champ->getNom()]['name']
															);
															
															$files_added[] = $_FILES[$sub_champ->getNom()]['tmp_name'];
														}
													} else {
														$this->registerAndRedirect(false, icf_translate_string("Le type de fichier est incorrect et n'a pas pu être envoyé", FormSubmission::$polylang_lang));
													}
												} else if($_FILES[$sub_champ->getNom()]['error'] != UPLOAD_ERR_NO_FILE) {
													$fileMaxSizeMo = (int)$this->max_file_size / 1024 / 1024;
													$this->registerAndRedirect(false, sprintf(icf_translate_string('Le fichier doit être inférieur à %1$sMo et n\'a pas pu être envoyé.', FormSubmission::$polylang_lang), $fileMaxSizeMo));
												}
											}
										}
									}
								} else {
									if($champ && $champ->getType() === "file" && isset($_FILES[$champ->getNom()]["size"])) {
										if($champ->isMultiple() && is_array($_FILES[$champ->getNom()]["size"])) {
											$nb_files = count($_FILES[$champ->getNom()]["size"]);
											for($i = 0; $i < $nb_files; $i++) {
												if($_FILES[$champ->getNom()]['error'][$i] == UPLOAD_ERR_OK && $_FILES[$champ->getNom()]['size'][$i] < $fileMaxSize) {
													$finfo = finfo_open(FILEINFO_MIME_TYPE);
													$mimetype = finfo_file($finfo, $_FILES[$champ->getNom()]['tmp_name'][$i]);
													
													if(in_array($mimetype, $champ->getAccept()) || empty($champ->getAccept())) {
														if(!in_array($_FILES[$champ->getNom()]['tmp_name'][$i], $files_added)) {
															$mail->AddAttachment(
																$_FILES[$champ->getNom()]['tmp_name'][$i], $_FILES[$champ->getNom()]['name'][$i]
															);
															
															$files_added[] = $_FILES[$champ->getNom()]['tmp_name'][$i];
														}
													} else {
														$this->registerAndRedirect(false, icf_translate_string("Le type de fichier est incorrect et n'a pas pu être envoyé", FormSubmission::$polylang_lang));
													}
												} else if($_FILES[$champ->getNom()]['error'][$i] != UPLOAD_ERR_NO_FILE) {
													$fileMaxSizeMo = (int)$this->max_file_size / 1024 / 1024;
													$this->registerAndRedirect(false, sprintf(icf_translate_string('Le fichier doit être inférieur à %1$sMo et n\'a pas pu être envoyé.', FormSubmission::$polylang_lang), $fileMaxSizeMo));
												}
											}
										} else {
											if($_FILES[$champ->getNom()]['error'] == UPLOAD_ERR_OK && $_FILES[$champ->getNom()]['size'] < $fileMaxSize) {
												$finfo = finfo_open(FILEINFO_MIME_TYPE);
												$mimetype = finfo_file($finfo, $_FILES[$champ->getNom()]['tmp_name']);
												
												if(in_array($mimetype, $champ->getAccept()) || empty($champ->getAccept())) {
													if(!in_array($_FILES[$champ->getNom()]['tmp_name'], $files_added)) {
														$mail->AddAttachment(
															$_FILES[$champ->getNom()]['tmp_name'], $_FILES[$champ->getNom()]['name']
														);
														
														$files_added[] = $_FILES[$champ->getNom()]['tmp_name'];
													}
												} else {
													$this->registerAndRedirect(false, icf_translate_string("Le type de fichier est incorrect et n'a pas pu être envoyé", FormSubmission::$polylang_lang));
												}
											} else if($_FILES[$champ->getNom()]['error'] != UPLOAD_ERR_NO_FILE) {
												$fileMaxSizeMo = (int)$this->max_file_size / 1024 / 1024;
												$this->registerAndRedirect(false, sprintf(icf_translate_string('Le fichier doit être inférieur à %1$sMo et n\'a pas pu être envoyé.', FormSubmission::$polylang_lang), $fileMaxSizeMo));
											}
										}
									}
								}
							}
						}
					}
				}
				
				$mail->AltBody = $tpl->text();
				$mail->Body = $tpl->HTML();
				
				$sent = $mail->Send();
				
			} catch(\Exception $exception) {
				$sent = false;
			}
			
			Logger::mail($mail->Subject, [
				"subject" => $mail->Subject ?? "",
				"reply" => $mail->getReplyToAddresses(),
				"cc" => $mail->getCcAddresses(),
				"to" => $mail->getToAddresses(),
				"alt_body" => $mail->AltBody ?? "",
				"from" => [
					"email" => $mail->From ?? "",
					"name" => $mail->FromName ?? ""
				],
				"error" => $mail->ErrorInfo ?? "",
				"message_id" => trim($mail->getLastMessageID(), "<>"),
			], $mail->getSentMIMEMessage());
			
			return $sent;
		}
		
		protected function includePHPMailer() {
			require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
			require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
			require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
		}
		
		protected function formatNumeroTel($string, $separator = " ") {
			$string = str_replace(["-", " ", "."], "", $string);
			$strlen = strlen($string);
			if($strlen % 2 !== 0) {
				$string = "#" . $string;
			}
			$stringParts = str_split($string, 2);
			$newString = implode($separator, $stringParts);
			
			return str_replace("#", "", $newString);
		}
		
		public static function getFormName() {
			return icf__("Formulaire");
		}
		
		public static function getIsMessageSent() {
			if(!static::$is_session_vars_initied) {
				static::session_vars_init();
			}
			
			return static::$is_message_sent;
		}
		
		public static function getReturnMessage() {
			if(!static::$is_session_vars_initied) {
				static::session_vars_init();
			}
			
			return static::$return_message;
		}
		
		public static function getSubmitForm() {
			if(!static::$is_session_vars_initied) {
				static::session_vars_init();
			}
			
			return static::$submit_form;
		}
		
		protected static function session_vars_init() {
			if(isset($_SESSION[static::$session_key . "_is_message_sent"])) {
				if($_SESSION[static::$session_key . "_is_message_sent"]) {
					static::$is_message_sent = true;
					static::$return_message = isset($_SESSION[static::$session_key . "_message_form_sent"]) ? $_SESSION[static::$session_key . "_message_form_sent"] : icf_translate_string("Merci, votre message a bien été envoyé.", FormSubmission::$polylang_lang);
					static::$submit_form = isset($_SESSION[static::$session_key . "_submit_form"]) ? $_SESSION[static::$session_key . "_submit_form"] : false;
				} else {
					static::$is_message_sent = false;
					static::$return_message = isset($_SESSION[static::$session_key . "_message_form_sent"]) ? $_SESSION[static::$session_key . "_message_form_sent"] : icf_translate_string("Une erreur est survenue lors de l'envoi du formulaire.", FormSubmission::$polylang_lang);
					static::$submit_form = isset($_SESSION[static::$session_key . "_submit_form"]) ? $_SESSION[static::$session_key . "_submit_form"] : false;
				}
				unset($_SESSION[static::$session_key . "_is_message_sent"]);
				unset($_SESSION[static::$session_key . "_message_form_sent"]);
				unset($_SESSION[static::$session_key . "_submit_form"]);
			}
			static::$is_session_vars_initied = true;
		}
	}

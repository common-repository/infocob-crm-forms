<?php
	
	namespace Infocob\CrmForms\Admin;
	
	use PHPMailer\PHPMailer\PHPMailer;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class AdditionalEmail {
		/**
		 * @var Form
		 */
		protected $form;
		protected $_donnees_formulaires = [];
		protected $enable = false;
		protected $send_mail = false;
		protected $template = "defaut-infocob-crm-forms";
		
		protected $title = "";
		protected $subtitle = "";
		protected $color = "";
		protected $color_text_title = "";
		protected $color_link = "";
		protected $logo = "";
		protected $societe = "";
		protected $border_radius = 0;
		
		protected $user_champ_email = "";
		protected $user_champ_prenom = "";
		protected $user_champ_nom = "";
		protected $from = "";
		protected $destinataires = [];
		protected $objet = "";
		
		protected $no_original_attachments = false;
		protected $attachements = [];
		
		public function __construct($config_email) {
			if(!empty($config_email["enable"])) {
				$this->enable = (bool)$config_email["enable"];
			}
			
			$this->from = sanitize_text_field($config_email["from"]) ?? "";
			
			$destinataires = !empty($config_email["to"]) ? $config_email["to"] : [];
			$to = [];
			foreach($destinataires as $destinataire) {
				$to[$destinataire["email"]] = $destinataire["fullname"];
			}
			$this->destinataires = $to;
			
			if(!empty($config_email["field_to"])) {
				$this->user_champ_email = sanitize_text_field($config_email["field_to"][0]["email"] ?? "");
				$this->user_champ_prenom = sanitize_text_field($config_email["field_to"][0]["firstname"] ?? "");
				$this->user_champ_nom = sanitize_text_field($config_email["field_to"][0]["lastname"] ?? "");
			}
			
			if(!empty($config_email["subtitle"])) {
				$this->subtitle = sanitize_text_field($config_email["subtitle"]);
			}
			
			if(!empty($config_email["subject"])) {
				$this->objet = sanitize_text_field($config_email["subject"]);
			}
			
			if(!empty($config_email["title"])) {
				$this->title = $config_email["title"];
			} else {
				$this->title = get_bloginfo("name") . " - Formulaire de contact";
			}
			
			if(!empty($config_email["subtitle"])) {
				$this->subtitle = $config_email["subtitle"];
			}
			
			if(!empty($config_email["societe"])) {
				$this->societe = $config_email["societe"];
			}
			
			if(!empty($config_email["border_radius"])) {
				$this->border_radius = $config_email["border_radius"];
			}
			
			if(!empty($config_email["color"])) {
				$this->color = $config_email["color"];
			}
			
			if(!empty($config_email["color_text_title"])) {
				$this->color_text_title = $config_email["color_text_title"];
			}
			
			if(!empty($config_email["color_link"])) {
				$this->color_link = $config_email["color_link"];
			}
			
			if(!empty($config_email["logo"])) {
				$attachment_id = $config_email["logo"]["attachment_id"] ?? false;
				$size = $config_email["logo"]["size"] ?? "";
				if(!empty($attachment_id)) {
					$image = wp_get_attachment_image_src($attachment_id, $size);
					$this->logo = $image[0] ?? "";
				}
			}
			
			if(!empty($config_email["template"])) {
				$this->template = $config_email["template"];
			}
			
			if(!empty($config_email["no_original_attachements"])) {
				$this->no_original_attachments = (bool)($config_email["no_original_attachements"] ?? false);
			}
			
			if(!empty($config_email["attachments"])) {
				$this->attachements = [];
				foreach ($config_email["attachments"] ?? [] as $attachment) {
					$path = get_attached_file($attachment["attachment_id"] ?? "");
					$pathinfo = pathinfo($path);
					$extension = $pathinfo['extension'] ?? "";
					$filename = sanitize_file_name(get_the_title($attachment["attachment_id"] ?? "")).".".$extension;
					
					$this->attachements[] = [
						"path" => $path,
						"name" => $filename
					];
				}
			}
		}
		
		/**
		 * @param Form $form
		 */
		public function setForm(Form $form): void {
			$this->form = $form;
			
			$dataForm = Database::getFormIfbFromDb($this->form->getId());
			if((!empty($dataForm["sendmail"]) && $dataForm["sendmail"]) || empty($dataForm)) {
				$this->send_mail = true;
			} else {
				$this->send_mail = false;
			}
		}
		
		public function sendAdditionalEmail() {
			if($this->enable) {
				$this->extractDatasFromPost();
				$tpl = $this->getTemplateEmail();
				$this->sendMail($tpl);
			}
		}
		
		public function extractDatasFromPost($return = false) {
			$this->_donnees_formulaires = [];
			$champs = $this->form->getFieldsGroups();
			
			foreach($champs as $champ) {
				if($champ->getType() == "groupe") {
					foreach($champ->getChamps() as $sub_champ) {
						
						if($champ->getType() == "select" && $champ->isRecipients()) {
							if($this->form->isRecipientsEnabled()) {
								$destinataires = $this->getValue($champ);
								$formatDest = [];
								foreach($destinataires as $post_id) {
									$post_meta = get_post_meta($post_id, 'infocob_crm_forms_admin_recipients_config', true);
									$formatDest[$post_meta["email"]] = $post_meta["firstname"] ?? "" . " " . $post_meta["lastname"] ?? "";
								}
								$this->form->addDestinataires($formatDest);
							}
						}
						
						$this->_donnees_formulaires[$sub_champ->getNom()] = $this->getValue($sub_champ);
					}
				} else {
					
					if($champ->getType() == "select" && $champ->isRecipients()) {
						if($this->form->isRecipientsEnabled()) {
							$destinataires = $this->getValue($champ);
							$formatDest = [];
							foreach($destinataires as $post_id) {
								$post_meta = get_post_meta($post_id, 'infocob_crm_forms_admin_recipients_config', true);
								$formatDest[$post_meta["email"]] = $post_meta["firstname"] ?? "" . " " . $post_meta["lastname"] ?? "";
							}
							$this->form->addDestinataires($formatDest);
						}
					}
					
					$this->_donnees_formulaires[$champ->getNom()] = $this->getValue($champ);
				}
			}
			$this->_donnees_formulaires["page_form"] = isset($_POST["page_form"]) ? $_POST["page_form"] : "";
			
			$this->_donnees_formulaires = Tools::sanitize_fields($this->_donnees_formulaires, true);
			
			if($return) {
				return $this->_donnees_formulaires;
			}
		}
		
		protected function getValue(Field $champ) {
			$value = isset($_POST[$champ->getNom()]) ? $_POST[$champ->getNom()] : "";
			if($champ->getType() === "checkbox") {
				$value = isset($_POST[$champ->getNom()]) ? "OUI" : "NON";
			}
			
			if(isset($_POST[$champ->getNom()]) && $_POST[$champ->getNom()] === "" && !empty($champ->getDefautPost())) {
				$value = $champ->getDefautPost();
			}
			
			return $value;
		}
		
		public function getTemplateEmail() {
			$tpl = new TemplateMail($this->template, $this->form, $this->_donnees_formulaires);
			$tpl->setTitle($this->title);
			$tpl->setSubtitle($this->subtitle);
			$tpl->setColor($this->color);
			$tpl->setColorTextTitle($this->color_text_title);
			$tpl->setColorLink($this->color_link);
			$tpl->setSociete($this->societe);
			$tpl->setBorderRadius($this->border_radius);
			$tpl->setLogo($this->logo);
			
			$nom = "";
			$prenom = "";
			if(isset($this->_donnees_formulaires[$this->user_champ_nom])) {
				$nom = $this->_donnees_formulaires[$this->user_champ_nom] ?? "";
			}
			if(isset($this->_donnees_formulaires[$this->user_champ_prenom])) {
				$prenom = $this->_donnees_formulaires[$this->user_champ_prenom] ?? "";
			}
			$tpl->setLastname($nom);
			$tpl->setFirstname($prenom);
			
			return $tpl;
		}
		
		protected function sendMail($tpl) {
			$this->includePHPMailer();
			
			$mail = new PHPMailer();
			
			$mail->AddReplyTo($this->form->getExpediteur(), htmlspecialchars_decode(get_bloginfo("name")));
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
				
				$destinataires = $this->destinataires;
				
				$user_dest = false;
				if(!empty($this->user_champ_email)) {
					$email = $this->_donnees_formulaires[$this->user_champ_email] ?? "";
					$nom = "";
					$prenom = "";
					if($this->_donnees_formulaires[$this->user_champ_nom]) {
						$nom = $this->_donnees_formulaires[$this->user_champ_nom] ?? "";
					}
					if($this->_donnees_formulaires[$this->user_champ_prenom]) {
						$prenom = $this->_donnees_formulaires[$this->user_champ_prenom] ?? "";
					}
					if($email) {
						$mail->addAddress($email, trim(mb_convert_encoding($prenom, 'ISO-8859-1', 'UTF-8') . " " . mb_convert_encoding($nom, 'ISO-8859-1', 'UTF-8')));
						$user_dest = true;
					}
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
					$mail->addBCC("formulaires@infocob-solutions.com", icf__("Copie Infocob"));
				}
				
				$mail->Subject = Tools::setFieldFromForm($this->objet, $this->_donnees_formulaires);
				
				if(!empty($_FILES) && !$this->no_original_attachments) {
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
													if($_FILES[$sub_champ->getNom()]['error'][$i] == UPLOAD_ERR_OK) {
														$finfo = finfo_open(FILEINFO_MIME_TYPE);
														$mimetype = finfo_file($finfo, $_FILES[$sub_champ->getNom()]['tmp_name'][$i]);
														
														if(in_array($mimetype, $sub_champ->getAccept()) || empty($sub_champ->getAccept())) {
															if(!in_array($_FILES[$sub_champ->getNom()]['tmp_name'][$i], $files_added)) {
																$mail->AddAttachment(
																	$_FILES[$sub_champ->getNom()]['tmp_name'][$i], $_FILES[$sub_champ->getNom()]['name'][$i]
																);
																
																$files_added[] = $_FILES[$sub_champ->getNom()]['tmp_name'][$i];
															}
														}
													}
												}
											} else {
												if($_FILES[$sub_champ->getNom()]['error'] == UPLOAD_ERR_OK) {
													$finfo = finfo_open(FILEINFO_MIME_TYPE);
													$mimetype = finfo_file($finfo, $_FILES[$sub_champ->getNom()]['tmp_name']);
													
													if(in_array($mimetype, $sub_champ->getAccept()) || empty($sub_champ->getAccept())) {
														if(!in_array($_FILES[$sub_champ->getNom()]['tmp_name'], $files_added)) {
															$mail->AddAttachment(
																$_FILES[$sub_champ->getNom()]['tmp_name'], $_FILES[$sub_champ->getNom()]['name']
															);
															
															$files_added[] = $_FILES[$sub_champ->getNom()]['tmp_name'];
														}
													}
												}
											}
										}
									}
								} else {
									if($champ && $champ->getType() === "file" && isset($_FILES[$champ->getNom()]["size"])) {
										if($champ->isMultiple() && is_array($_FILES[$champ->getNom()]["size"])) {
											$nb_files = count($_FILES[$champ->getNom()]["size"]);
											for($i = 0; $i < $nb_files; $i++) {
												if($_FILES[$champ->getNom()]['error'][$i] == UPLOAD_ERR_OK) {
													$finfo = finfo_open(FILEINFO_MIME_TYPE);
													$mimetype = finfo_file($finfo, $_FILES[$champ->getNom()]['tmp_name'][$i]);
													
													if(in_array($mimetype, $champ->getAccept()) || empty($champ->getAccept())) {
														if(!in_array($_FILES[$champ->getNom()]['tmp_name'][$i], $files_added)) {
															$mail->AddAttachment(
																$_FILES[$champ->getNom()]['tmp_name'][$i], $_FILES[$champ->getNom()]['name'][$i]
															);
															
															$files_added[] = $_FILES[$champ->getNom()]['tmp_name'][$i];
														}
													}
												}
											}
										} else {
											if($_FILES[$champ->getNom()]['error'] == UPLOAD_ERR_OK) {
												$finfo = finfo_open(FILEINFO_MIME_TYPE);
												$mimetype = finfo_file($finfo, $_FILES[$champ->getNom()]['tmp_name']);
												
												if(in_array($mimetype, $champ->getAccept()) || empty($champ->getAccept())) {
													if(!in_array($_FILES[$champ->getNom()]['tmp_name'], $files_added)) {
														$mail->AddAttachment(
															$_FILES[$champ->getNom()]['tmp_name'], $_FILES[$champ->getNom()]['name']
														);
														
														$files_added[] = $_FILES[$champ->getNom()]['tmp_name'];
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
				
				// Additional attachments from config
				foreach ($this->attachements as $attachement) {
					$mail->AddAttachment($attachement["path"] ?? "", $attachement["name"] ?? "");
				}
				
				$mail->AltBody = $tpl->text();
				$mail->Body = $tpl->HTML();
				
				$sent = false;
				if(!empty($destinataires) || $user_dest) {
					$sent = $mail->Send();
				}
				
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
		
	}

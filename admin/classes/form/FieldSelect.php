<?php
	
	namespace Infocob\CrmForms\Admin;
 
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FieldSelect extends Field {
		protected $placeholder = "";
		protected $is_recipients = null;
		protected $display_search = false;
		protected $valeurs = [];
		
		public function loadFromArray(array $data) {
			parent::loadFromArray($data);
			
			$optionsValues = [];
			if(isset($data["options"])) {
				$getOptions = isset($_GET[$this->getNom()]) ? Tools::sanitize_fields($_GET[$this->getNom()]) : false;
				foreach($data["options"] as $index => $option) {
					if(is_numeric($index)) {
						if(!empty($getOptions)) {
							unset($data["options"][$index]["selected"]);
							if(is_array($getOptions) && $this->isMultiple()) {
								if(in_array($option["libelle"], $getOptions)) {
									$data["options"][$index]["selected"] = "1";
								}
							} else if(is_string($getOptions)) {
								if($option["libelle"] === $getOptions) {
									$data["options"][$index]["selected"] = "1";
								}
							}
						}
						
						$optionsValues[$index] = $data["options"][$index];
					}
				}
				
				$options = get_option('infocob_crm_forms_settings');
				$recipients_option_enabled = filter_var(($options['recipients']['enabled'] ?? false), FILTER_VALIDATE_BOOLEAN);
				if($recipients_option_enabled === true) {
					$this->setRecipients(filter_var((isset($data["options"]["recipients_enabled"]) && $recipients_option_enabled), FILTER_VALIDATE_BOOLEAN));
				}
				
				if(isset($data["options"]["placeholder"])) {
					$this->setPlaceholder($data["options"]["placeholder"]);
				}
				$this->setValeurs($optionsValues);
				
				if(isset($data["search_select"])) {
					$this->setDisplaySearch((bool)$data["search_select"]);
				}
			}
		}
		
		/**
		 * @return string
		 */
		public function getPlaceholder(): string {
			return $this->placeholder;
		}
		
		/**
		 * @param string $placeholder
		 */
		public function setPlaceholder(string $placeholder): void {
			$this->placeholder = $placeholder;
		}
		
		/**
		 * @return bool
		 */
		public function isRecipients() {
			return $this->is_recipients;
		}
		
		/**
		 * @param bool $is_recipients
		 */
		public function setRecipients($is_recipients) {
			$this->is_recipients = $is_recipients;
		}
		
		/**
		 * @return array
		 */
		public function getValeurs(): array {
			return $this->valeurs;
		}
		
		/**
		 * @param array $valeurs
		 */
		public function setValeurs(array $valeurs): void {
			$this->valeurs = $valeurs;
		}
		
		/**
		 * @return bool
		 */
		public function getDisplaySearch(): bool {
			return $this->display_search;
		}
		
		/**
		 * @param bool $display_search
		 */
		public function setDisplaySearch(bool $display_search): void {
			$this->display_search = $display_search;
		}
		
		public function display() {
			$nom = $this->getNom();
			$display_search = $this->getDisplaySearch();
			if($this->is_multiple) {
				$nom .= "[]";
			}
			$select_multiple = $this->is_multiple ? "multiple='multiple'" : "";
			?>
            <label class="<?php echo $this->getCssClasses(); ?>">
				<?php if($this->is_display_libelle) { ?>
                    <span class="if-field-libelle"><?php echo $this->libelle; ?></span>
				<?php } ?>
                <select name="<?php echo esc_attr($nom); ?>" <?php echo $select_multiple; ?> class="<?php echo !empty($display_search) ? "display_search" : ""; ?>">
					<?php if(!$this->is_multiple) { ?>
                        <option value="">
							<?php if(!$this->is_display_libelle) { ?>
								<?php echo esc_attr($this->libelle); ?>
							<?php } ?>
                        </option>
					<?php } ?>
					<?php foreach($this->valeurs as $option) { ?>
						<?php $selected = !empty($option["selected"]) ? "selected" : ""; ?>
                        <option value="<?php echo $option["valeur"]; ?>" <?php echo $selected; ?>><?php echo esc_attr($option["libelle"]); ?></option>
					<?php } ?>
                </select>
            </label>
			<?php
		}
		
		public function get() {
			$cssClasses = $this->getCssClasses();
			$label_required = ($this->required) ? "field-required" : "";
			$libelle = esc_attr($this->libelle);
			$select_multiple = $this->is_multiple ? "multiple='multiple'" : "";
			$nom = esc_attr($this->getNom());
			
			// Select2JS
			$options = get_option('infocob_crm_forms_settings');
			$disable_select2JS = $options['form_config']['select2JS'] ?? false;
			
			if($this->is_multiple) {
				$nom .= "[]";
			}
			
			$is_display_libelle_field = "";
			if($this->is_display_libelle) {
				$is_display_libelle_field = '<span class="if-field-libelle">' . $libelle . '</span>';
			}
			
			$select_classes = !empty($this->getDisplaySearch()) ? "display_search" : "";
			
			if(!$disable_select2JS || $this->is_multiple) {
				$select_classes .= " select2JS";
			}
			
			if(!$disable_select2JS || $this->is_multiple) {
				$options = "<option></option>";
			} else {
				$options = "";
			}
			foreach($this->valeurs as $option) {
				$selected = !empty($option["selected"]) ? "selected" : "";
				
				if($this->isRecipients()) {
					$destinataires = $option["recipients"] ?? false;
					if(!empty($destinataires)) {
						$post = get_post($option["recipients"]);
						if($post instanceof \WP_Post) {
							$options .= '<option value="' . $post->ID . '" ' . $selected . '>' . ($option["libelle"] ?? "") . '</option>';
						}
					}
				} else {
					$options .= '<option value="' . ($option["valeur"] ?? "") . '" ' . $selected . '>' . ($option["libelle"] ?? "") . '</option>';
				}
			}
			
			$placeholder = "";
			if(!$disable_select2JS || $this->isMultiple()) {
				$placeholder = !empty($this->getPlaceholder()) ? "data-placeholder='" . esc_attr($this->getPlaceholder()) . "'" : "";
			}
			
			return '
            <label class="' . $cssClasses . '">
                ' . $is_display_libelle_field . '
                <select ' . $placeholder . ' data-allowClear="true" name="' . $nom . '" ' . $select_multiple . ' class="' . trim($select_classes) . '">
                    ' . $options . '
                </select>
            </label>
			';
		}
		
	}

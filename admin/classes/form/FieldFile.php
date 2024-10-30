<?php
	
	namespace Infocob\CrmForms\Admin;
 
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FieldFile extends Field {
		protected $accept = [];
		protected $is_display_libelle = true;
		
		public function loadFromArray(array $data) {
			parent::loadFromArray($data);
			
			if(isset($data["accept"])) {
				// Mange multiple mime type for one extension
				if(in_array("application/iges", $data["accept"])) {
					$data["accept"][] = "model/iges";
				}
				if(in_array("application/acad", $data["accept"])) {
					$data["accept"][] = "image/vnd.dwg";
					$data["accept"][] = "image/x-dwg";
				}
				if(in_array("application/dxf", $data["accept"])) {
					$data["accept"][] = "image/vnd.dwg";
					$data["accept"][] = "image/x-dwg";
				}
				
				$this->setAccept($data["accept"]);
			}
		}
		
		/**
		 * @return array
		 */
		public function getAccept(): array {
			return $this->accept;
		}
		
		/**
		 * @param array $accept
		 */
		public function setAccept(array $accept): void {
			$this->accept = $accept;
		}
		
		public function getAcceptString(): string {
			return implode(', ', $this->accept);
		}
		
		public function display() {
			?>
            <label class="<?php echo $this->getCssClasses(); ?>">
				<?php if($this->is_display_libelle) { ?>
                    <span class="if-field-libelle"><?php echo $this->libelle; ?></span>
				<?php } ?>
                <input type="file" name="<?php echo esc_attr($this->nom); ?>"
                       accept="<?php echo esc_attr($this->getAcceptString()); ?>" />
            </label>
			<?php
		}
		
		public function get() {
			$cssClasses = $this->getCssClasses();
			$label_required = ($this->required) ? "field-required" : "";
			$nom = esc_attr($this->nom);
			$nom = ($this->isMultiple()) ? $nom . "[]" : $nom;
			$libelle = esc_attr($this->libelle);
			$is_display_libelle = "";
			if($this->is_display_libelle) {
				$is_display_libelle = '<span class="if-field-libelle">' . $this->libelle . '</span>';
			}
			$required = ($this->required) ? "required" : "";
			
			$multiple = ($this->isMultiple()) ? "multiple" : "";
			
			return '
                <label class="' . $cssClasses . '">
                    ' . $is_display_libelle . '
                    <input type="file" name="' . $nom . '"
                            accept="' . esc_attr($this->getAcceptString()) . '" ' . $multiple . ' ' . $required . ' />
                </label>
            ';
		}
		
	}

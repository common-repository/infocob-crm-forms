<?php
	
	namespace Infocob\CrmForms\Admin;
 
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FieldRadio extends Field {
		protected $isChecked = false;
		
		public function loadFromArray(array $data) {
			parent::loadFromArray($data);
			
			if(isset($_GET[trim($this->getNom(), "[]")])) {
				$values = $_GET[trim($this->getNom(), "[]")];
				if(is_string($values)) {
					$this->isChecked = filter_var($values, FILTER_VALIDATE_BOOLEAN);
				} else if(is_array($values) && isset($values[$this->getValeur()])) {
					$this->isChecked = filter_var($values[$this->getValeur()], FILTER_VALIDATE_BOOLEAN);
				}
			}
		}
		
		public function display() {
			?>
            <label class="<?php echo $this->getCssClasses(); ?>">
                <input type="checkbox" name="<?php echo esc_attr($this->nom); ?>" <?php if($this->required){ ?>required<?php } ?> />
                <span><?php echo $this->getLibelle(); ?></span>
            </label>
			<?php
		}
		
		public function get() {
			$cssClasses = $this->getCssClasses();
			$input_required = ($this->required) ? "required" : "";
			$nom = esc_attr($this->nom);
			$libelle = $this->getLibelle();
			$checked = ($this->isChecked) ? "checked" : "";
			
			return '
                <label class="' . $cssClasses . '">
                    <input type="radio" name="' . $nom . '" ' . $input_required . ' ' . $checked . ' />
                    <span>' . $libelle . '</span>
                </label>
            ';
		}
	}

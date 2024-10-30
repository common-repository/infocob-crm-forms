<?php
	
	namespace Infocob\CrmForms\Admin;
 
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FieldHidden extends Field {
		
		protected function getCssClasses() {
			return "if-field-hidden " . $this->getCssClassFromName();
		}
		
		public function display() {
			?>
            <input type="hidden" name="<?php echo esc_attr($this->nom); ?>" class="<?php echo $this->getCssClasses(); ?>" />
			<?php
		}
		
		public function get() {
			$nom = esc_attr($this->nom);
			$cssClasses = $this->getCssClasses();
			$valeur = $this->getValeur();
			return '
            <input type="hidden" name="' . $nom . '" class="' . $cssClasses . '" value="' . $valeur . '" />
			';
		}
		
	}

<?php
	
	namespace Infocob\CrmForms\Admin;
 
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FieldTextarea extends Field {
		public function display() {
			$libelle = esc_attr($this->libelle);
			if($this->required) {
				$libelle .= " *";
			}
			?>
            <label class="<?php echo $this->getCssClasses(); ?>">
				<?php if($this->is_display_libelle) { ?>
                    <span class="if-field-libelle"><?php echo $this->libelle; ?></span>
				<?php } ?>
                <textarea name="<?php echo esc_attr($this->nom); ?>" rows="3" placeholder="<?php echo $libelle; ?>"></textarea>
            </label>
			<?php
		}
		
		public function get() {
			$cssClasses = $this->getCssClasses();
			$label_required = ($this->required) ? "field-required" : "";
			$input_required = ($this->required) ? "required" : "";
			$nom = esc_attr($this->nom);
			$libelle = esc_attr($this->libelle);
			$valeur = $this->valeur;
			if($this->required) {
				$libelle .= " *";
			}
			
			$is_display_libelle = "";
			if($this->is_display_libelle) {
				$is_display_libelle = '<span class="if-field-libelle">' . esc_attr($this->libelle) . '</span>';
			}
			
			$valeursLignes = explode("\\n", $valeur);
			$textareaValue = "";
			foreach($valeursLignes as $ligne) {
				$textareaValue .= $ligne . "\n";
			}
			
			return "
		    <label class='" . $cssClasses . "'>
		        " . $is_display_libelle . "
                <textarea name='" . $nom . "' rows='3' placeholder='" . $libelle . "' " . $input_required . ">" . $textareaValue . "</textarea>
            </label>";
		}
		
	}

<?php
	
	namespace Infocob\CrmForms\Admin;
 
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FieldPassword extends Field {
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
                <input type="password" name="<?php echo esc_attr($this->nom); ?>" placeholder="<?php echo $libelle; ?>" value="" <?php if($this->required){ ?>required<?php } ?> />
            </label>
			<?php
		}
		
		public function get() {
			$cssClasses = $this->getCssClasses();
			$input_required = ($this->required) ? "required" : "";
			$nom = esc_attr($this->nom);
			$libelle = esc_attr($this->libelle);
			$valeur = esc_attr($this->valeur);
			if($this->required) {
				$libelle .= " *";
			}
			$is_display_libelle = "";
			if($this->is_display_libelle) {
				$is_display_libelle = '<span class="if-field-libelle">' . $this->libelle . '</span>';
			}
			
			return '
            <label class="' . $cssClasses . '">
                ' . $is_display_libelle . '
                <input type="password" name="' . $nom . '" placeholder="' . $libelle . '" value="' . $valeur . '" ' . $input_required . ' />
            </label>
			';
		}
		
	}

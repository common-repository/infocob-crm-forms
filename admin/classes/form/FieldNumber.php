<?php
	
	namespace Infocob\CrmForms\Admin;
 
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FieldNumber extends Field {
		protected $step = "";
		protected $min = "";
		protected $max = "";
		
		public function loadFromArray(array $data) {
			parent::loadFromArray($data);
			
			if(isset($data["numbers"]["step"])) {
				$this->setStep($data["numbers"]["step"]);
			}
			if(isset($data["numbers"]["max"])) {
				$this->setMax($data["numbers"]["max"]);
			}
			if(isset($data["numbers"]["min"])) {
				$this->setMin($data["numbers"]["min"]);
			}
		}
		
		/**
		 * @return mixed
		 */
		public function getStep() {
			return $this->step;
		}
		
		/**
		 * @param mixed $step
		 */
		public function setStep($step): void {
			$this->step = $step;
		}
		
		/**
		 * @return mixed
		 */
		public function getMin() {
			return $this->min;
		}
		
		/**
		 * @param mixed $min
		 */
		public function setMin($min): void {
			$this->min = $min;
		}
		
		/**
		 * @return mixed
		 */
		public function getMax() {
			return $this->max;
		}
		
		/**
		 * @param mixed $max
		 */
		public function setMax($max): void {
			$this->max = $max;
		}
		
		public function display() {
			$libelle = esc_attr($this->libelle);
			$valeur = esc_attr($this->valeur);
			if($this->required) {
				$libelle .= " *";
			}
			?>
            <label class="<?php echo $this->getCssClasses(); ?>">
				<?php if($this->is_display_libelle) { ?>
                    <span class="if-field-libelle"><?php echo $this->libelle; ?></span>
				<?php } ?>
                <input type="number" name="<?php echo esc_attr($this->nom); ?>" placeholder="<?php echo $libelle; ?>" value="<?php echo $valeur; ?>" <?php if($this->required){ ?>required<?php } ?> />
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
				$is_display_libelle = '<span class="if-field-libelle">' . esc_attr($this->libelle) . '</span>';
			}
			
			$step = "";
			if($this->step !== "") {
				$step = "step='" . $this->step . "'";
			}
			$min = "";
			if($this->min !== "") {
				$min = "min='" . $this->min . "'";
			}
			$max = "";
			if($this->max !== "") {
				$max = "max='" . $this->max . "'";
			}
			
			return '
            <label class="' . $cssClasses . '">
                ' . $is_display_libelle . '
                <input type="number" name="' . $nom . '" placeholder="' . $libelle . '" value="' . $valeur . '" ' . $input_required . ' ' . $step . ' ' . $min . ' ' . $max . ' />
            </label>
			';
		}
		
	}

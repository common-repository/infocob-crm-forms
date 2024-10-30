<?php
	
	namespace Infocob\CrmForms\Admin;
 
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Field {
		protected $type = "";
		protected $col = 2;
		protected $nom = "";
		protected $libelle = "";
		protected $valeur = "";
		protected $required = false;
		protected $is_display_libelle = false;
		protected $is_multiple = false;
		protected $defaut_post = "";
		protected $columns_base = 4;
		
		public function loadFromArray(array $data) {
			if(isset($data["type"])) {
				$this->setType($data["type"]);
			}
			
			if(isset($data["col"])) {
				$this->setCol($data["col"]);
			}
			
			if(isset($data["nom"])) {
				$this->setNom($data["nom"]);
			}
			
			if(isset($data["libelle"])) {
				$this->setLibelle($data["libelle"]);
			}
			
			if(isset($data["valeur"])) {
				if(!empty($this->getNom()) && isset($_GET[$this->getNom()])) {
					$this->setValeur(sanitize_text_field($_GET[$this->getNom()]));
				} else {
					$this->setValeur($data["valeur"]);
				}
			}
			
			if(isset($data["required"])) {
				$this->setRequired($data["required"]);
			}
			
			if(isset($data["display_libelle"])) {
				$this->setIsDisplayLibelle($data["display_libelle"]);
			}
			
			if(isset($data["multiple"])) {
				$this->setIsMultiple($data["multiple"]);
			}
			
			if(isset($data["defaut_post"])) {
				$this->setDefautPost($data["defaut_post"]);
			}
			
			if(isset($data["columns_base"])) {
				$this->setColumnsBase($data["columns_base"]);
			}
		}
		
		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}
		
		/**
		 * @param string $type
		 */
		public function setType(string $type): void {
			$this->type = $type;
		}
		
		/**
		 * @return int
		 */
		public function getCol(): int {
			return $this->col;
		}
		
		/**
		 * @param int $col
		 */
		public function setCol(int $col): void {
			$this->col = $col;
		}
		
		/**
		 * @return string
		 */
		public function getNom(): string {
			return $this->nom;
		}
		
		/**
		 * @param string $nom
		 */
		public function setNom(string $nom): void {
			$this->nom = $nom;
		}
		
		/**
		 * @return string
		 */
		public function getLibelle(): string {
			return $this->libelle;
		}
		
		/**
		 * @param string $libelle
		 */
		public function setLibelle(string $libelle): void {
			$this->libelle = $libelle;
		}
		
		/**
		 * @return string
		 */
		public function getValeur(): string {
			return $this->valeur;
		}
		
		/**
		 * @param string $valeur
		 */
		public function setValeur(string $valeur): void {
			$this->valeur = $valeur;
		}
		
		/**
		 * @return bool
		 */
		public function isRequired(): bool {
			return $this->required;
		}
		
		/**
		 * @param bool $required
		 */
		public function setRequired(bool $required): void {
			$this->required = $required;
		}
		
		/**
		 * @return bool
		 */
		public function isIsDisplayLibelle(): bool {
			return $this->is_display_libelle;
		}
		
		/**
		 * @param bool $is_display_libelle
		 */
		public function setIsDisplayLibelle(bool $is_display_libelle): void {
			$this->is_display_libelle = $is_display_libelle;
		}
		
		/**
		 * @return bool
		 */
		public function isMultiple(): bool {
			return $this->is_multiple;
		}
		
		/**
		 * @param bool $is_multiple
		 */
		public function setIsMultiple(bool $is_multiple): void {
			$this->is_multiple = $is_multiple;
		}
		
		/**
		 * @return string
		 */
		public function getDefautPost(): string {
			return $this->defaut_post;
		}
		
		/**
		 * @param string $defaut_post
		 */
		public function setDefautPost(string $defaut_post): void {
			$this->defaut_post = $defaut_post;
		}
		
		/**
		 * @return int
		 */
		public function getColumnsBase(): int {
			return $this->columns_base;
		}
		
		/**
		 * @param int $columns_base
		 */
		public function setColumnsBase(int $columns_base): void {
			$this->columns_base = $columns_base;
		}
		
		protected function getCssClassFromName() {
			return "if-field-" . preg_replace("#[^a-z0-9]#i", "", $this->nom);
		}
		
		protected function getCssClassFromCol() {
			switch($this->col) {
				case 1 :
					return 'if-field-1-' . $this->getColumnsBase();
				
				case 2 :
					return 'if-field-2-' . $this->getColumnsBase();
				
				case 3 :
					return 'if-field-3-' . $this->getColumnsBase();
				
				case 4 :
					return 'if-field-4-' . $this->getColumnsBase();
				
				case 5 :
					return 'if-field-5-' . $this->getColumnsBase();
				
				case 6 :
					return 'if-field-6-' . $this->getColumnsBase();
				
				case 7 :
					return 'if-field-7-' . $this->getColumnsBase();
				
				case 8 :
					return 'if-field-8-' . $this->getColumnsBase();
				
				case 9 :
					return 'if-field-9-' . $this->getColumnsBase();
				
				case 10 :
					return 'if-field-10-' . $this->getColumnsBase();
				
				case 11 :
					return 'if-field-11-' . $this->getColumnsBase();
				
				case 12 :
					return 'if-field-12-' . $this->getColumnsBase();
			}
			
		}
		
		protected function getCssClasses() {
			$classes = "if-field";
			$classes .= " " . $this->getCssClassFromName();
			$classes .= " " . $this->getCssClassFromCol();
			if($this->required) {
				$classes .= " if-field-required";
			}
			
			return $classes;
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
                <input type="text" name="<?php echo esc_attr($this->nom); ?>" placeholder="<?php echo $libelle; ?>" value="<?php echo $valeur; ?>" <?php if($this->required){ ?>required<?php } ?> />
            </label>
			<?php
		}
		
		public function get() {
			$cssClasses = $this->getCssClasses();
			$label_required = ($this->required) ? "field-required" : "";
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
			
			return '
            <label class="' . $cssClasses . '">
                ' . $is_display_libelle . '
                <input type="text" name="' . $nom . '" placeholder="' . $libelle . '" value="' . $valeur . '" ' . $input_required . ' />
            </label>
			';
		}
		
		public static function getInstanceFromType(string $type = "") {
			switch($type) {
				case "select" :
					return new FieldSelect();
				case "checkbox" :
					return new FieldCheckbox();
				case "textarea" :
					return new FieldTextarea();
				case "file" :
					return new FieldFile();
				case "hidden" :
					return new FieldHidden();
				case "email" :
					return new FieldEmail();
				case "number" :
					return new FieldNumber();
				case "tel" :
					return new FieldTel();
				case "date" :
					return new FieldDate();
				case "groupe":
					return new FieldGroup();
				case "radio":
					return new FieldRadio();
				case "password":
					return new FieldPassword();
				default :
					return new Field();
			}
		}
		
	}

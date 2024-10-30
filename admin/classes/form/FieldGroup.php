<?php
	
	namespace Infocob\CrmForms\Admin;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FieldGroup extends Field {
		protected $champs;
		
		public function loadFromArray(array $data) {
			parent::loadFromArray($data);
			
			if(isset($data["champs"])) {
				$champs = [];
				foreach($data["champs"] as $champ) {
					$champ["columns_base"] = $this->columns_base;
					$field = Field::getInstanceFromType($champ["type"]);
					$field->loadFromArray($champ);
					$champs[] = $field;
				}
				$this->champs = $champs;
			}
		}
		
		protected function getCssClassFromLibelle() {
			return "if-group-" . preg_replace("#[^a-z0-9]#i", "", $this->libelle);
		}
		
		/**
		 * @return mixed
		 */
		public function getChamps() {
			return $this->champs;
		}
		
		/**
		 * @param mixed $champs
		 */
		public function setChamps($champs): void {
			$this->champs = $champs;
		}
		
		public function get() {
			$libelle = $this->getLibelle();
			$cssName = $this->getCssClassFromLibelle();
			$col = $this->getCssClassFromCol();
			
			$form = "<div class='if-field " . $cssName . " " . $col . "'>";
			$form .= "<fieldset>";
			if($this->is_display_libelle) {
				$form .= "<legend>" . $libelle . "</legend>";
			}
			$form .= "<div class='if-group " . $cssName . " if-field-" . $this->getColumnsBase() . "-" . $this->getColumnsBase() . "'>";
			foreach($this->getChamps() as $champ) {
				$form .= $champ->get();
			}
			$form .= "</div>";
			$form .= "</fieldset>";
			$form .= "</div>";
			
			return $form;
		}
		
	}

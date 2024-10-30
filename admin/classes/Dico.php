<?php
	
	namespace Infocob\CrmForms\Admin;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	class Dico {
		
		const TABLES = array(
			"contactfiche",
			"interlocuteurfiche",
			"actions",
			"affaire",
			"produitfiche",
			"ticket",
			"contrat",
			"historique",
			"inventaireproduit"
		);
		
		const CODES = array(
			"contactfiche"       => "C_CODE",
			"interlocuteurfiche" => "I_CODE",
			"actions"            => "AC_CODE",
			"affaire"            => "AF_CODE",
			"produitfiche"       => "P_CODE",
			"ticket"             => "TI_CODE",
			"contrat"            => "CT_CODE",
			"historique"         => "H_CODE",
			"inventaireproduit"  => "IP_CODE"
		);
		
		const PREFIX = array(
			"contactfiche"       => "C_",
			"interlocuteurfiche" => "I_",
			"actions"            => "AC_",
			"affaire"            => "AF_",
			"produitfiche"       => "P_",
			"ticket"             => "TI_",
			"contrat"            => "CT_",
			"historique"         => "H_",
			"inventaireproduit"  => "IP_"
		);
	}

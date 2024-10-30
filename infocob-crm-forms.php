<?php
	/**
	 * Plugin Name: Infocob CRM Forms
	 * Description: A plugin which allow to link Infocob data with forms data.
	 * Version: 2.3.7
	 * Author: Infocob web
	 * Author URI: https://www.infocob-web.com/
	 * License: GPL3
	 * License URI:  https://www.gnu.org/licenses/gpl-3.0.html
	 * Text Domain: infocob-crm-forms
	 * Domain Path: /languages
	 */
	
	namespace Infocob\CrmForms;
	
	use Infocob\CrmForms\Admin\InfocobCrmForms;
	
	include_once 'vendor/autoload.php';
	
	global $infocob_assets_version;
	global $infocob_db_version;
	global $infocob_gutenberg_blocks_version;
	$infocob_db_version = '1.3';
	$infocob_assets_version = '1.8';
	$infocob_gutenberg_blocks_version = '1.2';
	
    define('ROOT_INFOCOB_CRM_FORMS_FILE_PATH', __FILE__);
	define('ROOT_INFOCOB_CRM_FORMS_DIR_PATH', plugin_dir_path(__FILE__));
	define('ROOT_INFOCOB_CRM_FORMS_DIR_URL', plugin_dir_url(__FILE__));
	define('INFOCOB_CRM_FORMS_BASENAME', plugin_basename(__FILE__));
	define("INFOCOB_CRM_FORMS_HOSTNAME", parse_url(get_site_url(), PHP_URL_HOST)); // domain
	define("INFOCOB_CRM_FORMS_VERSIONS_NO_UPDATE", []);
	define("INFOCOB_CRM_FORMS_UPGRADE_VERSION", 1);
	if(!defined("INFOCOB_CRM_FORMS_COPY_EMAIL")) {
		define("INFOCOB_CRM_FORMS_COPY_EMAIL", false);
	}
	
	$infocobTracking = new InfocobCrmForms();
	$infocobTracking->init();

<?php
	
	namespace Infocob\CrmForms\Admin;
	
	include_once ABSPATH . '/wp-admin/includes/plugin.php';
	
	class InfocobCrmForms {
		
		public function __construct() {
			// nothing to do
		}
		
		public function init() {
			$this->add_actions();
			$this->add_filters();
			$this->addAdditionnalsFunctions();
			$this->register_wp_cli_cmd();
			
			$gutenberg = new GutenbergBlocks();
			$gutenberg->run();
		}
		
		public function add_actions() {
			add_action('init', [$this, 'infocob_init']);
			add_action('admin_init', [$this, 'infocob_admin_init']);
			
			add_action('admin_menu', [$this, 'infocob_admin_menu_pages']);
			
			add_action('admin_enqueue_scripts', [$this, 'infocob_admin_enqueue_scripts']);
			add_action('wp_enqueue_scripts', [$this, 'infocob_wp_enqueue_scripts']);
			
			add_action('add_meta_boxes', [$this, 'infocob_add_custom_box']);
			add_action('save_post_ifb_crm_forms', [$this, 'infocob_save_custom_box']);
			add_action('save_post_ifb_recipients', [$this, 'infocob_save_custom_box_recipients']);
			
			add_action('manage_ifb_crm_forms_posts_custom_column', [
				$this,
				'custom_ifb_crm_forms_column'
			], 10, 2);
			
			add_filter('manage_ifb_crm_forms_posts_columns', [$this, 'set_custom_edit_ifb_crm_forms_columns']);
			
			register_activation_hook(ROOT_INFOCOB_CRM_FORMS_FILE_PATH, [$this, 'onActivate']);
			register_activation_hook(ROOT_INFOCOB_CRM_FORMS_FILE_PATH, [new Database(), 'infocob_db_install']);
			register_activation_hook(ROOT_INFOCOB_CRM_FORMS_FILE_PATH, [new Database(), 'infocob_db_install_form']);
			
			add_action('admin_post_save_form_liaisons', [new FormLiaisonsCrm(), 'saveLiaisons']);
			add_action('wp_ajax_delete_data_form_liaisons', [new FormLiaisonsCrm(), 'delete_data_form_liaisons']);
			
			add_action('wpcf7_before_send_mail', [new FormSubmission(), 'process_cf7']);
			
			add_action('admin_post_nopriv_infocob-crm-forms-action_submit_form', [new FormSubmission(), 'process_ifb']);
			add_action('admin_post_infocob-crm-forms-action_submit_form', [new FormSubmission(), 'process_ifb']);
			
			add_action('wp_head', [$this, 'infocob_formspec_message_JS']);
			
			add_action('plugins_loaded', [$this, 'onPluginsLoaded']);
			add_action('plugins_loaded', [new Database(), 'infocob_update_process']);
			
			add_action('admin_action_rd_duplicate_post_as_draft', [$this, 'rd_duplicate_post_as_draft']);
			
			add_action('wp_ajax_infocob_crm_forms_export_action', [new FormImportExport(), 'export']);
			add_action('admin_post_infocob_crm_forms_import_action', [new FormImportExport(), 'import']);
			
			add_action('wp_ajax_infocob_crm_forms_get_logs_file', [new Logs(), 'wp_ajax_get_logs_file']);
			add_action('wp_ajax_infocob_crm_forms_download_logs_file', [new Logs(), 'wp_ajax_download_logs_file']);
		}
		
		public function add_filters() {
			add_filter('plugin_action_links_infocob-crm-forms/infocob-crm-forms.php', [$this, 'add_settings_link']);
			add_filter('site_transient_update_plugins', [$this, 'disable_plugin_updates']);
			add_filter('post_row_actions', [$this, 'rd_duplicate_post_link'], 10, 2);
		}
		
		public function onPluginsLoaded() {
			$saved_version = (int)get_option("INFOCOB_CRM_FORMS_UPGRADE_VERSION");
			if (empty($saved_version)) $saved_version = 0;
			// Upgrade system
			for ($i = ($saved_version + 1); $i <= INFOCOB_CRM_FORMS_UPGRADE_VERSION; $i++) {
				$upgrade_class = "\Infocob\CrmForms\Admin\Upgrade_version_" . $i;
				if (class_exists($upgrade_class)) {
					new $upgrade_class();
				}
			}
			
			update_option("INFOCOB_CRM_FORMS_UPGRADE_VERSION", INFOCOB_CRM_FORMS_UPGRADE_VERSION);
		}
		
		public function onActivate() {
			$saved_version = get_option("INFOCOB_CRM_FORMS_UPGRADE_VERSION");
			if (empty($saved_version)) {
				update_option("INFOCOB_CRM_FORMS_UPGRADE_VERSION", INFOCOB_CRM_FORMS_UPGRADE_VERSION);
			}
		}
		
		public function pll_register_string() {
			Polylang::addTranslations([
				// Email
				"Copie Infocob",
				
				// Formulaire
				"Formulaire",
				"Politique de confidentialité non accepté.",
				"Merci, votre message a bien été envoyé.",
				"Une erreur est survenue lors de l'envoi du message. Merci de réessayer plus tard.",
				"Anti-robot non validé.",
				"Tous les champs obligatoires doivent être complétés.",
				"Aucun destinataire disponible.",
				"Le type de fichier est incorrect et n'a pas pu être envoyé",
				'Le fichier doit être inférieur à %1$sMo et n\'a pas pu être envoyé.',
				"Une erreur est survenue lors de l'envoi du formulaire.",
			]);
			
			if (function_exists('pll_register_string')) {
				Polylang::registerStrings();
			}
		}
		
		public function infocob_role_caps() {
			/*
			 * Capabilities Super Administrator
			 * => Doesn't exists use "is_super_admin()"
			 */
			
			/*
			 * Capabilities Administrator
			 */
			$role = get_role("administrator");
			$caps = [
				Caps::$edit_settings,
				Caps::$edit_forms,
				Caps::$view_logs
			];
			foreach ($caps as $cap) {
				// Add a new capability.
				$role->add_cap($cap, true);
			}
			
			/*
			 * Capabilities Editor
			 */
			$role = get_role("editor");
			$caps = [
				Caps::$edit_forms,
				Caps::$view_logs
			];
			foreach ($caps as $cap) {
				// Add a new capability.
				$role->add_cap($cap, true);
			}
		}
		
		public function addAdditionnalsFunctions() {
			if (!function_exists('icf_e')) {
				function icf_e($string) {
					if (function_exists('pll_e')) {
						pll_e($string);
					} else {
						echo $string;
					}
				}
			}
			
			if (!function_exists('icf__')) {
				function icf__($string) {
					if (function_exists('pll__')) {
						return pll__($string);
					} else {
						return $string;
					}
				}
			}
			
			if (!function_exists('icf_translate_string')) {
				function icf_translate_string($string, $lang) {
					if (function_exists('pll_translate_string')) {
						return pll_translate_string($string, $lang);
					} else {
						return $string;
					}
				}
			}
		}
		
		public function register_wp_cli_cmd() {
			if (class_exists('\WP_CLI')) {
				\WP_CLI::add_command('icf', 'Infocob\CrmForms\Admin\WPCli\Core_Command', [
					"shortdesc" => "Provides information about the Infocob CRM Forms plugins"
				]);
			}
		}
		
		public function add_settings_link($links) {
			$url = esc_url(add_query_arg(
				[
					"page"      => "infocob-crm-forms-admin-settings-page",
					"post_type" => "ifb_crm_forms"
				],
				get_admin_url() . 'edit.php'
			));
			
			$settings_link = "<a href='" . $url . "'>" . __('Settings', "infocob-crm-forms") . "</a>";
			
			array_unshift(
				$links,
				$settings_link
			);
			
			return $links;
		}
		
		public function infocob_admin_init() {
		}
		
		public function disable_plugin_updates($value) {
			$options = get_option('infocob_crm_forms_settings');
			$breaking_changes_enable = !empty($options["breaking_change_update"]);
			$pluginData = get_plugin_data(ROOT_INFOCOB_CRM_FORMS_DIR_PATH . "infocob-crm-forms.php");
			$can_update = ((isset($pluginData["Version"]) && !in_array($pluginData["Version"], INFOCOB_CRM_FORMS_VERSIONS_NO_UPDATE)) || !isset($pluginData["Version"]));
			if (isset($value) && is_object($value) && !$can_update && !$breaking_changes_enable) {
				if (isset($value->response['infocob-crm-forms/infocob-crm-forms.php'])) {
					unset($value->response['infocob-crm-forms/infocob-crm-forms.php']);
				}
			}
			
			return $value;
		}
		
		public static function check_contact_form_7_is_activated($required = false, $notice = false) {
			$ok = true;
			if (is_admin() && current_user_can('activate_plugins') && !is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
				if ($notice) {
					add_action('admin_notices', function () {
						?>
						<div class="error">
							<p><?php _e("Sorry, the 'Contact form 7' plugin is required to activate the 'Infocob CRM Forms' plugin.", "infocob-crm-forms"); ?></p>
						</div>
						<?php
					});
				}
				
				if ($required) {
					deactivate_plugins(INFOCOB_CRM_FORMS_BASENAME);
					
					if (isset($_GET['activate'])) {
						unset($_GET['activate']);
					}
				}
				
				$ok = false;
			}
			
			return $ok;
		}
		
		public function custom_ifb_crm_forms_column($column, $post_id) {
			switch ($column) {
				case 'shortcode_form' :
					$admin_form_edit_json = get_post_meta($post_id, 'infocob_crm_forms_admin_form_config', true);
					$admin_form_edit = json_decode($admin_form_edit_json, true);
					
					if (!empty($admin_form_edit["shortcode_form"])) {
						echo '<input type="text" readonly class="infocob_crm_forms_copy" value="' . $admin_form_edit["shortcode_form"] . '"/>';
					} else {
						_e('Unable to get the shortcode', 'infocob-crm-forms');
					}
					break;
				
			}
		}
		
		public function set_custom_edit_ifb_crm_forms_columns($columns) {
			$reOrderColumns = array_slice($columns, 0, 2, true) + ["shortcode_form" => __('Shortcode', 'infocob-crm-forms')] + array_slice($columns, 2, count($columns) - 1, true);
			
			return $reOrderColumns;
		}
		
		public function infocob_shortcodes_init() {
			add_shortcode('infocob-crm-forms', 'Infocob\CrmForms\Admin\Shortcode::addForm');
		}
		
		public function infocob_admin_menu_pages() {
			add_submenu_page('edit.php?post_type=ifb_crm_forms', __('Settings', 'infocob-crm-forms'), __('Settings', 'infocob-crm-forms'), Caps::$edit_settings, 'infocob-crm-forms-admin-settings-page', [
				new AdminSettings(),
				'render'
			]);
			
			add_submenu_page('edit.php?post_type=ifb_crm_forms', __('Import/Export', 'infocob-crm-forms'), __('Import/Export', 'infocob-crm-forms'), Caps::$edit_settings, 'infocob-crm-forms-admin-import-export-page', [
				new FormImportExport(),
				'render'
			]);
			
			$webservice = new Webservice();
			$success = $webservice->test();
			if ($success) {
				add_submenu_page('edit.php?post_type=ifb_crm_forms', __('CRM Links', 'infocob-crm-forms'), __('CRM Links', 'infocob-crm-forms'), Caps::$edit_forms, 'infocob-crm-forms-admin-liaisons-crm-page', [
					new FormLiaisonsCrm(),
					'render'
				]);
			}
			
			add_submenu_page('edit.php?post_type=ifb_crm_forms', esc_html__('Logs', "infocob-crm-forms"), esc_html__('Logs', "infocob-crm-forms"), Caps::$view_logs, 'infocob-crm-forms-admin-logs', [
				new Logs(),
				'render'
			]);
		}
		
		public function infocob_init() {
			$status = session_status();
			if ($status === PHP_SESSION_NONE) {
				if (!(defined("DOING_CRON") && DOING_CRON)) {
					session_start();
				}
			}
			
			register_setting('infocob_crm_forms', 'infocob_crm_forms_settings', [
				"sanitize_callback" => function ($inputs) {
					if (!empty($inputs["api"]["domain"])) {
						$inputs["api"]["domain"] = rtrim($inputs["api"]["domain"], '/');
						preg_match('/^(?:https?:\/\/)?(.+)$/i', $inputs["api"]["domain"], $matches);
						if (!empty($matches[1])) {
							$inputs["api"]["domain"] = $matches[1];
						}
					}
					
					if (!empty($inputs["ec"]["domain"])) {
						$inputs["ec"]["domain"] = rtrim($inputs["ec"]["domain"], '/');
						preg_match('/^(?:https?:\/\/)?(.+)$/i', $inputs["ec"]["domain"], $matches);
						if (!empty($matches[1])) {
							$inputs["ec"]["domain"] = $matches[1];
						}
					}
					
					return $inputs;
				}
			]);
			
			$this->infocob_register_post_type();
			
			$this->infocob_shortcodes_init();
			
			$this->pll_register_string();
			
			$this->infocob_role_caps();
		}
		
		public function infocob_register_post_type() {
			//Posts Formulaire
			$labels = [
				'name'           => __('Infocob CRM Forms', 'infocob-crm-forms'),
				'singular_name'  => __('Infocob CRM Forms', 'infocob-crm-forms'),
				'menu_name'      => __('Infocob CRM Forms', 'infocob-crm-forms'),
				'name_admin_bar' => __('Infocob CRM Forms', 'infocob-crm-forms'),
				'add_new'        => __('Add', 'infocob-crm-forms'),
				'add_new_item'   => __('Create form', 'infocob-crm-forms'),
				'new_item'       => __('Create form', 'infocob-crm-forms'),
				'edit_item'      => __('Edit', 'infocob-crm-forms'),
				'view_item'      => __('See', 'infocob-crm-forms'),
				'all_items'      => __('Forms', 'infocob-crm-forms'),
				'search_items'   => __('Search', 'infocob-crm-forms'),
			];
			
			$args = [
				'labels'             => $labels,
				'description'        => __('Form', 'infocob-crm-forms'),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => false,
				'rewrite'            => ['slug' => _x('ifb_crm_forms', 'URL slug', 'infocob-crm-forms')],
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => 20,
				'menu_icon'          => 'dashicons-networking',
				'show_in_rest'       => true,
				'supports'           => ['title']
			];
			
			$option = get_option("infocob_crm_forms_settings");
			
			register_post_type('ifb_crm_forms', $args);
			
			if (isset($option["recipients"]["enabled"]) && filter_var($option["recipients"]["enabled"], FILTER_VALIDATE_BOOLEAN)) {
				//Posts Destinataires
				$labels = [
					'name'           => __('Recipients', 'infocob-crm-forms'),
					'singular_name'  => __('Recipient', 'infocob-crm-forms'),
					'menu_name'      => __('Recipients', 'infocob-crm-forms'),
					'name_admin_bar' => __('Recipients', 'infocob-crm-forms'),
					'add_new'        => __('Add', 'infocob-crm-forms'),
					'add_new_item'   => __('Create recipient', 'infocob-crm-forms'),
					'new_item'       => __('Create recipient', 'infocob-crm-forms'),
					'edit_item'      => __('Edit', 'infocob-crm-forms'),
					'view_item'      => __('See', 'infocob-crm-forms'),
					'all_items'      => __('Recipients', 'infocob-crm-forms'),
					'search_items'   => __('Search', 'infocob-crm-forms'),
				];
				
				$args = [
					'labels'             => $labels,
					'description'        => __('Recipient', 'infocob-crm-forms'),
					'public'             => false,
					'publicly_queryable' => false,
					'show_ui'            => true,
					'show_in_menu'       => "edit.php?post_type=ifb_crm_forms",
					'query_var'          => false,
					'rewrite'            => ['slug' => _x('ifb_recipients', 'URL slug', 'infocob-crm-forms')],
					'capability_type'    => 'post',
					'has_archive'        => false,
					'hierarchical'       => false,
					'menu_position'      => 20,
					'menu_icon'          => 'dashicons-networking',
					'show_in_rest'       => true,
					'supports'           => ['title']
				];
				
				register_post_type('ifb_recipients', $args);
			}
		}
		
		public function infocob_add_custom_box() {
			$formEdit = new FormEdit();
			
			add_meta_box('infocob_crm_forms_admin_form_config', // Unique ID
				__('Configuration', 'infocob-crm-forms'),       // Box title
				[$formEdit, 'renderFormConfigMetabox'],         // Content callback, must be of type callable
				'ifb_crm_forms' // Post type
			);
			
			add_meta_box('infocob_crm_forms_admin_form_email', // Unique ID
				__('Email', 'infocob-crm-forms'),              // Box title
				[$formEdit, 'renderFormEmailMetabox'],         // Content callback, must be of type callable
				'ifb_crm_forms' // Post type
			);
			
			$options = get_option('infocob_crm_forms_settings');
			$options_additional_email = isset($options['additional_email_max_number']) ? (int)$options['additional_email_max_number'] : 1;
			
			if (!empty(abs($options_additional_email))) {
				add_meta_box('infocob_crm_forms_admin_form_email_supp', // Unique ID
					__('Additional email', 'infocob-crm-forms'),        // Box title
					[$formEdit, 'renderFormAdditionalEmailMetabox'],    // Content callback, must be of type callable
					'ifb_crm_forms' // Post type
				);
			}
			
			$options_recipients = isset($options["recipients"]["enabled"]) ? filter_var($options["recipients"]["enabled"], FILTER_VALIDATE_BOOLEAN) : false;
			
			if ($options_recipients) {
				$recipientEdit = new RecipientEdit();
				
				add_meta_box('infocob_crm_forms_admin_recipients_config', // Unique ID
					__('Recipients', 'infocob-crm-forms'),                // Box title
					[$recipientEdit, 'renderRecipientConfig'],            // Content callback, must be of type callable
					'ifb_recipients' // Post type
				);
			}
		}
		
		/**
		 * Add the duplicate link to action list for post_row_actions
		 * @link https://rudrastyh.com/wordpress/duplicate-post.html
		 */
		public function rd_duplicate_post_as_draft() {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			global $wpdb;
			if (!(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'rd_duplicate_post_as_draft' == $_REQUEST['action']))) {
				wp_die('No post to duplicate has been supplied!');
			}
			
			/*
			 * Nonce verification
			 */
			if (!isset($_GET['duplicate_nonce']) || !wp_verify_nonce($_GET['duplicate_nonce'], "ifb_form_duplicate")) {
				return;
			}
			
			/*
			 * get the original post id
			 */
			$post_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
			/*
			 * and all the original post data then
			 */
			$post = get_post($post_id);
			
			/*
			 * if you don't want current user to be the new post author,
			 * then change next couple of lines to this: $new_post_author = $post->post_author;
			 */
			//$current_user    = wp_get_current_user();
			//$new_post_author = $current_user->ID;
			$new_post_author = $post->post_author;
			
			/*
			 * if post data exists, create the post duplicate
			 */
			if (isset($post) && $post != null) {
				
				/*
				 * new post data array
				 */
				$args = [
					'comment_status' => $post->comment_status,
					'ping_status'    => $post->ping_status,
					'post_author'    => $new_post_author,
					'post_content'   => $post->post_content,
					'post_excerpt'   => $post->post_excerpt,
					'post_name'      => $post->post_name,
					'post_parent'    => $post->post_parent,
					'post_password'  => $post->post_password,
					'post_status'    => 'draft',
					'post_title'     => $post->post_title,
					'post_type'      => $post->post_type,
					'to_ping'        => $post->to_ping,
					'menu_order'     => $post->menu_order
				];
				
				/*
				 * insert the post by wp_insert_post() function
				 */
				$new_post_id = wp_insert_post($args);
				
				/*
				 * get all current post terms ad set them to the new post draft
				 */
				$taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
				foreach ($taxonomies as $taxonomy) {
					$post_terms = wp_get_object_terms($post_id, $taxonomy, ['fields' => 'slugs']);
					wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
				}
				
				/*
				 * duplicate all post meta just in two SQL queries
				 */
				$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
				if (count($post_meta_infos) != 0) {
					$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
					foreach ($post_meta_infos as $meta_info) {
						$meta_key = $meta_info->meta_key;
						if ($meta_key == 'infocob_crm_forms_admin_form_config') {
							$config = json_decode($meta_info->meta_value, true);
							unset($config["shortcode_form"]);
							$meta_info->meta_value = json_encode($config, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
							//continue;
						}
						$meta_value = addslashes($meta_info->meta_value);
						$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
					}
					$sql_query .= implode(" UNION ALL ", $sql_query_sel);
					$wpdb->query($sql_query);
				}
				
				/*
				 * finally, redirect to the edit post screen for the new draft
				 */
				wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
				exit;
			} else {
				wp_die('Post creation failed, could not find original post: ' . $post_id);
			}
		}
		
		/*
         * Add the duplicate link to action list for post_row_actions
		 * @link https://rudrastyh.com/wordpress/duplicate-post.html
         */
		function rd_duplicate_post_link($actions, $post) {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			if ($post->post_type == 'ifb_crm_forms' && current_user_can(Caps::$edit_forms)) {
				$actions['duplicate'] = '<a href="admin.php?action=rd_duplicate_post_as_draft&post=' . $post->ID . '&duplicate_nonce=' . wp_create_nonce('ifb_form_duplicate') . '" title="Duplicate" rel="permalink">' . __('Duplicate', 'infocob-crm-forms') . '</a>';
			}
			
			return $actions;
		}
		
		public function infocob_save_custom_box_recipients($post_id) {
			$post_metas = [];
			
			$post_metas["recipients"] = $_POST["recipients"] ?? [];
			
			update_post_meta($post_id, 'infocob_crm_forms_admin_recipients_config', $post_metas);
		}
		
		public function infocob_save_custom_box($post_id) {
			$mode_avance = !empty($_POST["mode_avance_enable"]);
			
			if (array_key_exists('post_id', $_POST) && $mode_avance) {
				$config_avancee = json_decode(stripslashes($_POST["mode_avance_console"] ?? ""), true);
				
				$config_form_avancee = isset($config_avancee["infocob_crm_forms_admin_form_config"]) ? $config_avancee["infocob_crm_forms_admin_form_config"] : [];
				$config_email_avancee = isset($config_avancee["infocob_crm_forms_admin_form_email_config"]) ? $config_avancee["infocob_crm_forms_admin_form_email_config"] : [];
				$config_email_supp_avancee = isset($config_avancee["infocob_crm_forms_admin_form_email_supp_config"]) ? $config_avancee["infocob_crm_forms_admin_form_email_supp_config"] : [];
				
				//$_POST = array_merge($config_form_avancee, $config_email_avancee);
				
				$admin_form_edit_json = addslashes(json_encode($config_form_avancee, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
				$template_email_form_json = addslashes(json_encode($config_email_avancee, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
				$template_email_supp_form_json = addslashes(json_encode($config_email_supp_avancee, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
				
				update_post_meta($post_id, 'infocob_crm_forms_admin_form_config', $admin_form_edit_json);
				update_post_meta($post_id, 'infocob_crm_forms_admin_form_email_config', $template_email_form_json);
				update_post_meta($post_id, 'infocob_crm_forms_admin_form_email_supp_config', $template_email_supp_form_json);
				
			} else {
				if (array_key_exists('post_id', $_POST) &&
					array_key_exists('btn_send', $_POST) &&
					array_key_exists('max_file_size', $_POST) &&
					array_key_exists('input', $_POST)) {
					
					$admin_form_edit["post_id"] = sanitize_text_field($_POST["post_id"]);
					$admin_form_edit["shortcode_form"] = sanitize_text_field($_POST["shortcode_form"]);
					$admin_form_edit["fullwidth"] = !empty($_POST["fullwidth"]) ? true : false;
					$admin_form_edit["recipients_enabled"] = !empty($_POST["recipients_enabled"]) ? true : false;
					$admin_form_edit["email_supp_enable"] = !empty($_POST["email_supp_enable"]) ? true : false;
					$admin_form_edit["btn_send"] = !empty($_POST["btn_send"]) ? sanitize_text_field($_POST["btn_send"]) : "";
					$admin_form_edit["max_file_size"] = !empty($_POST["max_file_size"]) ? sanitize_text_field($_POST["max_file_size"]) : 2 * 1024 * 1024;
					$admin_form_edit["columns_base"] = !empty($_POST["columns_base"]) ? sanitize_text_field($_POST["columns_base"]) : 4;
					$admin_form_edit["input_rgpd"] = !empty($_POST["input_rgpd"]) ? wp_check_invalid_utf8($_POST["input_rgpd"]) : "";
					$admin_form_edit["disable_rgpd"] = !empty($_POST["disable_rgpd"]) ? true : false;
					$admin_form_edit["input"] = $_POST["input"];                                                                                                  //Tools::sanitize_fields($_POST["input"]);
					$admin_form_edit["redirect_page_submit"] = !empty($_POST["redirect_page_submit"]) ? sanitize_text_field($_POST["redirect_page_submit"]) : ""; //Tools::sanitize_fields($_POST["input"]);
					
					$admin_form_edit["type_formulaire"] = sanitize_text_field($_POST["type_formulaire"] ?? "");
					$admin_form_edit["ec_module_telechargement"] = sanitize_text_field($_POST["ec_module_telechargement"] ?? "");
					$admin_form_edit["ec_connection_fields"] = $_POST["ec_connection_fields"] ?? [];
					
					foreach ($admin_form_edit["input"] as &$values) {
						if (isset($values["type"]) && strcasecmp($values["type"], "groupe") !== 0) {
							$values["nom"] = preg_replace('/\s+/', '_', esc_attr(strtolower(trim($values["nom"]))));
							$values["col"] = (filter_var($values["col"], FILTER_VALIDATE_INT) <= filter_var($admin_form_edit["columns_base"], FILTER_VALIDATE_INT)) ? $values["col"] : 1;
						}
						
						if (isset($values["type"]) && strcasecmp($values["type"], "groupe") === 0) {
							if (empty($values["champs"])) {
								$values["champs"] = [];
							}
						}
					}
					
					$admin_form_edit_json = json_encode($admin_form_edit, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
					
					update_post_meta($post_id, 'infocob_crm_forms_admin_form_config', $admin_form_edit_json);
				}
				
				if (array_key_exists('email_from', $_POST) &&
					array_key_exists('email_subject', $_POST)) {
					$template_email_form = [];
					$template_email_form["email_from"] = !empty($_POST["email_from"]) ? sanitize_email($_POST["email_from"]) : sanitize_email(get_bloginfo('admin_email'));
					$template_email_form["emails_to"] = !empty($_POST["emails_to"]) ? Tools::sanitize_fields($_POST["emails_to"]) : [
						[
							"email"    => sanitize_email(get_bloginfo('admin_email')),
							"fullname" => "",
						]
					];
					
					$template_email_form["email_form_reply"] = !empty($_POST["email_form_reply"]) ? Tools::sanitize_fields($_POST["email_form_reply"]) : [
						"email"     => "",
						"firstname" => "",
						"lastname"  => "",
					];
					
					$template_email_form["email_subject"] = !empty($_POST["email_subject"]) ? sanitize_text_field($_POST["email_subject"]) : "Formulaire de contact | " . get_bloginfo('name');
					$template_email_form["email_societe"] = !empty($_POST["email_societe"]) ? sanitize_text_field($_POST["email_societe"]) : get_bloginfo("name");
					$template_email_form["email_title"] = !empty($_POST["email_title"]) ? sanitize_text_field($_POST["email_title"]) : get_bloginfo("name") . " - Formulaire de contact";
					$template_email_form["email_subtitle"] = !empty($_POST["email_subtitle"]) ? sanitize_text_field($_POST["email_subtitle"]) : "";
					$template_email_form["email_color"] = !empty($_POST["email_color"]) ? sanitize_text_field($_POST["email_color"]) : "#0271b8";
					$template_email_form["email_color_text_title"] = !empty($_POST["email_color_text_title"]) ? sanitize_text_field($_POST["email_color_text_title"]) : "#ffffff";
					$template_email_form["email_color_link"] = !empty($_POST["email_color_link"]) ? sanitize_text_field($_POST["email_color_link"]) : "#0271b8";
					$template_email_form["email_logo"] = !empty($_POST["email_logo"]) ? Tools::sanitize_fields($_POST["email_logo"]) : [];
					$template_email_form["email_template"] = !empty($_POST["email_template"]) ? sanitize_text_field($_POST["email_template"]) : "defaut-infocob-crm-forms";
					$template_email_form["email_border_radius"] = isset($_POST["email_border_radius"]) ? sanitize_text_field($_POST["email_border_radius"]) : 0;
					
					$template_email_form["email_recipients"] = isset($_POST["email_recipients"]) ? Tools::sanitize_fields($_POST["email_recipients"]) : [];
					
					$template_email_form_json = json_encode($template_email_form, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
					
					update_post_meta($post_id, 'infocob_crm_forms_admin_form_email_config', $template_email_form_json);
				}
				
				if (array_key_exists('additional_email', $_POST)) {
					$template_additional_email = [];
					
					$additional_email = $_POST["additional_email"];
					if (is_array($additional_email) && !empty($additional_email)) {
						$template_additional_email = [];
						
						$nbAddEmail = 0;
						foreach ($additional_email as $email) {
							$template_additional_email[$nbAddEmail]["enable"] = !empty($email["enable"]) ? $email["enable"] : false;
							$template_additional_email[$nbAddEmail]["from"] = !empty($email["from"]) ? sanitize_email($email["from"]) : sanitize_email(get_bloginfo('admin_email'));
							$template_additional_email[$nbAddEmail]["to"] = !empty($email["to"]) ? Tools::sanitize_fields($email["to"]) : [];
							
							$template_additional_email[$nbAddEmail]["field_to"] = !empty($email["field_to"]) ? Tools::sanitize_fields($email["field_to"]) : [];
							$template_additional_email[$nbAddEmail]["subject"] = !empty($email["subject"]) ? sanitize_text_field($email["subject"]) : "Formulaire de contact | " . get_bloginfo('name');
							$template_additional_email[$nbAddEmail]["societe"] = !empty($email["societe"]) ? sanitize_text_field($email["societe"]) : get_bloginfo("name");
							$template_additional_email[$nbAddEmail]["title"] = !empty($email["title"]) ? sanitize_text_field($email["title"]) : get_bloginfo("name") . " - Formulaire de contact";
							$template_additional_email[$nbAddEmail]["subtitle"] = !empty($email["subtitle"]) ? sanitize_text_field($email["subtitle"]) : "";
							$template_additional_email[$nbAddEmail]["color"] = !empty($email["color"]) ? sanitize_text_field($email["color"]) : "#0271b8";
							$template_additional_email[$nbAddEmail]["color_text_title"] = !empty($email["color_text_title"]) ? sanitize_text_field($email["color_text_title"]) : "#ffffff";
							$template_additional_email[$nbAddEmail]["color_link"] = !empty($email["color_link"]) ? sanitize_text_field($email["color_link"]) : "#0271b8";
							$template_additional_email[$nbAddEmail]["template"] = !empty($email["template"]) ? sanitize_text_field($email["template"]) : "defaut-infocob-crm-forms";
							$template_additional_email[$nbAddEmail]["logo"] = !empty($email["logo"]) ? Tools::sanitize_fields($email["logo"]) : [];
							$template_additional_email[$nbAddEmail]["template"] = !empty($email["template"]) ? sanitize_text_field($email["template"]) : "defaut-infocob-crm-forms";
							$template_additional_email[$nbAddEmail]["border_radius"] = isset($email["border_radius"]) ? sanitize_text_field($email["border_radius"]) : 0;
							
							$template_additional_email[$nbAddEmail]["recipients"] = isset($email["recipients"]) ? Tools::sanitize_fields($email["recipients"]) : [];
							
							$template_additional_email[$nbAddEmail]["no_original_attachements"] = !empty($email["no_original_attachements"]) ? $email["no_original_attachements"] : false;
							$template_additional_email[$nbAddEmail]["attachments"] = !empty($email["attachments"]) ? Tools::sanitize_fields($email["attachments"]) : [];
							
							$nbAddEmail++;
						}
					}
					
					$template_additional_email_json = json_encode($template_additional_email, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
					
					update_post_meta($post_id, 'infocob_crm_forms_admin_form_additional_email_config', $template_additional_email_json);
				}
			}
		}
		
		public function infocob_wp_enqueue_scripts($hook) {
		
		}
		
		public function infocob_admin_enqueue_scripts($hook) {
			global $infocob_assets_version;
			global $post_type;
			
			// Scripts
			// Libs
			wp_register_script('infocob_crm_forms_multiple_select_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/multiple-select/multiple-select.min.js', [], $infocob_assets_version);
			wp_register_script('infocob_crm_forms_select2_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/select2/js/select2.full.min.js', [], $infocob_assets_version);
			wp_register_script('infocob_crm_forms_sweetalert2_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/sweetalert2/sweetalert2.all.min.js', [], $infocob_assets_version);
			wp_register_script('infocob_crm_forms_sortable_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/sortable/sortable.min.js', [], $infocob_assets_version);
			wp_register_script('infocob_crm_forms_sortable_jquery_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/sortable/jquery-sortable.js', [], $infocob_assets_version);
			wp_register_script('infocob_crm_forms_popper_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/tippy/popper.min.js', [], $infocob_assets_version);
			wp_register_script('infocob_crm_forms_tippy_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/tippy/tippy-bundle.umd.min.js', [], $infocob_assets_version);
			
			wp_register_script('infocob_crm_forms_admin_quicktags_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'admin/assets/js/admin_quicktags.js', [
				'jquery',
				'quicktags'
			], $infocob_assets_version);
			wp_register_script('infocob_crm_forms_admin_settings_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'admin/assets/js/admin_settings.js', [
				'jquery',
				'wp-color-picker'
			], $infocob_assets_version);
			wp_register_script('infocob_crm_forms_admin_form_edit_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'admin/assets/js/admin_form_edit.js', [
				'jquery',
				'jquery-ui-core',
				'jquery-ui-tabs',
				'jquery-ui-accordion',
				'infocob_crm_forms_popper_js',
				'infocob_crm_forms_tippy_js',
				'wp-color-picker',
				'wp-i18n'
			], $infocob_assets_version);
			wp_set_script_translations('infocob_crm_forms_admin_form_edit_js', 'infocob-crm-forms', ROOT_INFOCOB_CRM_FORMS_DIR_PATH . 'languages');
			
			wp_register_script('infocob_crm_forms_admin_forms_liaisons_crm_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'admin/assets/js/admin_forms_liaisons_crm.js', [
				'jquery',
				'wp-i18n'
			], $infocob_assets_version);
			wp_set_script_translations('infocob_crm_forms_admin_forms_liaisons_crm_js', 'infocob-crm-forms', ROOT_INFOCOB_CRM_FORMS_DIR_PATH . 'languages');
			
			wp_register_script('infocob_crm_forms_admin_forms_export_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'admin/assets/js/admin_forms_import_export.js', [
				'jquery'
			], $infocob_assets_version);
			
			wp_register_script('infocob_crm_forms_admin_recipients_edit_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'admin/assets/js/admin_recipients_edit.js', [
				'jquery',
				'wp-i18n'
			], $infocob_assets_version);
			wp_set_script_translations('infocob_crm_forms_admin_recipients_edit_js', 'infocob-crm-forms', ROOT_INFOCOB_CRM_FORMS_DIR_PATH . 'languages');
			
			// Styles
			// Libs
			wp_register_style('infocob_crm_forms_multiple_select_css', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/multiple-select/multiple-select.min.css', [], $infocob_assets_version);
			wp_register_style('infocob_crm_forms_multiple_select_theme_css', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/multiple-select/themes/bootstrap.css', [], $infocob_assets_version);
			wp_register_style('infocob_crm_forms_select2_css', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/select2/css/select2.min.css', [], $infocob_assets_version);
			wp_register_style('infocob_crm_forms_sweetalert2_css', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/sweetalert2/sweetalert2.min.css', [], $infocob_assets_version);
			
			wp_register_style('infocob_crm_forms_admin_settings_css', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'admin/assets/css/admin_settings.css', [], $infocob_assets_version);
			wp_register_style('infocob_crm_forms_admin_form_edit_css', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'admin/assets/css/admin_form_edit.css', [], $infocob_assets_version);
			wp_register_style('infocob_crm_forms_admin_recipients_edit_css', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'admin/assets/css/admin_recipients_edit.css', [], $infocob_assets_version);
			wp_register_style('infocob_crm_forms_admin_forms_liaisons_crm_css', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'admin/assets/css/admin_forms_liaisons_crm.css', [], $infocob_assets_version);
			wp_register_style('infocob_crm_forms_admin_form_import_export_css', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'admin/assets/css/admin_form_import_export.css', [], $infocob_assets_version);
			
			if ($hook == "ifb_crm_forms_page_infocob-crm-forms-admin-settings-page") {
				// Scripts
				wp_enqueue_script('infocob_crm_forms_select2_js');
				wp_enqueue_script('infocob_crm_forms_admin_settings_js');
				
				// Styles
				wp_enqueue_style('infocob_crm_forms_select2_css');
				wp_enqueue_style('infocob_crm_forms_admin_settings_css');
			}
			
			if ($hook == "ifb_crm_forms_page_infocob-crm-forms-admin-liaisons-crm-page") {
				require_once(ABSPATH . 'wp-includes/pluggable.php');
				
				// Scripts
				wp_enqueue_script('infocob_crm_forms_multiple_select_js');
				wp_enqueue_script('infocob_crm_forms_admin_forms_liaisons_crm_js');
				
				// Styles
				wp_enqueue_style('infocob_crm_forms_multiple_select_css');
				wp_enqueue_style('infocob_crm_forms_multiple_select_theme_css');
				wp_enqueue_style('infocob_crm_forms_admin_forms_liaisons_crm_css');
				
				wp_localize_script('infocob_crm_forms_admin_forms_liaisons_crm_js', 'infocob_ajax_delete_form_liaisons', [
					'url'   => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce('delete_data_form_liaisons_nonce'),
				]);
				
				add_action("admin_head", function () {
					echo "<div class='infocob_crm_forms_loader'><span class='img_loader'></span><span class='text_loader'>" . __("Loading...", "infocob-crm-forms") . "</span></div>";
				});
			}
			
			if ($hook == "ifb_crm_forms_page_infocob-crm-forms-admin-import-export-page") {
				require_once(ABSPATH . 'wp-includes/pluggable.php');
				
				// Scripts
				wp_enqueue_script('infocob_crm_forms_admin_forms_export_js');
				
				// Styles
				wp_enqueue_style("infocob_crm_forms_admin_form_import_export_css");
				
				wp_localize_script('infocob_crm_forms_admin_forms_export_js', 'infocob_ajax_export_form', [
					'url'   => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce('infocob_crm_forms_export'),
				]);
			}
			
			if ($post_type === 'ifb_crm_forms') {
				wp_enqueue_media();
				
				// Scripts
				wp_enqueue_script('infocob_crm_forms_sweetalert2_js');
				wp_enqueue_script('infocob_crm_forms_multiple_select_js');
				wp_enqueue_script('infocob_crm_forms_admin_form_edit_js');
				wp_enqueue_script('infocob_crm_forms_admin_quicktags_js');
				wp_enqueue_script('infocob_crm_forms_sortable_js');
				wp_enqueue_script('infocob_crm_forms_sortable_jquery_js');
				
				// Styles
				wp_enqueue_style('infocob_crm_forms_multiple_select_css');
				wp_enqueue_style('infocob_crm_forms_multiple_select_theme_css');
				wp_enqueue_style('infocob_crm_forms_sweetalert2_css');
				wp_enqueue_style('infocob_crm_forms_admin_form_edit_css');
			}
			
			if ($post_type === 'ifb_recipients') {
				wp_enqueue_media();
				
				// Scripts
				wp_enqueue_script('infocob_crm_forms_admin_recipients_edit_js');
				
				// Styles
				wp_enqueue_style('infocob_crm_forms_admin_recipients_edit_css');
			}
			
			if ($hook === "ifb_crm_forms_page_infocob-crm-forms-admin-logs") {
				require_once(ABSPATH . 'wp-includes/pluggable.php');
				
				/*
				 * Datatables
				 */
				if (!wp_script_is("infocob_crm_forms_datatables_js")) {
					wp_register_script('infocob_crm_forms_datatables_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/datatables/js/jquery.dataTables.min.js', [], $infocob_assets_version);
				}
				wp_enqueue_script('infocob_crm_forms_datatables_js');
				
				/*
				 * File admin_forms_logs.js
				 */
				wp_register_script('infocob_crm_forms_logs_js', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'admin/assets/js/admin_forms_logs.js', [
					'jquery',
					'wp-i18n',
				], $infocob_assets_version);
				wp_enqueue_script('infocob_crm_forms_logs_js');
				wp_set_script_translations('infocob_crm_forms_logs_js', "infocob-crm-forms");
				
				wp_localize_script('infocob_crm_forms_logs_js', 'infocob_ajax_get_logs_forms', [
					'url'   => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce('get_logs_forms_nonce'),
				]);
				
				/*
				 * Styles
				 */
				wp_register_style('infocob_crm_forms_datatables_css', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'libs/datatables/css/jquery.dataTables.min.css', [], $infocob_assets_version);
				wp_enqueue_style('infocob_crm_forms_datatables_css');
				
				wp_register_style('infocob_crm_forms_logs_css', ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'admin/assets/css/admin_forms_logs.css', [], $infocob_assets_version);
				wp_enqueue_style('infocob_crm_forms_logs_css');
			}
		}
		
		public function infocob_formspec_message_JS() {
			$color = get_theme_mod('theme_couleur_main') ? get_theme_mod('theme_couleur_main') : "#0271b8";
			echo "<style>.infocob-crm-forms-ajax-loader svg { stroke: " . $color . "; }</style>";
			
			?>
			<script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function () {
                    let casesnobots = document.querySelectorAll('.if-casenorobot');


                    casesnobots.forEach(function (val, key) {
                        let text = val.innerHTML;

                        let input = document.createElement("input");
                        input.type = 'checkbox';
                        input.name = 'accept-rgpd';
                        input.required = 'required';

                        let span = document.createElement("span");
                        span.innerHTML = text;

                        val.innerHTML = "";
                        val.appendChild(input);
                        val.appendChild(span);
                    });
					
					<?php if (FormSenderIfb::getIsMessageSent() !== null) { ?>

                    var divPop = document.createElement("div");
                    divPop.classList.add('if-message-retour-pop');
                    divPop.style.display = "flex";
                    divPop.style.position = "fixed";
                    divPop.style.width = "100%";
                    divPop.style.height = "100%";
                    divPop.style.background = "rgba(0,0,0,.7)";
                    divPop.style.zIndex = 9999;
                    divPop.style.top = 0;
                    divPop.style.left = 0;
                    divPop.style.justifyContent = "center";

                    var divInner = document.createElement("div");
                    divInner.style.position = "relative";
                    divInner.style.display = "block";
                    divInner.style.width = "100%";
                    divInner.style.maxWidth = "500px";
                    divInner.style.height = "auto";
                    divInner.style.background = "white";
                    divInner.style.alignSelf = "center";
                    divInner.style.padding = "25px";

                    let titre = document.createElement("p");
                    titre.style.fontSize = "18px";
                    titre.style.fontWeight = "bold";
                    titre.style.textAlign = "center";
                    titre.style.color = <?php echo json_encode([FormSenderIfb::getIsMessageSent() ? "#85c385" : "#de7777"]); ?>;
                    titre.innerHTML = <?php echo json_encode(FormSenderIfb::getReturnMessage()); ?>;

                    let button = document.createElement("span");
                    button.style.display = "block";
                    button.style.width = "35px";
                    button.style.height = "35px";
                    button.style.opacity = "0.3";
                    button.style.padding = "10px";
                    button.style.position = "absolute";
                    button.style.top = "0";
                    button.style.right = "0";
                    button.style.zIndex = "4";
                    button.innerHTML = `
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                             viewBox="0 0 492 492" style="enable-background:new 0 0 492 492;" xml:space="preserve">
                                <path d="M300.188,246L484.14,62.04c5.06-5.064,7.852-11.82,7.86-19.024c0-7.208-2.792-13.972-7.86-19.028L468.02,7.872
                                    c-5.068-5.076-11.824-7.856-19.036-7.856c-7.2,0-13.956,2.78-19.024,7.856L246.008,191.82L62.048,7.872
                                    c-5.06-5.076-11.82-7.856-19.028-7.856c-7.2,0-13.96,2.78-19.02,7.856L7.872,23.988c-10.496,10.496-10.496,27.568,0,38.052
                                    L191.828,246L7.872,429.952c-5.064,5.072-7.852,11.828-7.852,19.032c0,7.204,2.788,13.96,7.852,19.028l16.124,16.116
                                    c5.06,5.072,11.824,7.856,19.02,7.856c7.208,0,13.968-2.784,19.028-7.856l183.96-183.952l183.952,183.952
                                    c5.068,5.072,11.824,7.856,19.024,7.856h0.008c7.204,0,13.96-2.784,19.028-7.856l16.12-16.116
                                    c5.06-5.064,7.852-11.824,7.852-19.028c0-7.204-2.792-13.96-7.852-19.028L300.188,246z"/>
                        </svg>
                    `;
                    button.style.textAlign = "center";
                    button.style.fontSize = "14px";
                    button.style.lineHeight = "20px";
                    button.style.fontWeight = "bold";
                    button.style.cursor = "pointer";

                    divInner.appendChild(titre);
                    divInner.appendChild(button);
                    divPop.appendChild(divInner);

                    divPop.addEventListener("click", function (ev) {
                        document.body.removeChild(divPop);
                    });

                    button.addEventListener("click", function (ev) {
                        document.body.removeChild(divPop);
                    });

                    divInner.addEventListener("click", function (ev) {
                        ev.stopPropagation();
                    });

                    document.body.appendChild(divPop);

                    // Emit event
                    let SendEvent = new CustomEvent('infocob-crm-forms_form-send-success', {
                        detail: {
                            form_id: <?php echo FormSenderIfb::getSubmitForm(); ?>
                        }
                    });
                    let body = document.getElementsByTagName("body");
                    if (body.length > 0) {
                        body[0].dispatchEvent(SendEvent);
                    }
					
					<?php } ?>
                });
			</script>
			<?php
		}
		
	}

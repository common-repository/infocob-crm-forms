<?php
	
	
	namespace Infocob\CrmForms\Admin;
 
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FormImportExport extends Controller {
		
		public function render() {
			if(isset($_GET["success"])) {
				if(!empty($_GET["success"])) {
					add_action('infocob-crm-forms_admin_notices', function() {
						?>
                        <div class="notice notice-success">
                            <p><?php _e('Import succeed !', 'infocob-crm-forms') ?><br />
                        </div>
						<?php
					});
				} else {
					add_action('infocob-crm-forms_admin_notices', function() {
						?>
                        <div class="notice notice-error">
                            <p><?php _e('Import failed !', 'infocob-crm-forms') ?><br />
                        </div>
						<?php
					});
				}
			}
			
			do_action("infocob-crm-forms_admin_notices");
			
			$wp_query_ifb_crm_forms = new \WP_Query([
				'post_type'   => 'ifb_crm_forms',
				'post_status' => 'publish'
			]);
			
			include ROOT_INFOCOB_CRM_FORMS_DIR_PATH . "admin/includes/admin_form_import_export.php";
		}
		
		public function export() {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			if(!empty($_POST) && isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], 'infocob_crm_forms_export') && current_user_can(Caps::$edit_forms) && !empty($_POST["form_export_id"])) {
				global $wpdb;
				
				$post_id           = absint($_POST["form_export_id"]);
				$post              = get_post($post_id);
				$export_config_crm = $_POST["export_config_crm"] ?? false;
				
				if(isset($post) && $post != null) {
					/*
					 * get all post meta
					 */
					$export          = [];
					$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
					if(count($post_meta_infos) != 0) {
						foreach($post_meta_infos as $meta_info) {
							$meta_key = $meta_info->meta_key;
							if(in_array($meta_key, [
								"infocob_crm_forms_admin_form_email_config",
								"infocob_crm_forms_admin_form_config",
								"infocob_crm_forms_admin_form_additional_email_config"
							])) {
								$config = json_decode($meta_info->meta_value, true);
								if(is_array($config)) {
									unset($config["shortcode_form"]);
								}
								$export[ $meta_key ] = $config;
							}
						}
					}
					
					$export["post"] = $post;
					
					/*
					* get config crm
					*/
					if(!empty($export_config_crm)) {
						$config_crm = $wpdb->get_results("SELECT * FROM infocob_form_ifb WHERE idPostContactForm=$post_id LIMIT 1", ARRAY_A);
						if(!empty($config_crm[0])) {
							$export["config_crm"] = $config_crm[0];
						}
					}
					
					$export_json = json_encode($export, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
					
					// download export json
					header('Content-Description: File Transfer');
					header('Content-Type: application/json');
					header('Content-disposition: attachment; filename=infocob_crm_forms_export.json');
					header('Content-Length: ' . strlen($export_json));
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Expires: 0');
					header('Pragma: public');
					echo $export_json;
					exit;
				}
			}
		}
		
		public function import() {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			$redirect_url = admin_url("edit.php?post_type=ifb_crm_forms&page=infocob-crm-forms-admin-import-export-page");
			if(!empty($_POST) && isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], 'infocob_crm_forms_import') && current_user_can(Caps::$edit_forms) && !empty($_FILES["import_json_file"])) {
				
				$import_json_file = $_FILES["import_json_file"];
				$config           = json_decode(file_get_contents($import_json_file["tmp_name"]), true);
				
				if(isset($config["post"])
				   && isset($config["infocob_crm_forms_admin_form_email_config"])) {
					
					$post                    = $config["post"];
					$config_form             = $config["infocob_crm_forms_admin_form_config"];
					$config_email            = $config["infocob_crm_forms_admin_form_email_config"] ?? "{}";
					$config_additional_email = $config["infocob_crm_forms_admin_form_additional_email_config"] ?? "[]";
					$config_crm              = $config["config_crm"] ?? false;
					
					$post_title  = isset($post["post_title"]) ? $post["post_title"] : false;
					$post_status = isset($post["post_status"]) ? $post["post_status"] : false;
					$post_name   = isset($post["post_name"]) ? $post["post_name"] : false;
					$post_type   = isset($post["post_type"]) ? $post["post_type"] : false;
					
					if($post_title !== false && $post_status !== false && $post_name !== false && $post_type !== false) {
						/*
						 * new post data array
						 */
						$current_user    = wp_get_current_user();
						$new_post_author = $current_user->ID;
						
						$args = array(
							'post_author' => $new_post_author,
							'post_name'   => $post_name,
							'post_status' => $post_status,
							'post_title'  => $post_title,
							'post_type'   => $post_type,
						);
						
						/*
						 * insert the post by wp_insert_post() function
						 */
						$new_post_id = wp_insert_post($args);
						
						if(!empty($new_post_id)) {
							$config_form["shortcode_form"] = "[infocob-crm-forms id='" . $new_post_id . "']";
							
							$admin_form_edit_json                = addslashes(json_encode($config_form, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
							$template_email_form_json            = addslashes(json_encode($config_email, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
							$template_additional_email_form_json = addslashes(json_encode($config_additional_email, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
							
							update_post_meta($new_post_id, 'infocob_crm_forms_admin_form_config', $admin_form_edit_json);
							update_post_meta($new_post_id, 'infocob_crm_forms_admin_form_email_config', $template_email_form_json);
							update_post_meta($new_post_id, 'infocob_crm_forms_admin_form_additional_email_config', $template_additional_email_form_json);
							
							/*
                            * get config crm
                            */
							if(!empty($config_crm)) {
								global $wpdb;
        
								$table  = 'infocob_form_ifb';
								$data   = [
									'idPostContactForm' => $new_post_id,
									'fieldAssoc'        => $config_crm["fieldAssoc"],
									'pivot'             => $config_crm["pivot"],
									'maj'               => $config_crm["maj"],
									'fichiersLies'      => $config_crm["fichiersLies"],
									'tables'            => $config_crm["tables"],
									'sendmail'          => $config_crm["sendmail"],
									'cloudFichiers'     => $config_crm["cloudFichiers"],
								];
								$format = ['%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s'];
								$wpdb->insert($table, $data, $format);
							}
							
							$redirect_url = add_query_arg(["success" => true], $redirect_url);
							wp_redirect($redirect_url);
							exit();
						}
					}
				}
			}
			
			$redirect_url = add_query_arg(["success" => false], $redirect_url);
			wp_redirect($redirect_url);
			exit();
		}
		
	}

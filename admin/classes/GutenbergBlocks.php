<?php
	
	namespace Infocob\CrmForms\Admin;
	
	// https://github.com/eudesgit/gutenberg-blocks-sample
	
	// If this file is called directly, abort.
	if(!defined('WPINC')) {
		die;
	}
	
	class GutenbergBlocks {
		protected $version;
		
		/**
		 * The unique identifier of this plugin (slug).
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string $plugin_name The string used to uniquely identify this plugin.
		 */
		protected $plugin_name;
		
		/**
		 * The array of actions registered with WordPress.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      array $actions The actions registered with WordPress to fire when the plugin loads.
		 */
		protected $actions;
		
		protected $filters = [];
		
		/**
		 * Define the core functionality of the plugin.
		 *
		 * Set the plugin name and the plugin version that can be used throughout the plugin.
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			global $infocob_gutenberg_blocks_version;
			
			$this->version = $infocob_gutenberg_blocks_version;
			
			$this->plugin_name = 'infocob-crm-forms';
			
			$this->actions = [];
			$this->filters = [];
			
			// Register hooks
			$this->hooks();
			
		}
		
		/**
		 * Getters
		 */
		public function get_plugin_name() {
			return $this->plugin_name;
		}
		
		/**
		 * Register all hooks
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function hooks() {
			$filter_block_name = "block_categories_all";
			if(version_compare(get_bloginfo('version'),'5.8', '<') ) {
				$filter_block_name = "block_categories";
			}
			add_filter($filter_block_name, function($categories, $post) {
				$category_slugs = wp_list_pluck($categories, 'slug');
				
				return in_array('infocob-gutenberg-category', $category_slugs, true) ? $categories : array_merge(
					$categories,
					array(
						array(
							'slug'  => 'infocob-gutenberg-category',
							'title' => 'Infocob',
							'icon'  => null,
						),
					)
				);
			}, 10, 2);
			
			$this->add_action('enqueue_block_editor_assets', $this, 'register_dynamic_block_liste_formulaires_action');
			$this->add_action('init', $this, 'register_block_type_liste_formulaires_action');
		}
		
		public function register_block_type_liste_formulaires_action() {
			$block_name = 'liste-formulaires';
			
			$block_namespace = 'infocob-crm-forms/' . $block_name;
			
			$script_slug       = $this->plugin_name . '-' . $block_name;
			$style_slug        = $this->plugin_name . '-' . $block_name . '-style';
			$editor_style_slug = $this->plugin_name . '-' . $block_name . '-editor-style';
			
			// Registering the block
			register_block_type(
				$block_namespace,  // Block name with namespace
				[
					'style'           => $style_slug,
					// General block style slug
					'editor_style'    => $editor_style_slug,
					// Editor block style slug
					'editor_script'   => $script_slug,
					// The block script slug
					'render_callback' => [$this, 'block_dynamic_render_liste_formulaires_cb'],
					// The render callback
				]
			);
		}
		
		/**
		 * Registers the dynamic server side block JS script and its styles
		 *
		 * @return void
		 * @since    1.0.0
		 */
		public function register_dynamic_block_liste_formulaires_action() {
			$block_name = 'liste-formulaires';
			
			$block_namespace = 'infocob-crm-forms/' . $block_name;
			
			$script_slug       = $this->plugin_name . '-' . $block_name;
			$style_slug        = $this->plugin_name . '-' . $block_name . '-style';
			$editor_style_slug = $this->plugin_name . '-' . $block_name . '-editor-style';
			
			// automatically load dependencies and version
			$asset_file = include(ROOT_INFOCOB_CRM_FORMS_DIR_PATH . 'blocks/liste-formulaires/build/block.build.asset.php');
			
			if(is_admin()) {
				// The JS block script
				wp_enqueue_script(
					$script_slug,
					ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'blocks/' . $block_name . '/build/block.build.js',
					$asset_file['dependencies'],
					$asset_file['version']
				);
				
				// The block style
				// It will be loaded on the editor and on the site
				wp_register_style(
					$style_slug,
					ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'blocks/' . $block_name . '/css/style.css',
					['wp-blocks'], // General style
					$this->version
				);
				
				// The block style for the editor only
				wp_register_style(
					$editor_style_slug,
					ROOT_INFOCOB_CRM_FORMS_DIR_URL . 'blocks/' . $block_name . '/css/editor.css',
					['wp-edit-blocks'], // Style for the editor
					$this->version
				);
			}
		}
		
		/**
		 * CALLBACK
		 *
		 * Render callback for the dynamic block.
		 *
		 * Instead of rendering from the block's save(), this callback will render the front-end
		 *
		 * @return string Rendered HTML
		 * @since    1.0.0
		 */
		public function block_dynamic_render_liste_formulaires_cb($block_attributes, $content) {
			$html = "";
			if(!empty($block_attributes["form_id"])) {
				$html = do_shortcode("[infocob-crm-forms id=" . $block_attributes["form_id"] . "]");
			}
			
			return $html;
		}
		
		/**
		 * Add a new action to the collection to be registered with WordPress.
		 *
		 * @param string $hook          The name of the WordPress action that is being registered.
		 * @param object $component     A reference to the instance of the object on which the action is defined.
		 * @param string $callback      The name of the function definition on the $component.
		 * @param int    $priority      Optional. The priority at which the function should be fired. Default is 10.
		 * @param int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
		 *
		 * @since    1.0.0
		 */
		protected function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
			$this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
		}
		
		/**
		 * A utility function that is used to register the actions and hooks into a single
		 * collection.
		 *
		 * @param array  $hooks         The collection of hooks that is being registered (that is, actions or filters).
		 * @param string $hook          The name of the WordPress filter that is being registered.
		 * @param object $component     A reference to the instance of the object on which the filter is defined.
		 * @param string $callback      The name of the function definition on the $component.
		 * @param int    $priority      The priority at which the function should be fired.
		 * @param int    $accepted_args The number of arguments that should be passed to the $callback.
		 *
		 * @return   array                                  The collection of actions and filters registered with WordPress.
		 * @since    1.0.0
		 * @access   private
		 */
		private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {
			
			$hooks[] = [
				'hook'          => $hook,
				'component'     => $component,
				'callback'      => $callback,
				'priority'      => $priority,
				'accepted_args' => $accepted_args
			];
			
			return $hooks;
			
		}
		
		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 *
		 * @since    1.0.0
		 */
		public function run() {
			$this->run_adds();
		}
		
		/**
		 * Register the filters and actions with WordPress.
		 *
		 * @since    1.0.0
		 */
		public function run_adds() {
			
			foreach($this->actions as $hook) {
				add_action($hook['hook'], array(
					$hook['component'],
					$hook['callback']
				), $hook['priority'], $hook['accepted_args']);
			}
			
		}
		
	}

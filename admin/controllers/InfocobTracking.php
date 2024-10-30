<?php
	
	
	namespace Infocob\Tracking\Admin;
	
	include_once ABSPATH . '/wp-admin/includes/plugin.php';
	
	class InfocobTracking {
		
		public function __construct() {
			// nothing to do
		}
		
		public function init() {
			$this->add_actions();
			$this->add_filters();
		}
		
		public function add_actions() {
			add_action('init', [$this, 'infocob_init']);
			add_action('admin_init', [$this, 'infocob_admin_init']);
			
			add_action('admin_menu', [$this, 'infocob_admin_menu_pages']);
			
			add_action('admin_enqueue_scripts', [$this, 'infocob_admin_enqueue_scripts']);
			add_action('wp_enqueue_scripts', [$this, 'infocob_wp_enqueue_scripts']);
			
			add_action('add_meta_boxes', [$this, 'infocob_add_custom_box']);
			add_action('save_post', [$this, 'infocob_save_custom_box']);
			
			add_action('manage_ifb_tracking_form_posts_custom_column', [
				$this,
				'custom_ifb_tracking_form_column'
			], 10, 2);
			
			add_filter('manage_ifb_tracking_form_posts_columns', [$this, 'set_custom_edit_ifb_tracking_form_columns']);
			
			add_action('admin_post_nopriv_infocob-tracking_submit_form_email', [new FormSubmission(), 'process']);
			add_action('admin_post_infocob-tracking_submit_form_email', [new FormSubmission(), 'process']);
			
			add_action('admin_post_nopriv_infocob-tracking_submit_form_validate', [new FormSubmission(), 'process']);
			add_action('admin_post_infocob-tracking_submit_form_validate', [new FormSubmission(), 'process']);
			
			add_action('admin_post_nopriv_infocob-tracking_submit_form_sendinblue', [new FormSubmission(), 'process']);
			add_action('admin_post_infocob-tracking_submit_form_sendinblue', [new FormSubmission(), 'process']);
			
			add_action('infocob_forms_after_submit_form', [new FormSubmission(), 'processAfterRegister']);
			
			add_action('wp_head', [$this, 'wp_head']);
			
			/*
			 * Allow to redirect at any moment
			 */
			add_action('setup_theme', function() {
				ob_start();
			});
			add_action('after_setup_theme', function() {
				ob_flush();
			});
		}
		
		public function wp_head() {
			$options                   = get_option('infocob_tracking_settings');
			$loader_background_opacity = isset($options["loader"]["background-opacity"]) ? dechex(number_format(255 * ($options["loader"]["background-opacity"] / 100), 0)) : "";
			$loader_background_color   = !empty($options["loader"]["background-color"]) ? "background:#" . substr($options["loader"]["background-color"], 1, strlen($options["loader"]["background-color"])) . $loader_background_opacity . ";" : "background:#000000" . $loader_background_opacity . ";";
			$loader_color              = !empty($options["loader"]["color"]) ? "border: 6px solid " . $options["loader"]["color"] . ";" : "";
			$loader_color_border       = !empty($options["loader"]["color"]) ? "border-color: " . $options["loader"]["color"] . " transparent " . $options["loader"]["color"] . " transparent;" : "";
			$loader_text_color         = !empty($options["loader"]["text-color"]) ? "color:" . $options["loader"]["text-color"] . ";" : "";
			
			$style = "<style>
                div.infocob_tracking_loader.loading {
                  " . $loader_background_color . "
                }
                div.infocob_tracking_loader span.text_loader {
                  " . $loader_text_color . "
                }
                div.infocob_tracking_loader span.img_loader:after {
                  " . $loader_color . "
                  " . $loader_color_border . "
                }
            </style>";
			
			echo $style;
			
			$this->infocob_formspec_message_JS();
		}
		
		public function add_filters() {
			if(!is_admin() && !wp_is_json_request()) {
				add_filter('wpcf7_contact_form_properties', [
					Cf7::class,
					'tracking_properties'
				], 10, 2);
			}
			
			add_filter('plugin_action_links_infocob-tracking/infocob-tracking.php', [$this, 'add_settings_link']);
		}
		
		
		public function add_settings_link($links) {
			$url = esc_url(add_query_arg(
				[
					"page"      => "infocob-tracking-admin-settings-page",
					"post_type" => "ifb_tracking_form"
				],
				get_admin_url() . 'edit.php'
			));
			
			$settings_link = "<a href='" . $url . "'>" . __('Settings') . "</a>";
			
			array_unshift(
				$links,
				$settings_link
			);
			
			return $links;
		}
		
		public function infocob_admin_init() {
			Cf7::tag_generator();
		}
		
		public function check_infocob_crm_forms_is_activated() {
			$ok = true;
			if(is_admin() && current_user_can('activate_plugins') && (!is_plugin_active('infocob-crm-forms/infocob-crm-forms.php') || !is_plugin_active('contact-form-7/wp-contact-form-7.php'))) {
				add_action('admin_notices', function() {
					?>
                    <div class="error">
                        <p><?php _e("Désolé, les plugins 'Infocob CRM Forms' et 'Contact Form 7' sont requis pour l'activation du plugin 'Infocob Tracking'", "infocob-tracking"); ?></p>
                    </div>
					<?php
				});
				
				deactivate_plugins(INFOCOB_TRACKING_BASENAME);
				
				if(isset($_GET['activate'])) {
					unset($_GET['activate']);
				}
				
				$ok = false;
			}
			
			return $ok;
		}
		
		public function custom_ifb_tracking_form_column($column, $post_id) {
			switch($column) {
				case 'shortcode_start' :
					$admin_form_edit_json = get_post_meta($post_id, 'infocob_tracking_admin_form_config', true);
					$admin_form_edit      = json_decode($admin_form_edit_json, true);
					
					if(!empty($admin_form_edit["shortcode_form_start"])) {
						echo '<input type="text" readonly class="infocob_tracking_copy" value="' . $admin_form_edit["shortcode_form_start"] . '"/>';
					} else {
						_e('Impossible d\'obtenir le shortcode', 'infocob-tracking');
					}
					break;
				
				case 'shortcode_end' :
					$admin_form_edit_json = get_post_meta($post_id, 'infocob_tracking_admin_form_config', true);
					$admin_form_edit      = json_decode($admin_form_edit_json, true);
					if(!empty($admin_form_edit["shortcode_form_end"])) {
						echo '<input type="text" readonly class="infocob_tracking_copy" value="' . $admin_form_edit["shortcode_form_end"] . '"/>';
					} else {
						_e('Impossible d\'obtenir le shortcode', 'infocob-tracking');
					}
					break;
				
				case 'shortcode_sendinblue' :
					$admin_form_edit_json = get_post_meta($post_id, 'infocob_tracking_admin_form_config', true);
					$admin_form_edit      = json_decode($admin_form_edit_json, true);
					if(!empty($admin_form_edit["shortcode_form_sendinblue"])) {
						echo '<input type="text" readonly class="infocob_tracking_copy" value="' . $admin_form_edit["shortcode_form_sendinblue"] . '"/>';
					} else {
						_e('Impossible d\'obtenir le shortcode', 'infocob-tracking');
					}
					break;
				
			}
		}
		
		public function set_custom_edit_ifb_tracking_form_columns($columns) {
			$reOrderColumns = array_slice($columns, 0, 2, true) + array("shortcode_start" => __('Landing page', 'infocob-tracking')) + array_slice($columns, 2, count($columns) - 1, true);
			$reOrderColumns = array_slice($reOrderColumns, 0, 3, true) + array("shortcode_end" => __('Liste d\'abonnements', 'infocob-tracking')) + array_slice($reOrderColumns, 3, count($reOrderColumns) - 1, true);
			$reOrderColumns = array_slice($reOrderColumns, 0, 4, true) + array("shortcode_sendinblue" => __('Landing page Sendinblue', 'infocob-tracking')) + array_slice($reOrderColumns, 4, count($reOrderColumns) - 1, true);
			
			return $reOrderColumns;
		}
		
		public function infocob_shortcodes_init() {
			add_filter('infocob_tracking_shortcode_addFormEnd', 'Infocob\Tracking\Admin\Shortcode::addFormEnd');
			
			add_shortcode('submit', 'Infocob\Tracking\Admin\Shortcode::addSubmit');
			add_shortcode('email', 'Infocob\Tracking\Admin\Shortcode::addEmail');
			add_shortcode('groupements', 'Infocob\Tracking\Admin\Shortcode::addGroupements');
			
			add_shortcode('infocob-tracking-start', 'Infocob\Tracking\Admin\Shortcode::addFormStart');
			add_shortcode('infocob-tracking-end', 'Infocob\Tracking\Admin\Shortcode::addFormEnd');
			add_shortcode('infocob-tracking-sendinblue', 'Infocob\Tracking\Admin\Shortcode::addFormSendinblue');
		}
		
		public function infocob_admin_menu_pages() {
			add_submenu_page('edit.php?post_type=ifb_tracking_form', 'Réglages', 'Réglages', 'manage_options', 'infocob-tracking-admin-settings-page', array(
				new AdminSettings(),
				'render'
			));
		}
		
		public function infocob_init() {
			$hasRequirements = $this->check_infocob_crm_forms_is_activated();
			
			if($hasRequirements) {
				register_setting('infocob_tracking', 'infocob_tracking_settings', [
					"sanitize_callback" => function($inputs) {
						if(!empty($inputs["api"]["domain"])) {
							$inputs["api"]["domain"] = rtrim($inputs["api"]["domain"], '/');
							preg_match('/^(?:https:\/\/)?(.+)$/i', $inputs["api"]["domain"], $matches);
							if(!empty($matches[1])) {
								$inputs["api"]["domain"] = $matches[1];
							}
						}
						
						return $inputs;
					}
				]);
				
				$this->infocob_register_post_type();
				
				$this->infocob_shortcodes_init();
			}
		}
		
		public function infocob_register_post_type() {
			//Posts Formulaire
			$labels = array(
				'name'           => 'Infocob Tracking',
				'singular_name'  => 'Infocob Tracking',
				'menu_name'      => 'Infocob Tracking',
				'name_admin_bar' => 'Infocob Tracking',
				'add_new'        => 'Ajouter',
				'add_new_item'   => 'Créer un formulaire',
				'new_item'       => 'Créer un formulaire',
				'edit_item'      => 'Modifier',
				'view_item'      => 'Voir',
				'all_items'      => 'Tous les formulaires',
				'search_items'   => 'Rechercher',
			);
			
			$args = array(
				'labels'             => $labels,
				'description'        => 'Tous les formulaire',
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => false,
				'rewrite'            => array('slug' => 'ifb_tracking_form'),
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => 20,
				'menu_icon'          => 'dashicons-rest-api',
				'show_in_rest'       => false,
				'supports'           => array('title')
			);
			
			register_post_type('ifb_tracking_form', $args);
		}
		
		public function infocob_add_custom_box() {
			$formEdit = new FormEdit();
			add_meta_box('infocob_tracking_admin_form_help', // Unique ID
				'Aide', // Box title
				[$formEdit, 'renderFormHelpMetabox'], // Content callback, must be of type callable
				'ifb_tracking_form', // Post type
				'advanced',
				'high'
			);
			
			add_meta_box('infocob_tracking_admin_form_config', // Unique ID
				'Configuration', // Box title
				[$formEdit, 'renderFormConfigMetabox'], // Content callback, must be of type callable
				'ifb_tracking_form' // Post type
			);
			
			add_meta_box('infocob_tracking_admin_form_start', // Unique ID
				'Formulaire - Landing page', // Box title
				[$formEdit, 'renderFormStartMetabox'], // Content callback, must be of type callable
				'ifb_tracking_form' // Post type
			);
			
			add_meta_box('infocob_tracking_admin_form_end', // Unique ID
				'Formulaire - Liste d\'abonnements', // Box title
				[$formEdit, 'renderFormEndMetabox'], // Content callback, must be of type callable
				'ifb_tracking_form' // Post type
			);
			
			add_meta_box('infocob_tracking_admin_form_sendinblue', // Unique ID
				'Formulaire - Landing page Sendinblue', // Box title
				[$formEdit, 'renderFormSendinblueMetabox'], // Content callback, must be of type callable
				'ifb_tracking_form' // Post type
			);
			
			add_meta_box('infocob_tracking_admin_form_email_demande', // Unique ID
				'Template Email - Demande de souscription', // Box title
				[$formEdit, 'renderFormEmailDemandeMetabox'], // Content callback, must be of type callable
				'ifb_tracking_form' // Post type
			);
		}
		
		public function infocob_save_custom_box($post_id) {
			if(array_key_exists('post_id', $_POST) &&
			   array_key_exists('backward_page', $_POST) &&
			   array_key_exists('redirect_page_email_sent', $_POST) &&
			   array_key_exists('redirect_page_subscription_confirm', $_POST) &&
			   array_key_exists('shortcode_form_start', $_POST) &&
			   array_key_exists('redirect_html_form_register', $_POST) &&
			   array_key_exists('shortcode_form_end', $_POST) &&
			   array_key_exists('shortcode_form_sendinblue', $_POST) &&
			   array_key_exists('type_form', $_POST)) {
				
				$admin_form_edit["post_id"]                            = sanitize_text_field($_POST["post_id"]);
				$admin_form_edit["backward_page"]                      = sanitize_text_field($_POST["backward_page"]);
				$admin_form_edit["redirect_page_email_sent"]           = sanitize_text_field($_POST["redirect_page_email_sent"]);
				$admin_form_edit["redirect_page_subscription_confirm"] = sanitize_text_field($_POST["redirect_page_subscription_confirm"]);
				$admin_form_edit["shortcode_form_start"]               = sanitize_text_field($_POST["shortcode_form_start"]);
				$admin_form_edit["redirect_html_form_register"]        = sanitize_text_field($_POST["redirect_html_form_register"]);
				$admin_form_edit["shortcode_form_end"]                 = sanitize_text_field($_POST["shortcode_form_end"]);
				$admin_form_edit["shortcode_form_sendinblue"]          = sanitize_text_field($_POST["shortcode_form_sendinblue"]);
				$admin_form_edit["type_form"]                          = sanitize_text_field($_POST["type_form"]);
				$admin_form_edit["groups_form"]                        = Tools::sanitize_fields($_POST["groups_form"] ?? []);
				
				$admin_form_edit_json = json_encode($admin_form_edit, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				
				update_post_meta($post_id, 'infocob_tracking_admin_form_config', $admin_form_edit_json);
			}
			
			if(array_key_exists('html_form_start', $_POST)) {
				$admin_form_edit = wp_check_invalid_utf8($_POST["html_form_start"]);
				
				update_post_meta($post_id, 'infocob_tracking_admin_form_start', $admin_form_edit);
			}
			
			if(array_key_exists('html_form_end', $_POST)) {
				$admin_form_edit = wp_check_invalid_utf8($_POST["html_form_end"]);
				
				update_post_meta($post_id, 'infocob_tracking_admin_form_end', $admin_form_edit);
			}
			
			if(array_key_exists('html_form_sendinblue', $_POST)) {
				$admin_form_edit = wp_check_invalid_utf8($_POST["html_form_sendinblue"]);
				
				update_post_meta($post_id, 'infocob_tracking_admin_form_sendinblue', $admin_form_edit);
			}
			
			if(array_key_exists('html_form_sendinblue_no_user', $_POST)) {
				$admin_form_edit = wp_check_invalid_utf8($_POST["html_form_sendinblue_no_user"]);
				
				update_post_meta($post_id, 'infocob_tracking_admin_form_sendinblue_no_user', $admin_form_edit);
			}
			
			if(array_key_exists('email_from', $_POST) &&
			   array_key_exists('email_subject', $_POST)) {
				$template_email_form               = [];
				$template_email_form["email_from"] = !empty($_POST["email_from"]) ? sanitize_email($_POST["email_from"]) : sanitize_email(get_bloginfo('admin_email'));
				
				$template_email_form["email_subject"]          = !empty($_POST["email_subject"]) ? sanitize_text_field($_POST["email_subject"]) : "Demande d'abonnement | " . get_bloginfo('name');
				$template_email_form["email_societe"]          = !empty($_POST["email_societe"]) ? sanitize_text_field($_POST["email_societe"]) : get_bloginfo("name");
				$template_email_form["email_title"]            = !empty($_POST["email_title"]) ? sanitize_text_field($_POST["email_title"]) : get_bloginfo("name") . " - Abonnement";
				$template_email_form["email_subtitle"]         = !empty($_POST["email_subtitle"]) ? sanitize_text_field($_POST["email_subtitle"]) : "";
				$template_email_form["email_color"]            = !empty($_POST["email_color"]) ? sanitize_text_field($_POST["email_color"]) : "#0271b8";
				$template_email_form["email_color_text_title"] = !empty($_POST["email_color_text_title"]) ? sanitize_text_field($_POST["email_color_text_title"]) : "#ffffff";
				$template_email_form["email_color_link"]       = !empty($_POST["email_color_link"]) ? sanitize_text_field($_POST["email_color_link"]) : "#0271b8";
				$template_email_form["email_template"]         = !empty($_POST["email_template"]) ? sanitize_text_field($_POST["email_template"]) : "defaut-infocob-tracking";
				$template_email_form["logo"]                   = !empty($_POST["logo"]) ? Tools::sanitize_fields($_POST["logo"]) : [];
				$template_email_form["email_template"]         = !empty($_POST["email_template"]) ? sanitize_text_field($_POST["email_template"]) : "defaut-infocob-tracking";
				$template_email_form["email_border_radius"]    = isset($_POST["email_border_radius"]) ? sanitize_text_field($_POST["email_border_radius"]) : 0;
				
				$template_email_form_json = json_encode($template_email_form, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				
				update_post_meta($post_id, 'infocob_tracking_admin_form_email_demande', $template_email_form_json);
			}
			
			// @TODO remove old system
			//if(array_key_exists('email_template', $_POST)) {
			//	$template_email_form = wp_check_invalid_utf8($_POST["email_template"]);
			//
			//	update_post_meta($post_id, 'infocob_tracking_admin_form_email_demande_html', $template_email_form);
			//}
		}
		
		public function infocob_wp_enqueue_scripts($hook) {
			//Scripts
			wp_register_script('infocob_tracking_main_js', ROOT_INFOCOB_TRACKING_DIR_URL . 'public/assets/js/main.js', array(
				'jquery'
			));
			
			//Styles
			wp_register_style('infocob_tracking_main_css', ROOT_INFOCOB_TRACKING_DIR_URL . 'public/assets/css/main.css');
			
			wp_enqueue_script('infocob_tracking_main_js');
			wp_enqueue_style('infocob_tracking_main_css');
		}
		
		public function infocob_admin_enqueue_scripts($hook) {
			global $post_type;
			
			// Scripts
			// Libs
			wp_register_script('infocob_tracking_multiple_select_js', ROOT_INFOCOB_TRACKING_DIR_URL . 'libs/multiple-select/multiple-select.min.js');
			wp_register_script('infocob_tracking_select2_js', ROOT_INFOCOB_TRACKING_DIR_URL . 'libs/select2/js/select2.full.min.js');
			wp_register_script('infocob_tracking_sweetalert2_js', ROOT_INFOCOB_TRACKING_DIR_URL . 'libs/sweetalert2/sweetalert2.all.min.js');
			
			wp_register_script('infocob_tracking_admin_quicktags_js', ROOT_INFOCOB_TRACKING_DIR_URL . 'admin/assets/js/admin_quicktags.js', array(
				'jquery',
				'quicktags'
			));
			wp_register_script('infocob_tracking_admin_settings_js', ROOT_INFOCOB_TRACKING_DIR_URL . 'admin/assets/js/admin_settings.js', array(
				'jquery',
				'wp-color-picker'
			));
			wp_register_script('infocob_tracking_admin_form_edit_js', ROOT_INFOCOB_TRACKING_DIR_URL . 'admin/assets/js/admin_form_edit.js', array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-tabs',
				'wp-color-picker'
			));
			
			// Styles
			// Libs
			wp_register_style('infocob_tracking_multiple_select_css', ROOT_INFOCOB_TRACKING_DIR_URL . 'libs/multiple-select/multiple-select.min.css');
			wp_register_style('infocob_tracking_multiple_select_theme_css', ROOT_INFOCOB_TRACKING_DIR_URL . 'libs/multiple-select/themes/bootstrap.css');
			wp_register_style('infocob_tracking_select2_css', ROOT_INFOCOB_TRACKING_DIR_URL . 'libs/select2/css/select2.min.css');
			wp_register_style('infocob_tracking_sweetalert2_css', ROOT_INFOCOB_TRACKING_DIR_URL . 'libs/sweetalert2/sweetalert2.min.css');
			
			wp_register_style('infocob_tracking_admin_settings_css', ROOT_INFOCOB_TRACKING_DIR_URL . 'admin/assets/css/admin_settings.css');
			wp_register_style('infocob_tracking_admin_form_edit_css', ROOT_INFOCOB_TRACKING_DIR_URL . 'admin/assets/css/admin_form_edit.css');
			
			if($hook == "ifb_tracking_form_page_infocob-tracking-admin-settings-page") {
				// Scripts
				wp_enqueue_script('infocob_tracking_select2_js');
				wp_enqueue_script('infocob_tracking_admin_settings_js');
				
				// Styles
				wp_enqueue_style('infocob_tracking_select2_css');
				wp_enqueue_style('infocob_tracking_admin_settings_css');
			}
			
			if($post_type === 'ifb_tracking_form') {
				// Scripts
				wp_enqueue_script('infocob_tracking_sweetalert2_js');
				wp_enqueue_script('infocob_tracking_multiple_select_js');
				wp_enqueue_script('infocob_tracking_admin_form_edit_js');
				wp_enqueue_script('infocob_tracking_admin_quicktags_js');
				
				// Styles
				wp_enqueue_style('infocob_tracking_multiple_select_css');
				wp_enqueue_style('infocob_tracking_multiple_select_theme_css');
				wp_enqueue_style('infocob_tracking_sweetalert2_css');
				wp_enqueue_style('infocob_tracking_admin_form_edit_css');
			}
		}
		
		public function infocob_formspec_message_JS() {
			?>
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function() {
                    let casesnobots = document.querySelectorAll('.if-casenorobot-infocob-tracking');
                    
                    casesnobots.forEach(function(val, key) {
                        let text = val.innerHTML;
                        
                        let input = document.createElement("input");
                        input.type = 'checkbox';
                        input.name = 'i-am-not-a-robot';
                        input.required = 'required';
                        
                        let span = document.createElement("span");
                        span.innerHTML = text;
                        
                        val.innerHTML = "";
                        val.appendChild(input);
                        val.appendChild(span);
                    });
					
					<?php if (FormSubmission::getIsMessageSent() !== null) { ?>
                    
                    var divPop = document.createElement("div");
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
                    titre.style.marginBottom = "15px";
                    titre.style.color = <?php echo json_encode([FormSubmission::getIsMessageSent() ? "#48a648" : "#d20f0f"]); ?>;
                    titre.innerHTML = "Formulaire";
                    
                    let text = document.createElement("p");
                    text.style.fontSize = "15px";
                    text.innerHTML = <?php echo json_encode(FormSubmission::getReturnMessage()); ?>;
                    
                    let button = document.createElement("span");
                    button.style.display = "block";
                    button.style.width = "40px";
                    button.style.height = "40px";
                    button.style.position = "absolute";
                    button.style.top = "0";
                    button.style.right = "0";
                    button.style.zIndex = "4";
                    button.innerHTML = "X";
                    button.style.background = "#dbdbdb";
                    button.style.textAlign = "center";
                    button.style.fontSize = "28px";
                    button.style.lineHeight = "40px";
                    button.style.fontWeight = "bold";
                    button.style.cursor = "pointer";
                    
                    divInner.appendChild(titre);
                    divInner.appendChild(text);
                    divInner.appendChild(button);
                    divPop.appendChild(divInner);
                    
                    divPop.addEventListener("click", function(ev) {
                        document.body.removeChild(divPop);
                    });
                    
                    button.addEventListener("click", function(ev) {
                        document.body.removeChild(divPop);
                    });
                    
                    divInner.addEventListener("click", function(ev) {
                        ev.stopPropagation();
                    });
                    
                    document.body.appendChild(divPop);
					<?php } ?>
                });
            </script>
			<?php
			
		}
		
	}

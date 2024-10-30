<?php
	
	
	namespace Infocob\Tracking\Admin;
	
	// don't load directly
	use WPCF7_ContactForm;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class FormEdit extends Controller {
		public $post_id;
		public $form = [];
		
		public function __construct() {
			$webservice = new Webservice();
			
			$options = get_option('infocob_tracking_settings');
			
			$apikey  = !empty($options["api"]["key"]) ? $options["api"]["key"] : "";
			$domain  = !empty($options["api"]["domain"]) ? $options["api"]["domain"] : "";
			$success = $webservice->test($apikey, $domain);
			
			//get the current screen
			$screen = get_current_screen();
			
			if($screen->id === "ifb_tracking_form") {
                if(!$success) {
                    add_action('admin_notices', function() {
                        ?>
                        <div class="notice notice-error">
                            <p><?php _e('Attention, la connexion à l\'API à échouée !<br />
                            Les formulaires ne fonctionneront pas !<br />
                            <a href="' . menu_page_url('infocob-tracking-admin-settings-page', false) . '">Cliquez ici pour changer les paramètres de connexion</a>', 'infocob-tracking'); ?></p>
                        </div>
                        <?php
                    });
                }
			}
		}
		
		public function renderFormConfigMetabox() {
			global $post;
			
			// Nonce field to validate form request came from current site
			wp_nonce_field(basename(__FILE__), 'event_fields');
			
			$admin_form_edit_json = get_post_meta($post->ID, 'infocob_tracking_admin_form_config', true);
			$admin_form_edit      = json_decode($admin_form_edit_json, true);
			$contact_form_list    = Tools::getPostsListContactForm7();
			
			$backward_page                      = !empty($admin_form_edit["backward_page"]) ? $admin_form_edit["backward_page"] : "";
			$redirect_page_email_sent           = !empty($admin_form_edit["redirect_page_email_sent"]) ? $admin_form_edit["redirect_page_email_sent"] : "";
			$redirect_page_subscription_confirm = !empty($admin_form_edit["redirect_page_subscription_confirm"]) ? $admin_form_edit["redirect_page_subscription_confirm"] : "";
			$shortcode_form_start               = !empty($admin_form_edit["shortcode_form_start"]) ? $admin_form_edit["shortcode_form_start"] : "[infocob-tracking-start id='" . $post->ID . "']";
			$shortcode_form_end                 = !empty($admin_form_edit["shortcode_form_end"]) ? $admin_form_edit["shortcode_form_end"] : "[infocob-tracking-end id='" . $post->ID . "']";
			$shortcode_form_sendinblue          = !empty($admin_form_edit["shortcode_form_sendinblue"]) ? $admin_form_edit["shortcode_form_sendinblue"] : "[infocob-tracking-sendinblue id='" . $post->ID . "']";
			$type_form                          = !empty($admin_form_edit["type_form"]) ? $admin_form_edit["type_form"] : "";
			$groups_form                        = !empty($admin_form_edit["groups_form"]) ? $admin_form_edit["groups_form"] : [];
			$wp_pages_list                      = !empty(get_pages()) ? get_pages() : [];
			$redirect_html_form_register        = !empty($admin_form_edit["redirect_html_form_register"]) ? $admin_form_edit["redirect_html_form_register"] : "";
			
			$webservice  = new Webservice();
			$groups_list = $webservice->getGroupementsList();
			
			$shortcode_regex = get_shortcode_regex();
			
			$cf7_shortcode_tag = 'contact-form-7';
			$shortcode_tag_end = 'infocob-tracking-end';
			$cf7_forms_pages   = [];
			$pages_with_shortcode_end   = [];
			$page_abo_list     = null;
			foreach($wp_pages_list as $wp_page) {
				/*
                 * Get pages only with cf7 shortcodes + tracking
                 */
				if(has_shortcode($wp_page->post_content, $cf7_shortcode_tag)) {
					if(preg_match('/' . $shortcode_regex . '/i', $wp_page->post_content, $matches)) {
						$atts = shortcode_parse_atts(trim($matches[0], '[]'));
						$atts = array_change_key_case((array) $atts, CASE_LOWER);
						
						$cf7_id = !empty($atts["id"]) ? $atts["id"] : false;
						if($cf7_id) {
							$cf7 = WPCF7_ContactForm::get_instance($cf7_id);
							$cf7_form_content = $cf7->prop('form');
							if(preg_match('/\[(tracking .+?)]/i', $cf7_form_content)) {
								$cf7_forms_pages[] = $wp_page;
                            }
						}
					}
				}
				
				/*
                 * Get pages only with shortcodes end
                 */
				if(has_shortcode($wp_page->post_content, $shortcode_tag_end)) {
					$pages_with_shortcode_end[] = $wp_page;
				}
			}
			
			$wp_pages_list_link = get_site_url(null, 'wp-admin/edit.php?post_type=page', 'admin');
			
			/*
			 * Info shortcode start
			 */
			$info_shortcode_start = "<strong>Attention</strong>, ce shortcode n'est pas utilisé, pour commencer à l'utiliser insérez le dans une <a href='" . $wp_pages_list_link . "'>page</a>";
			$shortcode_tag        = 'infocob-tracking-start';
			foreach($wp_pages_list as $wp_page) {
				if(has_shortcode($wp_page->post_content, $shortcode_tag)) {
					if(preg_match('/' . $shortcode_regex . '/i', $wp_page->post_content, $matches)) {
						$atts = shortcode_parse_atts(trim($matches[0], '[]'));
						$atts = array_change_key_case((array) $atts, CASE_LOWER);
						
						if(!empty($atts["id"]) && $atts["id"] == $post->ID) {
							$info_shortcode_start = "Configuration OK";
							break;
						}
					}
				}
			}
			
			/*
			 * Info shortcode end
			 */
			if(!empty($pages_with_shortcode_end) && !empty($backward_page)) {
				$info_shortcode_end = "Configuration OK";
			} else {
				$info_shortcode_end = "<strong>Attention</strong>, aucune page sélectionnée, insérez un shortcode 'infocob-tracking-end' dans une <a href='" . $wp_pages_list_link . "'>page</a> pour la rendre disponible";
			}
			
			/*
			 * Info shortcode sendinblue
			 */
			$info_shortcode_sendinblue = "<strong>Attention</strong>, ce shortcode n'est pas utilisé, pour commencer à l'utiliser insérez le dans une <a href='" . $wp_pages_list_link . "'>page</a>";
			$shortcode_tag             = 'infocob-tracking-sendinblue';
			foreach($wp_pages_list as $wp_page) {
				if(has_shortcode($wp_page->post_content, $shortcode_tag)) {
					if(preg_match('/' . $shortcode_regex . '/i', $wp_page->post_content, $matches)) {
						$atts = shortcode_parse_atts(trim($matches[0], '[]'));
						$atts = array_change_key_case((array) $atts, CASE_LOWER);
						
						if(!empty($atts["id"]) && $atts["id"] == $post->ID) {
							$info_shortcode_sendinblue = "Configuration OK";
							break;
						}
					}
				}
			}
			
			/*
			 * Info form register
			 */
			if(!empty($cf7_forms_pages) && !empty($redirect_html_form_register)) {
			    $info_form_register = "Configuration OK";
			} else {
				$info_form_register = "<strong>Attention</strong>, aucune page sélectionnée, insérez un shortcode CF7 contenant une liste d'abonnement (shortcode tracking) dans une page pour la rendre disponible";
            }
			
			// Output the field
			include ROOT_INFOCOB_TRACKING_DIR_PATH . "admin/includes/admin_form_config_metabox.php";
		}
		
		public function renderFormStartMetabox() {
			global $post;
			
			// Nonce field to validate form request came from current site
			wp_nonce_field(basename(__FILE__), 'event_fields');
			
			// @TODO default email
			$default_form = "<label for='email'>Email</label>\n"
			                . "[email id='email' value='' required='true']\n"
			                . "[submit value='Envoyer']";
			
			$admin_form_html = get_post_meta($post->ID, 'infocob_tracking_admin_form_start', true);
			$html_form       = !empty($admin_form_html) ? $admin_form_html : $default_form;
			
			$admin_form_edit_json = get_post_meta($post->ID, 'infocob_tracking_admin_form_config', true);
			$admin_form_edit      = json_decode($admin_form_edit_json, true);
			
			// Output the field
			include ROOT_INFOCOB_TRACKING_DIR_PATH . "admin/includes/admin_form_start_metabox.php";
		}
		
		public function renderFormEndMetabox() {
			global $post;
			
			// Nonce field to validate form request came from current site
			wp_nonce_field(basename(__FILE__), 'event_fields');
			
			$default_form = "Liste des abonnements : \n"
			                . "[groupements id='groupements']\n"
			                . "[submit value='Valider']";
			
			$admin_form_html = get_post_meta($post->ID, 'infocob_tracking_admin_form_end', true);
			$html_form       = !empty($admin_form_html) ? $admin_form_html : $default_form;
			
			$admin_form_edit_json = get_post_meta($post->ID, 'infocob_tracking_admin_form_config', true);
			$admin_form_edit      = json_decode($admin_form_edit_json, true);
			$wp_pages_list        = !empty(get_pages()) ? get_pages() : [];
			
			// Output the field
			include ROOT_INFOCOB_TRACKING_DIR_PATH . "admin/includes/admin_form_end_metabox.php";
		}
		
		public function renderFormSendinblueMetabox() {
			global $post;
			
			// Nonce field to validate form request came from current site
			wp_nonce_field(basename(__FILE__), 'event_fields');
			
			$default_form         = "[submit value='Je confirme vouloir changer mes préférences concernant mes abonnements emailing']";
			$default_form_no_user = "<p>Afin de procéder au changement de vos abonnements, veuillez créer un compte en cliquant sur le bouton ci-dessous.</p>\n"
			                        . "[submit value='Créer mon compte']";
			
			$admin_form_html = get_post_meta($post->ID, 'infocob_tracking_admin_form_sendinblue', true);
			$html_form       = !empty($admin_form_html) ? $admin_form_html : $default_form;
			
			$admin_form_html_no_user = get_post_meta($post->ID, 'infocob_tracking_admin_form_sendinblue_no_user', true);
			$html_form_no_user       = !empty($admin_form_html_no_user) ? $admin_form_html_no_user : $default_form_no_user;
			
			$admin_form_edit_json = get_post_meta($post->ID, 'infocob_tracking_admin_form_config', true);
			$admin_form_edit      = json_decode($admin_form_edit_json, true);
			$wp_pages_list        = !empty(get_pages()) ? get_pages() : [];
			
			// Output the field
			include ROOT_INFOCOB_TRACKING_DIR_PATH . "admin/includes/admin_form_sendinblue_metabox.php";
		}
		
		public function renderFormHelpMetabox() {
			global $post;
			$wp_pages = !empty(get_pages()) ? get_pages() : [];
			
			$shortcode_tag   = 'infocob-tracking-sendinblue';
			$shortcode_regex = get_shortcode_regex();
			
			$shortcode_pages = [];
			foreach($wp_pages as $wp_page) {
				if(has_shortcode($wp_page->post_content, $shortcode_tag)) {
					
					if(preg_match('/' . $shortcode_regex . '/i', $wp_page->post_content, $matches)) {
						$atts = shortcode_parse_atts(trim($matches[0], '[]'));
						$atts = array_change_key_case((array) $atts, CASE_LOWER);
						
						if(!empty($atts["id"]) && $atts["id"] == $post->ID) {
							$permalink            = get_permalink($wp_page->ID);
							$permalink_sendinblue = add_query_arg([
								"c_code" => "{{ contact.C_CODE }}",
								"i_code" => "{{ contact.I_CODE }}",
								"email"  => "{{ contact.EMAIL }}",
							], $permalink);
							
							$wp_page->permalink = $permalink_sendinblue;
							$shortcode_pages[]  = $wp_page;
						}
					}
				}
			}
			
			include ROOT_INFOCOB_TRACKING_DIR_PATH . "admin/includes/admin_form_help_metabox.php";
		}
		
		public function renderFormEmailDemandeMetabox() {
			global $post;
			
			// Nonce field to validate form request came from current site
			wp_nonce_field(basename(__FILE__), 'event_fields');
			
			$admin_form_edit_json = get_post_meta($post->ID, 'infocob_tracking_admin_form_email_demande', true);
			$admin_form_edit      = json_decode($admin_form_edit_json, true);
			
			$admin_form_edit_html = get_post_meta($post->ID, 'infocob_tracking_admin_form_email_demande_html', true);
			
			$email_from    = !empty($admin_form_edit["email_from"]) ? $admin_form_edit["email_from"] : get_bloginfo('admin_email'); //no-reply@dev.wordpress.local
			$email_subject = !empty($admin_form_edit["email_subject"]) ? $admin_form_edit["email_subject"] : get_bloginfo('name');
			
			$email_title            = !empty($admin_form_edit["email_title"]) ? $admin_form_edit["email_title"] : get_bloginfo("name") . " - Formulaire de contact";
			$email_color            = !empty($admin_form_edit["email_color"]) ? $admin_form_edit["email_color"] : "#0271b8";
			$email_color_text_title = !empty($admin_form_edit["email_color_text_title"]) ? $admin_form_edit["email_color_text_title"] : "#ffffff";
			$email_color_link       = !empty($admin_form_edit["email_color_link"]) ? $admin_form_edit["email_color_link"] : "#0271b8";
			$email_subtitle         = !empty($admin_form_edit["email_subtitle"]) ? $admin_form_edit["email_subtitle"] : "";
			$email_societe          = !empty($admin_form_edit["email_societe"]) ? $admin_form_edit["email_societe"] : get_bloginfo("name");
			$email_border_radius    = !empty($admin_form_edit["email_border_radius"]) ? $admin_form_edit["email_border_radius"] : 0;
			
			$email_template      = !empty($admin_form_edit["email_template"]) ? $admin_form_edit["email_template"] : "defaut-infocob-tracking";
			$email_list_template = $this->getEmailListTemplateDemandeAbonnement();
			
			$logo = !empty($admin_form_edit["logo"]) ? $admin_form_edit["logo"] : [];
			
			// Output the field
			include ROOT_INFOCOB_TRACKING_DIR_PATH . "admin/includes/admin_form_template_email_demande_metabox.php";
		}
		
		public function getEmailListTemplateDemandeAbonnement() {
			$templates = [];
			if(file_exists(get_stylesheet_directory() . "/infocob-tracking/mails/demande-abonnement/")) {
				$files = scandir(get_stylesheet_directory() . "/infocob-tracking/mails/demande-abonnement/");
				
				foreach($files as $file) {
					if(!in_array($file, [".", ".."])) {
						if(preg_match("/(\.twig)$/i", $file) && preg_match("/.*(?<!_text\.twig)$/i", $file)) {
							$file        = preg_replace("/(\.twig)$/i", "", $file);
							$templates[] = $file;
						}
					}
				}
			}
			
			return $templates;
		}
	}

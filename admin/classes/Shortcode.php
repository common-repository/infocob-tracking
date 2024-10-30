<?php
	
	
	namespace Infocob\Tracking\Admin;
	
	// don't load directly
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class Shortcode {
		protected static $post_id;
		
		public static function addSubmit($atts = [], $content = null, $tag = '') {
			// normalize attribute keys, lowercase
			$atts = array_change_key_case((array) $atts, CASE_LOWER);
			// override default attributes with user attributes
			$infocob_atts = shortcode_atts([
				'id'    => '',
				'class' => '',
				'value' => 'Envoyer',
			], $atts, $tag);
			
			$o = '<input type="submit" id="' . esc_html($infocob_atts['id']) . '" ' .
			     'class="' . esc_html($infocob_atts['class']) . '" ' .
			     'value="' . esc_html($infocob_atts['value']) . '" ' .
			     '>';
			
			return $o;
		}
		
		public static function addEmail($atts = [], $content = null, $tag = '') {
			// normalize attribute keys, lowercase
			$atts = array_change_key_case((array) $atts, CASE_LOWER);
			// override default attributes with user attributes
			$infocob_atts = shortcode_atts([
				'id'          => '',
				'class'       => '',
				'required'    => false,
				'value'       => '',
				'placeholder' => '',
			], $atts, $tag);
			
			$error_email = (!empty($_GET["infocob_tracking_error"]) && sanitize_text_field($_GET["infocob_tracking_error"]) == "email") ? true : false;
			$email       = !empty($_GET["infocob_tracking_email"]) ? sanitize_email($_GET["infocob_tracking_email"]) : false;
			$o           = "";
			if($error_email) {
				$o .= "<span class='infocob_tracking_error'>Cet email n'existe pas</span>";
			}
			
			if($email) {
				$infocob_atts['value'] = $email;
			}
			
			$o .= '<input type="email" id="' . esc_html($infocob_atts['id']) . '" ' .
			      'name="infocob_tracking_user_email" ' .
			      'class="' . esc_html($infocob_atts['class']) . '" ' .
			      'value="' . sanitize_email($infocob_atts['value']) . '" ' .
			      'placeholder="' . sanitize_text_field($infocob_atts['placeholder']) . '" ' .
			      'required="' . rest_sanitize_boolean($infocob_atts['required']) . '" ' .
			      '>';
			
			return $o;
		}
		
		public static function addGroupements($atts = [], $content = null, $tag = '') {
			if(!is_admin() && !wp_is_json_request()) {
				// normalize attribute keys, lowercase
				$atts = array_change_key_case((array) $atts, CASE_LOWER);
				// override default attributes with user attributes
				$infocob_atts = shortcode_atts([
					'class' => ''
				], $atts, $tag);
				
				// Check if contactfiche or interlocuteurfiche
				$admin_form_edit_json = get_post_meta(static::$post_id, 'infocob_tracking_admin_form_config', true);
				$form_config          = json_decode($admin_form_edit_json, true);
				
				$token = !empty($_GET["infocob_tracking_token"]) ? sanitize_text_field($_GET["infocob_tracking_token"]) : false;
				$o     = "";
				if($token) {
					$jwt    = Jwt::getJwt($token);
					$client = !empty($jwt->client) ? $jwt->client : false;
					if($client) {
						$email  = !empty($client->email) ? $client->email : false;
						$c_code = !empty($client->c_code) ? $client->c_code : false;
						$i_code = !empty($client->i_code) ? $client->i_code : false;
						if($email) {
							
							$webservice = new Webservice();
							//$contactfiches       = $webservice->usersInTable($email, 'contactfiche');
							//$interlocuteurfiches = $webservice->usersInTable($email, 'interlocuteurfiche');
							
							$filter_types = !empty($form_config["groups_form"]) ? $form_config["groups_form"] : [];
							
							$groupements = [];
							foreach($filter_types as $filter_type) {
								$groupements = array_merge($groupements, $webservice->getGroupements(false, false, $filter_type));
							}
							
							//$tablesLabel = $webservice->getTableLibelle(["contactfiche", "interlocuteurfiche"]);
							//$tables      = array_merge($contactfiches, $interlocuteurfiches);
							//$groupements = Tools::constructData($tables, $form_config, $tablesLabel, $filter_type);
							
							//$groupements = (array) $groupements;
							//ksort($groupements);
							
							$o .= "<input type='hidden' name='infocob-tracking-email' value='" . $email . "'>";
							$o .= "<input type='hidden' name='infocob-tracking-c_code' value='" . $c_code . "'>";
							$o .= "<input type='hidden' name='infocob-tracking-i_code' value='" . $i_code . "'>";
							
							if(!empty($groupements)) {
								$o           .= "<ul>";
								$id_checkall = "infocob-tracking-checkall-groupements-" . wp_generate_uuid4();
								$o           .= "<li>";
								$o           .= "<input id='" . $id_checkall . "' class='infocob-tracking-checkall-groupements' type='checkbox'>";
								$o           .= "<label for='" . $id_checkall . "'>Tout sélectionner</label>";
								$o           .= "</li>";
							}
							
							foreach($groupements as $groupement) {
								$ml_code = $groupement["ML_CODE"] ?? "";
								$ml_nom  = $groupement["ML_NOM"] ?? "";
								
								$exist_in_contactfiche       = !empty($c_code) && !empty($webservice->getGroupement($ml_code, "contactfiche", $c_code));
								$exist_in_interlocuteurfiche = !empty($i_code) && !empty($webservice->getGroupement($ml_code, "interlocuteurfiche", $i_code));
								$checked                     = ($exist_in_contactfiche || $exist_in_interlocuteurfiche) ? "checked" : "";
								
								$o .= "<li>";
								$o .= "<input class='" . $infocob_atts["class"] . "' type='checkbox' name='groupements[]' id='" . $ml_code . "' value='" . $ml_code . "' " . $checked . ">";
								$o .= "<label for='" . $ml_code . "'>" . $ml_nom . "</label>";
								$o .= "</li>";
							}
							$o .= !empty($groupements) ? "</ul>" : "";
							
							if(!empty($groupements)) {
								return $o;
							} else {
								return add_filter('infocob_tracking_shortcode_add_form_end', function() {
									return "<p>Aucune liste d'abonnement disponible !</p>";
								});
							}
						}
					}
				}
				
				$options            = get_option('infocob_tracking_settings');
				$redirect_error_url = !empty($options["forms"]["url_error_page"]) ? get_page_link($options["forms"]["url_error_page"]) : get_home_url();
				wp_redirect(esc_url_raw($redirect_error_url));
			}
			
			return "";
		}
		
		public static function addFormStart($atts = [], $content = null, $tag = '') {
			if(!is_admin() && !wp_is_json_request()) {
				// normalize attribute keys, lowercase
				$atts = array_change_key_case((array) $atts, CASE_LOWER);
				// override default attributes with user attributes
				$infocob_atts = shortcode_atts([
					'id' => ''
				], $atts, $tag);
				
				$post_id = esc_html($infocob_atts['id']);
				
				static::$post_id = $post_id;
				
				$admin_form_html = get_post_meta($post_id, 'infocob_tracking_admin_form_start', true);
				$html_form       = !empty($admin_form_html) ? $admin_form_html : "";
				
				$html_form = (new static)->replaceShortcodes($html_form, true);
				
				$action = admin_url('admin-post.php');
				
				$o = "<form action='" . $action . "' class='infocob-tracking-form-start' method='POST'>";
				$o .= "<div class='infocob_tracking_loader'><span class='img_loader'></span><span class='text_loader'>" . __("Chargement...", "infocob-tracking") . "</span></div>";
				$o .= "<input type='hidden' name='action' value='infocob-tracking_submit_form_email'>";
				$o .= "<input type='hidden' name='infocob-tracking-id' value='" . $post_id . "'>";
				
				$o .= "<div class='dadywinnie'>
		                <input type='text' name='winnie' value='' />
		            </div>";
				
				$o .= wp_nonce_field('infocob-tracking-action_submit_' . $post_id, 'infocob-tracking_submit_form_email_nonce', true, false);
				$o .= $html_form;
				$o .= "</form>";
				
				return $o;
			} else {
				return "";
			}
		}
		
		public static function addFormEnd($atts = [], $content = null, $tag = '') {
			if(!is_admin() && !wp_is_json_request()) {
				add_action('wp_head', function() {
					echo "<meta name='robots' content='noindex,nofollow'/>";
				});
				
				$token = !empty($_GET['infocob_tracking_token']) ? sanitize_text_field($_GET['infocob_tracking_token']) : "";
				
				$o = "";
				if(!empty($token)) {
					$jwt    = Jwt::getJwt($token);
					$client = !empty($jwt->client) ? $jwt->client : false;
					if($client) {
						if(isset($client->is_new) && $client->is_new && !empty($client->post_id)) {
							$admin_form_edit_json = get_post_meta($client->post_id, 'infocob_tracking_admin_form_config', true);
							$form_config          = json_decode($admin_form_edit_json, true);
							$redirect_post_id     = !empty($form_config["redirect_html_form_register"]) ? $form_config["redirect_html_form_register"] : "";
							$redirect_url         = add_query_arg([
								"infocob_tracking_token" => $token,
								"infocob-tracking-email" => $client->email
							], get_page_link($redirect_post_id));
							wp_redirect(esc_url_raw($redirect_url));
							exit;
						}
						
						// normalize attribute keys, lowercase
						$atts = array_change_key_case((array) $atts, CASE_LOWER);
						// override default attributes with user attributes
						$infocob_atts = shortcode_atts([
							'id' => ''
						], $atts, $tag);
						
						$post_id = esc_html($infocob_atts['id']);
						
						static::$post_id = $post_id;
						
						$admin_form_html = get_post_meta($post_id, 'infocob_tracking_admin_form_end', true);
						$html_form       = !empty($admin_form_html) ? $admin_form_html : "";
						
						$html_form = (new static)->replaceShortcodes($html_form);
						
						$action = admin_url('admin-post.php');
						
						$o = "<form action='" . $action . "' class='infocob-tracking-form-end' method='POST'>";
						$o .= "<div class='infocob_tracking_loader'><span class='img_loader'></span><span class='text_loader'>" . __("Chargement...", "infocob-tracking") . "</span></div>";
						$o .= "<input type='hidden' name='action' value='infocob-tracking_submit_form_validate'>";
						$o .= "<input type='hidden' name='infocob-tracking-id' value='" . $post_id . "'>";
						$o .= "<input type='hidden' name='infocob-tracking-token' value='" . $token . "'>";
						
						$o .= "<div class='dadywinnie'>
				                <input type='text' name='winnie' value='' />
				            </div>";
						
						$o .= wp_nonce_field('infocob-tracking-action_submit_' . $post_id, 'infocob-tracking_submit_form_validate_nonce', true, false);
						$o .= $html_form;
						$o .= "</form>";
					}
				}
				
				if(empty($token) || empty(Jwt::getJwt($token))) {
					$options            = get_option('infocob_tracking_settings');
					$redirect_error_url = !empty($options["forms"]["url_error_page"]) ? get_page_link($options["forms"]["url_error_page"]) : get_home_url();
					wp_redirect(esc_url_raw($redirect_error_url));
					exit();
				}
				
				return apply_filters('infocob_tracking_shortcode_add_form_end', $o);
			} else {
				return "";
			}
		}
		
		public static function addFormSendinblue($atts = [], $content = null, $tag = '') {
			if(!is_admin() && !wp_is_json_request()) {
				add_action('wp_head', function() {
					echo "<meta name='robots' content='noindex,nofollow'/>";
				});
				
				// normalize attribute keys, lowercase
				$atts = array_change_key_case((array) $atts, CASE_LOWER);
				// override default attributes with user attributes
				$infocob_atts = shortcode_atts([
					'id' => ''
				], $atts, $tag);
				
				$post_id = esc_html($infocob_atts['id']);
				
				static::$post_id = $post_id;
				
				$c_code = !empty($_GET["c_code"]) ? sanitize_text_field($_GET["c_code"]) : "";
				$i_code = !empty($_GET["i_code"]) ? sanitize_text_field($_GET["i_code"]) : "";
				$email  = !empty($_GET["email"]) ? sanitize_email($_GET["email"]) : "";
				
				$options = get_option('infocob_tracking_settings');
				if(empty($c_code) && empty($i_code) && empty($email)) {
					$redirect_error_url = !empty($options["forms"]["url_error_page"]) ? get_page_link($options["forms"]["url_error_page"]) : get_home_url();
					wp_redirect(esc_url_raw($redirect_error_url));
					exit();
				}
				
				$webservice          = new Webservice();
				$contactfiches       = $webservice->usersInTable($email, 'contactfiche');
				$interlocuteurfiches = $webservice->usersInTable($email, 'interlocuteurfiche');
				$tables              = array_merge($contactfiches, $interlocuteurfiches);
				
				if(!empty($tables)) {
					$admin_form_html = get_post_meta($post_id, 'infocob_tracking_admin_form_sendinblue', true);
					$html_form       = !empty($admin_form_html) ? $admin_form_html : "";
				} else {
					$admin_form_html = get_post_meta($post_id, 'infocob_tracking_admin_form_sendinblue_no_user', true);
					$html_form       = !empty($admin_form_html) ? $admin_form_html : "";
				}
				
				$html_form = (new static)->replaceShortcodes($html_form);
				
				$action = admin_url('admin-post.php');
				
				$o = "<form action='" . $action . "' class='infocob-tracking-form-sendinblue' method='POST'>";
				$o .= "<div class='infocob_tracking_loader'><span class='img_loader'></span><span class='text_loader'>" . __("Chargement...", "infocob-tracking") . "</span></div>";
				$o .= "<input type='hidden' name='action' value='infocob-tracking_submit_form_sendinblue'>";
				$o .= "<input type='hidden' name='infocob-tracking-id' value='" . $post_id . "'>";
				$o .= "<input type='hidden' name='infocob-tracking-c_code' value='" . $c_code . "'>";
				$o .= "<input type='hidden' name='infocob-tracking-i_code' value='" . $i_code . "'>";
				$o .= "<input type='hidden' name='infocob-tracking-email' value='" . $email . "'>";
				
				$o .= "<div class='dadywinnie'>
		                <input type='text' name='winnie' value='' />
		            </div>";
				
				$o .= wp_nonce_field('infocob-tracking-action_submit_' . $post_id, 'infocob-tracking_submit_form_sendinblue_nonce', true, false);
				$o .= $html_form;
				$o .= "</form>";
				
				return $o;
			} else {
				return "";
			}
		}
		
		public function replaceShortcodes($html_form, $no_robot = false) {
			$html_form = preg_replace_callback('/\[(?:text|email|submit|groupements) .*\]/im', function($shortcode) use ($no_robot) {
				if(!empty($shortcode[0])) {
					if(preg_match('/\[\ ?submit.*\]/im', $shortcode[0])) {
						$o = "";
						if($no_robot) {
							$options = get_option('infocob_tracking_settings');
							$label  = !empty($options["forms"]["no-robot-label"]) ? $options["forms"]["no-robot-label"] : "Je ne suis pas un robot";
							
							$o = "<label class='if-field col-12 if-field-slide-checkbox if-casenorobot-infocob-tracking'>
							" . $label . "
			            </label>";
						}
						
						return $o . do_shortcode($shortcode[0]);
					} else {
						return do_shortcode($shortcode[0]);
					}
				} else {
					return "";
				}
			}, $html_form);
			
			return $html_form;
		}
	}

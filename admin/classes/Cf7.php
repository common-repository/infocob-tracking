<?php
	
	
	namespace Infocob\Tracking\Admin;
	
	
	use Exception;
	use WPCF7_FormTag;
	
	class Cf7 {
		
		public static function tag_generator() {
			if(!function_exists('wpcf7_add_tag_generator')) {
				return;
			}
			
			wpcf7_add_tag_generator('tracking',
				__('Infocob tracking list', 'infocob_tracking'),
				'wpcf7-tg-pane-group',
				array(__CLASS__, 'tag_pane')
			);
		}
		
		public static function tag_pane($contact_form, $args = '') {
			$args = wp_parse_args($args, array());
			$type = 'tracking';
			
			$description = __("Generate a tracking tag.", 'infocob-tracking');
			
			include ROOT_INFOCOB_TRACKING_DIR_PATH . "admin/includes/admin_cf7_form_tracking_pane.php";
		}
		
		public static function add_shortcodes() {
			if(function_exists('wpcf7_add_form_tag')) {
				wpcf7_add_form_tag('tracking', array(__CLASS__, 'shortcode_handler'), true);
			} else if(function_exists('wpcf7_add_shortcode')) {
				wpcf7_add_shortcode('tracking', array(__CLASS__, 'shortcode_handler'), true);
			} else {
				throw new Exception('functions wpcf7_add_form_tag and wpcf7_add_shortcode not found.');
			}
		}
		
		public static function shortcode_handler($tag) {
			$tag = new WPCF7_FormTag($tag);
			return $tag->content;
		}
		
		function tracking_shortcode_handler($atts, $content = "") {
			return $content;
		}
		
		public static function tracking_properties($properties, $wpcf7form) {
			if(!is_admin()) {
				$form = $properties['form'];
				
				$form_parts = preg_split('/(\[\/?tracking(?:\]|\s.*?\]))/', $form, - 1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
				
				ob_start();
				
				foreach($form_parts as $form_part) {
					if(substr($form_part, 0, 10) == '[tracking ') {
						$tag_parts = explode(' ', rtrim($form_part, ']'));
						
						array_shift($tag_parts);
						
						$tag_id        = $tag_parts[0];
						$tag_html_type = 'select';
						$classes       = "";
						
						foreach($tag_parts as $i => $tag_part) {
							if($i == 0) {
								continue;
							} else if(substr($tag_part, 0, 5) == 'class') {
								explode(':', $tag_part);
								$classes .= substr($tag_part, 6, strlen($tag_part)) . ' ';
							}
						}
						
						// Check if contactfiche or interlocuteurfiche
						$o     = "";
						$token = !empty($_GET["infocob_tracking_token"]) ? sanitize_text_field($_GET["infocob_tracking_token"]) : false;
						if($token !== false) {
							$jwt    = Jwt::getJwt($token);
							$client = !empty($jwt->client) ? $jwt->client : false;
							if($client) {
								$email   = !empty($client->email) ? $client->email : false;
								$post_id = !empty($client->post_id) ? $client->post_id : false;
								if($email && $post_id) {
									
									$admin_form_edit_json = get_post_meta($post_id, 'infocob_tracking_admin_form_config', true);
									$form_config          = json_decode($admin_form_edit_json, true);
									
									$webservice  = new Webservice();
									$filter_type = !empty($form_config["groups_form"]) ? $form_config["groups_form"] : [];
									$groupements = [];
									foreach($filter_type as $filter) {
										$groupements = array_merge($groupements, $webservice->getGroupements(false, false, $filter));
									}
									
									$o                  .= "<input type='hidden' name='infocob_tracking_token' value='" . $token . "'>";
									$o                  .= !empty($groupements) ? "<ul>" : "";
									$ml_codes_displayed = [];
									foreach($groupements as $groupement) {
										$ml_nom  = $groupement["ML_NOM"];
										$ml_code = $groupement["ML_CODE"];
										
										if(!in_array($ml_code, $ml_codes_displayed)) {
											$o .= "<li>";
											$o .= "<input class='" . $classes . "' type='checkbox' name='infocob_tracking_groupements[]' id='" . $ml_code . "' value='" . $ml_code . "'>";
											$o .= "<label for='" . $ml_code . "'>" . $ml_nom . "</label>";
											$o .= "</li>";
										}
										
										$ml_codes_displayed[] = $ml_code;
									}
									$o .= !empty($groupements) ? "</ul>" : "";
								}
							}
							
							if(empty($token) || empty(Jwt::getJwt($token))) {
								$options            = get_option('infocob_tracking_settings');
								$redirect_error_url = !empty($options["forms"]["url_error_page"]) ? get_page_link($options["forms"]["url_error_page"]) : get_home_url();
								wp_redirect(esc_url_raw($redirect_error_url));
								exit();
							}
						}
						
						echo $o;
						
					} else {
						echo $form_part;
					}
				}
				
				$properties['form'] = ob_get_clean();
			}
			
			return $properties;
		}
	}

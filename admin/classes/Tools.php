<?php
	
	
	namespace Infocob\Tracking\Admin;
	
	
	use WP_Query;
	
	class Tools {
		
		public static function constructData($datas, $form_config, $tablesLabel, $filter_type) {
			$post_id = !empty($form_config["post_id"]) ? $form_config["post_id"] : false;
			$response = [];
			if($post_id) {
				$type_form  = !empty($form_config["type_form"]) ? $form_config["type_form"] : false;
				$webservice = new Webservice();
				
				foreach($datas as $data) {
					$table = "";
					$labelTable = "";
					$code = "";
					
					$groupements = [];
					if(!empty($data["C_CODE"])) {
						$labelTable = !empty($tablesLabel["CONTACTFICHE"]) ? $tablesLabel["CONTACTFICHE"] : false;
						if($labelTable) {
							foreach($filter_type as $filter) {
								$groupements = array_merge($groupements, $webservice->getGroupements("CONTACTFICHE", $data["C_CODE"], $filter));
							}
							$code  = $data["C_CODE"];
							$table = "CONTACTFICHE";
						}
					} else if(!empty($data["I_CODE"])) {
						$labelTable = !empty($tablesLabel["INTERLOCUTEURFICHE"]) ? $tablesLabel["INTERLOCUTEURFICHE"] : false;
						if($labelTable) {
							foreach($filter_type as $filter) {
								$groupements = array_merge($groupements, $webservice->getGroupements("INTERLOCUTEURFICHE", $data["I_CODE"], $filter));
							}
							$code  = $data["I_CODE"];
							$table = "INTERLOCUTEURFICHE";
						}
					}
					
					foreach($groupements as $groupement) {
						
						if(strtoupper($groupement["ML_ACTIF"]) === "T") {
							
							if(strcasecmp($type_form, 'subscribe') === 0 && $groupement["CHECKED"] != true) {
								// Only grps available (not already subscribe)
								$response[ $groupement["ML_CODE"] ][ $table ][] = [
									"TABLE_LIBELLE" => $labelTable,
									"ML_NOM"        => $groupement["ML_NOM"],
									"CHECKED"       => $groupement["CHECKED"],
									"CODE"          => $code,
								];
							} else if(strcasecmp($type_form, 'unsubscribe') === 0 && $groupement["CHECKED"] == true) {
								// Only grps already subscribe
								$response[ $groupement["ML_CODE"] ][ $table ][] = [
									"TABLE_LIBELLE" => $labelTable,
									"ML_NOM"        => $groupement["ML_NOM"],
									"CHECKED"       => false,
									"CODE"          => $code,
								];
							} else if(strcasecmp($type_form, 'auto') === 0) {
								// All
								$response[ $groupement["ML_CODE"] ][ $table ][] = [
									"TABLE_LIBELLE" => $labelTable,
									"ML_NOM"        => $groupement["ML_NOM"],
									"CHECKED"       => $groupement["CHECKED"],
									"CODE"          => $code,
								];
							}
						}
					}
				}
			}
			
			return $response;
		}
		
		public static function getPostsListContactForm7() {
			$args = array(
				'post_status'    => 'post_status',
				'posts_per_page' => - 1,
				'offset'         => 0,
				'orderby'        => 'post_title',
				'order'          => 'ASC',
				'post_type'      => 'wpcf7_contact_form'
			);
			
			$q     = new WP_Query();
			$posts = $q->query($args);
			
			return $posts;
		}
		
		/**
		 * Check if this is a request at the backend.
		 *
		 * @return bool true if is admin request, otherwise false.
		 */
		public static function is_admin_request() {
			/**
			 * Get current URL.
			 */
			$current_url = home_url(add_query_arg(null, null));
			
			/**
			 * Get admin URL and referrer.
			 */
			$admin_url = strtolower(admin_url());
			$referrer  = strtolower(wp_get_referer());
			
			/**
			 * Check if this is a admin request. If true, it
			 * could also be a AJAX request from the frontend.
			 */
			if(0 === strpos($current_url, $admin_url)) {
				/**
				 * Check if the user comes from a admin page.
				 */
				if(0 === strpos($referrer, $admin_url)) {
					return true;
				} else {
					/**
					 * Check for AJAX requests.
					 */
					if(function_exists('wp_doing_ajax')) {
						return !wp_doing_ajax();
					} else {
						return !(defined('DOING_AJAX') && DOING_AJAX);
					}
				}
			} else {
				return false;
			}
		}
		
		public static function sanitize_fields($data) {
			if(is_string($data)) {
				$data = sanitize_text_field($data);
				
			} else if(is_int($data)) {
				$data = (int) $data;
				
			} else if(is_bool($data)) {
				$data = (bool) $data;
				
			} else if(is_array($data)) {
				foreach($data as $key => &$value) {
					if(is_array($value)) {
						$value = static::sanitize_fields($value);
					} else {
						if(is_string($value)) {
							$value = sanitize_text_field($value);
						} else if(is_int($value)) {
							$value = (int) $value;
						} else if(is_bool($value)) {
							$value = (bool) $value;
						}
					}
				}
			}
			
			return $data;
		}
	}

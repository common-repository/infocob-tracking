<?php
	
	
	namespace Infocob\Tracking\Admin;
	
	use function sanitize_text_field;
	use function wp_verify_nonce;
	
	class FormSubmission extends Controller {
		
		protected $webservice;
		protected $form_config;
		
		protected static $session_key = "infocob_tracking";
		protected static $is_session_vars_initied = false;
		protected static $is_message_sent = null;
		protected static $return_message = "";
		
		public function __construct() {
			$this->webservice = new Webservice();
		}
		
		public function process() {
			$post_id = !empty($_POST["infocob-tracking-id"]) ? sanitize_text_field($_POST["infocob-tracking-id"]) : "";
			
			$admin_form_edit_json = get_post_meta($post_id, 'infocob_tracking_admin_form_config', true);
			$this->form_config    = json_decode($admin_form_edit_json, true);
			
			if(isset($_POST['winnie']) && $_POST['winnie'] == "") {
				if(isset($_POST['infocob-tracking_submit_form_email_nonce'])) {
					if(wp_verify_nonce(sanitize_text_field($_POST['infocob-tracking_submit_form_email_nonce']), 'infocob-tracking-action_submit_' . $post_id)) {
						if(!isset($_POST['i-am-not-a-robot'])) {
							$this->registerAndRedirect(false, "Veuillez indiquer que vous n'êtes pas un robot");
						}
						
						$this->processFormStart();
					}
				} else if(isset($_POST['infocob-tracking_submit_form_validate_nonce'])) {
					if(wp_verify_nonce(sanitize_text_field($_POST['infocob-tracking_submit_form_validate_nonce']), 'infocob-tracking-action_submit_' . $post_id)) {
						$this->processFormEnd();
					}
				} else if(isset($_POST['infocob-tracking_submit_form_sendinblue_nonce'])) {
					if(wp_verify_nonce(sanitize_text_field($_POST['infocob-tracking_submit_form_sendinblue_nonce']), 'infocob-tracking-action_submit_' . $post_id)) {
						$this->processFormSendinblue();
					}
				}
			}
		}
		
		protected function processFormStart() {
			$email = !empty($_POST["infocob_tracking_user_email"]) ? sanitize_email($_POST["infocob_tracking_user_email"]) : false;
			
			$contactfiches       = $this->webservice->usersInTable($email, 'contactfiche');
			$interlocuteurfiches = $this->webservice->usersInTable($email, 'interlocuteurfiche');
			$tables              = array_merge($contactfiches, $interlocuteurfiches);
			
			if($email) {
				$backward_page = !empty($this->form_config["backward_page"]) ? $this->form_config["backward_page"] : false;
				$post_id       = !empty($this->form_config["post_id"]) ? $this->form_config["post_id"] : false;
				$isNew         = empty($tables);
				
				if($backward_page) {
					$redirect_url = get_page_link($backward_page);
					
					$c_code = $contactfiches[0]["C_CODE"] ?? null;
					$i_code = $interlocuteurfiches[0]["I_CODE"] ?? null;
					
					$mail = new Mail();
					$mail->sendEmailConfirmation([
						"post_id"      => $post_id,
						"email"        => sanitize_email($email),
						"c_code"       => $c_code,
						"i_code"       => $i_code,
						"redirect_url" => $redirect_url,
						"is_new"       => $isNew
					], $this->form_config["post_id"]);
					exit();
				}
			}
		}
		
		protected function processFormEnd() {
			$token               = !empty($_POST["infocob-tracking-token"]) ? sanitize_text_field($_POST["infocob-tracking-token"]) : false;
			$selectedGroupements = !empty($_POST["groupements"]) ? Tools::sanitize_fields($_POST["groupements"]) : [];
			
			$redirect_page_subscription_confirm = $this->changeGroupements($token, $selectedGroupements, $this->webservice);
			
			if(!empty($redirect_page_subscription_confirm)) {
				wp_redirect(get_page_link($redirect_page_subscription_confirm));
				exit();
			}
			
		}
		
		public function processFormSendinblue() {
			$c_code = !empty($_POST["infocob-tracking-c_code"]) ? sanitize_text_field($_POST["infocob-tracking-c_code"]) : false;
			$i_code = !empty($_POST["infocob-tracking-i_code"]) ? sanitize_text_field($_POST["infocob-tracking-i_code"]) : false;
			$email  = !empty($_POST["infocob-tracking-email"]) ? sanitize_email($_POST["infocob-tracking-email"]) : false;
			
			$contactfiches       = $this->webservice->usersInTable($email, 'contactfiche');
			$interlocuteurfiches = $this->webservice->usersInTable($email, 'interlocuteurfiche');
			$tables              = array_merge($contactfiches, $interlocuteurfiches);
			
			// Process standard
			if(empty($c_code) && empty($i_code) && !empty($email)) {
				$email = sanitize_email($email);
				
				$c_code = $contactfiches[0]["C_CODE"] ?? null;
				$i_code = $interlocuteurfiches[0]["I_CODE"] ?? null;
				
			} else if(!empty($c_code) || !empty($i_code)) {
				$webservice = new Webservice();
				if(!empty($i_code)) {
					$interlocuteurfiche = $webservice->getInterlocuteurfiche($i_code);
					$email              = $this->getEmailInterlocuteur($interlocuteurfiche);
					
				} else if(!empty($c_code)) {
					$contactfiche = $webservice->getContactfiche($c_code);
					$email        = !empty($contactfiche["C_EMAIL"]) ? sanitize_email($contactfiche["C_EMAIL"]) : false;
				}
			}
			
			if($email) {
				$backward_page = !empty($this->form_config["backward_page"]) ? $this->form_config["backward_page"] : false;
				$post_id       = !empty($this->form_config["post_id"]) ? $this->form_config["post_id"] : false;
				$isNew         = empty($tables);
				
				if($backward_page) {
					$redirect_url = get_page_link($backward_page);
					
					$mail = new Mail();
					$mail->sendEmailConfirmation([
						"post_id"      => $post_id,
						"email"        => sanitize_email($email),
						"c_code"       => $c_code,
						"i_code"       => $i_code,
						"redirect_url" => $redirect_url,
						"is_new"       => $isNew
					], $this->form_config["post_id"]);
					exit();
				}
			}
			
			$options            = get_option('infocob_tracking_settings');
			$redirect_error_url = !empty($options["forms"]["url_error_page"]) ? get_page_link($options["forms"]["url_error_page"]) : get_home_url();
			wp_redirect(esc_url_raw($redirect_error_url));
		}
		
		public function processAfterRegister($response) {
			$contact_form_id     = !empty($response["CONTACT_FORM_ID"]) ? $response["CONTACT_FORM_ID"] : false;
			$token               = !empty($response["INFOCOB_TRACKING_TOKEN"]) ? $response["INFOCOB_TRACKING_TOKEN"] : false;
			$selectedGroupements = !empty($response["INFOCOB_TRACKING_GROUPEMENTS"]) ? $response["INFOCOB_TRACKING_GROUPEMENTS"] : false;
			$c_code              = !empty($response["C_CODE"]) ? $response["C_CODE"] : false;
			$i_code              = !empty($response["I_CODE"]) ? $response["I_CODE"] : false;
			
			$jwt              = Jwt::getJwt($token);
			
			if($jwt && $selectedGroupements) {
				$post_id              = $jwt->client->post_id ?? false;
				$admin_form_edit_json = get_post_meta($post_id, 'infocob_tracking_admin_form_config', true);
				$form_config          = json_decode($admin_form_edit_json, true);
				
				$dataMap              = [
					"selectedGroupements" => $selectedGroupements,
					"email"               => null,
					"c_code"              => $c_code,
					"i_code"              => $i_code,
					"filter_types"        => $form_config["groups_form"] ?? [],
				];
				$this->webservice->changeGroupements($dataMap);
			}
			
			return true;
		}
		
		public function getEmailInterlocuteur($interlocuteurfiche) {
			$email = false;
			if(!empty($interlocuteurfiche["I_EMAIL"])) {
				$email = sanitize_email($interlocuteurfiche["I_EMAIL"]);
			} else if(!empty($interlocuteurfiche["I_EMAIL2"])) {
				$email = sanitize_email($interlocuteurfiche["I_EMAIL2"]);
			} else if(!empty($interlocuteurfiche["I_EMAIL3"])) {
				$email = sanitize_email($interlocuteurfiche["I_EMAIL3"]);
			} else if(!empty($interlocuteurfiche["I_EMAIL4"])) {
				$email = sanitize_email($interlocuteurfiche["I_EMAIL4"]);
			} else if(!empty($interlocuteurfiche["I_EMAIL5"])) {
				$email = sanitize_email($interlocuteurfiche["I_EMAIL5"]);
			}
			
			return $email;
		}
		
		public function changeGroupements($token, $selectedGroupements, $webservice) {
			$jwt     = Jwt::getJwt($token);
			$email   = !empty($jwt->client->email) ? sanitize_email($jwt->client->email) : false;
			$post_id = !empty($jwt->client->post_id) ? sanitize_text_field($jwt->client->post_id) : false;
			$c_code  = !empty($jwt->client->c_code) ? sanitize_text_field($jwt->client->c_code) : false;
			$i_code  = !empty($jwt->client->i_code) ? sanitize_text_field($jwt->client->i_code) : false;
			
			$admin_form_edit_json = get_post_meta($post_id, 'infocob_tracking_admin_form_config', true);
			$form_config          = json_decode($admin_form_edit_json, true);
			
			$redirect_page_subscription_confirm = !empty($form_config["redirect_page_subscription_confirm"]) ? $form_config["redirect_page_subscription_confirm"] : false;
			
			if($jwt && $email) {
				$webservice = new Webservice();
				$options    = get_option('infocob_tracking_settings');
				$nom          = !empty($options["historique"]["nom"]) ? $options["historique"]["nom"] : "";
				$types         = !empty($options["historique"]["type"]) ? $options["historique"]["type"] : [];
				$destinataire = !empty($options["historique"]["destinataire"]) ? $options["historique"]["destinataire"] : "";
				$types_historique = explode(".", $types);
				
				$dataMap = [
					"selectedGroupements" => $selectedGroupements,
					"email"               => $email,
					"c_code"              => $c_code,
					"i_code"              => $i_code,
					"filter_types"        => $form_config["groups_form"] ?? [],
					"config_historique" => [
						"H_NOM_ACTION" => $nom,
						"H_TYPEACTION" => $types_historique[0] ?? "",
						"H_SOUSTYPEACTION" => $types_historique[1] ?? "",
						"H_SOUSSOUSTYPEACTION" => $types_historique[2] ?? "",
						"H_CODEINTERLOCUTEUR_DEST" => $destinataire,
					],
				];
				$webservice->changeGroupements($dataMap);
			}
			
			return $redirect_page_subscription_confirm;
		}
		
		public static function getIsMessageSent() {
			if(!static::$is_session_vars_initied) {
				static::session_vars_init();
			}
			
			return static::$is_message_sent;
		}
		
		public static function getReturnMessage() {
			if(!static::$is_session_vars_initied) {
				static::session_vars_init();
			}
			
			return static::$return_message;
		}
		
		protected static function session_vars_init() {
			if(isset($_SESSION[ static::$session_key . "_is_message_sent" ])) {
				if($_SESSION[ static::$session_key . "_is_message_sent" ]) {
					static::$is_message_sent = true;
					static::$return_message  = isset($_SESSION[ static::$session_key . "_message_form_sent" ]) ? $_SESSION[ static::$session_key . "_message_form_sent" ] : "Merci, votre message a bien été envoyé.";
				} else {
					static::$is_message_sent = false;
					static::$return_message  = isset($_SESSION[ static::$session_key . "_message_form_sent" ]) ? $_SESSION[ static::$session_key . "_message_form_sent" ] : "Une erreur est survenue lors de l'envoi du formulaire.";
				}
				unset($_SESSION[ static::$session_key . "_is_message_sent" ]);
				unset($_SESSION[ static::$session_key . "_message_form_sent" ]);
			}
			static::$is_session_vars_initied = true;
		}
		
		protected function registerAndRedirect($form_sent = false, $message = "", $redirect_url = false) {
			$_SESSION[ static::$session_key . "_is_message_sent" ]   = $form_sent;
			$_SESSION[ static::$session_key . "_message_form_sent" ] = $message;
			
			if($redirect_url === false) {
				$redirect_url = wp_get_referer();
			}
			
			header("Location: " . $redirect_url);
			die();
		}
		
	}

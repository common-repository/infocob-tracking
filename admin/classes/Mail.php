<?php
	
	
	namespace Infocob\Tracking\Admin;
	
	use Infocob\Tracking\Admin\Jwt;
	use PHPMailer;
	
	require_once ABSPATH . '/wp-includes/class-phpmailer.php';
	
	class Mail extends Controller {
		
		public function sendEmailConfirmation($payload, $post_id) {
			/*
			 * Generation JWT
			 */
			$token = JWT::generateJwt($payload);
			
			$admin_form_edit_json = get_post_meta($post_id, 'infocob_tracking_admin_form_config', true);
			$admin_form_edit      = json_decode($admin_form_edit_json, true);
			
			$backward_page     = !empty($admin_form_edit["backward_page"]) ? $admin_form_edit["backward_page"] : "#";
			$redirect_url      = get_page_link($backward_page);
			$subscription_link = add_query_arg('infocob_tracking_token', $token, $redirect_url);
			
			/*
			 * Get config
			 */
			$form_config_json = get_post_meta($post_id, 'infocob_tracking_admin_form_config', true);
			$form_config      = json_decode($form_config_json, true);
			
			$form_type = !empty($form_config["type_form"]) ? $form_config["type_form"] : "auto";
			$subject   = "Demande de modifications de vos abonnements";
			if(strcasecmp($form_type, "subscribe") === 0) {
				$subject = "Demande d'abonnement";
			}
			if(strcasecmp($form_type, "unsubscribe") === 0) {
				$subject = "Demande de désabonnement";
			}
			
			/*
			 * Send mail
			 */
			$form_config_template_email_json = get_post_meta($post_id, 'infocob_tracking_admin_form_email_demande', true);
			$form_config_template_email      = json_decode($form_config_template_email_json, true);
			
			$email_from    = !empty($form_config_template_email["email_from"]) ? $form_config_template_email["email_from"] : "";
			$email_subject = !empty($form_config_template_email["email_subject"]) ? $form_config_template_email["email_subject"] : "";
			
			$mail = new PHPMailer(true);
			$mail->setLanguage('fr');
			$mail->CharSet = 'UTF-8';
			
			try {
				//Recipients
				$from_name = get_bloginfo('name');
				$mail->setFrom($email_from, $from_name);
				$mail->addAddress($payload["email"]); // Add a recipient
				
				$vars_template_email                           = [];
				$vars_template_email["email_from"]             = $form_config_template_email["email_from"] ?? "";
				$vars_template_email["email_subject"]          = $form_config_template_email["email_subject"] ?? "";
				$vars_template_email["email_title"]            = $form_config_template_email["email_subject"] ?? "";
				$vars_template_email["email_color"]            = $form_config_template_email["email_subject"] ?? "";
				$vars_template_email["email_color_text_title"] = $form_config_template_email["email_subject"] ?? "";
				$vars_template_email["email_color_link"]       = $form_config_template_email["email_subject"] ?? "";
				$vars_template_email["email_subtitle"]         = $form_config_template_email["email_subject"] ?? "";
				$vars_template_email["email_societe"]          = $form_config_template_email["email_subject"] ?? "";
				$vars_template_email["email_border_radius"]    = $form_config_template_email["email_subject"] ?? "";
				$vars_template_email["subscription_link"]      = $subscription_link ?? "#";
				
				if(!empty($form_config_template_email["logo"])) {
					$attachment_id = $form_config_template_email["logo"]["attachment_id"] ?? false;
					$size = $form_config_template_email["logo"]["size"] ?? "";
					if($attachment_id) {
						$image = wp_get_attachment_image_src($attachment_id, $size);
						$vars_template_email["logo"] = $image[0] ?? "";
					}
				} else {
					$vars_template_email["logo"] = "";
				}
				
				$email_template = $form_config_template_email["email_template"] ?? "defaut-infocob-tracking";
				
				//$body    = $this->getTemplate("confirmation_email", $form_template_email, $vars_template_email);
				$template_mail = new TemplateDemandeAbonnement($email_template);
				$body          = $template_mail->HTML($vars_template_email);
				$AltBody       = $template_mail->text($vars_template_email);
				
				// Content
				$mail->isHTML(true); // Set email format to HTML
				$mail->Subject = $subject;
				$mail->Body    = $body;
				$mail->AltBody = $AltBody;
				
				$mail->send();
				
				$redirect_page_email_sent = !empty($form_config["redirect_page_email_sent"]) ? $form_config["redirect_page_email_sent"] : false;
				if(!empty($redirect_page_email_sent)) {
					wp_redirect(get_page_link($redirect_page_email_sent));
				} else {
					global $wp_query;
					$wp_query->set_404();
					status_header(404);
					get_template_part(404);
					exit();
				}
			} catch(\Exception $e) {
				echo "Erreur envoi email";
				die();
			}
		}
		
		public function getTemplate($template_name, $template_email, $vars = []) {
			$template = file_get_contents(ROOT_INFOCOB_TRACKING_DIR_PATH . 'admin/mails/' . $template_name . '.html');
			$template = str_replace('{% template_email %}', $template_email, $template);
			
			$complete_template = preg_replace_callback('/{% ?(.+) ?%}/mi', function($matches) use ($vars) {
				if(!empty($vars[ trim($matches[1]) ])) {
					return $vars[ trim($matches[1]) ];
				} else {
					return "UNDEFINED";
				}
			}, $template);
			
			return $complete_template;
		}
		
	}

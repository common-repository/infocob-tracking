<?php
	
	namespace Infocob\Tracking\Admin;
	
	// don't load directly
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class AdminSettings extends Controller {
		protected $options;
		
		public function __construct() {
			$this->options = get_option('infocob_tracking_settings');
			
			/*
			 * Webservice
			 */
			add_settings_section(
				'infocob_tracking_api_section',
				__('Clé API', 'infocob-tracking'),
				[$this, 'apiKeySection'],
				'infocob_tracking'
			);
			
			add_settings_field(
				'domain',
				__('Domaine', 'infocob-tracking'),
				[$this, 'apiDomainField'],
				'infocob_tracking',
				'infocob_tracking_api_section'
			);
			
			add_settings_field(
				'api_key',
				__('Clé API', 'infocob-tracking'),
				[$this, 'apiKeyField'],
				'infocob_tracking',
				'infocob_tracking_api_section'
			);
			
			/*
			 * Token
			 */
			add_settings_section(
				'infocob_tracking_token_section',
				__('Token', 'infocob-tracking'),
				[$this, 'tokenSection'],
				'infocob_tracking'
			);
			
			add_settings_field(
				'token_secret',
				__('Secret', 'infocob-tracking'),
				[$this, 'tokenSecretField'],
				'infocob_tracking',
				'infocob_tracking_token_section'
			);
			
			add_settings_field(
				'token_expiration',
				__('Temps avant expiration (seconds)', 'infocob-tracking'),
				[$this, 'tokenExpirationField'],
				'infocob_tracking',
				'infocob_tracking_token_section'
			);
			
			/*
			 * Historique
			 */
			add_settings_section(
				'infocob_tracking_historique_section',
				__('Historique', 'infocob-tracking'),
				[$this, 'historiqueSection'],
				'infocob_tracking'
			);
			
			add_settings_field(
				'historique_nom',
				__('Nom', 'infocob-tracking'),
				[$this, 'historiqueNameField'],
				'infocob_tracking',
				'infocob_tracking_historique_section'
			);
			
			add_settings_field(
				'historique_type',
				__('Type', 'infocob-tracking'),
				[$this, 'historiqueTypeField'],
				'infocob_tracking',
				'infocob_tracking_historique_section'
			);
			
			add_settings_field(
				'historique_destinataire',
				__('Destinataire', 'infocob-tracking'),
				[$this, 'historiqueDestinataireField'],
				'infocob_tracking',
				'infocob_tracking_historique_section'
			);
			
			/*
			 * Formulaires
			 */
			add_settings_section(
				'infocob_tracking_forms_section',
				__('Formulaires', 'infocob-tracking'),
				[$this, 'formsSection'],
				'infocob_tracking'
			);
			
			add_settings_field(
				'forms_url_error_page',
				__('Page d\'erreur URL incorrecte', 'infocob-tracking'),
				[$this, 'formsUrlErrorPageField'],
				'infocob_tracking',
				'infocob_tracking_forms_section'
			);
			
			add_settings_field(
				'forms_no_robot_label',
				__('Libellé no robot', 'infocob-tracking'),
				[$this, 'formsNoRobotLabelField'],
				'infocob_tracking',
				'infocob_tracking_forms_section'
			);
			
			/*
			 * Loader
			 */
			add_settings_section(
				'infocob_tracking_loader_section',
				__('Loader', 'infocob-tracking'),
				[$this, 'loaderSection'],
				'infocob_tracking'
			);
			
			add_settings_field(
				'loader_background_color',
				__('Couleur d\'arrière plan', 'infocob-tracking'),
				[$this, 'loaderBackgroundColorField'],
				'infocob_tracking',
				'infocob_tracking_loader_section',
				[
					"class" => "same-line"
				]
			);
			
			add_settings_field(
				'loader_color',
				__('Couleur', 'infocob-tracking'),
				[$this, 'loaderColorField'],
				'infocob_tracking',
				'infocob_tracking_loader_section'
			);
			
			add_settings_field(
				'loader_text_color',
				__('Couleur du texte', 'infocob-tracking'),
				[$this, 'loaderTextColorField'],
				'infocob_tracking',
				'infocob_tracking_loader_section'
			);
		}
		
		public function testApiConnection() {
			$webservice = new Webservice();
			
			$apikey  = !empty($this->options["api"]["key"]) ? $this->options["api"]["key"] : "";
			$domain  = !empty($this->options["api"]["domain"]) ? $this->options["api"]["domain"] : "";
			$success = $webservice->test($apikey, $domain);
			
			if($success) {
				add_action('infocob_tracking_settings_admin_notices', function() {
					?>
                    <div class="notice notice-success">
                        <p><?php _e('Connexion au webservice réussie !', 'infocob-tracking'); ?></p>
                    </div>
					<?php
				});
			} else {
				add_action('infocob_tracking_settings_admin_notices', function() {
					?>
                    <div class="notice notice-error">
                        <p><?php _e('Connexion au webservice échouée !', 'infocob-tracking'); ?></p>
                    </div>
					<?php
				});
			}
			
			do_action('infocob_tracking_settings_admin_notices');
		}
		
		public function render() {
			$this->testApiConnection();
			
			require_once plugin_dir_path(__FILE__) . '../views/settings.php';
		}
		
		/*
		 * Webservice
		 */
		public function apiKeySection() {
			echo __('Ces options permettent la configuration de la connexion au webservice Infocob', 'infocob-tracking');
		}
		
		public function apiKeyField() {
			?>
            <input id="apikey" type='text' name='infocob_tracking_settings[api][key]' value='<?php echo $this->options['api']['key']; ?>'>
			<?php
		}
		
		public function apiDomainField() {
			?>
            <input id="domain" type='text' name='infocob_tracking_settings[api][domain]' value='<?php echo $this->options['api']['domain']; ?>'>
			<?php
		}
		
		/*
		 * Token
		 */
		public function tokenSection() {
			echo __('Ces options permettent la configuration du token utilisé pour les liens d\'abonnements/désabonnements', 'infocob-tracking');
		}
		
		public function tokenSecretField() {
			?>
            <input id="token_secret" type='text' name='infocob_tracking_settings[token][secret]' value='<?php echo $this->options['token']['secret']; ?>' readonly>
            <button type="button" id="generate_token_secret">Générer</button>
			<?php
		}
		
		public function tokenExpirationField() {
			?>
            <input type='number' name='infocob_tracking_settings[token][expiration]' value='<?php echo !empty($this->options['token']['expiration']) ? $this->options['token']['expiration'] : 600; ?>'>
			<?php
		}
		
		/*
		 * Historique
		 */
		public function historiqueSection() {
			echo __('Ces options permettent la configuration des historiques créés suite à un changement de préférences d\'abonnements, cela permet de garder un suivi des modifications', 'infocob-tracking');
		}
		
		public function historiqueContactficheField() {
			$c_code       = !empty($this->options["historique"]["contactfiche"]) ? $this->options["historique"]["contactfiche"] : false;
			$webservice   = new Webservice();
			$contactfiche = $webservice->getContactfiche($c_code);
			$c_nom        = !empty($contactfiche["C_NOM"]) ? $contactfiche["C_NOM"] : "";
			$c_prenom     = !empty($contactfiche["C_PRENOM"]) ? $contactfiche["C_PRENOM"] : "";
			$fullname     = trim($c_prenom . " " . $c_nom);
			?>
            <select id="selectContactfiche" name="infocob_tracking_settings[historique][contactfiche]">
				<?php if($contactfiche) : ?>
                    <option value="<?php echo $c_code; ?>" selected><?php echo $fullname; ?></option>
				<?php endif; ?>
            </select>
			<?php
		}
		
		public function historiqueInterlocuteurficheField() {
			$i_code             = !empty($this->options["historique"]["interlocuteurfiche"]) ? $this->options["historique"]["interlocuteurfiche"] : false;
			$webservice         = new Webservice();
			$interlocuteurfiche = $webservice->getInterlocuteurfiche($i_code);
			$i_nom              = !empty($interlocuteurfiche["I_NOM"]) ? $interlocuteurfiche["I_NOM"] : "";
			$i_prenom           = !empty($interlocuteurfiche["I_PRENOM"]) ? $interlocuteurfiche["I_PRENOM"] : "";
			$fullname           = trim($i_prenom . " " . $i_nom);
			?>
            <select id="selectInterlocuteurfiche" name="infocob_tracking_settings[historique][interlocuteurfiche]">
				<?php if($interlocuteurfiche) : ?>
                    <option value="<?php echo $i_code; ?>" selected><?php echo $fullname; ?></option>
				<?php endif; ?>
            </select>
			<?php
		}
		
		public function historiqueNameField() {
			?>
            <input type='text' name='infocob_tracking_settings[historique][nom]' value='<?php echo isset($this->options['historique']['nom']) ? $this->options['historique']['nom'] : ""; ?>'>
			<?php
		}
		
		public function historiqueTypeField() {
			$types = Historique::getTypes();
			?>
            <select name="infocob_tracking_settings[historique][type]">
				<?php foreach($types as $type) : ?>
                    <option class="opt-level one" value="<?php echo $type["LTA_CODE"]; ?>" <?php if(isset($this->options['historique']['type']) && strcasecmp($this->options['historique']['type'], $type["LTA_CODE"]) === 0) { ?>selected<?php } ?>><?php echo $type["LTA_NOM"]; ?></option>
					<?php if(!empty($type["SOUS_TYPES"])) : ?>
						<?php foreach($type["SOUS_TYPES"] as $sous_type) : ?>
                            <option class="opt-level two" value="<?php echo $type["LTA_CODE"]. "." . $sous_type["LTA_CODE"]; ?>" <?php if(isset($this->options['historique']['type']) && strcasecmp($this->options['historique']['type'], $type["LTA_CODE"]. "." . $sous_type["LTA_CODE"]) === 0) { ?>selected<?php } ?>> - <?php echo $sous_type["LTA_NOM"]; ?></option>
							<?php if(!empty($sous_type["SOUS_TYPES"])) : ?>
								<?php foreach($sous_type["SOUS_TYPES"] as $sous_sous_type) : ?>
                                    <option class="opt-level three" value="<?php echo $type["LTA_CODE"]. "." . $sous_type["LTA_CODE"] . "." . $sous_sous_type["LTA_CODE"]; ?>" <?php if(isset($this->options['historique']['type']) && strcasecmp($this->options['historique']['type'], $type["LTA_CODE"]. "." . $sous_type["LTA_CODE"] . "." . $sous_sous_type["LTA_CODE"]) === 0) { ?>selected<?php } ?>> - - <?php echo $sous_sous_type["LTA_NOM"]; ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endforeach; ?>
            </select>
			<?php
		}
		
		public function historiqueDestinataireField() {
			$destinataires = Historique::getDestinataires();
			?>
            <select name="infocob_tracking_settings[historique][destinataire]">
                <option value=""></option>
				<?php foreach($destinataires as $destinataire) : ?>
                    <option value="<?php echo $destinataire["V_CODE"]; ?>" <?php if(isset($this->options['historique']['destinataire']) && strcasecmp($this->options['historique']['destinataire'], $destinataire["V_CODE"]) === 0) { ?>selected<?php } ?>><?php echo $destinataire["V_PRENOM"] . " " . $destinataire["V_NOM"]; ?></option>
				<?php endforeach; ?>
            </select>
			<?php
		}
		
		/*
		 * Formulaires
		 */
		public function formsSection() {
			echo __('Ces options permettent la configuration globale des formulaires créés', 'infocob-tracking');
		}
		
		public function formsUrlErrorPageField() {
			$wp_pages_list = !empty(get_pages()) ? get_pages() : [];
			?>
            <select name="infocob_tracking_settings[forms][url_error_page]">
                <option value=""><?php echo __("Aucune page", "infocob-tracking"); ?></option>
				<?php foreach($wp_pages_list as $page) : ?>
                    <option value="<?php echo $page->ID; ?>" <?php if(isset($this->options['forms']['url_error_page']) && strcasecmp($this->options['forms']['url_error_page'], $page->ID) === 0) { ?>selected<?php } ?>><?php echo $page->post_title; ?></option>
				<?php endforeach; ?>
            </select>
			<?php
		}
		
		public function formsNoRobotLabelField() {
			?>
            <input name="infocob_tracking_settings[forms][no-robot-label]" type='text' value="<?php echo isset($this->options['forms']['no-robot-label']) ? $this->options['forms']['no-robot-label'] : "Je ne suis pas un robot"; ?>">
			<?php
		}
		
		/*
		 * Loader
		 */
		public function loaderSection() {
			echo __('Ces options permettent la personnalisation du loader', 'infocob-tracking');
		}
		
		public function loaderBackgroundColorField() {
			?>
            <input name="infocob_tracking_settings[loader][background-color]" type='text' class='color-field' value="<?php echo isset($this->options['loader']['background-color']) ? $this->options['loader']['background-color'] : ""; ?>">
            <input id="loader_background_opacity" name="infocob_tracking_settings[loader][background-opacity]" type='number' min="0" max="100" step="1" value="<?php echo isset($this->options['loader']['background-opacity']) ? $this->options['loader']['background-opacity'] : 50; ?>">
			<label for="loader_background_opacity"><?php _e('Opacité (%)', 'infocob-tracking'); ?></label>
			<?php
		}
		
		public function loaderColorField() {
			?>
			<input name="infocob_tracking_settings[loader][color]" type='text' class='color-field' value="<?php echo isset($this->options['loader']['color']) ? $this->options['loader']['color'] : ""; ?>">
			<?php
		}
		
		public function loaderTextColorField() {
			?>
			<input name="infocob_tracking_settings[loader][text-color]" type='text' class='color-field' value="<?php echo isset($this->options['loader']['text-color']) ? $this->options['loader']['text-color'] : ""; ?>">
			<?php
		}
	}

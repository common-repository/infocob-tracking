<?php
	
	
	namespace Infocob\Tracking\Admin;
	
	
	class DetailHistorique {
		protected $webservice;
		protected $subscription = [];
		
		public function __construct() {
			$this->webservice = new Webservice();
		}
		
		public function add($subscription_type, $ml_code, $c_code = false, $i_code = false) {
			$subscription           = "";
			$subscription_type_text = "";
			if(strcasecmp($subscription_type, "subscribe") === 0) {
				$subscription_type_text .= "abonné(e) à";
			} else if(strcasecmp($subscription_type, "unsubscribe") === 0) {
				$subscription_type_text .= "désabonné(e) de";
			}
			
			if(!empty($c_code)) {
				$contactfiche = $this->webservice->getContactfiche($c_code);
			}
			
			if(!empty($i_code)) {
				$interlocuteurfiche = $this->webservice->getInterlocuteurfiche($i_code);
			}
			
			$tableLibelle                   = $this->webservice->getTableLibelle([
				"contactfiche",
				"interlocuteurfiche"
			]);
			$tableLibelleContactfiche       = !empty($tableLibelle["CONTACTFICHE"]) ? $tableLibelle["CONTACTFICHE"] : "Contactfiche";
			$tableLibelleInterlocuteurfiche = !empty($tableLibelle["INTERLOCUTEURFICHE"]) ? $tableLibelle["INTERLOCUTEURFICHE"] : "Interlocuteurfiche";
			
			$groupement = $this->webservice->getGroupement($ml_code);
			if(!empty($groupement)) {
				$ml_nom = !empty($groupement["ML_NOM"]) ? $groupement["ML_NOM"] : "";
				
				if(!empty($interlocuteurfiche)) {
					$i_nom    = !empty($interlocuteurfiche["I_NOM"]) ? $interlocuteurfiche["I_NOM"] : "";
					$i_prenom = !empty($interlocuteurfiche["I_PRENOM"]) ? $interlocuteurfiche["I_PRENOM"] : "";
					$fullname = trim($i_nom . " " . $i_prenom);
					
					$subscription .= "[" . $tableLibelleInterlocuteurfiche . "] " . $fullname . " (" . $i_code . ") s'est " . $subscription_type_text . " " . $ml_nom . " (" . $ml_code . ")\n";
				} else if(!empty($contactfiche)) {
					$c_nom    = !empty($contactfiche["C_NOM"]) ? $contactfiche["C_NOM"] : "";
					$c_prenom = !empty($contactfiche["C_PRENOM"]) ? $contactfiche["C_PRENOM"] : "";
					$fullname = trim($c_nom . " " . $c_prenom);
					
					$subscription .= "[" . $tableLibelleContactfiche . "] " . $fullname . " (" . $c_code . ") s'est " . $subscription_type_text . " " . $ml_nom . " (" . $ml_code . ")\n";
				}
			}
			
			$this->subscription[] = $subscription;
		}
		
		public function get() {
			$string = "";
			foreach($this->subscription as $sub) {
				$string .= $sub;
				$string .= "\n";
			}
			
			return $string;
		}
		
	}

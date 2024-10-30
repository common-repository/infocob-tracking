<?php
	
	
	namespace Infocob\Tracking\Admin;
	
	
	class Historique {
		
		public static function add($detail, $c_code = false, $i_code = false) {
			$webservice = new Webservice();
			$options    = get_option('infocob_tracking_settings');
			
			$nom          = !empty($options["historique"]["nom"]) ? $options["historique"]["nom"] : false;
			$type         = !empty($options["historique"]["type"]) ? $options["historique"]["type"] : false;
			$destinataire = !empty($options["historique"]["destinataire"]) ? $options["historique"]["destinataire"] : false;
			
			$body = [];
			if(!empty($i_code)) {
				$body["H_INTERLOCUTEURCONTACT"] = $i_code;
				
				$interlocuteurfiche = $webservice->getInterlocuteurfiche($i_code);
				if(!empty($interlocuteurfiche)) {
					$body["H_CODECONTACT"] = $interlocuteurfiche["I_CODECONTACT"];
				}
			} else if(!empty($c_code)) {
				$body["H_CODECONTACT"] = $c_code;
			}
			if($nom) {
				$body["H_NOM_ACTION"] = $nom;
			}
			if($type) {
				$body["H_TYPEACTION"] = $type;
			}
			if($destinataire) {
				$body["H_CODEINTERLOCUTEUR_DEST"] = $destinataire;
			}
			if($detail) {
				$body["H_DETAIL"] = $detail;
			}
			
			$webservice->addHistorique($body);
		}
		
		public static function getTypes() {
			$webservice = new Webservice();
			$response   = $webservice->getTypesHistorique();
			
			return ($response === false) ? [] : $response;
		}
		
		public static function getDestinataires() {
			$webservice = new Webservice();
			$response   = $webservice->getVendeurs();
			
			return ($response === false) ? [] : $response;
		}
		
		public static function getContactfiche($keywords) {
			$webservice = new Webservice();
			
			return $webservice->searchContactfiche($keywords);
		}
		
		public static function getInterlocuteurfiche($keywords, $c_code = false) {
			$webservice = new Webservice();
			if($c_code) {
				return $webservice->searchInterlocuteurficheFromContactfiche($c_code, $keywords);
			} else {
				return $webservice->searchInterlocuteurfiche($keywords);
			}
		}
	}

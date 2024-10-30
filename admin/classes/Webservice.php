<?php
	
	namespace Infocob\Tracking\Admin;
	
	class Webservice {
		protected $domain_client = "";
		protected $api_key = "";
		
		/**
		 * Webservice constructor.
		 */
		public function __construct() {
			$options = get_option('infocob_tracking_settings');
			
			$this->domain_client = !empty($options["api"]["domain"]) ? $options["api"]["domain"] : "";
			$this->api_key       = !empty($options["api"]["key"]) ? $options["api"]["key"] : "";
		}
		
		public function test($apikey, $domain) {
			$url      = "https://" . $domain . "/api";
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $apikey
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $apikey
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && isset($response["success"])) {
					return $response["success"];
				}
			}
			
			return false;
		}
		
		public function usersInTable($email, $table) {
			$filter_code = "C_EMAIL=" . strtolower($email);
			if(strcasecmp($table, "interlocuteurfiche") === 0) {
				$filter_code = "I_EMAIL=" . strtolower($email);
			}
			
			$url      = "https://" . $this->domain_client . "/api/" . strtolower($table) . "/?" . $filter_code;
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["result"])) {
					return $response["result"];
				} else if(!empty($response["status_code"]) && $response["status_code"] == 404) {
					return [];
				}
			}
			
			return false;
		}
		
		/**
		 * @param $c_code
		 *
		 * @return bool|array
		 */
		public function getContactfiche($c_code) {
			$url      = "https://" . $this->domain_client . "/api/contactfiche/" . $c_code;
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["result"])) {
					return $response["result"][0];
				}
			}
			
			return false;
		}
		
		/**
		 * @param $c_code
		 *
		 * @return bool|array
		 */
		public function getInterlocuteurfiche($i_code) {
			$url      = "https://" . $this->domain_client . "/api/interlocuteurfiche/" . $i_code;
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["result"])) {
					return $response["result"][0];
				}
			}
			
			return false;
		}
		
		/**
		 * @param $c_code
		 *
		 * @return bool|array
		 */
		public function getGroupementsFromContact($c_code) {
			$url      = "https://" . $this->domain_client . "/api/contactfiche/" . $c_code . "/mailinglist/";
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["result"])) {
					return $response["result"];
				} else if(!empty($response["status_code"]) && $response["status_code"] == 404) {
					return [];
				}
			}
			
			return false;
		}
		
		/**
		 * @param $i_code
		 *
		 * @return bool|array
		 */
		public function getGroupementsFromInterlocuteur($i_code) {
			$url      = "https://" . $this->domain_client . "/api/interlocuteurfiche/" . $i_code . "/mailinglist/";
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["result"])) {
					return $response["result"];
				} else if(!empty($response["status_code"]) && $response["status_code"] == 404) {
					return [];
				}
			}
			
			return false;
		}
		
		public function getDesabo($de_code = false, $ml_code = false, $c_code = false, $i_code = false, $de_email = false) {
			$url = "https://" . $this->domain_client . "/api/desabo";
			$get = [];
			if($de_code) {
				$url .= "/" . $de_code;
			} else {
				if($ml_code) {
					$get["DE_ML_CODE"] = $ml_code;
				}
				if($c_code) {
					$get["DE_CODECONTACT"] = $c_code;
				}
				if($i_code) {
					$get["DE_CODEINTERLOCUTEUR"] = $i_code;
				}
				if($de_email) {
					$get["DE_EMAIL"] = $de_email;
				}
			}
			
			$url      = add_query_arg($get, $url);
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["result"])) {
					return $response["result"];
				}
			}
			
			return false;
		}
		
		public function addDesabo($ml_code = false, $c_code = false, $i_code = false, $de_email = false) {
			$body = [];
			if($ml_code) {
				$body["DE_ML_CODE"] = $ml_code;
			}
			if($c_code) {
				$body["DE_CODECONTACT"] = $c_code;
			}
			if($i_code) {
				$body["DE_CODEINTERLOCUTEUR"] = $i_code;
			}
			if($de_email) {
				$body["DE_EMAIL"] = $de_email;
			}
			
			$url      = "https://" . $this->domain_client . "/api/desabo";
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'body'      => $body,
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["result"])) {
					return $response["result"];
				}
			}
			
			return false;
		}
		
		public function delDesabo($de_code) {
			$url      = "https://" . $this->domain_client . "/api/desabo/" . $de_code;
			//$response = wp_remote_request($url, array(
			//	'method'    => 'DELETE',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "DELETE",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["success"])) {
					return $response["success"];
				}
			}
			
			return false;
		}
		
		public function addDelContactFromGroupement($c_code, $groupement, $add) {
			$method = ($add) ? "POST" : "DELETE";
			
			$url      = "https://" . $this->domain_client . "/api/mailinglist/" . $groupement . "/contactfiche/" . $c_code;
			//$response = wp_remote_request($url, array(
			//	'method'    => $method,
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["success"])) {
					return $response["success"];
				}
			}
			
			return false;
		}
		
		public function addDelInterlocuteurFromGroupement($i_code, $groupement, $add) {
			$method = ($add) ? "POST" : "DELETE";
			
			$url      = "https://" . $this->domain_client . "/api/mailinglist/" . $groupement . "/interlocuteurfiche/" . $i_code;
			//$response = wp_remote_request($url, array(
			//	'method'    => $method,
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["success"])) {
					return $response["success"];
				}
			}
			
			return false;
		}
		
		public function getGroupements($table = false, $x_code = false, $ml_type = false) {
			$filter_type = "";
			if($ml_type) {
				$filter_type = http_build_query(["ML_TYPE" => $ml_type, "ML_ACTIF" => "T"]);
			}
			
			$url      = "https://" . $this->domain_client . "/api/mailinglist/?" . $filter_type;
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["result"])) {
					$groupements = $response["result"];
					
					$active_groupements = [];
					if($table !== false && strtoupper($table) === "INTERLOCUTEURFICHE" && $x_code !== false) {
						$active_groupements = $this->getGroupementsFromInterlocuteur($x_code);
					} else if($table !== false && strtoupper($table) === "CONTACTFICHE" && $x_code !== false) {
						$active_groupements = $this->getGroupementsFromContact($x_code);
					}
					
					foreach($groupements as $index => $groupement) {
						if(is_array($active_groupements) && in_array($groupement, $active_groupements)) {
							$groupements[ $index ]["CHECKED"] = true;
						} else {
							$groupements[ $index ]["CHECKED"] = false;
						}
					}
					
					return $groupements;
				}
			}
			
			return false;
		}
		
		public function getGroupement($ml_code, $table = null, $code = null) {
			$url      = "https://" . $this->domain_client . "/api/mailinglist/" . $ml_code;
			$url .= ($table) ? "/".$table : "";
			$url .= ($code) ? "/".$code : "";
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["result"])) {
					return $response["result"][0];
				}
			}
			
			return false;
		}
		
		public function getGroupementsList() {
			$url      = "https://" . $this->domain_client . "/api/groupement";
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["result"])) {
					$groupements = $response["result"];
					
					return $groupements;
				}
			}
			
			return [];
		}
		
		public function getTableLibelle($tables) {
			$url      = "https://" . $this->domain_client . "/api/dictionnaire";
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			$results = json_decode($response, true);
			
			$tablesLibelles = [];
			if(isset($results['success']) && $results['success']) {
				foreach($results['result'] as $index => $values) {
					if(in_array(strtolower($values['DI_CHAMP']), $tables)) {
						$tablesLibelles[ $values['DI_CHAMP'] ] = $values['DI_DISPLAYLABEL'];
					}
				}
			}
			
			return $tablesLibelles;
		}
		
		public function getChamps($table) {
			$url      = "https://" . $this->domain_client . "/api/dictionnaire/" . $table;
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			$results = json_decode($response, true);
			
			$tablesLibelles = [];
			if(isset($results['success']) && $results['success']) {
				foreach($results['result'] as $index => $values) {
					$tablesLibelles[ $values['DI_CHAMP'] ] = $values['DI_DISPLAYLABEL'];
				}
			}
			
			return $tablesLibelles;
		}
		
		public function addHistorique($data) {
			$url      = "https://" . $this->domain_client . "/api/historique";
			//$response = wp_remote_request($url, array(
			//	'method'    => 'POST',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'body'      => $data,
			//	'sslverify' => false
			//));
			
			$dataQuery = http_build_query($data);
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $dataQuery,
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["result"])) {
					return $response["result"];
				}
			}
			
			return false;
		}
		
		public function getTypesHistorique() {
			$url      = "https://" . $this->domain_client . "/api/listetypeaction";
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["result"])) {
					return $response["result"];
				}
			}
			
			return false;
		}
		
		public function getVendeurs() {
			$url      = "https://" . $this->domain_client . "/api/vendeur?V_VALIDE=";
			//$response = wp_remote_request($url, array(
			//	'method'    => 'GET',
			//	'headers'   => array(
			//		"Authorization" => "Bearer " . $this->api_key
			//	),
			//	'sslverify' => false
			//));
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["result"])) {
					return $response["result"];
				}
			}
			
			return false;
		}
		
		public function searchContactfiche($keywords) {
			$criterias = [
				"?C_NOM=" . $keywords,
				"?C_PRENOM=" . $keywords
			];
			
			$contactfiches = [];
			foreach($criterias as $criteria) {
				$url      = "https://" . $this->domain_client . "/api/contactfiche" . $criteria . "&fin=10";
				//$response = wp_remote_request($url, array(
				//	'method'    => 'GET',
				//	'headers'   => array(
				//		"Authorization" => "Bearer " . $this->api_key
				//	),
				//	'sslverify' => false
				//));
				
				$curl = curl_init();
				$curlOpts = [
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer " . $this->api_key
					)
				];
				
				if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
				
				if($response) {
					$response = json_decode($response, true);
					if(!empty($response) && !empty($response["result"])) {
						$contactfiches = array_merge($contactfiches, $response["result"]);
					}
				}
			}
			
			return array_unique($contactfiches, SORT_REGULAR);
		}
		
		public function searchInterlocuteurfiche($keywords) {
			$criterias = [
				"?I_NOM=" . $keywords,
				"?I_PRENOM=" . $keywords
			];
			
			$interlocuteurfiches = [];
			foreach($criterias as $criteria) {
				$url      = "https://" . $this->domain_client . "/api/interlocuteurfiche" . $criteria . "&fin=10";
				//$response = wp_remote_request($url, array(
				//	'method'    => 'GET',
				//	'headers'   => array(
				//		"Authorization" => "Bearer " . $this->api_key
				//	),
				//	'sslverify' => false
				//));
				
				$curl = curl_init();
				$curlOpts = [
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer " . $this->api_key
					)
				];
				
				if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
				
				if($response) {
					$response = json_decode($response, true);
					if(!empty($response) && !empty($response["result"])) {
						$interlocuteurfiches = array_merge($interlocuteurfiches, $response["result"]);
					}
				}
			}
			
			return array_unique($interlocuteurfiches, SORT_REGULAR);
		}
		
		public function searchInterlocuteurficheFromContactfiche($c_code, $keywords) {
			$criterias = [
				"?I_NOM=" . $keywords,
				"?I_PRENOM=" . $keywords
			];
			
			$interlocuteurfiches = [];
			foreach($criterias as $criteria) {
				$url      = "https://" . $this->domain_client . "/api/contactfiche/" . $c_code . "/interlocuteurfiche" . $criteria . "&fin=10";
				//$response = wp_remote_request($url, array(
				//	'method'    => 'GET',
				//	'headers'   => array(
				//		"Authorization" => "Bearer " . $this->api_key
				//	),
				//	'sslverify' => false
				//));
				
				$curl = curl_init();
				$curlOpts = [
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer " . $this->api_key
					)
				];
				
				if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
				
				if($response) {
					$response = json_decode($response, true);
					if(!empty($response) && !empty($response["result"])) {
						$interlocuteurfiches = array_merge($interlocuteurfiches, $response["result"]);
					}
				}
			}
			
			return array_unique($interlocuteurfiches, SORT_REGULAR);
		}
		
		public function changeGroupements($dataMap) {
			$url      = "https://" . $this->domain_client . "/api/tracking-abo-desabo";
			
			$dataQuery = http_build_query($dataMap);
			
			$curl = curl_init();
			$curlOpts = [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $dataQuery,
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer " . $this->api_key
				)
			];
			
			if(!INFOCOB_VERIFY_SSL) {
				$curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
				$curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
			}
			
			curl_setopt_array($curl, $curlOpts);
			$response = curl_exec($curl);
			curl_close($curl);
			
			if($response) {
				$response = json_decode($response, true);
				if(!empty($response) && !empty($response["success"])) {
					return $response["success"];
				}
			}
			
			return false;
		}
	}

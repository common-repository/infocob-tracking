<?php
	
	namespace Infocob\Tracking\Admin;
	
	use Exception;
	
	class Jwt {
		
		public static function getJwt($jwt) {
			$options = get_option('infocob_tracking_settings');
			$secret  = !empty($options["token"]["secret"]) ? $options["token"]["secret"] : "";
			
			try {
				$decoded_base64 = base64_decode($jwt);
				$decoded        = \Firebase\JWT\JWT::decode($decoded_base64, $secret, array('HS256'));
				$decoded_array  = $decoded;
				
				return $decoded_array;
			} catch(Exception $e) {
				return false;
			}
		}
		
		public static function generateJwt($datas) {
			$options         = get_option('infocob_tracking_settings');
			$secret          = !empty($options["token"]["secret"]) ? $options["token"]["secret"] : "";
			$expiration_time = !empty($options["token"]["expiration"]) ? $options["token"]["expiration"] : "";
			
			$payload = array(
				"iss"    => INFOCOB_TRACKING_HOSTNAME,
				"aud"    => INFOCOB_TRACKING_HOSTNAME,
				"iat"    => time(),
				"exp"    => time() + $expiration_time,
				"client" => $datas
			);
			
			$jwt = \Firebase\JWT\JWT::encode($payload, $secret);
			
			$jwt_base64 = base64_encode($jwt);
			
			return $jwt_base64;
		}
		
	}

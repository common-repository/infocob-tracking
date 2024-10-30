<?php
	
	
	namespace Infocob\Tracking\Admin;
	
	
	class Database {
		
		public static function infocobFormsHasForm($idForm) {
			global $wpdb;
			$table_name = 'infocob_form';
			$select     = $wpdb->get_row("SELECT * FROM $table_name WHERE idPostContactForm = $idForm", ARRAY_A);
			if(is_null($select)) {
				return false;
			}
			$tables = json_decode($select["tables"], true);
			
			return !empty($tables);
		}
	}

<?php
	
	
	namespace Infocob\Tracking\Admin;
	
	
	class Ajax {
		protected $webservice;
		
		public function __construct() {
			$this->webservice = new Webservice();
		}
	}

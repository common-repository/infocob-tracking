<?php
	
	namespace Infocob\Tracking\Admin;
	
	class TemplateDemandeAbonnement extends Template {
		protected $type;
		protected $templates_dir = [];
		
		public function __construct($template_name, $debug = false) {
			$this->type = "demande-abonnement";
			$this->setTemplateFile($template_name);
			$this->setTemplateDir();
			
			parent::__construct($debug);
		}
		
		
	}

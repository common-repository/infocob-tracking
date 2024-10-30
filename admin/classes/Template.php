<?php
	
	
	namespace Infocob\Tracking\Admin;
	
	
	abstract class Template {
		protected $debug = false;
		
		protected $file = false;
		protected $file_text = false;
		protected $twig;
		protected $templates_dir = [];
		protected $type = "";
		protected $tpl;
		
		public function __construct($debug = false) {
			$loader = new \Twig\Loader\FilesystemLoader($this->templates_dir);
			$twig   = new \Twig\Environment($loader, ['debug' => $debug]);
			
			if($debug) {
				$twig->addExtension(new \Twig\Extension\DebugExtension());
			}
			
			$this->debug = $debug;
			$this->twig = $twig;
		}
		
		protected function setTemplateFile($template_name) {
			if(file_exists(get_stylesheet_directory() . "/infocob-tracking/mails/" . $this->type . "/" . $template_name . ".twig")) {
				$this->file = $template_name . ".twig";
			} else {
				$this->file = "defaut-infocob-tracking.twig";
			}
			
			if(file_exists(get_stylesheet_directory() . "/infocob-tracking/mails/" . $this->type . "/" . $template_name . "_text.twig")) {
				$this->file_text = $template_name . "_text.twig";
			} else {
				$this->file_text = "defaut-infocob-tracking_text.twig";
			}
		}
		
		protected function setTemplateDir() {
			$templates_dir = [];
			if(file_exists(ROOT_INFOCOB_TRACKING_DIR_PATH . "admin/mails/" . $this->type . "/")) {
				$templates_dir[] = ROOT_INFOCOB_TRACKING_DIR_PATH . "admin/mails/" . $this->type . "/";
			}
			
			if(file_exists(get_stylesheet_directory() . "/infocob-tracking/mails/" . $this->type . "/")) {
				$templates_dir[] = get_stylesheet_directory() . "/infocob-tracking/mails/" . $this->type . "/";
			}
			
			$this->templates_dir = $templates_dir;
		}
		
		protected function render($vars = [], $text = false) {
			if($this->file !== false && $this->file_text !== false && $this->twig instanceof \Twig\Environment) {
				$file = ($text) ? $this->file_text : $this->file;
				
				$tpl = $this->twig->render($file, $vars);
				
				$this->tpl = $tpl;
			} else {
				$this->tpl = "TEMPLATE MAIL ERROR !";
			}
		}
		
		public function text($vars = []) {
			$this->render($vars, true);
			return $this->tpl;
		}
		
		public function HTML($vars = []) {
			$this->render($vars);
			return $this->tpl;
		}
		
	}

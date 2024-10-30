<?php
	
	
	namespace Infocob\Tracking\Admin;
	
	// don't load directly
	use Infocob\CrmForms\Admin\Form;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	
	class TemplateMail {
		protected $title = "";
		protected $subtitle = "";
		protected $color = "";
		protected $color_text_title = "";
		protected $color_link = "";
		protected $logo = "";
		protected $societe = "";
		protected $border_radius = 0;
		protected $data_form = [];
		protected $form;
		protected $tpl = null;
		protected $file;
		protected $file_text;
		
		public function __construct(string $template_file, Form $form, array $data) {
			$this->form      = $form;
			$this->data_form = $data;
			
			$this->setTemplateFile($template_file);
		}
		
		public function setTemplateFile($template_file) {
			if($template_file && file_exists(get_stylesheet_directory() . "/infocob-crm-forms/mails/" . $template_file . ".twig")) {
				$this->file = $template_file . ".twig";
			} else {
				$this->file = "defaut-infocob-crm-forms.twig";
			}
			
			if($template_file && file_exists(get_stylesheet_directory() . "/infocob-crm-forms/mails/" . $template_file . "_text.twig")) {
				$this->file_text = $template_file . "_text.twig";
			} else {
				$this->file_text = "defaut-infocob-crm-forms_text.twig";
			}
		}
		
		public function renderTemplate($text = false) {
			if($this->file !== false && $this->file_text !== false) {
				$templates_folders = [ROOT_INFOCOB_CRM_FORMS_DIR_PATH . "admin/mails/"];
				if(file_exists(get_stylesheet_directory() . "/infocob-crm-forms/mails/")) {
					$templates_folders[] = get_stylesheet_directory() . "/infocob-crm-forms/mails/";
				}
				
				$loader = new \Twig\Loader\FilesystemLoader($templates_folders);
				$twig   = new \Twig\Environment($loader);
				
				$data = $this->extractData();
				
				$file = ($text) ? $this->file_text : $this->file;
				
				$domain_name    = $_SERVER['SERVER_NAME'] ?? "";
				$permalink_rgpd = !empty(get_option('wp_page_for_privacy_policy')) ? get_permalink(get_option('wp_page_for_privacy_policy')) : "";
				
				$tpl = $twig->render($file, [
					"title"            => $this->title,
					"subtitle"         => $this->subtitle,
					"color"            => $this->color,
					"color_text_title" => $this->color_text_title,
					"color_link"       => $this->color_link,
					"logo"             => $this->logo,
					"societe"          => $this->societe,
					"domain"           => $domain_name,
					"rgpd_url"         => $permalink_rgpd,
					"border_radius"    => $this->border_radius,
					"form"             => $data
				]);
				
				$this->tpl = $tpl;
			} else {
				$this->tpl = "TEMPLATE MAIL ERROR !";
			}
		}
		
		public function text() {
			$this->renderTemplate(true);
			
			return $this->tpl;
		}
		
		public function HTML() {
			$this->renderTemplate();
			
			return $this->tpl;
		}
		
		public function extractData() {
			$data   = [];
			$champs = $this->form->getFieldsGroups();
			
			foreach($champs as $champ) {
				if($champ->getType() != "file") {
					if($champ->getType() == "groupe") {
						foreach($champ->getChamps() as $sub_champ) {
							if(isset($this->data_form[ $sub_champ->getNom() ])) {
								$data[] = [
									"type"    => $sub_champ->getType(),
									"nom"     => $sub_champ->getNom(),
									"libelle" => $sub_champ->getLibelle(),
									"valeur"  => $this->data_form[ $sub_champ->getNom() ]
								];
							}
						}
					} else if($champ->getType() == "select" && $champ->isMultiple()) {
						$options = [];
						foreach($champ->getValeurs() as $option) {
							if(in_array($option["valeur"], $this->data_form[ $champ->getNom() ])) {
								$options[] = [
									"libelle" => $option["libelle"],
									"valeur"  => $this->data_form[ $champ->getNom() ],
								];
							}
						}
						$data[] = [
							"type"    => $champ->getType(),
							"nom"     => $champ->getNom(),
							"libelle" => $champ->getLibelle(),
							"valeur"  => $options
						];
					} else {
						if(isset($this->data_form[ $champ->getNom() ])) {
							$data[] = [
								"type"    => $champ->getType(),
								"nom"     => $champ->getNom(),
								"libelle" => $champ->getLibelle(),
								"valeur"  => $this->data_form[ $champ->getNom() ]
							];
						}
					}
				}
			}
			
			return $data;
		}
		
		public function getTitle(): string {
			return $this->title;
		}
		
		public function setTitle($title): void {
			$this->title = $title;
		}
		
		public function getSubtitle(): string {
			return $this->subtitle;
		}
		
		public function setSubtitle($subtitle): void {
			if(empty($subtitle)) {
				$this->subtitle = $this->data_form["page_form"];
			} else {
				$this->subtitle = $subtitle;
			}
		}
		
		public function getColor(): string {
			return $this->color;
		}
		
		public function setColor($color): void {
			$this->color = $color;
		}
		
		public function getLogo() {
			return $this->logo;
		}
		
		public function setLogo($logo): void {
			$this->logo = $logo;
		}
		
		public function getDataForm(): array {
			return $this->data_form;
		}
		
		public function setDataForm(array $data_form): void {
			$this->data_form = $data_form;
		}
		
		public function getForm() {
			return $this->form;
		}
		
		public function setForm($form): void {
			$this->form = $form;
		}
		
		public function getColorTextTitle(): string {
			return $this->color_text_title;
		}
		
		public function setColorTextTitle(string $color_text_title): void {
			$this->color_text_title = $color_text_title;
		}
		
		public function getColorLink(): string {
			return $this->color_link;
		}
		
		public function setColorLink(string $color_link): void {
			$this->color_link = $color_link;
		}
		
		public function getSociete(): string {
			return $this->societe;
		}
		
		public function setSociete(string $societe): void {
			$this->societe = $societe;
		}
		
		public function getBorderRadius() {
			return $this->border_radius;
		}
		
		public function setBorderRadius($border_radius): void {
			$this->border_radius = $border_radius;
		}
		
	}

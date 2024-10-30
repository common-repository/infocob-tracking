<?php
	/**
	 * Plugin Name: Infocob Tracking
	 * Description: Extension de gestion d'abonnements en liaison avec Infocob CRM (plugin 'Infocob CRM Forms' requis).
	 * Version: 1.1.7
	 * Author: Infocob web
	 * Author URI: https://www.infocob-web.com/
	 * License: GPL3
	 * License URI:  https://www.gnu.org/licenses/gpl-3.0.html
	 * Text Domain: infocob-tracking
	 */
	
	namespace Infocob\Tracking;
	
	use Infocob\Tracking\Admin\InfocobTracking;
	
	include_once __DIR__ . '/vendor/autoload.php';
	require_once(ABSPATH . 'wp-includes/pluggable.php');
	
	define('ROOT_INFOCOB_TRACKING_DIR_PATH', plugin_dir_path(__FILE__));
	define('ROOT_INFOCOB_TRACKING_DIR_URL', plugin_dir_url(__FILE__));
	define('INFOCOB_TRACKING_BASENAME', plugin_basename(__FILE__));
	define("INFOCOB_TRACKING_HOSTNAME", parse_url(get_site_url(), PHP_URL_HOST)); // domain
	define("INFOCOB_VERIFY_SSL", false); // domain
	
	$infocobTracking = new InfocobTracking();
	$infocobTracking->init();

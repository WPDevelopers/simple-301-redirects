<?php
/**
 * Plugin Name:     Simple 301 Redirects
 * Plugin URI:      https://wordpress.org/plugins/simple-301-redirects/
 * Description:     Create a list of URLs that you would like to 301 redirect to another page or site. Now with wildcard support.
 * Author:          WPDeveloper
 * Author URI:      https://wpdeveloper.net/
 * Text Domain:     simple-301-redirects
 * Domain Path:     /languages
 * Version:         2.0.10
 */

/*  Copyright 2009-2021  WPDeveloper

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('ABSPATH')) {
	exit();
}

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
	require_once dirname(__FILE__) . '/vendor/autoload.php';
}


if (!class_exists("Simple301redirects")) {

	final class Simple301Redirects {

		private function __construct()
		{
			$this->define_constants();
			add_action('plugins_loaded', [$this, 'on_plugins_loaded']);

			if( ! defined('WP_CLI') || ( defined('WP_CLI') && ! WP_CLI ) ) {
				add_action('simple301redirects_loaded', [$this, 'init_plugin']);
				// add the redirect action, high priority
				add_action('init', array($this,'redirect'), 1);
			}
		}
		public static function init()
		{
			static $instance = false;

			if (!$instance) {
				$instance = new self();
			}

			return $instance;
		}

		public function on_plugins_loaded()
		{
			do_action('simple301redirects_loaded');
		}

		public function define_constants()
		{
			define('SIMPLE301REDIRECTS_VERSION', '2.0.10');
			define('SIMPLE301REDIRECTS_SETTINGS_NAME', '301_redirects');
			define('SIMPLE301REDIRECTS_PLUGIN_FILE', __FILE__);
			define('SIMPLE301REDIRECTS_PLUGIN_BASENAME', plugin_basename(__FILE__));
			define('SIMPLE301REDIRECTS_PLUGIN_SLUG', 'simple-301-redirects');
			define('SIMPLE301REDIRECTS_PLUGIN_ROOT_URI', plugins_url('/', __FILE__));
			define('SIMPLE301REDIRECTS_ROOT_DIR_PATH', plugin_dir_path(__FILE__));
			define('SIMPLE301REDIRECTS_ASSETS_DIR_PATH', SIMPLE301REDIRECTS_ROOT_DIR_PATH . 'assets/');
			define('SIMPLE301REDIRECTS_ASSETS_URI', SIMPLE301REDIRECTS_PLUGIN_ROOT_URI . 'assets/');
		}

		/**
		 * Initialize the plugin
		 *
		 * @return void
		 */
		public function init_plugin()
		{
			$this->load_textdomain();
			if (is_admin()) {
				new Simple301Redirects\Admin();
			}
			$this->load_installer();
		}

		public function load_textdomain()
		{
			load_plugin_textdomain('simple-301-redirects', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/');
		}
		public function load_installer()
		{
			$Installer = new Simple301Redirects\Installer();
			$Installer->migrate();
		}
		/**
		 * redirect function
		 * Read the list of redirects and if the current page
		 * is found in the list, send the visitor on her way
		 * @access public
		 * @return void
		 */
		public function redirect() {
			// this is what the user asked for (strip out home portion, case insensitive)

			$redirects = get_option('301_redirects');
			if (!empty($redirects)) {
				$userrequest = \Simple301Redirects\Helper::str_ireplace(get_option('home'),'',$this->get_address());
				$userrequest = ltrim($userrequest);
				$param = explode('?', $userrequest, 2);
				$userrequest = current($param);

				$wildcard = get_option('301_redirects_wildcard');
				$do_redirect = '';

				// compare user request to each 301 stored in the db
				foreach ($redirects as $storedrequest => $destination) {
					// check if we should use regex search
					if ($wildcard === 'true' && strpos($storedrequest,'*') !== false) {
						// wildcard redirect

						// don't allow people to accidentally lock themselves out of admin
						if ( strpos($userrequest, '/wp-login') !== 0 && strpos($userrequest, '/wp-admin') !== 0 ) {
							// Make sure it gets all the proper decoding and rtrim action
							$storedrequest = str_replace('*','(.*)',$storedrequest);
							$pattern = '/^' . str_replace( '/', '\/', rtrim( $storedrequest, '/' ) ) . '/';
							$destination = str_replace('*','$1',$destination);
							$output = preg_replace($pattern, $destination, $userrequest);
							if ($output !== $userrequest) {
								// pattern matched, perform redirect
								$do_redirect = $output;
							}
						}
					}elseif(urldecode(trim($userrequest, '/')) == trim($storedrequest,'/')){
						// simple comparison redirect
						$do_redirect = $destination;
					}

					// redirect. the second condition here prevents redirect loops as a result of wildcards.
					if ($do_redirect !== '' && trim($do_redirect,'/') !== trim($userrequest,'/')) {
						// check if destination needs the domain prepended
						if (strpos($do_redirect,'/') === 0){
							$do_redirect = home_url().$do_redirect;
						}
						header ('HTTP/1.1 301 Moved Permanently');
						header ('Location: ' . $do_redirect);
						exit();
					}
					else { unset($redirects); }
				}
			}
		} // end funcion redirect

		/**
		 * getAddress function
		 * utility function to get the full address of the current request
		 * credit: http://www.phpro.org/examples/Get-Full-URL.html
		 * @access public
		 * @return void
		 */
		public function get_address() {
			if( ! isset( $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'] ) ) return;

			// return the full address
			return $this->get_protocol().'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}

		public function get_protocol() {
			// Set the base protocol to http
			$protocol = 'http';
			// check for https
			if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) {
				$protocol .= "s";
			}

			return $protocol;
		} // end function get_protocol
	}
}


/**
 * Initializes the main plugin
 *
 * @return \Simple301Redirects
 */
if (!function_exists('Simple301Redirects_Start')) {
	function Simple301Redirects_Start()
	{
		return Simple301Redirects::init();
	}
}

// Plugin Start
Simple301Redirects_Start();
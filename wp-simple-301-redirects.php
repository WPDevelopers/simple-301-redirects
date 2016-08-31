<?php
/*
Plugin Name: Simple 301 Redirects
Plugin URI: http://www.scottnelle.com/simple-301-redirects-plugin-for-wordpress/
Description: Create a list of URLs that you would like to 301 redirect to another page or site. Now with wildcard support.
Version: 1.08a
Author: Scott Nellé
Author URI: http://www.scottnelle.com/
*/

/*  Copyright 2009-2016  Scott Nellé  (email : contact@scottnelle.com)

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

if ( ! class_exists( 'Simple301redirects' ) ) {
	class Simple301Redirects {
		/**
		 * Database version, for determining which storage format we should be using.
		 * @var int
		 */
		private $db_version = 2;

		/**
		 * Capability required to manage redirects.
		 * @var string
		 */
		private $capability;

		/**
		 * Option for storing plugin settings settings.
		 * @var string
		 */
		private $settings_option = 's301r_settings';

		/**
		 * Option to use for storing redirects.
		 * @var string
		 */
		private $redirects_option = 's301r_redirects';

		/**
		 * Option to use for storing redirects.
		 * @var string
		 */
		private $db_version_option = 's301r_db_version';

		/**
		 * Set up Simple 301 Redirects functionality.
		 */
		private function __construct() {
			// Check db version. Compares a class property against an autoloaded option--minimal performance impact.
			$this->maybe_upgrade_db();

			// Set capability required to manage redirects and settings.
			$this->capability = apply_filters( 's301r_capability', 'manage_options' );

			// Add the redirect action, high priority.
			add_action( 'init', array( $this, 'redirect' ), apply_filters( 's301r_priority', 1 ) );

			// Create the menu item.
			add_action( 'admin_menu', array( $this, 'create_menu' ) );

			// If submitted, process the data.
			if ( isset( $_POST['301_redirects'] ) ) {
				add_action( 'admin_init', array( $this, 'save_redirects' ) );
			}
		}

		/**
		 * Create the link to the settings page.
		 */
		function create_menu() {
			add_options_page( __( '301 Redirects', 's301r' ), __( '301 Redirects', 's301r' ), $this->capability, '301options', array( $this, 'options_page' ) );
		}

		/**
		 * Render the content of the settings page.
		 * @todo Rebuild this into a listing page and forms for making edits.
		 * @todo Enhance with ajax to keep the workflow on one page.
		 */
		function options_page() {
		?>
		<div class="wrap s301r">

			<?php
				if (isset($_POST['301_redirects'])) {
					?>
					<div id="message" class="updated"><p><?php esc_html_e( 'Settings saved.', 's301r' ); ?></p></div>
					<?php
				}
			?>

			<h2><?php esc_html_e( 'Simple 301 Redirects', 's301r' ); ?></h2>

			<form method="post" id="simple_301_redirects_form" action="options-general.php?page=301options&savedata=true">

			<?php wp_nonce_field( 'save_redirects', '_s301r_nonce' ); ?>

			<table class="widefat">
				<thead>
					<tr>
						<th colspan="2"><?php esc_html_e( 'Request', 's301r' ); ?></th>
						<th colspan="1"><?php esc_html_e( 'Destination', 's301r' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php echo $this->expand_redirects(); ?>
					<tr>
						<td style="width:35%;"><input type="text" name="301_redirects[request][]" value="" style="width:99%;" /></td>
						<td>&raquo;</td>
						<td style="width:60%;"><input type="text" name="301_redirects[destination][]" value="" style="width:99%;" /></td>
					</tr>
				</tbody>
			</table>

			<?php $wildcard_checked = (get_option('301_redirects_wildcard') === 'true' ? ' checked="checked"' : ''); ?>
			<p><input type="checkbox" name="301_redirects[wildcard]" id="wps301-wildcard"<?php echo $wildcard_checked; ?> /><label for="wps301-wildcard"> <?php esc_html_e( 'Use Wildcards?', 's301r' ); ?></label></p>

			<p class="submit"><input type="submit" name="submit_301" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 's301r' ) ?>" /></p>
			</form>
		<?php
		} // end of function options_page

		/**
		 * Utility function to return the current list of redirects as form fields.
		 * @return string <html>
		 */
		function expand_redirects() {
			$redirects = $this->redirects_option;
			$output = '';
			if ( ! empty( $redirects ) ) {
				foreach ($redirects as $request => $destination) {
					$output .= '

					<tr>
						<td><input type="text" name="301_redirects[request][]" value="'.$request.'" style="width:99%" /></td>
						<td>&raquo;</td>
						<td><input type="text" name="301_redirects[destination][]" value="'.$destination.'" style="width:99%;" /></td>
						<td><span class="wps301-delete"></span></td>
					</tr>

					';
				}
			} // end if
			return $output;
		}

		/**
		 * Handle all posted data, checking nonces and calling the appropriate method to complete the request.
		 */
		function handle_posted_data() {
			if ( ! current_user_can( $this->capability ) ) {
				wp_die( 'You do not have sufficient permissions to access this page.' );
			}
			check_admin_referer( 'save_redirects', '_s301r_save_nonce' );
		}

		/**
		 * Create a new redirect.
		 *
		 */
		function add_redirect( $request, $destination, $position = 'bottom' ) {

		}

		/**
		 * Edit an existing redirect.
		 *
		 */
		function edit_redirect( $request, $destination, $index ) {

		}

		/**
		 * Delete an existing redirect.
		 *
		 */
		function delete_redirect( $index ) {

		}

		/**
		 * Save the redirects from the options page to the database.
		 * @param mixed $data
		 */
		function save_redirects( $data ) {
			if ( ! current_user_can( $this->capability ) ) {
				wp_die( 'You do not have sufficient permissions to access this page.' );
			}
			check_admin_referer( 'save_redirects', '_s301r_nonce' );

			$data = $_POST['301_redirects'];

			$redirects = array();

			for($i = 0; $i < sizeof($data['request']); ++$i) {
				$request = trim( sanitize_text_field( $data['request'][$i] ) );
				$destination = trim( sanitize_text_field( $data['destination'][$i] ) );

				if ($request == '' && $destination == '') { continue; }
				else { $redirects[$request] = $destination; }
			}

			update_option( $this->redirects_option, $redirects);

			if (isset($data['wildcard'])) {
				update_option('301_redirects_wildcard', 'true');
			}
			else {
				delete_option('301_redirects_wildcard');
			}
		}

		/**
		 * Read the list of redirects and if the current page
		 * is found in the list, send the visitor on her way.
		 */
		function redirect() {
			// this is what the user asked for (strip out home portion, case insensitive)
			$userrequest = str_ireplace(get_option('home'),'',$this->get_address());
			$userrequest = rtrim($userrequest,'/');

			$redirects = get_option('301_redirects');
			if (!empty($redirects)) {

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
					}
					elseif(urldecode($userrequest) == rtrim($storedrequest,'/')) {
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
		 * Utility function to get the full address of the current request
		 * credit: http://www.phpro.org/examples/Get-Full-URL.html
		 * @todo Test to see if this is causing problems with query strings.
		 */
		function get_address() {
			// return the full address
			return $this->get_protocol().'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		} // end function get_address

		function get_protocol() {
			// Set the base protocol to http
			$protocol = 'http';
			// check for https
			if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) {
    			$protocol .= "s";
			}

			return $protocol;
		} // end function get_protocol

		/**
		 * Maybe updgrade the database.
		 */
		function maybe_upgrade_db() {
			$current_db_version = ( get_option( 's301r_db_version' ) ) ? intval( get_option( 's301r_db_version' ) ) : 1;
			if ( $this->db_version === $current_db_version ) {
				// Up-to-date.
				return;
			}

			// Upgrade through each version of the database, in case users jump multiple versions.
			while ($current_db_version < $latest_db_version) {
				if ( $current_db_version === 1 ) {
					$this->upgrade_db_v2();
				}
				// elseif ( $db_version === 2 ) $this->upgrade_db_v3(); // a future version would look like this
				$current_db_version++;
			} // end while
		}

		/**
		 * Upgrade to the db v2 storage format.
		 * Specifically not deleting the old options to allow for easy roll-back. I'm a coward.
		 */
		function upgrade_db_v2() {
			// New settings format
			$wildcard = ( get_option( '301_redirects_wildcard' ) === 'true' ) ? 'true' : 'false';
			$v2_settings = array(
				'wildcard' => $wildcard,
			);
			update_option( $this->settings_option, $v2_settings );

			// New redirect format, numerically indexed.
			$counter = 0;
			$v1_redirects = get_option( '301_redirects' );
			$v2_redirects  = array();
			if ( ! empty( $v1_redirects ) ) {
				foreach ($v1_redirects as $request => $destination) {
					$v2_redirects[ $counter ] = array(
						'request' => $request,
						'destination' => $destination,
					);
					$counter++;
				}
			}
			update_option( $this->redirects_option, $v2_redirects );

			// New db version.
			update_option( $this->db_version_option, 2 );
		}

	} // end class Simple301Redirects

} // end check for existance of class

// Instantiate
$s301r_plugin = new Simple301Redirects();

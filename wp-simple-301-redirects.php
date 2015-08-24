<?php
/*
Plugin Name: Simple 301 Redirects
Plugin URI: http://www.scottnelle.com/simple-301-redirects-plugin-for-wordpress/
Description: Create a list of URLs that you would like to 301 redirect to another page or site. Now with wildcard support.
Version: 1.07a
Author: Scott Nellé
Author URI: http://www.scottnelle.com/
License: GPLv3
*/

/*  Copyright 2009-2014  Scott Nellé  (email : contact@scottnelle.com)

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

//@todo: finish javascript edit functionality
//@todo: only load js on the appropriate page - http://codex.wordpress.org/Function_Reference/wp_enqueue_script#Link_Scripts_Only_on_a_Plugin_Administration_Screen
//@todo: notify the bulk upload author that you'll be updating the storage format. maybe even patch his plugin for him. https://wordpress.org/plugins/simple-301-redirects-addon-bulk-uploader/

if (!class_exists("Simple301redirects")) {

	class Simple301Redirects {

		function initialize_admin() {
			$this->maybe_upgrade_db(); // upgrade the storage format if needed

			// load necessary js
			wp_register_script( 's301r-script', plugins_url( '/js/simple-301-redirects.js', __FILE__ ), array('jquery') );
			add_action('admin_enqueue_scripts', array($this,'write_scripts'));

			// if submitted, process the data
			if (isset($_POST['301_redirects'])) { $this->save_redirects(); }
		}

		function write_scripts() {
			wp_enqueue_script( 's301r-script' );

			// make ajax_url available to the script
			wp_localize_script( 's301r-script', 's301r_ajax', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'delete_nonce' => wp_create_nonce( 'delete-redirect-nonce' ),
				)
			);
		}

		/**
		 * create_menu function
		 * generate the link to the options page under settings
		 * @access public
		 * @return void
		 */
		function create_menu() {
		  add_options_page('301 Redirects', '301 Redirects', 'manage_options', '301options', array($this,'options_page'));
		}

		/**
		 * options_page function
		 * generate the options page in the wordpress admin
		 * @access public
		 * @return void
		 */
		function options_page() {
		?>
		<div class="wrap simple_301_redirects">

		<h2>Simple 301 Redirects</h2>

			<?php
				if (isset($_POST['301_redirects'])) {
					echo '<div id="message" class="updated"><p>Settings saved</p></div>';
				}
			?>

			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="37PPAVKMDMHQW">
				<p>
					Love this plugin? Feeling generous?
					<input style="vertical-align: middle;" type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				</p>
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>

			<form method="post" id="simple_301_redirects_form" action="options-general.php?page=301options" enctype="multipart/form-data">

				<?php wp_nonce_field( 'save_redirects', '_s301r_nonce' ); ?>

				<table class="widefat">
					<thead>
						<tr>
							<th colspan="2">Request <small>example:&nbsp;/about.htm</small></th>
							<th>Destination <small>example:&nbsp;<?php echo get_option('home'); ?>/about/</small></th>
							<th class="s301r-delete-head">Delete</th>
						</tr>
					</thead>
					<tbody>
						<?php echo $this->expand_redirects(); ?>
						<tr>
							<td style="width:35%;"><input type="text" name="301_redirects[request]" value="" style="width:99%;" /></td>
							<td style="width:2%;">&raquo;</td>
							<td><input type="text" name="301_redirects[destination]" value="" style="width:99%;" /></td>
							<td style="width:4%;">&nbsp;</td>
						</tr>
					</tbody>
				</table>

				<?php
				$settings = get_option( 's301r_settings' );
				$wildcard_checked = ($settings['wildcard'] === 'true') ? ' checked="checked"' : ''; ?>
				<p><input type="checkbox" name="301_redirects[wildcard]" id="wps301-wildcard"<?php echo $wildcard_checked; ?> /><label for="wps301-wildcard"> Use Wildcards?</label></p>

				<p class="submit"><input type="submit" name="submit_s301r" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>

				<hr />

				<p><label for="sitemap"><strong>Input from Sitemap</strong></label></p>
				<p><input type="file" id="sitemap" name="sitemap" /></p>
				<p class="submit"><input type="submit" name="submit_s301r" class="button-primary" value="<?php _e('Upload') ?>" /></p>

			</form>

			<hr />
			<div class="documentation">
				<h2>Documentation</h2>
				<h3>Basic Redirects</h3>
				<p>Basic redirects work similar to the format that Apache uses: the request should be relative to your WordPress root. The destination can be either a full URL to any page on the web, or relative to your WordPress root.</p>
				<h4>Example</h4>
				<ul>
					<li><strong>Request:</strong> /old-page/</li>
					<li><strong>Destination:</strong> /new-page/</li>
				</ul>

				<h3>Wildcard Redirects</h3>
				<p>Wildcard redirects can match more than one URL. They use an asterisk (*) to represent any characters. This is a powerful way to redirect an entire directory of pages with one line.</p>
				<h4>Example</h4>
				<ul>
					<li><strong>Request:</strong> /old-folder/*</li>
					<li><strong>Destination:</strong> /redirect-everything-here/</li>
				</ul>

				<p>You can also use the asterisk in the destination to replace whatever it matched in the request if you like. Something like this:</p>
				<h4>Example</h4>
				<ul>
					<li><strong>Request:</strong> /old-folder/*</li>
					<li><strong>Destination:</strong> /some/other/folder/*</li>
				</ul>
				<p>Or:</p>
				<ul>
					<li><strong>Request:</strong> /old-folder/*/content/</li>
					<li><strong>Destination:</strong> /some/other/folder/*</li>
				</ul>
			</div>
		</div>
		<?php
		} // end of function options_page

		/**
		 * expand_redirects function
		 * utility function to return the current list of redirects as form fields
		 * @access public
		 * @return string <html>
		 */
		function expand_redirects() {
			$redirects = get_option('s301r_redirects');
			$output = '';
			$counter = 0;
			if (!empty($redirects)) {
				foreach ($redirects as $index => $redirect) {
					$counter++;
					$row_class = ($counter % 2 === 0) ? 'row_static' : 'row_static alternate';
					$output .= '

					<tr id="s301r_row_'.$index.'" class="'.$row_class.'">
						<td class="s301r_request">'.$redirect['request'].'</td>
						<td>&raquo;</td>
						<td class="s301r_destination"><input name="301_redirects[update_destintation]['.$index.']" value="'.$redirect['destination'].'" style="width:80%" /></td>
						<td class="s301r-delete"><input type="checkbox" name="301_redirects[delete][]" value="'.$index.'"></td>
					</tr>

					';
				}
			} // end if
			return $output;
		}

		/**
		 * save_redirects function
		 * save the redirects from the options page to the database
		 * @access public
		 * @param mixed $data
		 * @return void
		 */
		function save_redirects() {
			if ( !current_user_can('manage_options') )  { wp_die( 'You do not have sufficient permissions to access this page.' ); }
			check_admin_referer( 'save_redirects', '_s301r_nonce' );

			// get existing redirects
			$redirects = get_option( 's301r_redirects' );
			if ($redirects == '') { $redirects = array(); }

			if ( $_FILES['sitemap']['size'] ) {
				$urls = $this->process_sitemap($_FILES['sitemap']);
				foreach ($urls as $url) {
					$redirects[] = $this->create_redirect($url, '');
				}
			}

			$data = $_POST['301_redirects'];

			if ( isset($data['update_destintation']) ) {
				foreach ( $data['update_destintation'] as $index => $destination ) {
					$redirects[$index]['destination'] = trim($destination);
				}
			}

			// delete checked redirect
			if (isset($data['delete']) && is_array($data['delete'])) {
				foreach ($data['delete'] as $index) {
					unset($redirects[$index]);
				}
			}

			// save new redirect
			if ( trim( $data['request'] ) != '' ) {
				$redirects[] = $this->create_redirect($data['request'], $data['destination']);
			}

			update_option('s301r_redirects', $redirects);

			if (isset($data['wildcard'])) {
				$settings['wildcard'] = 'true';
			}
			else {
				$settings['wildcard'] = 'false';
			}

			update_option('s301r_settings', $settings);
		}

		/**
		* process_sitemap
		* Read sitemap from a user-uploaded sitemap.xml file and
		* return the list of redirects processed from the file.
		* @access private
		* @param filename
		* @return array redirect list
		*/
		private function process_sitemap($file) {
			if ( $file['type'] !== 'text/xml') {
				//create flash for wrong file type		//!!TO DO!!!!!
				return;
			}
			try {
				$xml = simplexml_load_file($file['tmp_name']);
			} catch (Exception $e) {
				//create flash for no XML parser installed or not valid XML
				return;
			}
			$urls = array();
			foreach ($xml->url as $url) {
				if ( !empty($url) ) $urls[] = (string) $url->loc;
			}
			return $urls;
		}

		private function create_redirect($request, $destination) {
			return array(
				'request' => trim($request),
				'destination' => trim($destination)
			);
		}

		/**
		 * redirect function
		 * Read the list of redirects and if the current page
		 * is found in the list, send the visitor on her way
		 * @access public
		 * @return void
		 */
		function redirect() {
			// this is what the user asked for (strip out home portion, case insensitive)
			$userrequest = str_ireplace(get_option('home'),'',$this->get_address());
			$userrequest = rtrim($userrequest,'/');

			$this->maybe_upgrade_db(); // upgrade the storage format if needed @todo: benchmark this, tune for speed

			$redirects = get_option('s301r_redirects');
			if (!empty($redirects)) {
				$settings = get_option('s301r_settings');
				$do_redirect = '';

				// compare user request to each 301 stored in the db
				foreach ($redirects as $key => $redirect) {
					// check if we should use regex search
					if ($settings['wildcard'] === 'true' && strpos($redirect['request'],'*') !== false) {
						// wildcard redirect

						// don't allow people to accidentally lock themselves out of admin
						if ( strpos($userrequest, '/wp-login') !== 0 && strpos($userrequest, '/wp-admin') !== 0 ) {
							// Make sure it gets all the proper decoding and rtrim action
							$redirect['request'] = str_replace('*','(.*)',$redirect['request']);
							$pattern = '/^' . str_replace( '/', '\/', rtrim( $redirect['request'], '/' ) ) . '/';
							$redirect['destination'] = str_replace('*','$1',$redirect['destination']);
							$output = preg_replace($pattern, $redirect['destination'], $userrequest);
							if ($output !== $userrequest) {
								// pattern matched, perform redirect
								$do_redirect = $output;
							}
						}
					}
					elseif(urldecode($userrequest) == rtrim($redirect['request'],'/')) {
						// simple comparison redirect
						$do_redirect = $redirect['destination'];
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

		function ajax_delete() {
			if ( ! wp_verify_nonce( $_POST['delete_nonce'], 'delete-redirect-nonce' ) || !current_user_can( 'manage_options' )) {
				echo 'failure'; exit;
			}

			$row = $_POST['row_id'];

			// data check
			if( preg_match('/^s301r_row_[0-9]+$/', $row) === 0 ) { echo 'failure'; exit; } // someone is messing with the dom

			$index = intval( str_replace('s301r_row_', '', $row) );
			$redirects = get_option( 's301r_redirects' );

			if (is_array( $redirects ) && isset( $redirects[$index] )) { // delete the redirect
				unset($redirects[$index]);
				update_option( 's301r_redirects', $redirects );
				echo 'success';
				exit;
			}
			else { echo 'failure'; exit; } // something went wrong
		}

		/**
		 * getAddress function
		 * utility function to get the full address of the current request
		 * credit: http://www.phpro.org/examples/Get-Full-URL.html
		 * @access public
		 * @return void
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

		function maybe_upgrade_db() {
			$latest_db_version = 2;
			$db_version = ( get_option( 's301r_db_version' ) ) ? intval( get_option( 's301r_db_version' ) ) : 1;
			if ( $latest_db_version === $db_version ) return; // return early, don't slow down the admin

			while ($db_version < $latest_db_version) { // upgrade through each version of the database, in case users jump multiple versions
				if ( $db_version === 1 ) $this->upgrade_db_v2();
				// elseif ( $db_version === 2 ) $this->upgrade_db_v3(); // a future version would look like this

				$db_version++;
			} // end while
		} // end function maybe_upgrade_db

		function upgrade_db_v2() {
			// new settings format
			$wildcard = ( get_option('301_redirects_wildcard') === 'true' ) ? 'true' : 'false';
			$v2_settings = array(
				'wildcard' => $wildcard,
			);
			update_option( 's301r_settings', $v2_settings );
			delete_option( '301_redirects_wildcard' );

			// new redirect format
			$counter = 0;
			$v1_redirects = get_option('301_redirects');
			$v2_redirects  = array();

			if (!empty($v1_redirects)) {
				update_option( 's301r_archive_data', $v1_redirects ); // save a backup in case something goes wrong during upgrade

				foreach ($v1_redirects as $request => $destination) {
					$v2_redirects[$counter] = array(
						'request' => $request,
						'destination' => $destination,
					);
					$counter++;
				}
			}
			update_option( 's301r_redirects', $v2_redirects );
			delete_option( '301_redirects' );

			// new db version
			update_option( 's301r_db_version', 2 );
		} // end function upgrade_db_v2

	} // end class Simple301Redirects

} // end check for existance of class

// instantiate
$redirect_plugin = new Simple301Redirects();

if (isset($redirect_plugin)) {
	add_action('init', array($redirect_plugin,'redirect'), 1); // add the redirect action, high priority

	// set up admin
	add_action('admin_init', array($redirect_plugin,'initialize_admin'));
	add_action('admin_menu', array($redirect_plugin,'create_menu'));

	// ajax
	add_action( 'wp_ajax_s301r_delete_redirect', array($redirect_plugin,'ajax_delete'));
}

// this is here for php4 compatibility
if(!function_exists('str_ireplace')){
  function str_ireplace($search,$replace,$subject){
    $token = chr(1);
    $haystack = strtolower($subject);
    $needle = strtolower($search);
    while (($pos=strpos($haystack,$needle))!==FALSE){
      $subject = substr_replace($subject,$token,$pos,strlen($search));
      $haystack = substr_replace($haystack,$token,$pos,strlen($search));
    }
    $subject = str_replace($token,$replace,$subject);
    return $subject;
  }
}

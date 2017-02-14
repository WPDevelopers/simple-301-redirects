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
		 * The URL for the plugin's admin page.
		 * @var string
		 */
		private $settings_url;

		/**
		 * Set up Simple 301 Redirects functionality.
		 */
		public function __construct() {
			// Check db version. Compares a class property against an autoloaded option--minimal performance impact.
			$this->maybe_upgrade_db();

			$this->settings_url = admin_url( 'options-general.php?page=301options' );

			// Set capability required to manage redirects and settings.
			$this->capability = apply_filters( 's301r_capability', 'manage_options' );

			// Add the redirect action, high priority.
			add_action( 'init', array( $this, 'redirect' ), apply_filters( 's301r_priority', 1 ) );

			// Create the menu item.
			add_action( 'admin_menu', array( $this, 'create_menu' ) );
		}

		/**
		 * Create the link to the settings page.
		 */
		function create_menu() {
			add_options_page( __( '301 Redirects', 's301r' ), __( '301 Redirects', 's301r' ), $this->capability, '301options', array( $this, 'options_page' ) );
		}

		/**
		 * Render the content of the settings page.
		 */
		function options_page() {
			?>
			<div class="wrap s301r">
				<?php
				if ( ! empty( $_POST ) ) {
					$result = $this->handle_posted_data();
					if ( ! empty( $result['status'] ) && ! empty( $result['message'] ) ) {
						$this->admin_notice( $result['status'], $result['message'] );
					}
				}
				?>
				<h2><?php esc_html_e( 'Simple 301 Redirects', 's301r' ); ?></h2>

				<?php if ( isset( $_GET['s301r_action'] ) && 'delete' === sanitize_text_field( $_GET['s301r_action'] ) && isset( $_GET['index'] ) && isset( $_GET['hash'] ) ) : ?>
					<?php
					$index = intval( $_GET['index'] );
					$hash = sanitize_text_field( $_GET['hash'] );
					$redirect = $this->get_redirect_by_index( $index );
					?>
					<?php if ( ! $this->check_hash( $index, $hash ) ) : ?>
						<?php $this->admin_notice( 'error', __( 'There was a mismatch between the values in the database and the redirect you tried to delete.', 's301r' ) ); ?>
					<?php else : ?>
						<form method="post" id="simple_301_redirects_form" action="<?php echo esc_url( $this->settings_url . '&s301r_action=delete' ); ?>">
							<p><?php esc_html_e( 'Are you sure you want to delete this redirect?', 's301r' ); ?></p>
							<p><strong><?php esc_html_e( 'Request:', 's301r' ); ?></strong> <?php echo esc_html( $redirect['request'] ); ?></p>
							<p><strong><?php esc_html_e( 'Destination:', 's301r' ); ?></strong> <?php echo esc_html( $redirect['destination'] ); ?></p>
							<p><strong><?php esc_html_e( 'Wildcard?', 's301r' ); ?></strong> <?php echo empty( $redirect['wildcard'] ) ? esc_html__( 'No', 's301r' ) : esc_html__( 'Yes', 's301r' ); ?></p>

							<?php wp_nonce_field( 'delete_redirect', '_s301r_nonce' ); ?>
							<input type="hidden" name="index" value="<?php echo esc_attr( $index ); ?>" />
							<input type="hidden" name="hash" value="<?php echo esc_attr( $hash ); ?>" />
							<div><input type="submit" name="delete_301" class="button-primary" value="<?php esc_attr_e( 'Delete Redirect', 's301r' ) ?>" /></div>
						</form>
					<?php endif; ?>
				<?php elseif ( isset( $_GET['s301r_action'] ) && 'edit' === sanitize_text_field( $_GET['s301r_action'] ) && isset( $_GET['index'] ) && isset( $_GET['hash'] ) ) : ?>
					<?php
					$index = intval( $_GET['index'] );
					$hash = sanitize_text_field( $_GET['hash'] );
					$redirect = $this->get_redirect_by_index( $index );
					?>
					<?php if ( ! $this->check_hash( $index, $hash ) ) : ?>
						<?php $this->admin_notice( 'error', __( 'There was a mismatch between the values in the database and the redirect you tried to edit.', 's301r' ) ); ?>
					<?php else : ?>
						<form method="post" id="simple_301_redirects_form" action="<?php echo esc_url( $this->settings_url . '&s301r_action=edit' ); ?>">
							<h3><?php esc_html_e( 'Edit a Redirect', 's301r' ); ?></h3>
							<?php wp_nonce_field( 'edit_redirect', '_s301r_nonce' ); ?>
							<input type="hidden" name="index" value="<?php echo esc_attr( $index ); ?>" />
							<input type="hidden" name="hash" value="<?php echo esc_attr( $hash ); ?>" />
							<p>
								<?php esc_html_e( 'Request', 'simple-301-redirects' ); ?> <input type="text" name="request" value="<?php echo esc_attr( $redirect['request'] ); ?>" />
								<?php esc_html_e( 'Destination', 'simple-301-redirects' ); ?> <input type="text" name="destination" value="<?php echo esc_attr( $redirect['destination'] ); ?>" />
								<div class="redirect-options">
									<label><input type="checkbox" name="wildcard" value="true" <?php echo empty( $redirect['wildcard'] ) ? '' : 'checked="checked"'; ?> /> <?php esc_html_e( 'Wildcard redirect', 'simple-301-redirects' ); ?></label>&nbsp;
								</div>
								<div><input type="submit" name="submit_301" class="button-primary" value="<?php esc_attr_e( 'Edit Redirect', 's301r' ) ?>" /></div>
							</p>
						</form>
					<?php endif; ?>
				<?php else : ?>
					<form method="post" id="simple_301_redirects_form" action="<?php echo esc_url( $this->settings_url . '&s301r_action=add' ); ?>">
						<h3><?php esc_html_e( 'Add a New Redirect', 's301r' ); ?></h3>
						<?php wp_nonce_field( 'add_redirect', '_s301r_nonce' ); ?>
						<p>
							<?php esc_html_e( 'Request', 'simple-301-redirects' ); ?> <input type="text" name="request" />
							<?php esc_html_e( 'Destination', 'simple-301-redirects' ); ?> <input type="text" name="destination" />
							<div class="redirect-options">
								<label><input type="checkbox" name="wildcard" value="true" /> <?php esc_html_e( 'Wildcard redirect', 'simple-301-redirects' ); ?></label>&nbsp;
								<label><input type="radio" name="position" value="top" /> <?php esc_html_e( 'Insert before existing redirects', 'simple-301-redirects' ); ?></label>&nbsp;
								<label><input type="radio" name="position" value="bottom" checked /><?php esc_html_e( 'Insert after existing redirects', 'simple-301-redirects' ); ?></label>
							</div>
							<div><input type="submit" name="submit_301" class="button-primary" value="<?php esc_attr_e( 'Add Redirect', 's301r' ) ?>" /></div>
						</p>
					</form>

					<table class="wp-list-table widefat striped">
						<thead>
							<tr>
								<th style="width: 40%;"><?php esc_html_e( 'Request', 's301r' ); ?></th>
								<th style="width: 50%;"><?php esc_html_e( 'Destination', 's301r' ); ?></th>
								<th><?php esc_html_e( 'Wildcard', 's301r' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php $this->list_redirects(); ?>
						</tbody>
					</table>
				<?php endif; ?>
			</div>
			<?php
		}

		/**
		 * Utility function to return the current list of redirects as admin table rows.
		 */
		function list_redirects() {
			$redirects = get_option( $this->redirects_option );
			if ( ! empty( $redirects ) && is_array( $redirects ) ) {
				foreach ($redirects as $index => $data) {
					if ( ! empty( $data['request'] ) && ! empty( $data['destination'] ) ) {
						?>
						<tr>
							<td>
								<a href="<?php echo esc_url( $this->settings_url . '&s301r_action=edit&index=' . $index .'&hash=' . $this->generate_hash( $index ) ); ?>" aria-label="<?php esc_attr_e( 'Edit Redirect', 's301r' ); ?>"><?php echo esc_html( $data['request'] ); ?></a>
								<div class="row-actions">
									<span class="edit"><a href="<?php echo esc_url( $this->settings_url . '&s301r_action=edit&index=' . $index .'&hash=' . $this->generate_hash( $index ) ); ?>"><?php esc_html_e( 'Edit', 's301r' ); ?></a> |</span>
									<span class="trash"><a href="<?php echo esc_url( $this->settings_url . '&s301r_action=delete&index=' . $index .'&hash=' . $this->generate_hash( $index ) ); ?>"><?php esc_html_e( 'Delete', 's301r' ); ?></a></span>
								</div>
							</td>
							<td>
								<?php echo esc_html( $data['destination'] ); ?>
							</td>
							<td>
								<?php if ( ! empty( $data['wildcard'] ) && 'true' === $data['wildcard'] ) : ?>
									&#10003;<!-- checkmark -->
								<?php endif; ?>
							</td>
						</tr>
						<?php
					}
				}
			}
		}

		function admin_notice( $type, $message ) {
			?>
			<div class="<?php echo esc_attr( 'notice is-dismissible notice-' . $type ); ?>"><p><?php echo esc_html( $message ); ?></p></div>
			<?php
		}

		/**
		 * Handle all posted data.
		 * Check nonces and call the appropriate method to complete the request.
		 */
		function handle_posted_data() {
			if ( ! current_user_can( $this->capability ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.', 's301r' ) );
			}

			if ( empty( $_POST ) ) {
				wp_die( __( 'Empty POST data', 's301r' ) );
			}

			if ( isset( $_GET['s301r_action'] ) && 'add' === sanitize_text_field( $_GET['s301r_action'] ) ) {
				// Save new redirect.
				check_admin_referer( 'add_redirect', '_s301r_nonce' );

				// Get posted values.
				if ( ! empty( $_POST['request'] ) ) {
					$request = sanitize_text_field( $_POST['request'] );
				}
				if ( ! empty( $_POST['destination'] ) ) {
					$destination = sanitize_text_field( $_POST['destination'] );
				}
				if ( ! empty( $_POST['wildcard'] ) && 'true' === sanitize_text_field( $_POST['wildcard'] ) ) {
					$wildcard = true;
				} else {
					$wildcard = false;
				}
				if ( ! empty( $_POST['position'] ) && 'top' === sanitize_text_field( $_POST['position'] ) ) {
					$position = 'top';
				} else {
					$position = 'bottom';
				}

				// Add the new redirect and get a response.
				return $this->add_redirect( $request, $destination, $wildcard, $position );
			} elseif ( isset( $_GET['s301r_action'] ) && 'edit' === sanitize_text_field( $_GET['s301r_action'] ) ) {
				// Edit existing redirect.
				check_admin_referer( 'edit_redirect', '_s301r_nonce' );

				// Get posted values.
				if ( isset( $_POST['index'] ) && isset( $_POST['hash'] ) ) {
					$index = absint( $_POST['index'] );
					$hash = sanitize_text_field( $_POST['hash'] );
				}

				// Get posted values.
				if ( ! empty( $_POST['request'] ) ) {
					$request = sanitize_text_field( $_POST['request'] );
				}
				if ( ! empty( $_POST['destination'] ) ) {
					$destination = sanitize_text_field( $_POST['destination'] );
				}
				if ( ! empty( $_POST['wildcard'] ) && 'true' === sanitize_text_field( $_POST['wildcard'] ) ) {
					$wildcard = true;
				} else {
					$wildcard = false;
				}
				return $this->edit_redirect( $index, $hash, $request, $destination, $wildcard );
			} elseif ( isset( $_GET['s301r_action'] ) && 'delete' === sanitize_text_field( $_GET['s301r_action'] ) ) {
				// Delete existing redirect.
				check_admin_referer( 'delete_redirect', '_s301r_nonce' );

				// Get posted values.
				if ( isset( $_POST['index'] ) && isset( $_POST['hash'] ) ) {
					$index = absint( $_POST['index'] );
					$hash = sanitize_text_field( $_POST['hash'] );
				}
				return $this->delete_redirect( $index, $hash );
			}
		}

		/**
		 * Create a new redirect.
		 *
		 * @param string URL path to match in order to trigger a redirect.
		 * @param string URL or URL path to redirect to upon a successful match.
		 * @param bool Is this a wildcard redirect?
		 * @param string Where should this redirect be positioned in the list? Values are 'bottom' or 'top'.
		 * @return array Result array with status and message.
		 */
		function add_redirect( $request, $destination, $wildcard = false, $position = 'bottom' ) {
			// Ensure we have appropriate values.
			if ( empty( $request ) || empty( $destination ) ) {
				return array( 'status' => 'error', 'message' => __( 'Redirects require a request and a destination.', 's301r' ) );
			} elseif ( ! in_array( $position, array( 'bottom', 'top' ) ) ) {
				return array( 'status' => 'error', 'message' => __( 'Invalid setting for the postition option.', 's301r' ) );
			}

			// Process the values.
			$data = array(
				'request' => trim( $request ),
				'destination' => trim( $destination ),
			);
			if ( true === $wildcard ) {
				$data['wildcard'] = 'true';
			}

			$redirects = (array) get_option( $this->redirects_option );

			if ( 'top' === $position ) {
				array_unshift( $redirects, $data );
			} else {
				$redirects[] = $data;
			}
			update_option( $this->redirects_option, $redirects );

			return array( 'status' => 'success', 'message' => __( 'Your new redirect has been saved.', 's301r' ) );
		}

		/**
		 * Edit an existing redirect.
		 *
		 * @param int Index of a specific redirect.
		 * @param string Hash representing a single redirect.
		 * @param string URL path to match in order to trigger a redirect.
		 * @param string URL or URL path to redirect to upon a successful match.
		 * @return array Result array with status and message.
		 * @todo Validate data before save.
		 */
		function edit_redirect( $index, $hash, $request, $destination, $wildcard = false ) {
			if ( ! $this->check_hash( $index, $hash ) ) {
				return array( 'status' => 'error', 'message' => __( 'There was a mismatch between the values in the database and the redirect you tried to edit. For safety, no redirects have been edited.', 's301r' ) );
			}

			// Process the values.
			$data = array(
				'request' => trim( $request ),
				'destination' => trim( $destination ),
			);
			if ( true === $wildcard ) {
				$data['wildcard'] = 'true';
			}

			$redirects = (array) get_option( $this->redirects_option );
			$redirects[ $index ] = $data;
			update_option( $this->redirects_option, $redirects );

			return array( 'status' => 'success', 'message' => __( 'The redirect has been updated.', 's301r' ) );
		}

		/**
		 * Delete an existing redirect.
		 *
		 * @param int Index of a specific redirect.
		 * @param string Hash representing a single redirect.
		 * @return array Result array with status and message.
		 */
		function delete_redirect( $index, $hash ) {
			if ( ! $this->check_hash( $index, $hash ) ) {
				return array( 'status' => 'error', 'message' => __( 'There was a mismatch between the values in the database and the redirect you tried to delete. For safety, no redirects have been deleted.', 's301r' ) );
			}

			$redirects = (array) get_option( $this->redirects_option );
			if ( isset( $redirects[ $index ] ) ) {
				unset( $redirects[ $index ] );
				update_option( $this->redirects_option, $redirects );

				return array( 'status' => 'success', 'message' => __( 'The redirect was deleted.', 's301r' ) );
			} else {
				return array( 'status' => 'error', 'message' => __( 'That redirect seems to be missing. It may have already been deleted. No changes have been made to the database.', 's301r' ) );
			}
		}

		/**
		 * Generate a hash based on a redirect's request and destination.
		 *
		 * @param int Index of a specific redirect.
		 * @return string Hash representing a single redirect or empty string.
		 */
		function generate_hash( $index ) {
			$redirect = $this->get_redirect_by_index( $index );
			return $redirect ? md5( serialize( $redirect ) ) : '';
		}

		/**
		 * Make check an index/hash pair to ensure that operations on an existing redirect are safe.
		 *
		 * @param int Index of a specific redirect.
		 * @param string Hash representing a single redirect.
		 * @return bool
		 */
		function check_hash( $index, $hash ) {
			return ( $this->generate_hash( $index ) === $hash );
		}

		/**
		 * Get a redirect from the list by its index.
		 *
		 * @param int Index of a specific redirect.
		 * @return array|bool Request data or false.
		 */
		function get_redirect_by_index( $index ) {
			$redirects = get_option( $this->redirects_option );
			if (
				is_array( $redirects )
				&& isset( $redirects[ $index ] )
				&& is_array( $redirects[ $index ] )
			) {
				return $redirects[ $index ];
			}
			return false;
		}

		/**
		 * Read the list of redirects and if the current page
		 * is found in the list, send the visitor on her way.
		 * @todo Update this to work with the new storage format.
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
		}

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
		}

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
			while ( $current_db_version < $this->db_version ) {
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
					// Maybe add wildcard setting for this redirect.
					if ( 'true' === $wildcard && false !== strpos( $request, '*' ) ) {
						$v2_redirects[ $counter ]['wildcard'] === 'true';
					}
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

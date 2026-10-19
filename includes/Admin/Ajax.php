<?php
namespace Simple301Redirects\Admin;

class Ajax {
    public function __construct()
    {
        add_action('wp_ajax_simple301redirects/admin/wildcard', [$this, 'wildcard']);
        add_action('wp_ajax_simple301redirects/admin/get_wildcard', [$this, 'get_wildcard']);
        add_action('wp_ajax_simple301redirects/admin/install_plugin', [$this, 'install_plugin']);
        add_action('wp_ajax_simple301redirects/admin/activate_plugin', [$this, 'activate_plugin']);
        add_action('wp_ajax_simple301redirects/admin/hide_notice', [$this, 'hide_notice']);
        add_action('wp_ajax_simple301redirects/admin/fetch_all_links', [$this, 'fetch_all_links']);
        add_action('wp_ajax_simple301redirects/admin/create_new_link', [$this, 'create_new_link']);
        add_action('wp_ajax_simple301redirects/admin/update_link', [$this, 'update_link']);
        add_action('wp_ajax_simple301redirects/admin/delete_link', [$this, 'delete_link']);
        add_action('wp_ajax_simple301redirects__client_consent', [$this, 'client_consent']);
        add_action('wp_ajax_simple301redirects__complete_setup', [$this, 'complete_setup']);
    }
    public function get_wildcard()
    {
        check_ajax_referer('simple301redirects', 'security');
        if( ! current_user_can( 'manage_options' ) ) wp_die();
		wp_send_json_success(get_option('301_redirects_wildcard'));
		wp_die();
    }
    public function wildcard() 
    {
        check_ajax_referer('simple301redirects', 'security');
        if( ! current_user_can( 'manage_options' ) ) wp_die();
        update_option('301_redirects_wildcard', sanitize_text_field($_POST['toggle']));
		wp_send_json_success($_POST['toggle']);
		wp_die();
    }
    public function install_plugin()
    {
        check_ajax_referer('simple301redirects', 'security');
        if( ! current_user_can( 'install_plugins' ) ) wp_die();
        $slug = isset($_POST['slug']) ? sanitize_text_field($_POST['slug']) : '';
        // The only plugin this page offers to install is BetterLinks from WordPress.org.
        if ($slug !== 'betterlinks') {
            wp_send_json_error(__('This plugin can\'t be installed from here.', 'simple-301-redirects'));
        }
        $result = \Simple301Redirects\Helper::install_plugin($slug);
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        wp_send_json_success(__('Plugin is installed successfully!', 'simple-301-redirects'));
        wp_die();
    }

    public function activate_plugin()
    {
        check_ajax_referer('simple301redirects', 'security');
        if( ! current_user_can( 'activate_plugins' ) ) wp_die();
        $basename = isset($_POST['basename']) ? sanitize_text_field($_POST['basename']) : '';
        if ($basename !== 'betterlinks/betterlinks.php') {
            wp_send_json_error(__('This plugin can\'t be activated from here.', 'simple-301-redirects'));
        }
        $result = activate_plugin($basename, '', false );
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        if ($result === false) {
            wp_send_json_error(__('Plugin couldn\'t be activated.', 'simple-301-redirects'));
        }
        wp_send_json_success(__('BetterLinks is activated!', 'simple-301-redirects'));
        wp_die();
    }
    public function hide_notice()
    {
        check_ajax_referer('simple301redirects', 'security');
        if( ! current_user_can( 'manage_options' ) ) wp_die();
        $hide = isset($_POST['hide']) ? sanitize_text_field($_POST['hide']) : false;
        update_option('simple301redirects_hide_btl_notice', $hide);
        wp_send_json_success($hide);
        wp_die();
    }

    public function fetch_all_links()
    {
        check_ajax_referer('simple301redirects', 'security');
        if( ! current_user_can( 'manage_options' ) ) wp_die();
        wp_send_json_success(get_option('301_redirects', []));
        wp_die();
    }

    public function create_new_link()
    {
        check_ajax_referer('simple301redirects', 'security');
        if( ! current_user_can( 'manage_options' ) ) wp_die();
        $key = (isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '');
        $value = (isset($_POST['value']) ? sanitize_text_field($_POST['value']) : '');
        $links = get_option('301_redirects', []);
		if(!empty($key) && !isset($links[$key])){
			$links[$key] = $value;
			update_option('301_redirects', $links);
		}
        wp_send_json_success($links);
        wp_die();
    }
    public function update_link()
    {
        check_ajax_referer('simple301redirects', 'security');
        if( ! current_user_can( 'manage_options' ) ) wp_die();
        $key = (isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '');
        $oldKey = (isset($_POST['oldKey']) ? sanitize_text_field($_POST['oldKey']) : '');
        $value = (isset($_POST['value']) ? sanitize_text_field($_POST['value']) : '');
        $links = get_option('301_redirects', []);
		if(isset($links[$oldKey])){
			if(isset($oldKey) && $oldKey != $key){
				unset($links[$oldKey]);
			}
			$links[$key] = $value;
			update_option('301_redirects', $links);
		}
        wp_send_json_success($links);
        wp_die();
    }

    public function delete_link()
    {
        check_ajax_referer('simple301redirects', 'security');
        if( ! current_user_can( 'manage_options' ) ) wp_die();
        $key = (isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '');
        $links = get_option('301_redirects');
		if(isset($links[$key])){
			unset($links[$key]);
			update_option('301_redirects', $links);
		}
        wp_send_json_success($links);
        wp_die();
    }

    public function client_consent()
    {
        check_ajax_referer('simple301redirects', 'security');
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( "You don't have permission to do this.", 'simple-301-redirects' ) );
        }
        $opt_in_value = isset( $_POST['opt_in_value'] ) ? sanitize_text_field( wp_unslash( $_POST['opt_in_value'] ) ) : 'no';
        $opt_in = WPDev\PluginUsageTracker::get_instance( SIMPLE301REDIRECTS_PLUGIN_FILE, [
            'opt_in'       => true,
            'goodbye_form'    => true,
            'email_marketing' => false,
            'item_id'         => 'c1e613119bf3e9188767',
        ] );

        $opt_in_value = 'yes' === $opt_in_value ? 'yes' : 'no';
        $opt_in->opt_in( $opt_in_value, SIMPLE301REDIRECTS_PLUGIN_SLUG );

        // A settings switch may reuse this endpoint; only the setup wizard advances its step.
        $context = isset( $_POST['context'] ) ? sanitize_key( wp_unslash( $_POST['context'] ) ) : '';
        if ( 'settings' !== $context ) {
            update_option( QuickSetup::STEP_OPTION, 1 );
        }
        wp_send_json_success( [
            'result' => $opt_in_value,
        ] );
    }

    public function complete_setup()
    {
        check_ajax_referer('simple301redirects', 'security');
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( "You don't have permission to do this.", 'simple-301-redirects' ) );
        }
        $is_update = update_option( QuickSetup::STEP_OPTION, 'complete' );
        wp_send_json_success( [
            'result' => ( $is_update || QuickSetup::is_complete() ) ? 'complete' : 'error',
        ] );
    }
}

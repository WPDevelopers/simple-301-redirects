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
        if( ! current_user_can( 'manage_options' ) ) wp_die();
        $slug = isset($_POST['slug']) ? sanitize_text_field($_POST['slug']) : '';
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
        if( ! current_user_can( 'manage_options' ) ) wp_die();
        $basename = isset($_POST['basename']) ? sanitize_text_field($_POST['basename']) : '';
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
        wp_send_json_success(get_option('301_redirects'));
        wp_die();
    }

    public function create_new_link()
    {
        check_ajax_referer('simple301redirects', 'security');
        if( ! current_user_can( 'manage_options' ) ) wp_die();
        $key = (isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '');
        $value = (isset($_POST['value']) ? sanitize_text_field($_POST['value']) : '');
        $links = get_option('301_redirects');
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
        $links = get_option('301_redirects');
		if(isset($links[$oldKey])){
			if(isset($oldKey) && $oldKey != $param['key']){
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
}
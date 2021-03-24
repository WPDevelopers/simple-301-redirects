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
    }
    public function get_wildcard()
    {
        check_ajax_referer('wp_rest', 'security');
		wp_send_json_success(get_option('301_redirects_wildcard'));
		wp_die();
    }
    public function wildcard() 
    {
        check_ajax_referer('wp_rest', 'security');
        update_option('301_redirects_wildcard', $_POST['toggle']);
		wp_send_json_success($_POST['toggle']);
		wp_die();
    }
    public function install_plugin()
    {
        check_ajax_referer('wp_rest', 'security');
        $slug = isset($_POST['slug']) ? $_POST['slug'] : '';
        $result = \Simple301Redirects\Helper::install_plugin($slug);
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        wp_send_json_success(__('Plugin is installed successfully!', 'simple-301-redirects'));
        wp_die();
    }

    public function activate_plugin()
    {
        check_ajax_referer('wp_rest', 'security');
        $basename = isset($_POST['basename']) ? $_POST['basename'] : '';
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
        check_ajax_referer('wp_rest', 'security');
        $hide = isset($_POST['hide']) ? $_POST['hide'] : false;
        update_option('simple301redirects_hide_btl_notice', $hide);
        wp_send_json_success($hide);
        wp_die();
    }
}
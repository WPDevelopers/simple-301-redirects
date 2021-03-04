<?php
namespace Simple301Redirects\Admin;

class Ajax {
    public function __construct()
    {
        add_action('wp_ajax_simple301redirects/admin/wildcard', [$this, 'wildcard']);
        add_action('wp_ajax_simple301redirects/admin/get_wildcard', [$this, 'get_wildcard']);
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
}
<?php
namespace Simple301Redirects;

class Admin {
    public function __construct()
    {
        $this->add_menu();
        $this->load_assets();
        $this->init_ajax();
        $this->init_tools();
        $this->usage_tracker();
        add_filter('Simple301Redirects/Admin/skip_no_conflict', [$this, 'skip_no_conflict']);
    }
    public function add_menu()
    {
        new Admin\Menu();
    }
    public function load_assets()
    {
        new Admin\Assets();
    }
    public function init_ajax()
    {
        new Admin\Ajax();
    }
    public function init_tools()
    {
        new Admin\Tools();
    }
    public function skip_no_conflict()
	{
		$whitelist = ['127.0.0.1', '::1'];
		if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
			return true;
		}
		return false;
    }
    public function usage_tracker()
    {
        $tracker = Admin\WPDev\PluginUsageTracker::get_instance( SIMPLE301REDIRECTS_PLUGIN_FILE, [
            'opt_in'       => true,
            'goodbye_form' => true,
            'item_id'      => 'c1e613119bf3e9188767'
        ] );
        $tracker->set_notice_options(array(
            'notice' => __( 'Want to help make <strong>Simple 301 Redirects</strong> even more awesome? You can get a <strong>10% discount</strong> coupon on our Premium products if you allow us to track the non-sensitive usage data.', 'simple-301-redirects' ),
            'extra_notice' => __( 'We collect non-sensitive diagnostic data and plugin usage information. 
            Your site URL, WordPress & PHP version, plugins & themes and email address to send you the 
            discount coupon. This data lets us make sure this plugin always stays compatible with the most 
            popular plugins and themes. No spam, I promise.', 'simple-301-redirects' ),
        ));
        $tracker->init();
    }
}
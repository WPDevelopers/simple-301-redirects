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
		if (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $whitelist, true)) {
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
        add_action('init', function() use ($tracker) {
            $tracker->set_notice_options(array(
                'notice' => __( 'Want to help make <strong>Simple 301 Redirects</strong> even more awesome? Allow us to send usage data to WPDeveloper and get a <strong>10% discount</strong> coupon on our Premium products.', 'simple-301-redirects' ),
                'extra_notice' => sprintf(
                    /* translators: 1: external service host name, 2: privacy policy URL. */
                    __( 'If you allow it, Simple 301 Redirects sends these details to WPDeveloper\'s WPInsight service at %1$s: your site URL and site name, WordPress version, site language and charset, PHP version, server software, whether the site is a multisite, text direction, the server path of the plugin, your active and inactive plugins, the active theme and its version, the Simple 301 Redirects version, and the email address of the administrator who allows tracking (used to send the discount coupon). They are sent once now, then about once a day (only the details that changed), and once more when the plugin is deactivated, together with the email address of the administrator who deactivates it and any reason they enter in the deactivation form. Like any web request, this also reveals your server\'s IP address. Nothing is sent if you click "No Thanks." <a href="%2$s" target="_blank" rel="noopener noreferrer">Privacy Policy</a>', 'simple-301-redirects' ),
                    '<code>send.wpinsight.com</code>',
                    esc_url( 'https://wpdeveloper.com/privacy-policy/' )
                ),
            ));
        });
        $tracker->init();
    }
}
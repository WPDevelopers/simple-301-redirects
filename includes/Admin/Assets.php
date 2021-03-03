<?php
namespace Simple301Redirects\Admin;

class Assets {
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'plugin_scripts']);
    }
    /**
	 * Enqueue Files on Start Plugin
	 *
	 * @function plugin_script
	 */
	public function plugin_scripts($hook)
	{
		if (\Simple301Redirects\Helper::plugin_page_hook_suffix($hook)) {
			add_action(
				'wp_print_scripts',
				function () {
					$isSkip = apply_filters('Simple301Redirects/Admin/skip_no_conflict', false);

					if ($isSkip) {
						return;
					}

					global $wp_scripts;
					if (!$wp_scripts) {
						return;
					}

					$pluginUrl = plugins_url();
					foreach ($wp_scripts->queue as $script) {
						$src = $wp_scripts->registered[$script]->src;
						if (strpos($src, $pluginUrl) !== false && !strpos($src, '301options') !== false) {
							wp_dequeue_script($wp_scripts->registered[$script]->handle);
						}
					}
				},
				1
			);
			wp_enqueue_style('simple-301-redirects-admin-style', SIMPLE301REDIRECTS_ASSETS_URI . 'css/simple-301-redirects.css', [], filemtime(SIMPLE301REDIRECTS_ASSETS_DIR_PATH . 'css/simple-301-redirects.css'), 'all');

			wp_enqueue_script(
				'simple-301-redirects-admin-core',
				SIMPLE301REDIRECTS_ASSETS_URI . 'js/simple-301-redirects.core.min.js',
				['jquery'],
				filemtime(SIMPLE301REDIRECTS_ASSETS_DIR_PATH . 'js/simple-301-redirects.core.min.js'),
				true
			);
			wp_localize_script('simple-301-redirects-admin-core', 'Simple301Redirects', [
				'nonce' => wp_create_nonce('wp_rest'),
				'rest_url' => rest_url(),
				'namespace' =>  'simple301redirects/v1/',
				'plugin_root_url' => SIMPLE301REDIRECTS_PLUGIN_ROOT_URI,
				'plugin_root_path' => SIMPLE301REDIRECTS_ROOT_DIR_PATH,
				'site_url' => site_url(),
				'route_path' => parse_url(admin_url(), PHP_URL_PATH),
			]);
		}
	}
}
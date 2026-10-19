<?php
namespace Simple301Redirects\Admin;

/**
 * Quick Setup wizard: the first-run screen an admin lands on after activating the plugin.
 *
 * Mirrors BetterLinks' Quick Setup. Activation sets SETUP_FLAG; the next admin page load
 * redirects once to the wizard. The wizard records its progress in STEP_OPTION
 * (1 = consent answered, 'complete' = finished) and the menu entry stays until it is complete.
 */
class QuickSetup
{
    const PAGE_SLUG       = 'simple-301-redirects-quick-setup';
    const SETUP_FLAG      = 'simple301redirects_quick_setup';
    const STEP_OPTION     = 'simple301redirects_quick_setup_step';
    const BACKFILL_OPTION = 'simple301redirects_quick_setup_backfilled';

    public function __construct()
    {
        add_action('admin_init', [$this, 'maybe_complete_legacy_quick_setup'], 9);
        add_action('admin_init', [$this, 'quick_setup']);
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('current_screen', [$this, 'remove_admin_notices']);
    }

    public static function is_complete()
    {
        return 'complete' === get_option(self::STEP_OPTION);
    }

    /**
     * Retire the Quick Setup entry on sites that were set up before the wizard existed.
     *
     * Runs once per install: if redirects already exist, the site is past onboarding.
     */
    public function maybe_complete_legacy_quick_setup()
    {
        if (get_option(self::BACKFILL_OPTION)) {
            return;
        }
        update_option(self::BACKFILL_OPTION, 1, false);

        if (self::is_complete()) {
            return;
        }
        $redirects = get_option(SIMPLE301REDIRECTS_SETTINGS_NAME);
        if (is_array($redirects) && count($redirects) > 0) {
            update_option(self::STEP_OPTION, 'complete');
            delete_option(self::SETUP_FLAG);
        }
    }

    /**
     * Send the admin to the wizard once, on the first admin page load after activation.
     */
    public function quick_setup()
    {
        if (self::is_complete() || ! get_option(self::SETUP_FLAG)) {
            return;
        }
        // Not on AJAX, network admin or bulk activation, and only for someone who can run the wizard.
        if (wp_doing_ajax() || is_network_admin() || isset($_GET['activate-multi']) || ! current_user_can('manage_options')) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return;
        }
        delete_option(self::SETUP_FLAG);

        wp_safe_redirect(admin_url('options-general.php?page=' . self::PAGE_SLUG));
        exit;
    }

    public function add_menu()
    {
        if (self::is_complete()) {
            return;
        }
        add_options_page(
            __('Simple 301 Redirects Quick Setup', 'simple-301-redirects'),
            __('301 Redirects Setup', 'simple-301-redirects'),
            'manage_options',
            self::PAGE_SLUG,
            [$this, 'load_template']
        );
    }

    public function load_template()
    {
        echo '<div id="simple301redirectsquicksetup" class="s3r-qs-root"></div>';
    }

    /**
     * The wizard asks for usage-data consent itself, so it shows no admin notices (same as BetterLinks).
     */
    public function remove_admin_notices($screen)
    {
        if ($screen && 'settings_page_' . self::PAGE_SLUG === $screen->id) {
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
        }
    }
}

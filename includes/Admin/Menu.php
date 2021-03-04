<?php 
namespace Simple301Redirects\Admin;

class Menu {
    public function __construct()
    {
        add_action('admin_menu', array($this,'create_menu'));
        // if submitted, process the data
        if (isset($_POST['301_redirects'])) {
            add_action('admin_init', array($this,'save_redirects'));
        }
    }

    /**
     * create_menu function
     * generate the link to the options page under settings
     * @access public
     * @return void
     */
    public function create_menu() {
        add_options_page('301 Redirects', '301 Redirects', 'manage_options', '301options', array($this,'load_main_template'));
    }

    public function load_main_template()
	{
		echo '<div id="simple301redirectsbody" class="simple301redirects"></div>';
	}
}
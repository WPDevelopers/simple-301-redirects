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

    /**
     * options_page function
     * generate the options page in the wordpress admin
     * @access public
     * @return void
     */
	public function options_page() {
        ?>
        <div class="wrap simple_301_redirects">
            <script>
                //todo: This should be enqued
                jQuery(document).ready(function(){
                    jQuery('span.wps301-delete').html('Delete').css({'color':'red','cursor':'pointer'}).click(function(){
                        var confirm_delete = confirm('Delete This Redirect?');
                        if (confirm_delete) {
                            
                            // remove element and submit
                            jQuery(this).parent().parent().remove();
                            jQuery('#simple_301_redirects_form').submit();
                            
                        }
                    });
                    
                    jQuery('.simple_301_redirects .documentation').hide().before('<p><a class="reveal-documentation" href="#">Documentation</a></p>')
                    jQuery('.reveal-documentation').click(function(){
                        jQuery(this).parent().siblings('.documentation').slideToggle();
                        return false;
                    });
                });
            </script>
        
        <?php
            if (isset($_POST['301_redirects'])) {
                echo '<div id="message" class="updated"><p>Settings saved</p></div>';
            }
        ?>
        
            <h2>Simple 301 Redirects</h2>
            
            <form method="post" id="simple_301_redirects_form" action="options-general.php?page=301options&savedata=true">
            
            <?php wp_nonce_field( 'save_redirects', '_s301r_nonce' ); ?>

            <table class="widefat">
                <thead>
                    <tr>
                        <th colspan="2">Request</th>
                        <th colspan="2">Destination</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2"><small>example: /about.htm</small></td>
                        <td colspan="2"><small>example: <?php echo get_option('home'); ?>/about/</small></td>
                    </tr>
                    <?php echo $this->expand_redirects(); ?>
                    <tr>
                        <td style="width:35%;"><input type="text" name="301_redirects[request][]" value="" style="width:99%;" /></td>
                        <td style="width:2%;">&raquo;</td>
                        <td style="width:60%;"><input type="text" name="301_redirects[destination][]" value="" style="width:99%;" /></td>
                        <td><span class="wps301-delete">Delete</span></td>
                    </tr>
                </tbody>
            </table>
            
            <?php $wildcard_checked = (get_option('301_redirects_wildcard') === 'true' ? ' checked="checked"' : ''); ?>
            <p><input type="checkbox" name="301_redirects[wildcard]" id="wps301-wildcard"<?php echo $wildcard_checked; ?> /><label for="wps301-wildcard"> Use Wildcards?</label></p>
            
            <p class="submit"><input type="submit" name="submit_301" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
            </form>
            <div class="documentation">
                <h2>Documentation</h2>
                <h3>Simple Redirects</h3>
                <p>Simple redirects work similar to the format that Apache uses: the request should be relative to your WordPress root. The destination can be either a full URL to any page on the web, or relative to your WordPress root.</p>
                <h4>Example</h4>
                <ul>
                    <li><strong>Request:</strong> /old-page/</li>
                    <li><strong>Destination:</strong> /new-page/</li>
                </ul>
                
                <h3>Wildcards</h3>
                <p>To use wildcards, put an asterisk (*) after the folder name that you want to redirect.</p>
                <h4>Example</h4>
                <ul>
                    <li><strong>Request:</strong> /old-folder/*</li>
                    <li><strong>Destination:</strong> /redirect-everything-here/</li>
                </ul>
        
                <p>You can also use the asterisk in the destination to replace whatever it matched in the request if you like. Something like this:</p>
                <h4>Example</h4>
                <ul>
                    <li><strong>Request:</strong> /old-folder/*</li>
                    <li><strong>Destination:</strong> /some/other/folder/*</li>
                </ul>
                <p>Or:</p>
                <ul>
                    <li><strong>Request:</strong> /old-folder/*/content/</li>
                    <li><strong>Destination:</strong> /some/other/folder/*</li>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * expand_redirects function
     * utility function to return the current list of redirects as form fields
     * @access public
     * @return string <html>
     */
	public	function expand_redirects() {
        $redirects = get_option('301_redirects');
        $output = '';
        if (!empty($redirects)) {
            foreach ($redirects as $request => $destination) {
                $output .= '
                
                <tr>
                    <td><input type="text" name="301_redirects[request][]" value="'.$request.'" style="width:99%" /></td>
                    <td>&raquo;</td>
                    <td><input type="text" name="301_redirects[destination][]" value="'.$destination.'" style="width:99%;" /></td>
                    <td><span class="wps301-delete"></span></td>
                </tr>
                
                ';
            }
        } // end if
        return $output;
    }

    /**
     * save_redirects function
     * save the redirects from the options page to the database
     * @access public
     * @param mixed $data
     * @return void
     */
    public function save_redirects($data) {
        if ( !current_user_can('manage_options') )  { wp_die( 'You do not have sufficient permissions to access this page.' ); }
        check_admin_referer( 'save_redirects', '_s301r_nonce' );
        
        $data = $_POST['301_redirects'];

        $redirects = array();
        
        for($i = 0; $i < sizeof($data['request']); ++$i) {
            $request = trim( sanitize_text_field( $data['request'][$i] ) );
            $destination = trim( sanitize_text_field( $data['destination'][$i] ) );
        
            if ($request == '' && $destination == '') { continue; }
            else { $redirects[$request] = $destination; }
        }
        
        update_option('301_redirects', $redirects);
        
        if (isset($data['wildcard'])) {
            update_option('301_redirects_wildcard', 'true');
        }
        else {
            delete_option('301_redirects_wildcard');
        }
    }
		

}
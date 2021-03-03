<?php
namespace Simple301Redirects;

class Installer {
    
    public function migrate()
    {
        if(empty(get_option('simple301redirects_version')) || version_compare(get_option('simple301redirects_version'), SIMPLE301REDIRECTS_VERSION, '<')){
            $this->option_value_migration_from_serialize_to_json();
        }
        $this->set_version_number();
    }

    public function set_version_number()
    {
        if (get_option('simple301redirects_version') != SIMPLE301REDIRECTS_VERSION) {
			update_option('simple301redirects_version', SIMPLE301REDIRECTS_VERSION);
		}
    }

    public function option_value_migration_from_serialize_to_json()
    {
        $option = get_option('301_redirects');
        if(!empty($option)){
            update_option('301_redirects', json_encode($option));
        }
    }
}
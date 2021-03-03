<?php
namespace Simple301Redirects;

class Installer {
    
    public function migrate()
    {
        $this->set_version_number();
    }

    public function set_version_number()
    {
        if (get_option('simple301redirects_version') != SIMPLE301REDIRECTS_VERSION) {
			update_option('simple301redirects_version', SIMPLE301REDIRECTS_VERSION);
		}
    }
}
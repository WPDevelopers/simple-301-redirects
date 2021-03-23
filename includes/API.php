<?php
namespace Simple301Redirects;

class API { 
    public function __construct()
    {
        $this->register_settings_rest_API();
        add_filter('jwt_auth_whitelist', [$this, 'whitelist_API']);
    }
    public function register_settings_rest_API(){
        new API\Settings();
    }
    public function whitelist_API($endpoints)
	{
		$endpoints[] = '/wp-json/simple301redirects/v1/*';
		$endpoints[] = '/index.php?rest_route=/simple301redirects/v1/*';
		return $endpoints;
	}
}
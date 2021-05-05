<?php
namespace Simple301Redirects;

class API { 
    public function __construct()
    {
        $this->register_settings_rest_API();
        add_filter('jwt_auth_whitelist', [$this, 'whitelist_API']);
        add_filter( 'rest_url', array($this, 'rest_url_ssl') );
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
    public function rest_url_ssl($url){
        if ( is_ssl() || ( is_admin() && force_ssl_admin() ) ) {
                $url = set_url_scheme( $url, 'https' );
                return $url;
        }
        return $url;
    }
}
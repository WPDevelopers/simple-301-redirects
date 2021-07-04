<?php
namespace Simple301Redirects;

class API { 
    public function __construct()
    {
        $this->register_settings_rest_API();
        add_filter( 'rest_url', array($this, 'rest_url_ssl') );
    }
    public function register_settings_rest_API(){
        new API\Settings();
    }
    public function rest_url_ssl($url){
        if ( is_ssl() || ( is_admin() && force_ssl_admin() ) ) {
                $url = set_url_scheme( $url, 'https' );
                return $url;
        }
        return $url;
    }
}
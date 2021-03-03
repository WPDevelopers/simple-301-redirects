<?php
namespace Simple301Redirects;

class API { 
    public function __construct()
    {
        $this->register_settings_rest_API();
    }
    public function register_settings_rest_API(){
        new API\Settings();
    }
}
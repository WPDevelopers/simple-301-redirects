<?php
namespace Simple301Redirects;

class Helper {
    /**
	 * Check Supported Post type for admin page and plugin main settings page
	 *
	 * @return bool
	 */

	public static function plugin_page_hook_suffix($hook)
	{
		if ($hook == 'settings_page_301options') {
			return true;
		}
		return false;
	}
    public static function str_ireplace($search,$replace,$subject){
        $token = chr(1);
        $haystack = strtolower($subject);
        $needle = strtolower($search);
        while (($pos=strpos($haystack,$needle))!==FALSE){
            $subject = substr_replace($subject,$token,$pos,strlen($search));
            $haystack = substr_replace($haystack,$token,$pos,strlen($search));
        }
        $subject = str_replace($token,$replace,$subject);
        return $subject;
    }
}
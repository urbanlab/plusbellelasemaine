<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utils {
    /**
     * Indique si la page courante est la home (default_controller) ou non
     * @return type
     */
    public function is_home()
    {
        $CI = & get_instance();
        return ( ($CI->router->fetch_class() === $CI->router->routes['default_controller'] &&  $CI->router->fetch_method() === 'index') ? true : false);
    }
}
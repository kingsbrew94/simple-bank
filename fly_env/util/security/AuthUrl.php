<?php namespace FLY_ENV\Util\Security;

use FLY\DSource\App;

/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @version 2.0.0
 * @package FLY_ENV\Util\Security
 */

 class AuthUrl {

    private static $valid_route;

    private static $request_url;

    private static $request_query;

    private static $has_valid_route = false;

    private static $url_has_match = false;

    private static $router_index;

    private static $request_method;
    
    private static $routers;

    use RouterSearch;

    public function __construct($routers, $host_dir)
    {
        self::$routers = $routers;
        self::$request_url = self::parseDOMUrl($_SERVER['REQUEST_URI']);
        self::$request_query = !empty($_REQUEST) ? $_REQUEST: [];
        self::$request_method = strtolower($_SERVER['REQUEST_METHOD']);        
        $this->match_route_url($routers, $host_dir);
        self::$router_index = $this->getValidRouteIndex($routers);
    }

    public function get_valid_route() { return self::$valid_route; }

    public function has_valid_route() { return self::$has_valid_route; }

    public function url_has_match() { return self::$url_has_match; }
    
    public function request_query() { return self::$request_query; }
    
    public function get_route_index() { return self::$router_index; }

    public function get_request_method() { return self::$request_method; }
    
    private function match_route_url($routers, $host_dir)
    {
        if(App::router_search_type() === 'r') {
            $this->randSearch($routers,$host_dir);
            return;
        }
        $this->matches_route_direct_url($routers, $host_dir);
    }
   
    private function matches_route_dynamic_url(&$router)
    {  
        if(isset($router->{'@name'})) {
            throw new \Exception('Regex url does not support url namespace');
        }
        $this->match_regex_with_url($router->{'@url'},self::$request_url,$router);
        return self::$has_valid_route;
    }

    private function match_regex_with_url($pattern, $value, &$router)
    {
        self::$url_has_match = \preg_match($pattern.'@',$value);
        if(!self::$url_has_match) {
            self::$has_valid_route = false;
            self::$valid_route = null;
        } else {
            $router->{'@url'} = $value;
            self::$has_valid_route = true;
            self::$valid_route = $router;
        }                                        
        
    }

    private static function parseDOMUrl($uri)
    {
        return urldecode(
            parse_url($uri, PHP_URL_PATH)
        );
    }
 }
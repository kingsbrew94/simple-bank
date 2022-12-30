<?php namespace FLY\DSource;

use FLY_ENV\Util\Routers\Router;

/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @version 2.0.0
 * @package FLY\DSource
 */

final class App {
    
    private static $name;

    private static $key;

    private static $url;

    private static $page_404;

    private static $logo;

    private static $http_referer;

    private static $host_dir;

    private static $check_appkey_updates = 0;

    private static $router_search_type = "s";

    private static $app_configs = null;

    private static $get_routers = null;

    private static $post_routers = null;

    private static $app_model = null;

    private static $app_model_method = null;

    private static $root_file = "";

    private static $has_sse = false;

    public function __construct($app_detail, $app_configs, array $get_routers, array $post_routers)
    {
        $this->set_fields($app_detail, $app_configs, $get_routers, $post_routers);
        $this->set_app_key($app_detail);
        $this->enforce_https();
        $this->load_libs();
        $this->set_app_root_file();
        $this->set_host_directory();
    }
    
    static public function root_file()
    {
        return self::$root_file;
    }

    private function set_app_root_file()
    {
        self::$root_file = explode('/',$_SERVER['SCRIPT_NAME']);
        self::$root_file = self::$root_file[count(self::$root_file) - 1];
    }

    private function set_fields($app_detail, $app_configs, array $get_routers, array $post_routers): App
    {

        self::$name = $app_detail->app_name;
        self::$logo = $app_detail->app_logo;

        self::$url  = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI']:"";
        self::$app_configs = $app_configs;    
        $this->set_http_referer();
        $this->set_router_search_type();
        self::$page_404 = $app_detail->page_404;
        self::$get_routers = $get_routers;
        self::$post_routers = $post_routers;
        return $this;
    }

    private function set_router_search_type()
    {
        if(isset(self::$app_configs->router_search_type) && self::$app_configs->router_search_type === '-r')
            self::$router_search_type = 'r';
    }

    private function enforce_https()
    {
        if(isset(self::$app_configs->security)) {
            if(isset(self::$app_configs->security->{'sse'})) {
                if(self::$app_configs->security->{'sse'} === true) {
                    self::$has_sse = true;
                }                
            }
            if(isset(self::$app_configs->security->{'ssl'})) {
                if(self::$app_configs->security->{'ssl'} === true) {                
                    $using_ssl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' || $_SERVER['SERVER_PORT'] == 443;
                    if (!$using_ssl) {
                        header('HTTP/1.1 301 Moved Permanently');
                        header('Location: https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
                        exit;
                    }
                }
            }
        }
    }

    private function set_http_referer()
    {
        self::$http_referer = isset($_SERVER["HTTP_HOST"]) ? $_SERVER['HTTP_HOST'].'/':'';
        self::$http_referer = str_replace(self::$url,'/',self::$http_referer);

        if(self::$app_configs->security->{'ssl'} && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
            self::$http_referer = 'https://'.self::$http_referer;
        else
            self::$http_referer = 'http://'.self::$http_referer;      
    }

    private function set_host_directory()
    {

        if(self::$url === "") self::$host_dir = self::$url;
        else {
            $url_arr = explode('/',str_replace(self::$root_file,'',$_SERVER['SCRIPT_NAME']));
            array_shift($url_arr);
            self::$host_dir = \implode('/',$url_arr);
        }
    }

    private function set_app_key($app_detail)
    {
        if(empty($app_detail->app_key) || !isset($app_detail->app_key)) throw new \Exception('Error: App Key is missing generate new app key and add to the "watch" file');

        if(self::$check_appkey_updates === 0) {
            self::$key  = $app_detail->app_key;
            ++self::$check_appkey_updates;                      
        }
    }

    private function load_libs()
    {
        require_once 'fly_env/util/libs/funcs/index.php';
    }

    public function launch()
    {
        $this->init_routers();
    }

    private function init_routers()
    {
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                Router::get(self::$get_routers,self::$post_routers,self::$host_dir);
            break;
            case 'POST':
                Router::post(self::$post_routers,self::$get_routers,self::$host_dir);
            break;
        }
    }

    public static function has_sse() { return self::$has_sse; }

    public static function host_directory() { return self::$host_dir; }

    public static function http_referer() { return self::$http_referer; }
    
    public static function name() { return self::$name; }
    
    public static function key() { return self::$key; }

    public static function url() { return self::$url; }

    public static function logo() { return self::$logo; }

    public static function page_404_path() { return self::$page_404; }
    
    public static function app_model_name() { return self::$app_model; }

    public static function app_model_method_name() { return self::$app_model_method; }

    public static function router_search_type() { return self::$router_search_type; }

    public static function api_mode() { return self::$app_configs->security->{'api_mode'}?? false; }

    public static function set_app_model_name($app_model)
    {
        self::$app_model = $app_model;
    }

    public static function set_app_model_method_name($app_model_method)
    {
        self::$app_model_method = $app_model_method;
    }

    public static function set_name($app_name) 
    {
        self::$name = $app_name;
    } 

    public static function set_logo($app_logo) 
    {
        self::$logo = $app_logo;
    } 

    public static function set_page_404_path($error_file_path)
    {
        self::$page_404 = $error_file_path;
    }

    public static function cancel_sse()
    {
        self::$has_sse = false;
    }

    public static function activate_sse()
    {
        self::$has_sse = true;
    }
}
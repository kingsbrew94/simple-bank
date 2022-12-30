<?php namespace FLY_ENV\Util\Routers;

use FLY\DSource\App;
use FLY_ENV\Util\Security\RouterNISearch;

/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @version 2.0.0
 * @package FLY_ENV\Util\PipeFields
 */


 abstract class PipeFields {

    private $controller;

    private $view;

    private $mvc_method;

    private $url;

    private $static;

    private $payload;

    private static $has_fml   = false;

    private static $has_model = false;

    private static $routers   = [];

    private $has_static       = false;

    private $has_controller   = false;

    private $route_index;

    private $request_method;

    private $controller_array;

    use RouterNISearch;

    public function __construct($router, $route_index, $request_method, $routers)
    {
        $router = (array) $router;
        $this->route_index = $route_index;
        $this->request_method = $request_method;
        self::$routers = $routers;
        $this->set_controller($router); 
        $this->set_mvc_method();
        $this->set_view();
        $this->set_url($router);
        $this->set_static($router);  
        $this->set_fields($router); 
        $this->set_fml();   
        $this->set_model_flag(self::$has_model);
    }

    public function controller()
    {
        return $this->controller;
    }

    public function route_index()
    {
        return $this->route_index;
    }

    public function request_method()
    {
        return $this->request_method;
    }

    public function mvc_method()
    {
        return $this->mvc_method;
    }

    public function view()
    {
        return $this->view;
    }

    public function request_url()
    {
        return $this->url;
    }

    public function template_path()
    {
        $static = FLY_ENV_STATIC_HTMLS_PATH.$this->static;
        if(self::$has_fml) {
            $static = FLY_ENV_STATIC_FML_PORTAL_PATH;
        } 
        return $static;
    }

    public function payload()
    {
        return $this->payload;
    }

    public function fml_mode()
    {
        return self::$has_fml;
    }

    public function has_model()
    {
        return self::$has_model;
    }

    public function has_template()
    {
        return $this->has_static;
    }

    public function has_controller()
    {
        return $this->has_controller;
    }

    public function set_model_flag($flag) 
    {
        self::$has_model = $flag;
    }

    private function set_controller($router)
    {
        if(array_key_exists('@controller', $router) && !empty($router['@controller'])) {
            if(is_string($router['@controller'])) {
                $this->controller_array = explode('@',$router['@controller']);
                $this->controller = $this->prepare_controller($this->controller_array[count($this->controller_array) - 1]);
                $this->has_controller = true;
            }
        } 
    }

    private function prepare_controller($controller)
    {
        if(!is_int(strpos($controller,'App::Controllers'))) {
            $controller = 'App::Controllers::'.$controller;
        }
        return str_replace('::','\\',$controller);
    }

    private function set_mvc_method()
    {
        if($this->has_controller) {
            $len = count($this->controller_array);
            if($len === 1) {
                $this->method = 'index';
            } else if($len === 2) {
                $this->mvc_method = $this->controller_array[0] === "" ? 'index': $this->controller_array[0];
            } else {
                throw new \Exception('Controller key must contain a method name with its corresponding Controller class');
            }
        }
    }
    
    private function set_view()
    {
        if($this->has_controller && strpos($this->controller,'Controller') > 0) {
            $this->view = \str_replace('Controller','View', $this->controller);
        }
    }

    private function view_exists()
    {
        return class_exists($this->view(),true);
    }

    private function set_url($router)
    {
        if(array_key_exists('@url', $router)) {
            $this->url = $router['@url'];
        } 
    }

    public static function url_payload($url_key)
    {
        $payload = self::parse_url_name($url_key);
        $url_key = $payload[0];
        $result = (
            App::router_search_type() === 'r'
            ? self::rdmGetUrlByName(self::$routers,$payload[0])
            : self::getUrlByName(self::$routers,$payload[0])
        );
        return $result !== null ? $result.$payload[1]: $result;
    }

    private static function parse_url_name($url_name)
    {
        $data = [];
        if(preg_match('%^([:]\s*[\w\W]+?)([/]?[?][\w\W]*)%',$url_name,$match)) {
            $data [] = preg_replace('%\s+%','',$match[1]);                        
            $data [] = $match[2];            
        } else {
            $data [] = $url_name;
            $data [] = '';
        }
        return $data;
    }

    private function set_static($router)
    {

        if(array_key_exists('@static',$router)) {
            if(!empty($router['@static'])) {
                $this->static = $router['@static'];
                $this->has_static = true;
            }
        } else if(strpos($this->url,'@') > 0) {
            $this->static = explode('@',$this->url);
            $this->static = $this->static[count($this->static) - 1];
            $this->has_static = true;
        }

        if(strpos($this->static,'~') === 0) {
            $this->static = str_replace('~','',$this->static).'.php';
        } else {
            $this->static .= '.wave.php';
        }
    }

    private function set_fields($router)
    {
        if(array_key_exists('@payload',$router)) {

            if(!is_array((array) $router['@payload'])) {
                throw new \Exception('Router fields must be an array');
            }
            if(!empty($router['@payload'])) {
                $this->payload = (array) $router['@payload'];
            }
        } else $this->payload = [];
    }

    private function set_fml()
    {
        if($flag = $this->view_exists()) {
            self::$has_fml =  $flag;
            $this->write_to_portal();
        }
    }

    private function write_to_portal()
    {
        if(self::$has_fml) {
            fopen(FLY_ENV_STATIC_FML_PORTAL_PATH,'w');
            $this->has_static = true;
        }
    }
 }
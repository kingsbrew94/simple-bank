<?php namespace FLY\Libs;

use Exception;
use FLY\Security\Sessions;

/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @package libs
 */

class Request {

    private static $request_method;

    private static $has_error = FALSE;

    private static $inst = null;

    public function __construct()
    {
        self::$inst = $this;   
    }

    static public function keyToUpperCase() 
    {
        $requestKeys = array_keys($_REQUEST);
        foreach ($requestKeys as $key) {
            $_REQUEST[strtoupper($key)] = $_REQUEST[$key]; 
            if(strtoupper($key) <> $key) unset($_REQUEST[$key]);
        }
        return self::class;
    }

    static public function instance()
    {
        return self::$inst == null ? new Request(): self::$inst;
    }

    static public function set_error($flag)
    {
        self::$has_error = $flag;
    }

    static public function has_error()
    {
        return self::$has_error;
    }

    public function __get($name)
    {
        self::set_request_method();
        return $this->get_request_value($name);
    }

    public function __set($name,$value)
    {
       self::set_request_method();
       $this->set_request($name,$value);
    }

    private function set_request($name,$value)
    {
        if(isset($_REQUEST[$name])) {
            $_REQUEST[$name] = $value;
        } else {
            throw new \Exception('The request key '.$name.' does not exists');
        }
    }

    static public function getIPAddress()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    static public function unsigned_all()
    {
        $requestKeys = array_keys($_REQUEST);
        $data = [];
        foreach ($requestKeys as $key) {
            $data[
                trim(preg_replace(
                    '%\:(?:\s*)[?_]?[a-z]*%','',$key
                ))
            ] = $_REQUEST[$key];
        }
        return $data;
    }

    static public function all()
    {
        return $_REQUEST;
    }

    static public function count()
    {
        return count(self::all());
    }

    static public function query()
    {
        return $_SERVER['QUERY_STRING']?? '';
    }

    static public function hasQuery()
    {
        return !is_empty(self::query());
    }

    static public function exists($requestKey)
    {
        $flag  = false;
        try {
            $flag = isset($_REQUEST[$requestKey]) && !is_empty($_REQUEST[$requestKey]);
        } catch(Exception $e) {
            $flag = false;
        }
        return $flag;
    }

    static public function get($requestKey)
    {
        $requestKey = isset($_REQUEST[$requestKey]) 
            ? $requestKey : 
            (isset($_REQUEST[strtoupper($requestKey)]) ? strtoupper($requestKey) : strtolower($requestKey));
        return trim(htmlentities($_REQUEST[trim($requestKey)],ENT_QUOTES));
    }

    static public function set($requestKey, $requestValue) 
    {
        $_REQUEST[$requestKey] = $requestValue;
    }

    static public function add($requestKey, $requestValue)
    {
        $requestKey = isset($_REQUEST[$requestKey]) 
            ? $requestKey : 
            (isset($_REQUEST[strtoupper($requestKey)]) ? strtoupper($requestKey) : strtolower($requestKey));
        $_REQUEST[$requestKey] = $requestValue;
    }

    static public function remove($requestKey)
    {
        $requestKey = isset($_REQUEST[$requestKey]) 
            ? $requestKey : 
            (isset($_REQUEST[strtoupper($requestKey)]) ? strtoupper($requestKey) : strtolower($requestKey));
        self::set_request_method();
        if(self::$request_method === 'post') {
            $_POST[$requestKey] = null;
            unset($_POST[$requestKey]);
        } else if(self::$request_method === 'get') {
            $_GET[$requestKey] = null;
            unset($_GET[$requestKey]);
        }
        
        $_REQUEST[$requestKey] = null;
        unset($_REQUEST[$requestKey]);
    } 

    static public function remove_all() 
    {
        foreach($_REQUEST as $requestKey => $requestValue) {
            self::remove($requestKey);
            $requestValue = null;
            unset($requestValue);
        }
    }

    static public function change_key($currentKey, $newKey) 
    {
        $value = $_REQUEST[$currentKey];
        self::remove($currentKey);
        self::set($newKey,$value);
    }

    static public function is_empty(): bool
    {
        foreach ($_REQUEST as $key => $value) {
            if('csrf_token' <> $key && '' <> trim($value))
                return false;
        }
        return true;
    }

    static public final function method()
    {
        return self::$request_method;
    }

    static private function set_request_method()
    {
        self::$request_method = strtolower($_SERVER['REQUEST_METHOD']);
    }

    static public function get_request_method()
    {
        return self::$request_method;
    }

    private function get_request_value($request_index)
    {
        return $this->route_request($request_index);
    }

    /**
     * @param $request_index
     * @return string
     */
    private function route_request($request_index)
    {
        $request = NULL;
        switch(strtolower(self::$request_method)) {
            case 'post':
                $request = isset($_POST[$request_index]) ? (function($request_index){
                    return $_POST[$request_index] = $_REQUEST[$request_index];
                })($request_index): null;
            break;
            case 'get':
                $_GET = $_REQUEST;
                $request = isset($_GET[$request_index]) ? (function($request_index){
                    return $_GET[$request_index] = $_REQUEST[$request_index];
                })($request_index): null;
            break;
            default:
                $request = isset($_REQUEST[$request_index]) ? $_REQUEST[$request_index]: null;
            break;
        }
        $request = htmlentities(trim($request),ENT_QUOTES);
        return !is_empty($request) ? $request : self::get($request_index);
    }

}
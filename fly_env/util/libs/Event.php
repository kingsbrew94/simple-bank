<?php namespace FLY\Libs;

/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @package libs
 */

class Event {
    
    static private $callbacks = [];

    static public function on($eventName, $callback) 
    {
        if(!is_callable($callback)) {
            throw new \Exception("Invalid callback!");
        }
        $eventName = strtolower($eventName);
        self::$callbacks[$eventName] = $callback;
        return __CLASS__;
    }

    static public function trigger($eventName, $data = null) 
    {
        $payload = null;
        $eventName = strtolower($eventName);
        if(isset(self::$callbacks[$eventName])) 
            $payload= self::$callbacks[$eventName]($data);                
        
        return $payload;
    }
}

<?php namespace FLY\Security;

class Sessions 
{
   private static $sessionId;

   private static $tokens = [];
   
   public function __construct()
   {
        $session_status = session_status();
        if($session_status === PHP_SESSION_ACTIVE || session_id()){
			session_regenerate_id();
		}else{
		    session_start();
        }
   }

   public static function start()
   {
       return new Sessions();
   }

   public static function add($key, $value)
   {
       $_SESSION[$key] = $value;
   }

   public static function tokens($key,$token) 
   {
       if(self::exists($key) && is_array(self::get($key))) {
           self::$tokens = self::get($key);
           array_push(self::$tokens,$token);
           self::set($key,self::$tokens);
       } else {
            array_push(self::$tokens,$token);
            self::set($key,self::$tokens);
       }
   }

   public static function set($key, $value) 
   {
       self::change_session($_SESSION[$key], $value);
   }

   private static function change_session(&$session, $value) 
   {
       $session = $value;
   }

   public static function remove($key)
   {
       if(self::exists($key)) {
            $_SESSION[$key] = NULL;
            unset($_SESSION[$key]);
       }
   }

   public static function generateSID()
   {
       self::$sessionId = session_regenerate_id();
       return self::$sessionId;
   }

   public static function view()
   {
    return $_SESSION;
   }

   public static function get($key)
   {
       return $_SESSION[$key];
   }

   public static function exists($key):bool
   {
       $flag = false;
        try{ 
            if(isset($_SESSION[$key]))
                $flag =  true;
        } catch(\Error $err) {
            $flag = false;
        } 
        return $flag;
   }

   public static function removeAll() 
   {
       session_destroy();
   }
}
<?php namespace FLY\Routers;
/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @version 2.0.0
 * @package FLY\Routers
 */

class Redirect 
{ 
    public static function to($uri) {
        header('Location:'.$uri);
        exit(); 
    }
}
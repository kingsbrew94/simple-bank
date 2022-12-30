<?php namespace FLY_ENV\Util\Routers;

use FLY_ENV\Util\Security\AuthUrl;
use FLY\Routers\Redirect;
use FLY\DSource\App;
use FLY\MVC\View;

/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @version 2.0.0
 * @package FLY_ENV\Util\Routers
 */

final class Router extends AuthUrl {

   private static $active_router;

   public function __construct($routers, $host_dir)
   {
      parent::__construct($routers, $host_dir);      
   }

   static public function set_pipe($get_routers, $post_routers)
   {
       return new Pipe(
           self::$active_router->get_valid_route(),
           self::$active_router->get_route_index(),
           self::$active_router->get_request_method(),
           self::routers($get_routers,$post_routers)
       );
   }

   static public function get($get_routers,$post_routers, $host_dir)
   {
      self::$active_router = new Self($get_routers, $host_dir);
      if(self::$active_router->has_valid_route())
          self::set_pipe($get_routers,$post_routers)->direct_get_packets();
      else
         Redirect::to(App::http_referer().App::host_directory().App::page_404_path());
   }
   
   static private function routers($get_routers,$post_routers)
   {
        foreach($post_routers as $router) {
            array_push($get_routers,$router);
        }
        return $get_routers;
   }

   static public function post($post_routers,$get_routers, $host_dir)
   {
      self::$active_router = new Self($post_routers, $host_dir);
      if(self::$active_router->has_valid_route()) {
          View::set_request_body();
         if(View::csrf_token_valid())
            self::set_pipe($get_routers,$post_routers)->direct_post_packets();
      } else
         Redirect::to(App::http_referer().App::host_directory().App::page_404_path());
   }
}
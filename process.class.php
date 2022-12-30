<?php
use FLY\DSource\App;
/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @package process
 */

final class Process 
{
    private $watchjson;

    private $get_routers;

    private $post_routers;

    public function __construct()
    {
        $this->load_watch_json();
        $this->load_routers();
    }
    
    public function execute()
    {
        require_once 'fly_env/initializer/env_types.php';
        require_once 'fly_env/initializer/loader.class.php';
        $this->run_app();
    }
    
    private function run_app()
    {
        if(isset($this->watchjson[0]) && isset($this->watchjson[1])) {
            $app = new App($this->watchjson[0], $this->watchjson[1], $this->get_routers, $this->post_routers);
            $app->launch();
        } 
    }

    private function load_watch_json()
    {
        if(file_exists('watch.json')) {
            $this->watchjson = file_get_contents('watch.json');
            $this->watchjson = json_decode($this->watchjson);
        } else  {
            throw new Exception(`could not found watch.json at`.urldecode(
                parse_url(
                        $_SERVER['REQUEST_URI'],
                        PHP_URL_PATH
                ))
            );
        }
    }

    private function load_routers()
    {
        if(file_exists('routes/get.json')) {
            $this->get_routers = file_get_contents('routes/get.json');
            $this->get_routers = json_decode($this->get_routers);
        }

        if(file_exists('routes/post.json')) {
            $this->post_routers = file_get_contents('routes/post.json');
            $this->post_routers = json_decode($this->post_routers);
        }
    }
} try { (new Process)->execute(); } catch(Exception $ex) { echo $ex->getMessage(); }
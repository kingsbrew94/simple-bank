<?php namespace FLY_ENV\Util\Routers;

use FLY_ENV\Util\Security\PipeFieldValidator;
use FLY_ENV\Util\Neuro\Stage;

/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @version 2.0.0
 * @package FLY_ENV\Util\Routers
 */


 final class Pipe extends PipeFields {

    private $stage;

    private $validator;
    
    private static $rendered = false;
    
    public function __construct($valid_router, $route_index, $request_method, $routers)
    {
        parent::__construct($valid_router, $route_index, $request_method, $routers);
    }

    public function direct_get_packets()
    {
        if($this->get_packet_valid()) {
            $this->stage = new Stage($this);
            
            $this->execute_static_routers();
            $this->execute_cv_request();
            $this->execute_ct_request();
            $this->excute_bachelor_controller();
            $this->execute_controller_request();
        }
    }

    public function direct_post_packets()
    { 
        if($this->post_packet_valid()) {
            $this->stage = new Stage($this);
            $this->execute_post_controller_request();
        }
    }

    private function post_packet_valid()
    {
        $this->validator = new PipeFieldValidator($this);
        return (
            $this->validator->validate_controller()      
        );
    }

    private function get_packet_valid()
    {
        $this->validator = new PipeFieldValidator($this);
        return (
            $this->validator->validate_static_routers() &&
            $this->validator->validate_template()   &&
            $this->validator->validate_controller() &&
            $this->validator->validate_view()
        );
    }

    private function execute_post_controller_request()
    {
        if($this->has_controller() && !$this->has_template()) {
            $this->stage->route_post_controller_request();
        }
    }

    private function execute_static_routers()
    {
        if(!$this->has_controller() && $this->has_template()) {
            $this->stage->route_static_request();
        }        
    }

    private function execute_cv_request()
    {
        if($this->has_controller() && $this->has_template() && class_exists($this->view(),true) && $this->validator->validate_mvc_method($this->view())) {
            $this->stage->route_cv_request();
        }
    }

    private function execute_ct_request()
    {
        if($this->has_controller() && $this->has_template() && !class_exists($this->view(),true) && !$this->fml_mode() && !self::$rendered) {
            $this->stage->route_controller_template_request();
            self::$rendered = true;
        }
    }

    private function excute_bachelor_controller()
    {
        if($this->has_controller() && $this->has_template() && !$this->validator->validate_mvc_method($this->view()) && !self::$rendered) {
            $this->stage->route_controller_template_request();
            self::$rendered = true;
        }      
    }

    private function execute_controller_request()
    {
        if($this->has_controller() && !$this->has_template()) {
            $this->stage->route_controller_request();
        }
    }
 }
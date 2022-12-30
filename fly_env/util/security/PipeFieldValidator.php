<?php namespace FLY_ENV\Util\Security;

use FLY_ENV\Util\Routers\Pipe;
use FLY\MVC\{ Model, View, Controller };

/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @version 2.0.0
 * @package FLY_ENV\Util\Security
 */


class PipeFieldValidator {
    
    private $pipe;

    public function __construct(Pipe $pipe)
    {
        $this->pipe = $pipe;
    }

    public function validate_static_routers()
    {
        $flag = true;

        if(!$this->pipe->has_controller() && !$this->pipe->has_template()) {
            $flag = false;
            throw new \Exception('Router '.$this->pipe->route_index().' at routes/'.$this->pipe->request_method().'.json without a Controller must have template to render');
        }
        return $flag;
    }

    public function validate_template()
    {
        $flag = true;
        if($this->pipe->has_template()) {
            $path = $this->pipe->template_path();
            if(!file_exists($path))  {
                $flag = false;
                throw new \Exception('Error: File '.$path.' was not found in router '.$this->pipe->route_index().' at routes/'.$this->pipe->request_method().'.json');
            }
        }
        return $flag;
    }

    public function validate_controller()
    {
        $flag = true;
        if($this->pipe->has_controller()) {
            if(get_parent_class($this->pipe->controller()) !== Controller::class) {
                $flag = false;
                throw new \Exception('Class '.$this->pipe->controller().' is not an instance of a controller');
            }         
        }
        return $flag;
    }

    public function validate_view()
    {
        $flag = true;
        if($this->pipe->has_template() && $this->pipe->has_controller()) {
            
            if(!class_exists($this->pipe->view(),true) && $this->pipe->fml_mode()) {
                $error_message  = 'Controller class '.$this->pipe->controller().' must have a matching view class '.$this->pipe->view(); 
                $error_message .= ' that either executes or return FLY\DOM\Application instance';
                $flag = false;
                throw new \Exception($error_message);
            }
        }
        return $flag;
    }

    public function validate_mvc_method($class)
    {
        $flag = true;
        if(!method_exists($class, $this->pipe->mvc_method())) {
            $flag = false;
            if(empty($this->pipe->mvc_method())) {
                $error_message = 'MVC Method not defined in router '.$this->pipe->route_index().' at routes/'.$this->pipe->request_method().'.json';
                throw new \Exception($error_message);
            }
        }
        return $flag;
    }
 }
 
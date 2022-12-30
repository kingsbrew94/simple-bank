<?php namespace FLY_ENV\Util\Neuro;

use FLY_ENV\Util\Routers\Pipe;
use FLY\MVC\View;
use FLY\Libs\Request;

/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @version 2.0.0
 * @package FLY_ENV\Util\Neuro
 */

final class Stage {

    private $pipe;

    private $controller_class;

    private $view_class;

    private $stage_context = null;

    private $fml_class;

    public function __construct(Pipe $pipe)
    {
        $this->pipe = $pipe;
        $this->stage_context = [];           
    }

    public function route_post_controller_request()
    {
        $this->route_controller_request();
    }

    public function route_cv_request()
    {
        $controller = $this->pipe->controller();
        $view = $this->pipe->view();
        $this->view_class = new $view();
        $this->init_route_cv_request($controller);
    }

    public function route_controller_template_request()
    {
        $controller = $this->pipe->controller();
        $this->view_class = new View;
        $this->init_route_controller_template_request($controller);
    }

    public function route_static_request()
    {
        $this->setParentView();
        $this->view_class->execute_template_request_render(
            $this->pipe->payload()            
        );       
    }
    
    public function route_controller_request()
    {
        $controller = $this->pipe->controller();
        $this->controller_class = new $controller();
        $this->setParentView();
        $method = $this->pipe->mvc_method();
        $response = $this->controller_class::$method(Request::instance()); //request params    
        
        if(isset($this->view_class) && $this->view_class !== null)
            $this->view_class->execute_post_get_request($response);   
    }

    private function setParentView()
    {
        $this->view_class = new View();
        $this->view_class->add_pipe($this->pipe); 
    }

    private function init_route_controller_template_request($controller)
    {
        $this->controller_class = new $controller();
        $method = $this->pipe->mvc_method();
        $this->controller_class->setView($this->view_class);
        $this->controller_class->getView()->add_pipe($this->pipe);
        $this->controller_class::$method(Request::instance()); //request params
        $this->check_rendering_state($this->controller_class);
    }

    private function init_route_cv_request($controller)
    {
        $this->load_context();
        $this->controller_class = new $controller();
        $method = $this->pipe->mvc_method();
        $this->controller_class->setView($this->view_class);
        $this->controller_class->setMVCMethod($method);
        $this->controller_class->getView()->add_pipe($this->pipe);
        if($this->pipe->fml_mode()) {
            $this->controller_class::$method(Request::instance()); //request params
            $this->fml_class = $this->controller_class->executeView();
            $this->load_context();
            $this->controller_class->setContext($this->stage_context);        
        } else {
            $this->fml_class = $this->controller_class->executeView();
            $this->load_context();
            $this->controller_class->setContext($this->stage_context);        
            $this->controller_class::$method(Request::instance()); //request params
        }
        $this->check_rendering_state($this->controller_class);
    }

    private function check_rendering_state($controller_class)
    {
        if($controller_class->renderCountState() > 1) {
            $errmsg = "render_view() method must be called once in each method of Class ".$this->pipe->controller();
            throw new \Exception($errmsg);
        }
        if($controller_class->renderCountState() === 1) {
            $this->complete_mvc_request($controller_class);
        }
    }

    private function load_context()
    {
        if(array_key_exists('context',get_class_vars($this->pipe->view())) && !$this->view_class->fresh_context_isset()) {
            $this->stage_context = array_merge(
                $this->stage_context,
                !is_array($this->view_class::$context) ? [] : $this->view_class::$context
            );              
        } else {
            $this->stage_context = $this->view_class::$context;
        }
    }
    
    private function complete_mvc_request($controller_class)
    {   
        if($this->pipe->fml_mode() === true) {
            $view = $controller_class->getView();
            $view->setApplicationPayload($this->stage_context);
            $this->init_fml_object($view);
            $view->fmlExecute($this->pipe->view(),$this->pipe->mvc_method());
            
        } else $controller_class->getView()->execute_template_request_render($this->stage_context);
    }

    private function init_fml_object($view)
    {
        if(isset($this->fml_class)) {
            if(is_object($this->fml_class)) {
                $view->render_fml(get_class($this->fml_class));
            } else {
                $error_message  = 'Expects method '.$this->pipe->mvc_method().' of class ';
                $error_message .= $this->pipe->view(). ' to return FLY\DOM\Application instance';
                throw new \Exception($error_message);
            }
        }
    }
}
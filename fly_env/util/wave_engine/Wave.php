<?php namespace FLY_ENV\Util\Wave_Engine;

use FLY\MVC\View;
use FLY\Security\Sessions;

class Wave {

    private $view;

    private $pipe;

    private $content;

    private $fml_mode;

    public function __construct(View $view)
    {
        $this->view = $view;
        $this->pipe = $this->view->get_pipe();
        $path = $this->pipe->template_path();
        $this->fml_mode = $view::_fmlIsActive();
        if(!file_exists($path)) throw new \Exception('Error: File '.$path.' was not found');
        else 
            $this->content = file_get_contents($path); 
    }

    public function interpret()
    {    
        $this->getTranslation();
    }

    private function getTranslation()
    {
        $this->express_csrf();
        $translate = (new FML($this->content,$this->fml_mode));
        $this->content = $translate->listen();
        $this->express_csrf();
        $this->view->set_wave_payload(FMLTranslator::get_payload());
        $translate = (new ExpTranslator($this->content));
        $translate->translate();
        $this->content = $translate->listen();
        
        $translate = (new SyntaxTranslator($this->content));
        $translate->translate();
        $this->content = $translate->listen();
    }

    public function get_template()
    {
        return $this->content;
    }

    private function express_csrf()
    {
        new Sessions;
        $pattern = '/{~(?:\s*)(?:@CSRF|@csrf)(?:\s*)~}/';
        $csrf = '{~ csrf_token() ~}';
        $this->content = preg_replace(
            $pattern,
            $csrf,
            $this->content
        );
    }
}
<?php namespace FLY\MVC;

use App\Models\Model_Handles;
use FLY\Libs\Request;

class Controller {

    private static $view;
    
    private static $mvc_method;

    private static $payload = [];

    private static $renderCount = 0;

    private static $context;

    protected static $state = [];
    
    use Model_Handles;

    public function __construct()
    {
        self::$payload = [];
        if(method_exists($this, 'main'))   $this->main();

        if(method_exists($this, '__init__')) $this->__init__(Request::instance());
    }

    public function __destruct() 
    {
        if(method_exists($this,'__deinit__')) $this->__deinit__();
    }

    protected function __deinit__() {/** Overridable Method */}

    protected function __init__(Request $request) { /** Overridable Method */ }

    public function setView(View $view) 
    {
        self::$view = $view;
    }

    public function getView()
    {
        return self::$view;
    }

    final static protected function render_view(array $context = null)
    {
        if($context !== null && is_array($context)) {
            if(self::$renderCount === 0)
                ++self::$renderCount;
            self::add_context($context);
        } else {
            ++self::$renderCount;
        }
    }

    public function renderCountState()
    {
        return self::$renderCount;
    }

    final static protected function add_context(array $data)
    {
        $keys = array_keys($data);
        
        foreach($keys as $key) {
            if(\is_int($key)) throw new \Exception('Payload varible keys must not be a number');
            continue;
        }
        self::$payload = array_merge(self::$payload, $data);
        if(self::$view === null) self::$view = new View;
        self::$view->setCurrentData(self::$payload);
    }

    final static protected function get_context()
    {
        return self::$context;
    }

    final public function setContext($context)
    {
        self::$context = [];

        if(!empty($context)) self::$context = $context;

        return self::$context;
    }
    
    public function setMVCMethod($method)
    {
        self::$mvc_method = $method;
    }
    
    public function executeView()
    {
        $method = self::$mvc_method;
        return self::$view::$method();
    }

}
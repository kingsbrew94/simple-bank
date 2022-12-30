<?php namespace FLY\DOM;


class Build extends Widget {

    private $widget;
    private $app;
    public function __construct(Widget $app = null,array $widget, $version ='', $encoding = '')
    {
        parent::__construct();
        $this->app = $app;
        $this->set_widget($widget);
    }

    private function set_widget(array $widget) 
    {
        $root_element = 'fml_fragment';
        
        if($this->app !== null) {
            if($this->app->root_element_exists())
                $root_element = $this->app->get_root_element();
        }
    
        $this->widget = $this->tag($root_element)([
            'children' => $widget
        ]);
    }

    public function get_widget()
    {
        return $this->widget;
    }
}
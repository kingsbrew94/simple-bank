<?php namespace FLY_ENV\Util\Wave_Engine;


class FML {

    private $content;

    private $fml_active;

    public function __construct($content, $fml_mode=false)
    {
        $this->content = $content;
        $this->fml_active = $fml_mode;
        do{
            $this->transpile_empty_tags();
            $this->transpile_full_tags();
        }
        while(FMLTranslator::rotationFound());
 
       
        $this->cleanFML();
        $this->clearUninitilizedProps();
        $this->processFMLEntities();
        
    }

    private function transpile_full_tags()
    {   
        do {
            $transpiler = new FullTagsTranspiler($this->content,$this->fml_active);
            $this->content = $transpiler->translate();
        } while(FullTagsTranspiler::matchFound());
    }

    private function transpile_empty_tags()
    {
       do {
            $transpiler = new EmptyTagsTranspiler($this->content, $this->fml_active);
            $this->content = $transpiler->translate();
       } while(EmptyTagsTranspiler::matchFound());
    }

    public function listen()
    {
        return $this->content;
    }

    private function clearUninitilizedProps()
    {
        $props_pattern = '#\{[:](?:\s*)(?:str)[.](?:[a-zA-Z_][a-zA-Z_0-9]*)?(?:\s*)\}#';
        $props_text_pattern = '#\{[:](?:\s*)(?:val)[.](?:[a-zA-Z][a-zA-Z_0-9]*)?(?:\s*)\}#';
        $props_children_pattern = '#\{[:](?:\s*)(?:children)(?:\s*)\}#';
        $this->content = preg_replace($props_pattern,'""',$this->content);
        $this->content = preg_replace($props_text_pattern,'',$this->content);
        $this->content = preg_replace($props_children_pattern,'',$this->content);
    }

    private function cleanFML()
    {
        $pattern = "#(?=(?:</fml_fragment>))(?:<fml_fragment>)|(?:</fml_fragment>)#";
        $this->content = \preg_replace($pattern,'',$this->content);
    }

    private function processFMLEntities()
    {
        $this->content = str_replace('&fmleq;','=',$this->content);  
        $this->content = str_replace('&fmlqt;','"',$this->content);
        $this->content = str_replace('&fmlsqt;',"'",$this->content);
        $this->content = str_replace('&fmllcb;','{',$this->content);
        $this->content = str_replace('#fmllcb;','{',$this->content);
        $this->content = str_replace('&fmlrcb;','}',$this->content); 
        $this->content = str_replace('#fmlrcb;','}',$this->content);
        $this->content = str_replace('#wvfmllcb;','{#',$this->content);
        $this->content = str_replace('#wvfmlrcb;','#}',$this->content);
    }

}
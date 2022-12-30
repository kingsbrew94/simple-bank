<?php namespace FLY_ENV\Util\Wave_Engine;

/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @package FLY_ENV\Util\Wave_Engine
*/

abstract class FMLTranslator {

    protected $content;

    protected $tagNamespace = "wv";

    protected $tagData = "";

    protected $matchedTagName = "";

    protected $matchedTag = "";

    protected $quotedAttrTag = false;
         
    protected $fml_activated = false;

    private $tag_token_props = array();

    private $component_url = "";

    static private $payload = array();

    static protected $hasAttributes = false;

    static protected $rotateETT = false;

    static protected $rotateFTT = false;

    static protected $match_found = false;

    public function __construct(string $content, $fml_mode=false)
    {
        $this->content = $this->encapsulateEscapeEntities($content);
        $this->fml_activated = $fml_mode;  
        $this->parseFMLEntities();

        Pattern::setNamespace('wv');  
        $this->calculateFMLTagAttrDepth();
    }

    private function parseFMLEntities()
    {
        if($this->fml_activated) {
            $this->content = html_entity_decode($this->content);
        }
    }
    private function calculateFMLTagAttrDepth()
    {
        if(preg_match(Pattern::fmlTagToken(),$this->content,$match)) 
            Pattern::setCurlTokenDepth($match[0]);
        else Pattern::setCurlTokenDepth('}{}{');
    }

    private function encapsulateEscapeEntities($content)
    {
        return $this->parseHashTag($content);
    }
    
    private function parseHashTag($content): string
    {
        $content = str_replace('\{',"&fmllcb;",$content);
        $content = str_replace('\}',"&fmlrcb;",$content);
        $content = str_replace('\"',"&fmlqt;",$content);
        return str_replace("'","&fmlsqt;",$content);
    }

    static function rotationFound()
    {
        return self::$rotateETT || self::$rotateFTT;
    }

    public function listen()
    {
        return $this->content;
    }

    protected function process()
    {
        if($this->file_is_wave($this->matchedTagName)) {
            $this->match_tag_tokens();
            return $this->transpile();
        } else {
            throw new \Exception('Unable to locate wave component at '.'"'.$this->component_url().'" was not found');
        }
    }

    static public function get_payload()
    {
        return self::$payload;
    }

    static public function matchFound()
    {
        return static::$match_found;
    }

    protected function match_tag_tokens()
    {  
        $this->parseTagData();
        if($this->quotedAttrTag && self::$hasAttributes) 
            $this->fetchQuotedTagProps();
        else if(self::$hasAttributes) 
            $this->fetchCurlyTagProps();
    }

    private function fetchCurlyTagProps() 
    { 
        while(preg_match(Pattern::curlyFullAttributeMatch(),$this->tagData,$match)) {
    
            if(preg_match(Pattern::findAttributeValue(),$match[2], $mt)) {
                $match[2] = $mt[1];
            }
            $this->tag_token_props[trim($match[1])] = $match[2];
            $this->tagData = str_replace($match[0],'',$this->tagData);
        }
        while(preg_match(Pattern::curlyTagParamsMatch(),$this->tagData,$match)) {
            $temp_err = explode(',',$match[1]);

            foreach($temp_err as $param) {
                $param = trim($param);
                $this->tag_token_props[$param] = "$"."$param";                
            }
            $this->tagData = str_replace($match[0],'',$this->tagData);
        }
        while(preg_match(Pattern::curlyFalseBoolAttrMatch(),$this->tagData,$match)) {
            $this->tag_token_props[trim($match[1])] = 0;
            $this->tagData = str_replace($match[0],'',$this->tagData);
        }
        while(preg_match(Pattern::curlyTrueBoolAttrMatch(),$this->tagData,$match)) {
            $this->tag_token_props[trim($match[1])] = TRUE;
            $this->tagData = str_replace($match[0],'',$this->tagData);
        }
    }

    private function fetchQuotedTagProps()
    {
        while(preg_match(Pattern::quotedFullAttributeMatch(),$this->tagData,$match)) {
            $this->tag_token_props[trim($match[1])] = $match[2];
            $this->tagData = str_replace($match[0],'',$this->tagData);
        }
    }

    protected function parse_child_url(string $matchedTagName)
    {
        $matchedTagName = str_replace('.','/',$matchedTagName);
        $matchedTagName = FLY_ENV_STATIC_HTMLS_PATH.$matchedTagName;
        $this->component_url = $matchedTagName;
        return $matchedTagName;
    }

    private function parseTagData()
    {
        $this->tagData = preg_replace('#(?:\n+|\s+)#',' ',$this->tagData);
    }

    protected function component_url()
    {
        return $this->component_url;
    }

    protected function file_is_wave($matchedTagName)
    {
        return file_exists($this->parse_child_url($matchedTagName).'.wave.php');
    }
    
    protected function escape_slashes($str)
    {
        return str_replace('/','\/',$str);
    }

    protected function assign_tokens_to_sub_page($child_page_content)
    {
        foreach($this->tag_token_props as $key => $value) {
            $props_pattern = '#\{[:](?:\s*)str[.]'.$key.'(?:\s*)\}#';
            $props_text_pattern = '#\{[:](?:\s*)val[.]'.$key.'(?:\s*)\}#';
            $child_page_content = preg_replace($props_pattern,'&fmlqt;'.$value.'&fmlqt;',$child_page_content);
            $child_page_content = preg_replace($props_pattern,'&fmlsqt;'.$value.'&fmlsqt;',$child_page_content);
            $child_page_content = preg_replace($props_text_pattern,$value,$child_page_content);                
        }
        array_push(self::$payload,$this->tag_token_props);
        return $child_page_content;
    }

    protected function reset_content($matched_tag,$child_page_content)
    {
        $child_page_content = str_replace("&fmleq;",'=',$child_page_content);
        return str_replace($matched_tag, $child_page_content, $this->content);
    }
    
    abstract protected function setTagProps(array $tag_props);
    
    abstract protected function findMatch(): array;

    abstract protected function transpile(): string;

    abstract protected function translate(): string;
}
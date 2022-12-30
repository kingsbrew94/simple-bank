<?php namespace FLY_ENV\Util\Wave_Engine;

class FullTagsTranspiler extends FMLTranslator {

    private $tagChildren = "";

    public function translate(): string
    {
        $tag_props = $this->findMatch();

        if(static::$match_found) {
            $this->setTagProps($tag_props);
            self::$rotateETT = true;
           return $this->process();
        }
        self::$rotateFTT = false;
        return $this->content;
    }

    protected function setTagProps(array $tag_props)
    {
        if(self::$hasAttributes) 
           $this->tagData = $tag_props[2];      
        $this->matchedTagName = $tag_props[1];
        $this->matchedTag     = $tag_props[0];
        $lastIndex = count($tag_props) - 1;
        $this->tagChildren = $tag_props[$lastIndex];
    }

    protected function findMatch(): array
    {   
        static::$match_found = preg_match(Pattern::getCurlyFullTagMatch(),$this->content, $match);
        if(!static::$match_found) {
            static::$match_found = preg_match(Pattern::getShallowCurlyFullTagMatch(),$this->content, $match);
        }
        if(!static::$match_found) {
            static::$match_found = preg_match(Pattern::nonAttributesCurlyFullTagMatch(),$this->content, $match);
        }
        if(!static::$match_found && $this->fml_activated) {
            static::$match_found = preg_match(Pattern::getQuoteFullTagMatch(),$this->content,$match);
            $this->quotedAttrTag = static::$match_found;
        }
        self::$hasAttributes = isset($match[2]);
      
        
        return $match;
    }

    protected function transpile(): string
    {
        $child_page_content = file_get_contents($this->component_url().'.wave.php');
        $child_page_content = $this->assign_tokens_to_sub_page($child_page_content);
        $child_page_content = $this->assign_children_to_sub_page($child_page_content);
        return $this->reset_content($this->matchedTag,$child_page_content);
    }

    private function assign_children_to_sub_page($child_page_content)
    {
        $pattern = '#(?:\s*)\{[:](?:\s*)children(?:\s*)\}#';
        $child_page_content = preg_replace($pattern,$this->tagChildren,$child_page_content);
        return $child_page_content;
    }
}
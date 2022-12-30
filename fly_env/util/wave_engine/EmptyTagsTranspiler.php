<?php namespace FLY_ENV\Util\Wave_Engine;


class EmptyTagsTranspiler extends FMLTranslator {

    public function translate(): string
    {
        $tag_props = $this->findMatch();
        if(self::$match_found) {
            $this->setTagProps($tag_props);
            self::$rotateFTT = true;
            return $this->process();
        }
        self::$rotateETT = false;
        return $this->content;
    }

    protected function setTagProps(array $tag_props)
    {
        if(self::$hasAttributes) 
           $this->tagData = $tag_props[2];      
        $this->matchedTagName = $tag_props[1];
        $this->matchedTag = $tag_props[0];
    }

    protected function findMatch(): array
    {
        self::$match_found = preg_match(Pattern::getCurlyEmptyTagMatch(),$this->content, $match);
        if(!self::$match_found) {
            self::$match_found = preg_match(Pattern::getShallowCurlyEmptyTagMatch(),$this->content, $match);
        }
        if(!static::$match_found) {
            static::$match_found = preg_match(Pattern::nonAttributeCurlyEmptyTagMatch(),$this->content, $match);
        }
        if(!self::$match_found && $this->fml_activated) {
            self::$match_found = preg_match(Pattern::getQuoteEmptyTagMatch(),$this->content,$match);
            $this->quotedAttrTag = self::$match_found;
        } 
        self::$hasAttributes = isset($match[2]);
        return $match;
    }

    protected function transpile(): string
    {
        $child_page_content = file_get_contents($this->component_url().'.wave.php');
        $child_page_content = $this->assign_tokens_to_sub_page($child_page_content);
        return $this->reset_content($this->matchedTag,$child_page_content);
    }
}
<?php namespace FLY_ENV\Util\Wave_Engine;


class ExpTranslator {

    private $content;

    private $private_content;

    public function __construct(string $content, $flag = true)
    {
        if($flag) $this->content = $content;
        else $this->private_content = $content;
    }

    public function translate()
    {
        while(preg_match(Pattern::templateExpPattern(),$this->content)) 
        {
            $this->content = preg_replace(
                Pattern::templateExpPattern(),
                Pattern::syntaxTemplateExp(),
                $this->content
            );
        }

        while(preg_match(Pattern::templateVarExpPattern(),$this->content)) 
        {
            $this->content = preg_replace(
                Pattern::templateVarExpPattern(),
                Pattern::syntaxTemplateVar(),
                $this->content
            );
        }
    } 

    public function listen()
    {
        return $this->content;
    }
}
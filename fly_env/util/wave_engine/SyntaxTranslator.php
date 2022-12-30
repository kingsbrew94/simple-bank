<?php namespace FLY_ENV\Util\Wave_Engine;

class SyntaxTranslator {

    private $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function translate()
    {
        $this->handle_semantics();
    }

    public function listen()
    {
        return $this->content;
    }

    private function handle_semantics()
    {
    
        $this->handle_loops();
        $this->handle_conditions();
        $this->handle_modules();
        $this->handle_returns();
        $this->handle_methods();
        $this->handle_js_css_templates();
    }

    private function handle_js_css_templates()
    {
        while(preg_match(Pattern::templateJavaScript(),$this->content,$match)) {
            $this->content = preg_replace(
                Pattern::templateJavaScript(),
                "
                    <script>
                        {$match[2]}
                    </script>
                ",
                $this->content
            );
        }

        while(preg_match(Pattern::templateCSS(),$this->content,$match)) {
            $this->content = preg_replace(
                Pattern::templateCSS(),
                "
                    <style>
                        {$match[2]}
                    </style>
                ",
                $this->content
            );
        }
    }

    private function setActions(string $type, $PHPSyntaxCallback)
    {
        while(preg_match(Pattern::templateBracedActionPattern($type),$this->content,$match))
        {
            $this->content = preg_replace(
                Pattern::templateBracedActionPattern($type),
                $PHPSyntaxCallback($match[1]),
                $this->content
            );
        }
        while(preg_match(Pattern::templateUnBracedActionPattern($type),$this->content,$match))
        {
            $this->content = preg_replace(
                Pattern::templateUnBracedActionPattern($type),
                $PHPSyntaxCallback($match[1]),
                $this->content
            );   
        }
    }

    private function setOtherAction(string $type,string $PHPSyntax)
    {
        while(preg_match(Pattern::templateSubBracedActionPattern($type),$this->content))
        {
            $this->content = preg_replace(
                Pattern::templateSubBracedActionPattern($type),
                $PHPSyntax,
                $this->content
            );
        }
        while(preg_match(Pattern::templateSubUnBracedActionPattern($type),$this->content))
        {
            $this->content = preg_replace(
                Pattern::templateSubUnBracedActionPattern($type),
                $PHPSyntax,
                $this->content
            ); 
        }
    }

    private function parsePTN($typePattern, $PHPSyntax)
    {
        $this->content = preg_replace(Pattern::ptn("[@]{$typePattern}"),$PHPSyntax, $this->content);
    }

    private function handle_conditions()
    {
       $this->setActions('if',Pattern::syntaxConditionAction());
       $this->setOtherAction('elif',Pattern::syntaxElseIfAction());
       $this->parsePTN('else\s*[:]',Pattern::syntaxElseAction());
    }

    private function handle_loops()
    {
        $this->setActions('each',Pattern::syntaxEachLoopAction());
        $this->setActions('for',Pattern::syntaxLoopAction());
        $this->setActions('while',Pattern::syntaxLoopAction());
        $this->content = preg_replace(Pattern::ptn('[@]thenend(?:\s+)'),'<?php break; ?>', $this->content);
        $this->content = preg_replace(Pattern::ptn('[@]thenskip(?:\s+)(?:[1-9]*)?(?:\s*)'),'<?php continue; ?>',$this->content);
    }

    private function handle_modules()
    {
        while(preg_match(Pattern::templateVoidFunctionPattern(),$this->content)) {
            $this->content = preg_replace(
                Pattern::templateVoidFunctionPattern(),
                Pattern::syntaxFunction(),
                $this->content
            );
        }
        $contents = preg_replace(Pattern::ptn('[@]return ((?:(?:\s*)(?:.*?)(?:\s*))*?)[;]'),'<?php return $1; ?>', $this->content);
        $this->content = $this->handle_namespaces($contents);
    }

    private function handle_namespaces($contents)
    {
        $contains_namespace = preg_match('%[@]namespace(?:\s+)((?:[a-zA-Z_][a-zA-Z0-9_:]+))(?:\s*)%',$contents,$match);
        if($contains_namespace) {
            $replacement = str_replace('::','\\',$match[1]);
            $pattern ='%[@]namespace(?:\s+)('.$match[1].')%';
            $contents = preg_replace($pattern,'<?php namespace '.$replacement.'; ?>', $contents);
        }
        $contains_namespace_use = preg_match('%[@]use(?:\s+)((?:[a-zA-Z_][a-zA-Z0-9_:]+))(?:\s*)%',$contents,$match);
        if($contains_namespace_use) {
            $replacement = str_replace('::','\\',$match[1]);
            $pattern ='%[@]use(?:\s+)('.$match[1].')%';
            $contents = preg_replace($pattern,'<?php use '.$replacement.'; ?>', $contents);
        }
        return $contents;
    }
    
    private function handle_methods()
    {
        while(preg_match('%[@]([a-zA-Z][a-zA-Z0-9]*)(?:\s*)\((?:[\w\d\s\W\S\D]*)\)%',$this->content, $match)) {
            $callbackString =  $this->callbackString(
                $this->parseTemplateCallers(
                    $this->parseTemplateCallers($match[0]),
                     "'"
                )
            );
            $this->parseTemplateCallback(
                $callbackString,
                $this->content
            );
            if(!\in_array($match[1], Dictionary::callerStacks())) {
                $tk = \str_replace('@','#fmlat;',$callbackString);
                $this->content = \str_replace($callbackString,$tk,$this->content);
                continue;
            }
        }
        $this->content = str_replace('#fmlat;','@',$this->content);
    }

    private function parseTemplateCallers($tokens,$quote_type = '"') 
    {
        $tokens_len = strlen($tokens);
        $track_double_quotes = 0;
        $new_tokens = "";
        for ($i=0; $i < $tokens_len; $i++) { 
            if(($tokens[$i] === $quote_type)) ++$track_double_quotes;

            if($track_double_quotes === 2) $track_double_quotes = 0;

            if($track_double_quotes > 0 && ((2 % $track_double_quotes) !== 0 || $track_double_quotes === 1) && $tokens[$i] === '(') {
                $new_tokens .="&fmlleftbrk;";                 
            } else if($track_double_quotes > 0 && ((2 % $track_double_quotes) !== 0 || $track_double_quotes === 1) && $tokens[$i] === ')') {
                $new_tokens .="&fmlrightbrk;";                 
            } else {
                $new_tokens .= $tokens[$i]; 
            }       
        }
        return $new_tokens;
    }
    
    private function parseTemplateCallback($callbackString, $contents)
    {
        $callbackString = str_replace('&fmlleftbrk;','(',$callbackString);
        $callbackString = str_replace('&fmlrightbrk;',')',$callbackString);
        $stacks = Dictionary::callerStacks();
        foreach($stacks as $caller) {
            $output = "<?= {$caller}($1); ?>";
            if(preg_match('/^[:]\s*([a-zA-Z_][a-zA-Z_0-9]*)/',$caller,$match)) {
                $caller = $match[1];
                $output = "<?php {$caller}($1); ?>";
            }
            $expression = preg_replace('%[@]'.$caller.'(?:\s*)\(([\w\d\s\W\S\D]*)\)%',$output, $callbackString);
            $contents   = \str_replace($callbackString,$expression,$contents);
        }
        $this->content = $contents;
    }

    private function callbackString($string) 
    {
        $length = strlen($string);
        $str = "";
        $rightBracketCount = 0;
        $leftBracketCount = 0;
        for($index = 0; $index < $length; $index++) {
            if(($leftBracketCount > 0 && $rightBracketCount > 0) && ($leftBracketCount === $rightBracketCount))
                break;
            $str .= $string[$index];
            if($string[$index] === '(') ++$leftBracketCount;
            else if($string[$index] === ')') ++$rightBracketCount;
        }
        return $str;
    }

    private function handle_returns()
    {
        $contents = preg_replace(Pattern::ptn('[@]return (.*)'),'<?php return $1; ?>', $this->content);
        $this->content = $contents;        
    }

}
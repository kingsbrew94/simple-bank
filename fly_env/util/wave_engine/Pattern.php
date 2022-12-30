<?php namespace FLY_ENV\Util\Wave_Engine;


class Pattern {
    
    static private $namespace = "";
    
    static private $curlTagAttVal = "";

    static public function setNamespace($namespace)
    {
        self::$namespace = $namespace;
    }

    static function setCurlTokenDepth($content)
    {
        $tokens = ['[{]([^\{\}])*[}]']; 
        self::$curlTagAttVal = $tokens[0];
        for($i=0; $i<self::countCurls($content); $i++) {
            if($i === 0) 
                array_push($tokens,'[{](?:[^\{\}]|'.$tokens[0].')*[}]');
            else if($i > 0) 
                array_push($tokens,'[{](?:[^\{\}]|'.$tokens[$i].')*[}]');      
            self::$curlTagAttVal = $tokens[$i + 1];
        }
    }

    static private function countCurls($content) 
    {
        $contLen = strlen($content);
        $lcurlIndex = strpos($content,'{');
        $rcurlIndex = strpos($content,'}');
        $counter = 0;

        if(is_int($lcurlIndex) || is_int($rcurlIndex)) {
            for($i = 0; $i < ($contLen % 247);  $i++) {
                $char = trim($content[$i]);
                if($char === '{' or $char === '}') ++$counter;
            }
        }
        return $counter;
    }
    
    static function fmlTagToken()
    {
        
        $nsp = self::$namespace;
        return '%
        (?=<'.$nsp.'[:](?:(?:[a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)))'.
            '(?:<'.$nsp.'[:](?:[a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)
                (?:[\w\d\s\W\S\D]*?)[>]
             )
        %xm';
    }

    static function nonAttributesCurlyFullTagMatch()
    {
        $nsp = self::$namespace;
        return '%
        (?=<'.$nsp.'[:](?:([a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)))'.
            '(?:<'.$nsp.'[:](?:[a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)>)     
                (
                    (?:[\w\d\s\W\S\D]*?)
                )                                                         # lazy search tag children
            </'.$nsp.'[:]\1>                                          
        %xm';
    }

    static function getShallowCurlyFullTagMatch()
    {
        $nsp = self::$namespace;
        return '%
        (?=<'.$nsp.'[:](?:([a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)))'.
            '(?:<'.$nsp.'[:](?:[a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)
                (
                    (?: 
                        (?:
                            (?:\s*)(?:[!]?\s*[a-zA-Z_][a-zA-Z0-9_-]*)(?:\s*)               # get attribute key
                            (?:(?:=)?(?:\s*)
                                (?:
                                    (?:
                                        (?:
                                            (?:[{])((?:[^{}][}]?)*)(?:[}])
                                        )
                                    )
                                )
                            )?  # lazy search attribute value
                        )*(?:\s*) 
                        | 
                        (?:[{](?:\s*)[a-zA-Z_][a-zA-Z0-9_,\s]*(?:\s*)[}]))*)?(?:\s*)>
                )                                               # get end of the starting tag
                (
                    (?:[\w\d\s\W\S\D]*?)
                )                                                         # lazy search tag children
            </'.$nsp.'[:]\1>                                          
        %xm';
    }

    static function getCurlyFullTagMatch()
    {
        $nsp = self::$namespace;
        return '%
        (?=<'.$nsp.'[:](?:([a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)))'.
            '(?:<'.$nsp.'[:](?:[a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)
                (
                    (?: 
                        (?:
                            (?:\s*)(?:[!]?\s*[a-zA-Z_][a-zA-Z0-9_-]*)(?:\s*)               # get attribute key
                            (?:(?:=)?(?:\s*)
                                (?:
                                    (?:
                                        (?:
                                            (?:[{])((?:[^{}][}]?|'.self::$curlTagAttVal.')*)(?:[}])
                                        )
                                    )
                                )
                            )?  # lazy search attribute value
                        )*(?:\s*) 
                        | 
                        (?:[{](?:\s*)[a-zA-Z_][a-zA-Z0-9_,\s]*(?:\s*)[}]))*)?(?:\s*)>
                )                                               # get end of the starting tag
                (
                    (?:[\w\d\s\W\S\D]*?)
                )                                                         # lazy search tag children
            </'.$nsp.'[:]\1>                                          
        %xm';
    }

    static function getQuoteFullTagMatch()
    {
        $nsp = self::$namespace;
        return '%
            (?=<'.$nsp.'[:](?:([a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)))'.
                '(?:<'.$nsp.'[:](?:[a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)
                (
                    (?: 
                        (?:
                            (?:\s*)(?:[a-zA-Z_][a-zA-Z0-9_-]*)(?:\s*)               # get attribute key
                            (?:(?:=)?(?:\s*)
                                (?:
                                    (?:
                                        (?:
                                            (?:["])(?:[^\"]*)(?:["])
                                        )
                                    )
                                )
                            )?  # lazy search attribute value
                        )*(?:\s*) 
                    )*)?(?:\s*)>
                )                            # get end of the starting tag
                (
                    (?:[\w\d\s\W\S\D]*?)
                )                            # lazy search tag children
            </'.$nsp.'[:]\1>                                          
        %xm';
    }

    static public function waveVar() 
    {
        return '%
          (?:[{][#])((?:[\w\d\s\W\S\D]*?))(?:[#][}])
        %xm';
    }

    static public function nonAttributeCurlyEmptyTagMatch()
    {
        $nsp = self::$namespace;
        return '%
        (?=<'.$nsp.'[:](?:([a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)))'.
            '(?:<'.$nsp.'[:](?:[a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)/>)
        %xm';
    }

    static public function getShallowCurlyEmptyTagMatch()
    {
        $nsp = self::$namespace;
        return '%
        (?=<'.$nsp.'[:](?:([a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)))'.
            '(?:<'.$nsp.'[:](?:[a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)
                ((?: 
                    (?:
                        (?:\s*)(?:[!]?\s*[a-zA-Z_][a-zA-Z0-9_-]*)(?:\s*)               # get attribute key
                        (?:(?:=)?(?:\s*)
                            (?:
                                (?:
                                    (?:
                                        (?:[{])((?:[^{}][}]?)*)(?:[}])
                                    )
                                )
                            )
                        )?  # lazy search attribute value
                    )*(?:\s*) 
                    | 
                    (?:[{](?:\s*)[a-zA-Z_][a-zA-Z0-9_,\s]*(?:\s*)[}]))*)?(?:\s*)/>
            )
        %xm';
    }

    static public function getCurlyEmptyTagMatch()
    {
        $nsp = self::$namespace;
        return '%
        (?=<'.$nsp.'[:](?:([a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)))'.
            '(?:<'.$nsp.'[:](?:[a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)
                ((?: 
                    (?:
                        (?:\s*)(?:[!]?\s*[a-zA-Z_][a-zA-Z0-9_-]*)(?:\s*)               # get attribute key
                        (?:(?:=)?(?:\s*)
                            (?:
                                (?:
                                    (?:
                                        (?:[{])((?:[^{}][}]?|'.self::$curlTagAttVal.')*)(?:[}])
                                    )
                                )
                            )
                        )?  # lazy search attribute value
                    )*(?:\s*) 
                    | 
                    (?:[{](?:\s*)[a-zA-Z_][a-zA-Z0-9_,\s]*(?:\s*)[}]))*)?(?:\s*)/>
            )
        %xm';
    }

    static public function getQuoteEmptyTagMatch()
    {
        $nsp = self::$namespace;
        return '%
        (?=<'.$nsp.'[:](?:([a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)))'.
            '(?:<'.$nsp.'[:](?:[a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)
                ((?: 
                    (?:
                        (?:\s*)(?:[a-zA-Z_][a-zA-Z0-9_-]*)(?:\s*)               # get attribute key
                        (?:(?:=)?(?:\s*)
                            (?:
                                (?:
                                    (?:
                                        (?:["])(?:[^\"]*)(?:["])
                                    )
                                )
                            )
                        )?  # lazy search attribute value
                    )*(?:\s*) 
                )*)?(?:\s*)/>
            )
        %xm';
    }

    static public function curlyFullAttributeMatch() 
    {
        return '%
        (?:
            (?:\s*)([a-zA-Z_][a-zA-Z0-9_-]*)(?:\s*)               # get attribute key
            (?:(?:=)(?:\s*)
                (?:
                    (?:
                        (?:
                            (?:[{])((?:[^{}][}]?|'.self::$curlTagAttVal.')*)(?:[}])
                        )
                    )
                )
            )  # lazy search attribute value
        )
        %xm';
    }

    static public function findAttributeValue() {
        return '%
            (?:[&][&]fmlsqt[;][{](.*)[}])
        %xm';
    }

    static public function curlyTagParamsMatch()
    {
        return '%
            (?:
                [{](?:\s*)([a-zA-Z_][a-zA-Z0-9_,\s]*)(?:\s*)[}]
            )
        %xm';
    }

    static public function curlyFalseBoolAttrMatch()
    {
        return '%
            (?:\s*)[!]\s*([a-zA-Z_][a-zA-Z0-9_-]*)(?:\s*)
        %xm';
    }

    static public function curlyTrueBoolAttrMatch()
    {
        return '%
            (?:\s*)([a-zA-Z_][a-zA-Z0-9_-]*)(?:\s*)
        %xm';
    }

    static public function quotedFullAttributeMatch() 
    {
        return '%
        (?:
            (?:\s*)([a-zA-Z_][a-zA-Z0-9_-]*)(?:\s*)               # get attribute key
            (?:(?:=)(?:\s*)
                (?:
                    (?:
                        (?:
                            (?:["])([^\"]*)(?:["])
                        )
                    )
                )
            )  # lazy search attribute value
        )
        %xm';
    }

    static public function validationDataFields()
    {
        return '%
            (?:
                (?:\s*)\:(?:\s*)min\s*[|]\s*(?:[0-9]*)\s*[|]                 |
                (?:\s*)\:(?:\s*)max\s*[|]\s*(?:[0-9]*)\s*[|]                 |
                (?:\s*)\:(?:\s*)[|]\s*(?:[0-9]*)\s*[|]                       |
                (?:\s*)\:(?:\s*)text                                         |
                (?:\s*)\:(?:\s*)email                                        |
                (?:\s*)\:(?:\s*)url                                          |
                (?:\s*)\:(?:\s*)ip                                           |
                (?:\s*)\:(?:\s*)salphaNum                                    |
                (?:\s*)\:(?:\s*)alphaNum                                     |
                (?:\s*)\:(?:\s*)alpha                                        |      
                (?:\s*)\:(?:\s*)tel                                          |
                (?:\s*)\:(?:\s*)unum                                         |
                (?:\s*)\:(?:\s*)snum                                         |
                (?:\s*)\:(?:\s*)natNum                                       |
                (?:\s*)\:(?:\s*)num                                          |
                (?:\s*)\:(?:\s*)int                                          |
                (?:\s*)\:(?:\s*)float                                        |
                (?:\s*)\:(?:\s*)double                                       |
                (?:\s*)\:(?:\s*)date                                         |
                (?:\s*)\:(?:\s*)time                                         |
                (?:\s*)\:(?:\s*)dateTime                                     |
                (?:\s*)\:(?:\s*)bool                                         |
                (?:\s*)(?:\:)(?:\s*)\(((?:[\w\d\s\W\S\D]*?))\)               |
                (?:\s*)(?:\:)(?:\s*)\{((?:[\w\d\s\W\S\D]*?))\}               |
                (?:\s*)(?:\:)(?:\s*)\%([\w\d\s\W\S\D]*)                      |
                (?:\s*)\:(?:\s*)[?]\s*min\s*[|]\s*(?:[0-9]*)\s*[|]           |
                (?:\s*)\:(?:\s*)[?]\s*max\s*[|]\s*(?:[0-9]*)\s*[|]           |
                (?:\s*)\:(?:\s*)[?]\s*[|]\s*(?:[0-9]*)\s*[|]                 |
                (?:\s*)\:(?:\s*)[?](?:\s*)text                               |
                (?:\s*)\:(?:\s*)[?](?:\s*)email                              |
                (?:\s*)\:(?:\s*)[?](?:\s*)url                                |
                (?:\s*)\:(?:\s*)[?](?:\s*)ip                                 |
                (?:\s*)\:(?:\s*)[?](?:\s*)salphaNum                          |
                (?:\s*)\:(?:\s*)[?](?:\s*)alphaNum                           |
                (?:\s*)\:(?:\s*)[?](?:\s*)alpha                              |
                (?:\s*)\:(?:\s*)[?](?:\s*)tel                                |
                (?:\s*)\:(?:\s*)[?](?:\s*)unum                               |
                (?:\s*)\:(?:\s*)[?](?:\s*)snum                               |
                (?:\s*)\:(?:\s*)[?](?:\s*)natNum                             |
                (?:\s*)\:(?:\s*)[?](?:\s*)num                                |
                (?:\s*)\:(?:\s*)[?](?:\s*)int                                |
                (?:\s*)\:(?:\s*)[?](?:\s*)float                              |
                (?:\s*)\:(?:\s*)[?](?:\s*)double                             |
                (?:\s*)\:(?:\s*)[?](?:\s*)time                               |
                (?:\s*)\:(?:\s*)[?](?:\s*)date                               |
                (?:\s*)\:(?:\s*)[?](?:\s*)dateTime                           |
                (?:\s*)\:(?:\s*)[?](?:\s*)bool                               |
                (?:\s*)(?:\:)(?:\s*)(?:[?])(?:\s*)\(((?:[\w\d\s\W\S\D]*?))\) |
                (?:\s*)(?:\:)(?:\s*)(?:[?])(?:\s*)\{((?:[\w\d\s\W\S\D]*?))\} |
                (?:\s*)(?:\:)(?:\s*)(?:[?])(?:\s*)\%([\w\d\s\W\S\D]*)
            )
        %ixm';
    }

    static public function templateVoidFunctionPattern()
    {
        $pat = '(?:["\'](?:[\w\W]*?)(?:[)]\s*[:])?(?:[\w\W]*?)["\'][\w\W\d\D\s\S]*?)?';
        return '%
        (?=@def\s+[_a-zA-Z][_a-zA-Z0-9]*\s*[(])
        (?![)]\s*[:])
        @(def)\s+([_a-zA-Z][_a-zA-Z0-9]*)\s*[(]
        ((?:[\w\W\d\D\s\S]*?'.
         $pat
        .')+?)\s*[)]\s*[:]
        ([\w\d\s\W\S\D]*?)
        @en\1
        %xm';
    }

    static public function syntaxFunction()
    {
        return '
            <?php function $2($3) { ?>
            $4
            <?php } ?>
        ';
    }

    static public function ptn($type)
    {
        return '%'.$type.'%xm';
    }

    static public function templateBracedActionPattern($type)
    {
        $pat = '(?:["\'](?:[\w\W]*?)(?:[)]\s*[:])?(?:[\w\W]*?)["\'][\w\W\d\D\s\S]*?)?';

        return '%
        (?=@'.$type.'\s*[(])
        (?![)]\s*[:])
        @('.$type.')\s*[(]
        ((?:[\w\W\d\D\s\S]*?'.
         $pat
        .')+?)\s*[)]\s*[:]
        ([\w\d\s\W\S\D]*?)
        @end\1
        %xm';
    }

    static public function templateJavaScript()
    {
        return '%
        (?=@js\s*[:])
        @(js)\s*[:]
        ([\w\d\s\W\S\D]*?)
        @end\1
        %xm';
    }

    static public function templateCSS()
    {
        return '%
        (?=@css\s*[:])
        @(css)\s*[:]
        ([\w\d\s\W\S\D]*?)
        @end\1
        %xm';
    }

    static function templateSubBracedActionPattern($type)
    {
        $pat = '(?:["\'](?:[\w\W]*?)(?:[)]\s*[:])?(?:[\w\W]*?)["\'][\w\W\d\D\s\S]*?)?';

        return '%
        (?=@'.$type.'\s*[(])
        (?![)]\s*[:])
        @(?:'.$type.')\s*[(]
        ((?:[\w\W\d\D\s\S]*?'.
         $pat
        .')+?)\s*[)]\s*[:]
        %xm';
    }

    static public function syntaxConditionAction()
    {
        return function($type) {
            return
            '
            <?php '.$type.'($2): ?>
                $3
                <?php end'.$type.' ?>
            ';
        };
    }

    static public function syntaxElseIfAction()
    {

        return
        '
            <?php elseif ($1): ?>
        ';
    }

    static public function syntaxElseAction()
    {
        return '<?php else: ?>';
    }


    static public function syntaxEachLoopAction()
    {
        return function($type) {

            return
            '
            <?php for'.$type.'($2){ ?>
                $3
                <?php } ?>
            '; 
        };
    }

    static public function syntaxLoopAction()
    {
        return function($type) {
            return 
            '
            <?php '.$type.'($2){ ?>
                $3
                <?php } ?>
            ';
        };
    }

    static public function templateUnbracedActionPattern($type)
    {
        $pat = '(?:["\'](?:[\w\W]*?)(?:[)]\s*[:])?(?:[\w\W]*?)["\'][\w\W\d\D\s\S]*?)?';

        return '%
        (?=@'.$type.'\s+)
        (?!\s*[:])
        @('.$type.')\s+
        ((?:[\w\W\d\D\s\S]*?'.
         $pat
        .')+?)\s*[:]
        ([\w\d\s\W\S\D]*?)
        @end\1
        %xm';
    }

    static public function templateSubUnbracedActionPattern($type)
    {
        $pat = '(?:["\'](?:[\w\W]*?)(?:[)]\s*[:])?(?:[\w\W]*?)["\'][\w\W\d\D\s\S]*?)?';

        return '%
        (?=@'.$type.'\s+)
        (?!\s*[:])
        @(?:'.$type.')\s+
        ((?:[\w\W\d\D\s\S]*?'.
         $pat
        .')+?)\s*[:]
        %xm';
    }
    
    static public function templateVarExpPattern()
    {
        $pat = '(?:["\'](?:[\w\W]*?)(?:\#\s*\})?(?:[\w\W]*?)["\'][\w\W\d\D\s\S]*?)?';
        return '%
        (?=\{\~\s*)
        (?!\~\})
        (?:\{\~)\s*
        (
            (?:[\w\W\d\D\s\S]*?)'.$pat.'
        )\s*(?:\~\})
        
        %xm';
    }

    static public function templateExpPattern()
    {
        $pat = '(?:["\'](?:[\w\W]*?)(?:\!\s*\})?(?:[\w\W]*?)["\'][\w\W\d\D\s\S]*?)?';
        return '%
        (?=[$]\{\~\s*)
        (?!\~\})
        (?:[$]\{\~)\s*
        (
            (?:[\w\W\d\D\s\S]*?)'.$pat.'
        )\s*(?:\~\})
        
        %xm';
    }

    static public function syntaxTemplateVar()
    {
        return '<?= $1 ?>';
    }

    static public function syntaxTemplateExp()
    {
        return '<?php $1; ?>';
    }

    static public function htmlEmptyDataTag()
    {
        return '%
        (?=<(?:(?:[a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)))'.
            '(?:<(?:[a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)
                ((?: 
                    (?:
                        (?:\s*)(?:[a-zA-Z_][a-zA-Z0-9_-]*)(?:\s*)               # get attribute key
                        (?:(?:=)?(?:\s*)
                            (?:
                                (?:
                                    (?:
                                        (?:["])(?:[^\"]*)(?:["])
                                    )
                                )
                            )
                        )?  # lazy search attribute value
                    )*(?:\s*) 
                )*)?(?:\s*)[/]?>
            )
        %xm';
    }

    static public function htmlFullDataTag()
    {
        return '%
            (?=<(?:([a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)))'.
                '(?:<(?:[a-zA-Z_][a-zA-Z0-9_.-]*)(?:\s*)
                (?:
                    (?: 
                        (?:
                            (?:\s*)(?:[a-zA-Z_][a-zA-Z0-9_-]*)(?:\s*)               # get attribute key
                            (?:(?:=)?(?:\s*)
                                (?:
                                    (?:
                                        (?:
                                            (?:["])(?:[^\"]*)(?:["])
                                        )
                                    )
                                )
                            )?  # lazy search attribute value
                        )*(?:\s*) 
                    )*)?(?:\s*)>
                )                            # get end of the starting tag
                (
                    (?:[\w\d\s\W\S\D]*?)
                )                            # lazy search tag children
            </\1>                                          
        %xm';
    }

}
<?php

/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model\Algorithm
 * @version 3.0.0
 */

namespace FLY\Model\Algorithm;

use FLY\Model\ModelField;

/**
 * @trait  QueryParser
 * @todo   Helps to parse sql queries 
 */

trait QueryParser {

    /**
     * @var array $patterns
     * @todo Stores patterns for parsing
     */

    private $patterns = [
        '/
            (?:
                (
                    [_a-zA-Z][_a-zA-Z0-9]*\s*[:]\s*[{][\s\S\W\w\d\D(?:\\\})]*[}]
                )
            )
        /ixm',
        '/
            (?:
                [(]
                    (
                        [_a-zA-Z][_a-zA-Z]*\s*[:]\s*[\s\S\W\w\d\D(?:\\\})]*
                    )
                [)]
            )
        /ixm',
        '/
            (?:
                ([_a-zA-Z0-9][_a-zA-Z0-9]*)\s*[:]\s*(
                    (?:
                        (?:\".*\"),?|
                        (?:\'.*\'),?|
                        (?:[a-zA-Z0-9_\s>=<%],?)
                    )*
                )
            )
        /ixm',
        '/
            (?:
                (
                    [_a-zA-Z][_a-zA-Z0-9]*\s*[:]\s*[{][\s\S\W\w\d\D(?:\\\})]*[}]
                )
            )
        /ixm'
    ];


    /**
     * @method string setFields()
     * @param array $fields
     * @return string
     */

    private function setFields(array $fields): string {
        $fieldQueries = [];
        foreach($fields as $fieldText) {

            $field = ModelField::class;
    
            if($this->isCompoundField($fieldText)) {
                $fieldText = ":".$this->processComponundFields($fieldText);
            }
            
            if($this->fieldIsQuery($fieldText)) {
                $field::name($this->getFieldQuery($fieldText));
            } else if($this->fieldHasAggregate($fieldText)) {
                $field::{$this->getFieldAggregate($fieldText)}(...explode(',',$this->getFieldName($fieldText)));
            } else {
                $field::name($this->getFieldName($fieldText));
            }

            if($this->fieldHasAlias($fieldText)) {
                $field::as($this->getFieldAlias($fieldText));
            }

            array_push($fieldQueries,$field::get());
        }
        
        return implode(',',$fieldQueries);
    }


    /**
     * @method boolean isCompundField
     * @param string $field
     * @return boolean
     */

    private function isCompoundField(string $field): bool
    {
        return preg_match($this->patterns[3],preg_replace('/\s+/',' ',$field));
    }


    /**
     * @method string processCompoundFields()
     * @param string $fieldText
     * @return string
     */

    private function processComponundFields(string $fieldText): string
    {
        $fieldText = str_replace('\}','#rgtcurl;',$fieldText);
        $fieldText = str_replace('\{','#lftcurl;',$fieldText);
        $fieldText = preg_replace('/\s+/',' ',$fieldText);
        $colonMarked = false;

        while(preg_match($this->patterns[0],$fieldText,$match)) {
            $matchQuery = $match[1];
            $token = "";
            $colonIndex = 0;

            for($i=0; $i< strlen($matchQuery);$i++) {

                $query = trim($matchQuery[$i]);

                if($query === '') { 
                    continue;
                } else if($query === ':') $colonMarked = true;
                  
                if($colonMarked && $query === '{') {
                    $colonIndex = $i+1;
                    $token =strtoupper($token).'(';
                    break;
                } else if($colonMarked && $query <> '{' && $query <> ':') {
                    $colonMarked = false;
                }

                if(!$colonMarked) $token .= $query;
            }

            for($j = $colonIndex; $j < (strlen($matchQuery)); $j++) {

                if($j + 1 === (strlen($matchQuery)) && $matchQuery[$j] === '}') {
                    $token .= ')';
                    continue;
                }
                $token .= $matchQuery[$j];
            }
            $fieldText = str_replace($match[0],$token,$fieldText);
        }

        while(preg_match($this->patterns[1],$fieldText,$match)&& preg_match($this->patterns[2],$match[1],$mt)) {
            $fieldText = str_replace($mt[0],strtoupper($mt[1])."(".$mt[2].")",$fieldText);
        }

        $fieldText = str_replace('#rgtcurl;','\}',$fieldText);
        return str_replace('#lftcurl;','\{',$fieldText);       
    }  

}
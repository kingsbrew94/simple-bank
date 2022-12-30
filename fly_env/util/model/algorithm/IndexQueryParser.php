<?php

/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model\Algorithm
 * @version 3.0.0
 */

namespace FLY\Model\Algorithm;

class IndexQueryParser {

    private static string $multi_pattern = '/
        (?:
            ^\s*\[\s*([a-zA-Z_*][a-zA-Z0-9\s\(\)*]*)\s*\]\s*(?:[?]\s*(.*))?
        )
    /xim';

    private static string $single_pattern = '/
        (?:
            ^\s*[$]\[\s*([a-zA-Z_*][a-zA-Z0-9]*\s*\(.*\))\s*\]\s*(?:[?]\s*(.*))?
        )
    /xim';

    private static string $delete_pattern = '/
        (?:
            ^\s*\[\s*([*]|\s*)\s*\]\s*(?:[?]\s*(.*))?
        )
    /xim';

    private ?string $fields;

    private ?string $search;

    private bool $has_fields = false;

    private bool $has_search = false;

    public static function getMultiPattern()
    {
        return self::$multi_pattern;
    }

    public static function getSinglePattern()
    {
        return self::$single_pattern;
    }

    public static function getDeletePattern()
    {
        return self::$delete_pattern;
    }

    public function interpret(string $index_query) 
    {
        if(preg_match(self::$multi_pattern,$index_query,$match)) {
            $matchLength      = count($match);
            $this->has_fields = $matchLength > 1;
            $this->has_search = $matchLength > 2;

            if($this->has_fields) $this->fields = ':'.preg_replace('/\s+/',',:',trim($match[1]));
            
            if($this->has_search) $this->search = $match[2];

            return $this;
        } else if(preg_match(self::$single_pattern,$index_query,$match)) {
            $matchLength      = count($match);
            $this->has_fields = $matchLength > 1;
            $this->has_search = $matchLength > 2;
            if($this->has_fields) $this->fields = preg_replace('/\s+/','',$match[1]);
            if($this->has_search) $this->search = $match[2];
            return $this;
        }
        return null;
    }

    public function interpretUpdate(string $index_query) 
    {
        if(preg_match(self::$multi_pattern,$index_query,$match)) {
            $matchLength      = count($match);
            $this->has_fields = $matchLength > 1;
            $this->has_search = $matchLength > 2;

            if($this->has_fields) $this->fields = preg_replace('/\s+/',',',trim($match[1]));
            
            if($this->has_search) $this->search = $match[2];

            return $this;
        } 
        return null;
    }

    public function interpretDelete(string $index_query) 
    {
        if(preg_match(self::$multi_pattern,$index_query,$match)) {
            $matchLength      = count($match);
            $this->has_fields = $matchLength > 1;
            $this->has_search = $matchLength > 2;
            if($this->has_fields) $this->fields = preg_replace('/\s+/','',$match[1]);
            if($this->has_search) $this->search = $match[2];
            return $this;
        } 
        return null;
    }

    public function getFields(): string 
    {
        return $this->fields;
    }

    public function getSearch(): string 
    {
        return $this->search;
    }

    public function hasFields(): bool
    {
        return $this->has_fields;
    }

    public function hasSearch(): bool
    {
        return $this->has_search;
    }
}
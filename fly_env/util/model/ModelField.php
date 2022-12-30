<?php 
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model
 * @version 3.0.0
 */

namespace FLY\Model;

/**
 * @class  ModelField
 * @todo   organizes model fields
 */

class ModelField {

    /**
     * @var string $field_format 
     * @todo stores field format 
     */

    private static string $field_format;

    /**
     * @method mixed __callStatic()
     * @param string $name
     * @param array $arguments
     * @return string
     * @todo Set's up sql aggregate functions
     */

    static function __callStatic(string $name, array $arguments): string
    {
        if(!in_array($name,get_class_methods(__CLASS__))) {
            self::$field_format = strtoupper($name)."(".implode(',',$arguments).")";
        }
        return __CLASS__;
    }

    /**
     * @method name
     * @param string $name
     * @return string
     * @todo Organizes model fields
     */

    static function name(string $name): string 
    {
        self::$field_format = $name;
        return __CLASS__;
    }

    /**
     * @method string as
     * @param string $alias
     * @return string
     * @todo Set's field aliases and return then field format
     */

    static function as(string $alias): string
    {
        self::$field_format .= " AS '{$alias}'";
        return self::$field_format;
    }

    /**
     * @method string get()
     * @return string
     * @todo Return's the final final field format
     */

    static function get(): string
    {
        return self::$field_format;
    }
}
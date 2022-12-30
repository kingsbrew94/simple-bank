<?php namespace FLY_ENV\Util\Wave_Engine;

class Dictionary {

    public static function callerStacks()
    {
        return [
            'url',
            'statics',
            'usecss',
            'usejs',
            'usecdnjs',
            'usecdncss',
            'cdnurl',
            'import',
            'thisYear',
            'thisMonth',
            'dateQuery',
            ':echo',
            'word_lmt',
            'str_capitalize'
        ];
    }
}
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace FLY\Libs\File_API;

use FLY\Security\KeyGen;

/**
 * Description of File
 *
 */
class File {
    
    private static $file;
    
    private static function assign(&$var, $value) 
    {
        $var = $value;
    }

    public static function rename($path)
    {
        $base        = pathinfo($path);
        $file_ext    = $base['extension'];
        $bare_name   = str_replace('.'.$file_ext,'',$base['basename']);
        $bare_name   = preg_replace('/((\s+)|(\s*)\'(\s*))/', '', $bare_name);
        $hashed_name = preg_replace('/(?:\#|\&|\=|\?)/','_',$bare_name.KeyGen::token(8,'',true).".{$file_ext}");
        $oldname     = FLY_APP_ROOT_DIR.$path;
        $newname     = FLY_APP_ROOT_DIR.self::default_path($path).'/'.$hashed_name;
        rename($oldname, $newname);
        return $hashed_name;
    }

    private static function default_path($path)
    {
        $path_arr = explode('/',$path);
        unset($path_arr[count($path_arr) - 1 ]);
        return implode('/',$path_arr);
    }
    
    public static function set_file(string $file_objectname) 
    {
        self::$file = $_FILES[$file_objectname];
    }
    
    public static function is_present(string $file_objectname)
    {
        return (
            isset($_FILES[$file_objectname]['name'])     && 
            is_string($_FILES[$file_objectname]['name']) &&
            trim($_FILES[$file_objectname]['name'])      <> ""
        );
    }

    public static function get_name()
    {
        return self::$file['name'];
    }
    
    public static function set_name($filename) 
    {
        self::$file['name'] = $filename;
        self::assign(self::$file['name'], $filename);
    }
    
    public static function get_temp()
    {
        return self::$file['tmp_name'];
    }
    
    public static function set_temp($tmp_name) 
    {
        self::$file['tmp_name'] = $tmp_name;
        
        self::assign(self::$file['tmp_name'], $tmp_name);
    }
    
    public static function get_type()
    {
        return self::$file['type'];
    }
    
    public static function set_type($type) 
    {
        self::assign(self::$file['type'], $type);
    }

    public static function upload_file_exists($name)
    {
        self::set_file($name);
        return  isset(self::$file['tmp_name']) && trim(self::$file['tmp_name']) !== "";
    }

    public static function get_size()
    {
        return self::$file['size'];
    }
    
    public static function set_size($size) 
    {
        self::assign(self::$file['size'], $size);
    }
    
    public static function get_error()
    {
        return self::$file['error'];
    }
    
    public static function set_error($error) 
    {
        self::assign(self::$file['error'], $error);
    }

    public static function remove(string $fileName)
    {
        $flag = false;
        if(!is_dir(FLY_APP_ROOT_DIR.$fileName) && file_exists(FLY_APP_ROOT_DIR.$fileName)) {
            unlink(FLY_APP_ROOT_DIR.$fileName);
            $flag = true;
        }
        return $flag;
    }
    
    public static function exists(string $fileName)
    {
        return file_exists(FLY_APP_ROOT_DIR.$fileName);
    }

    public static function write($fileName,$message,$mode='w',$use_include_path=false)
    {
        $file = fopen($fileName,$mode,$use_include_path);
        fwrite($file,$message);
        fclose($file);
        return basename($fileName);
    }

    public static function local_write($fileName,$message,$mode='w',$use_include_path=false)
    {
        $file = fopen(FLY_APP_ROOT_DIR.$fileName,$mode,$use_include_path);
        fwrite($file,$message);
        fclose($file);
        return basename($fileName);
    }

    public static function move_to(string $path) 
    {
        if(file_exists($path.self::$file['name'])) return (object)['state' => false, 'message' => 'file_exists'];
        
        if(move_uploaded_file(self::$file['tmp_name'], $path.self::$file['name'])) {
            return (object)['state' => true, 'message' => 'success'];
        }

        return (object)['state' => false, 'message' => self::$file['error']];
    }
}

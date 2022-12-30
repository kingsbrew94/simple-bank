<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FLY\Libs\File_API;

use FLY\Security\KeyGen;

/**
 * Description of UploadImage
 *
 * 
 */
class Upload {
    
    private $auth;

    private static $FILE_PATH = NULL;

    private $file_new_name    = '';
    
    public function __construct($path)
    {
        self::$FILE_PATH  = FLY_ENV_STATIC;
        self::$FILE_PATH .= $path;
    }

    public function image($name) {
       File::set_file($name);
       if(!$this->auth_image_file(File::class)) {
           $result = $this->auth->get_state();
           if($result->{'state'} === false) 
               return $result;
       } 

       return $this->move_file(File::class);
    }


    public function file($name) 
    {
        File::set_file($name);
        if(!$this->auth_file(File::class,$name)) {
            $result = $this->auth->get_state();
            if($result->{'state'} === false) 
                return $result;
        } 
        return $this->move_file(File::class);
    }

    public function file_size()
    {
        return File::get_size();
    }

    private function move_file($file)
    {
        $file::set_temp($file::get_temp());
        $result = $file::move_to(self::$FILE_PATH);
        
        if($result->{'state'} === false) {
            if($result->{'message'} === 'file_exists') {
                return (object)[
                    'state'   => false,
                    'message' => 'The file you just submitted already exists'
                ];
            }
        }
        $this->change_file_name($file);
        return (object)[
            'state'     => true,
            'filename'  => $this->file_new_name
        ];
    }

    private function auth_image_file($file)
    {
        $file::set_name($file::get_name());

        $auth = new AuthFile($file, self::$FILE_PATH);
        $this->auth = $auth;
        return $auth->validImage();
    }

    private function auth_file($file,$name)
    {
        $file::set_name($file::get_name());
        $auth = new AuthFile($file,self::$FILE_PATH);
        $this->auth = $auth;
        return $auth->validFile() && $file::is_present($name);
    }

    private function change_file_name($file)
    {
        $file_name = $file::get_name();
        $file_ext = $this->get_file_extension($file);
        $bare_name = str_replace(".{$file_ext}",'', $file_name);
        $bare_name = preg_replace('/((\s+)|(\s*)\'(\s*))/', '', $bare_name);
        $hashed_name = preg_replace('/(?:\#|\&|\=|\?)/','_',$bare_name.KeyGen::token(8,'',true).".{$file_ext}");
        $oldname = self::$FILE_PATH.$file_name;
        $newname = self::$FILE_PATH.$hashed_name;
        rename($oldname, $newname);
        $this->file_new_name = $hashed_name;
    }

    public function getFileExtension($name)
    {
        File::set_file($name);
        return strtolower($this->get_file_extension(File::class));
    }

    private function get_file_extension($file)
    {
        $name_split = explode('.',$file::get_name());
        
        $extension = strtolower(end($name_split));
        return $extension;
    }
}

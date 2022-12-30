<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FLY\Libs\File_API;


/**
 * Description of AuthFile
 *
 */

class AuthFile {
    
    private $file;

    private $state;

    private $image_path;

    public function __construct($file, $image_path) {
        $this->file = $file;
        $this->image_path = $image_path;
    }

    public function get_state()
    {
        return $this->state;
    }
    
    public function validImage()
    {
        $is_image = $this->is_image();
        $is_present = $this->is_present();

        if(!$is_image->{'state'}) {
            $this->state = $is_image;
        } else if(!$is_present->{'state'}) {
            $this->state = $is_present;
        }

        return $is_image->{'state'} && $is_present->{'state'};
    }

    public function validFile()
    {
        $is_present = $this->is_present();
        if(!$is_present->{'state'}) $this->state = $is_present;

        return $is_present->{'state'};
    }
    
    private function is_image() 
    {
        $name_split = explode('.',$this->file::get_name());
        
        $extension = strtolower(end($name_split));
        if(!in_array($extension, $this->file_types())) return (object) ['state' => false, 'message' => 'Upload file must be an image file'];
        return (object)['state' => true];
    }

    private function file_types()
    {
        return array('jpg','png','jpeg');
    }
    
    public function is_present()
    {
        if(file_exists($this->image_path.$this->file::get_name())) {
            return (object)['state' => false, 'message' => 'The file you are uploading already exists'];
        }
        return (object)['state' => true];
    }
}

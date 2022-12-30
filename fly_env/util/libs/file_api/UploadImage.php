<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FLY\Libs\File_API;

/**
 * Description of UploadImage
 *
 * 
 */
class UploadImage extends Upload {

    public function __construct($path)
    {
        parent::__construct($path);
    }

    public function upload_file($name)
    {
        return $this->image($name);
    }

    public function image_size()
    {
        return $this->file_size();
    }
}

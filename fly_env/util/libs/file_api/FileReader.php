<?php namespace FLY\Libs\File_API;

class FileReader {
    
    private static $fileType;

    static public function fetchJSON(string $path) 
    {
        self::$fileType = 'json';
        return self::fetch(FLY_APP_ROOT_DIR.$path.'.json');
    }

    static public function fetchFile(string $path) 
    {
        $path_arr = explode('.',$path);
        self::$fileType = $path_arr[count($path_arr) - 1];
        return self::fetch($path);
    }

    static public function fetchLocalFile(string $path) 
    {
        return self::fetchFile(FLY_APP_ROOT_DIR.$path);
    }

    static private function fetch(string $path) 
    {
        $content = NULL;
        if(file_exists($path)) {
            $content = file_get_contents($path);
            if(self::$fileType === 'json') $content = json_decode($content);
        }        
        return $content;
    }
}
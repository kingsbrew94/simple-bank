<?php 

/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @version 2.0.0
 */


 class PackageLoader {

    private $package_name;

    private $route_valid;

    private $className;

    private $env_root;

    private $packagePath = "";

    public function __construct()
    {
        $this->env_root = $this->getENVRoot();
        $this->route_valid = false;
    }

  
    private function search($packageName) 
    {
        $this->className = $this->getClassName($packageName);
        $parsedNamespace = $this->parseNamespace($packageName);
        
        $this->fetchPackage($parsedNamespace);
        
        $this->_requirePackage();
    }

    private function fetchPackage($parsedNamespace) 
    {
        if(isset($this->env_root) && !empty($this->env_root)) {
            $this->findValidPackage($parsedNamespace);
        }
    }

    private function findValidPackage($parsedNamespace) {
    
        if(file_exists("{$this->env_root}/{$parsedNamespace}".EXT)) {
            $this->packagePath = "{$this->env_root}/{$parsedNamespace}".EXT;
            $this->route_valid   = true;

        } else if(file_exists("{$parsedNamespace}".EXT)) {
                $this->packagePath = "{$parsedNamespace}".EXT;
                $this->route_valid = true;

        } else if(file_exists($this->env_root.strtolower($this->package_name).$this->className.EXT)) {
                $this->packagePath = $this->env_root.strtolower($this->package_name)."/".$this->className.EXT;
                $this->route_valid = true; 
                
        } else if(file_exists(strtolower($this->package_name).$this->className.EXT)) {
            $this->packagePath = strtolower($this->package_name).$this->className.EXT;
            $this->route_valid = true; 
        }
    }

    private function _requirePackage()
    {
        if($this->route_valid) {
            require_once($this->packagePath);
        } else {
            try 
            {
                throw new Exception("File {$this->packagePath} could not be found");
            }
            catch(Exception $err) {
                echo $err->getMessage();
            }
        }
        
    }

    private function getENVRoot()
    {
        return str_replace(FLY_ENV,"",__DIR__);
    }

    private function getClassName($packageName) 
    {
        $package_arr =  explode('\\',$packageName);
        return $package_arr[count($package_arr) - 1];
    }

    private function parseNamespace($packageName) 
    {
        $pack_arr = $this->resetNamespace($packageName);
        $pack = implode('/',$pack_arr);
        $this->package_name = $pack;
        if(count($pack_arr) === 1) {
            $pack = $this->setDefaultMVCIfExists($pack);
        } else {
            $pack .= $this->className;
        }
        return $pack;
    }

    private function resetNamespace($packageName): array
    {
        $pack_arr = $this->replaceFLY_ENVwithFLY(explode('\\',$packageName));
        $pack_arr[count($pack_arr) - 1] = "";
    
        return $pack_arr;
    }

    private function replaceFLY_ENVwithFLY(array $pack_arr): array
    {
        if($pack_arr[0] === 'FLY') $pack_arr[0] = FLY_ENV_CORE_NAMESPACE;
        return $pack_arr;
    }

    private function setDefaultMVCIfExists($pack)
    {
        if(strpos($this->className, 'Controller') > 0) {
            $pack = FLY_ENV_APP_CONTROLLERS.$this->className;

        } else if(strpos($this->className,'Model') > 0) {
            $pack = FLY_ENV_APP_MODELS.$this->className;

        } else if(strpos($this->className,'View') > 0) {
            $pack = FLY_ENV_APP_VIEWS.$this->className;
        }
        return $pack;
    }

    public function findPackage() 
    {
        spl_autoload_register(function($packageName) {
            $this->search($packageName);
        });
    }

 }(new PackageLoader())->findPackage();

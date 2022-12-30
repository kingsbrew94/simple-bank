<?php  
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @version 2.0.0
 */
class Migration {

    private $appRegContent;

    private $modelPath;

    private $mainPath;

    private $dbName;

    public function __construct(array $appRegContent, $modelPath, $dbName, $mainPath)
    {
        $this->appRegContent = $appRegContent;
        $this->dbName = $dbName;
        $this->modelPath = $modelPath;
        $this->mainPath = $mainPath;
    }

    public function createRegistries() 
    {
        RegistryGen::create($this->modelPath,$this->dbName,$this->appRegContent,$this->mainPath);
    }
}
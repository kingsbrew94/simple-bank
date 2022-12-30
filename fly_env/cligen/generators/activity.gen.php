<?php 
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @version 2.0.0
 */
class ActivityGen {

    private string $object_name;

    private string $model_name;

    private string $db_name;

    private string $baseUrl;

    use ActivityTemplate;

    public static function set(string $baseUrl, string $object_name, string $model_name,string $db_name)
    {
        return new ActivityGen($baseUrl,$object_name,$model_name,$db_name);
    }

    public function __construct(string $baseUrl, string $object_name, string $model_name, string $db_name)
    {
        $this->object_name = $object_name;
        $this->db_name     = $db_name;
        $this->model_name  = $model_name;
        $this->baseUrl     = $baseUrl;
    }

    public function generateActivity() 
    {
        $this->generateRepository();
        $this->generateDAO();
        $this->generateService();
        $this->generateCRUDEvent();
    }

    public function generateService(): void 
    {
        $this->writeCode($this->Service($this->object_name), '/services/','Service');
    }

    public function generateDAO(): void
    {
        $this->writeCode($this->DirectAccessObject($this->object_name,$this->model_name,$this->db_name),'/dao/','DAO');
    }

    public function generateRepository(): void
    {
        $this->writeCode($this->Repository($this->object_name),'/repositories/','Repository');
    }

    public function generateCRUDEvent(): void
    {
        $this->writeCode($this->CRUDEvent($this->object_name),'/events/','CRUDEvent');
    }

    private function writeCode(string $code, string $path,string $type)
    {
        CVA_Gen::createKeyDirIfNotExists($this->baseUrl.$path);
        $url = $this->baseUrl.$path.$this->object_name.$type.'.php';
        $class_file = fopen($url,'w');
        fwrite($class_file,$code);
        fclose($class_file);
    }
}
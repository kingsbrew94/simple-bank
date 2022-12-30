<?php 
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @version 2.0.0
 */
class SQLCreateHand {

    private $sqli;

    private $host;

    private $username;

    private $password;
    
    private $database_name;

    private $query = "";

    public function __construct($qryContent,$config_value)
    {
        $this->query = $qryContent;
        $this->set_config_vars($config_value);
    }
    
    public function create_handlers()
    {
        $this->connect();
        $this->createHandler();
    
    }


    private function createHandler()
    {
        if(trim($this->query) <> "") { 
            $this->runQuery(
                "[ok] handler created",
                "Error creating: ".$this->sqli->error
            );
        } 
    }

    private function runQuery($succ_msg,$err_msg)
    {
        if($this->sqli->query($this->query) === TRUE) {
            echo PHP_EOL.$succ_msg.PHP_EOL.PHP_EOL;
        } else {
           die(PHP_EOL.$err_msg.PHP_EOL);
        }
    }

    private function set_config_vars($config_value)
    {
       $this->host = $config_value['host'];
       $this->database_name = $config_value['database'];
       $this->username = $config_value['user'];
       $this->password = $config_value['password'];
    }
    
    private function connect()
    {
        $this->sqli =  new mysqli($this->host,$this->username,$this->password,$this->database_name);
        // Check connection
        if ($this->sqli->connect_error) {
            die("Connection failed: " . $this->sqli->connect_error);
        } 
    }
}
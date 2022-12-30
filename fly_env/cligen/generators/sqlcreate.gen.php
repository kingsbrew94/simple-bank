<?php 
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @version 2.0.0
 */
class SQLCreateModel {

    private $sqli;

    private $host;

    private $username;

    private $password;
    
    private $database_name;

    private $tableName;

    private $database_exist = false;

    private $model_migrated = false;

    private static $tablesCreated = false;

    private $insTableName;

    private $query = "";

    public function __construct($tableName,$qryContent,$config_value)
    {
        $this->tableName = $tableName;
        $this->query = $qryContent;
        $this->set_config_vars($config_value);
    }

    public function create_database()
    {
        $host = $this->host;
        $username = $this->username;
        $password = $this->password;
    
        $conn = new mysqli($host, $username, $password);
        $flag = false;
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        // Create database
        
        if(!$this->database_exist($conn)) {
            $sql = "CREATE DATABASE IF NOT EXISTS ".$this->database_name;
            if ($conn->query($sql) === TRUE) {
                echo PHP_EOL."- Database created successfully".PHP_EOL.PHP_EOL;
                $flag = true;
            } else {
                echo PHP_EOL."Error creating database: " . $conn->error.PHP_EOL;
            }
            $conn->close();
        }
        return $flag;
    }

    private function database_exist($conn)
    {
        $query  = "USE ".$this->database_name;
        $this->database_exist = false;
        try {
            if($conn->query($query) === true) $this->database_exist = true;
        } catch(Exception $ex) {}
      
        return $this->database_exist;
    }

    public function dbExists() { return $this->database_exist; }

    public function drop_model()
    {
        $this->connect(true);
        if($this->database_exist($this->sqli)) {
            $this->runQuery(
                "- Database '{$this->database_name}' was successfully droped.",
                "Model migration error: ".$this->query."  ".$this->sqli->error
            );
        } else {
            echo "\n>> Warning: Database '{$this->database_name}' does not exists.\n\n";
        }
        return $this->model_migrated;
    }

    public function drop_store()
    {
        $this->connect();
        if($this->table_exists()) {
            $this->runQuery(
                "- Table '{$this->tableName}' was successfully droped from Database '{$this->database_name}'.",
                "Model migration error: ".$this->query."  ".$this->sqli->error
            );
        } else {
            echo "\n>> Warning: Table '{$this->tableName}' does not exists at Database '{$this->database_name}'.\n\n";
        }
        return $this->model_migrated;
    }

    public function create_models()
    {
        $this->connect();
        switch($this->getQueryType()) {
            case 'TABLE':
                $this->createTable();
            break;
            case 'INSERT':
                $this->addRow();
            break;
            case 'ALTER':
                $this->alterTable();
            break;
            case 'DROP_TABLE':
                $this->drop_store();
            break;
            default:
                if($this->database_exist) {
                    $this->model_migrated = false;
                } else self::$tablesCreated = true;
            break;
        }
        
    }

    private function alterTable()
    {    
        $this->query = preg_replace(Patterns::ifExistsPattern(),' ',$this->query);
        if(preg_match(Patterns::alterAddPattern(),$this->query,$match)) {
            if(!$this->fieldExists($match['columnName'],$match['tableName'])) {
                $this->runQuery(
                    "- New Column '{$match['columnName']}' was successfully added to Table '{$match['tableName']}'",
                    "Error altering table: {$this->tableName} {$this->sqli->error}"
                );
            }
        } else if(preg_match(Patterns::alterDropPattern(),$this->query,$match)) {
            if($this->fieldExists($match['columnName'],$match['tableName'])) {
                $this->runQuery(
                    "- Column '{$match['columnName']}' was successfully droped from Table '{$match['tableName']}'",
                    "Error altering table: {$this->tableName} {$this->sqli->error}"
                );
            }
        } else if(preg_match(Patterns::alterModifyPattern(),$this->query,$match)) {
            if($this->fieldExists($match['columnName'],$match['tableName'])) {
                $this->runQuery(
                    "- Column '{$match['columnName']}' was successfully modified at Table '{$match['tableName']}'",
                    "Error altering table: {$this->tableName} {$this->sqli->error}"
                );
            }
        } 
    }

    private function fieldExists($fieldName,$tableName)
    {
        return $this->modelObjectExists("SELECT $fieldName FROM $tableName");
    }

    private function createTable()
    {
        if(!$this->table_exists() && $this->query <> "") { 
            $this->runQuery(
                '- Table '.$this->tableName.' created successfully',
                "Error creating table: ".$this->tableName.' '.$this->sqli->error
            );
        } 
    }

    private function runQuery($succ_msg,$err_msg)
    {
        if($this->sqli->query($this->query) === TRUE) {
            echo PHP_EOL.$succ_msg.PHP_EOL.PHP_EOL;
            self::$tablesCreated = true;
            $this->model_migrated = true;
        } else {
            die(PHP_EOL.$err_msg.PHP_EOL);
        }
    }

    private function addRow()
    {
        if($this->tableName === '' && $this->checkModifications()) {
            $this->runQuery(
                "- New row(s) successfully added to Table {$this->insTableName}",
                "Model migration error: ".$this->query."  ".$this->sqli->error
            );
        }
    }

    private function getQueryType()
    {
        $type = "ANY";
        if(preg_match(Patterns::createTable(),$this->query)) $type = 'TABLE';
        else if(preg_match(Patterns::insertInto(),$this->query)) $type = 'INSERT';
        else if(preg_match(Patterns::alterTable(),$this->query)) $type = 'ALTER';
        else if(preg_match(Patterns::dropTable(),$this->query)) $type  = 'DROP_TABLE';

        return $type;
    }

    public function model_migrated()
    {
        return $this->model_migrated;
    }

    public function tables_created()
    {
        return self::$tablesCreated;
    }

    static public function resetMigrationStateFlag()
    {
        self::$tablesCreated = false;
    }

    private function table_exists($tableName = "")
    {
        $tableName = $this->tableName === "" ? $tableName : $this->tableName;
        return $this->modelObjectExists(
            "SELECT * FROM $tableName"
        );
    }

    private function modelObjectExists($query)
    {
        $flag = false;
        try {
            $flag = $this->sqli->query($query);
            $flag = $flag && true;
        } catch(Exception $ex) {}
        return $flag; 
    }

    private function testQuery($query)
    {
        return $this->sqli->query($query)->num_rows > 0;
    }

    private function checkModifications()
    {
        if($this->tableName === "" && preg_match(Patterns::insertQueryPattern(),$this->query,$match)) {
            if($this->table_exists($match['tableName'])) {
                $tempQuery = $this->query;
                $this->insTableName = $match['tableName'];
                do {
                    $tableName = $match['tableName'];
                    $insertFields = $this->fetchInsertFieldsNames($match['insertFields']);
                    $insertValues = $this->fetchInsertFields($match['insertValues']);
                    $query = ($this->constructIQuery($tableName,$insertFields,$insertValues));
                    if(is_string($query) && $this->testQuery($query)) {
                        $this->query = str_replace($match['insertData'],'',$this->query);
                    } 
                    $tempQuery = str_replace($match['insertData'],'',$tempQuery);
                } while(preg_match(Patterns::insertQueryPattern(),$tempQuery,$match));
                if(!preg_match(Patterns::insertQueryPattern(),$this->query,$match)) {
                    $this->query = "";
                }
            } else { $this->query = ""; }
        }
        return $this->query <> "";
    }

    private function fetchInsertFieldsNames($fieldNamesStr)
    {
        $fields = $this->fetchInsertFields($fieldNamesStr);
        if(empty($fields)) {
            $oldfields = explode(',',$fieldNamesStr);
            foreach($oldfields as $field) {
                array_push($fields,trim($field));
            }
        }
        return $fields;
    }

    private function fetchInsertFields($fieldStr)
    {
        $fieldStr = str_replace("\'","&sqt;",$fieldStr);
        $fieldStr = str_replace('\"',"&dqt;",$fieldStr);
        $fields = [];

        while(preg_match(Patterns::fieldSingleStrings(),$fieldStr,$match)) {
            $match['fieldName'] = str_replace('&sqt;',"'",$match['fieldName']);
            $match['fieldName'] = str_replace('&dqt;','"',$match['fieldName']);
            array_push($fields,$match['fieldName']);
            $fieldStr = str_replace($match[0],'',$fieldStr);
        }

        while(preg_match(Patterns::fieldDoubleStrings(),$fieldStr,$match)) {
            $match['fieldName'] = str_replace('&sqt;',"'",$match['fieldName']);
            $match['fieldName'] = str_replace('&dqt;','"',$match['fieldName']);
            array_push($fields,$match['fieldName']);
            $fieldStr = str_replace($match[0],'',$fieldStr);
        }
        return $fields;
    }

    private function constructIQuery(string $tableName,array $insertFields,array $insertValues)
    {
        $query = "";        
        $payload = array_combine($insertFields,$insertValues);
        $counter = 1;
        if(!is_bool($payload)) {
            $payloadLen = count($payload);
            foreach($payload as $fieldName => $fieldValue) {
                $fieldValue = str_replace('"',"'",$fieldValue);
                $query .= "{$fieldName}='{$fieldValue}' ";
                if($counter++ <> $payloadLen) $query .="AND ";
            }
        } else die("Unmatched insert query fields from '{$this->query}'".PHP_EOL);
        return "SELECT * FROM {$tableName} WHERE {$query}";
    }

    private function set_config_vars($config_value)
    {
       $this->host = $config_value['host'];
       $this->database_name = $config_value['database'];
       $this->username = $config_value['user'];
       $this->password = $config_value['password'];
    }
    
    private function connect($flag = false)
    {
        $this->sqli =  new mysqli($this->host,$this->username,$this->password);

        if(!$flag)
            $this->sqli =  new mysqli($this->host,$this->username,$this->password,$this->database_name);
        // Check connection
        if ($this->sqli->connect_error) {
            die("Connection failed: " . $this->sqli->connect_error);
        } 
    }
}
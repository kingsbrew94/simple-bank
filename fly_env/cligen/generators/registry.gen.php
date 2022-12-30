<?php 
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @version 2.0.0
 */
class RegistryGen {

    private static $modelContents;

    private static $dbName;

    private static $regStore;

    private static $tableName;

    private static $fields = [];

    private static $primaryKeys   = [];

    private static $foreignKeys   = [];

    private static $modelsArray   = [];

    private static $alterAddQry   = [];

    private static $alterDropQry  = [];

    private static $droppedTables = [];

    private static $droppedDBs    = [];

    private static $protocols     = [];

    private static $modelPath    = "";

    private static $iniCounter   = 0;

    private static $mainPath  = "";

    private static $model_configs = [];
    
    public function __construct($modelPath,$dbName,$regStore,$mainPath)
    {
        self::$modelContents = file_get_contents($modelPath);
        self::$dbName = $dbName;
        self::$regStore = $regStore;   
        self::$mainPath = $mainPath;
    }

    static public function create($path,$dbName,$regStore,$mainPath)
    {
        self::$modelPath = $path;
        $model_path    = "${path}/models.sql";
        $handlers_path = "${path}/handlers.sql";
        if(file_exists($model_path)) {
            new Self($model_path,$dbName,$regStore,$mainPath);
            self::parseModelContent();
            self::createModels();
        } else {
            echo PHP_EOL.">> Warning: No models was found in ".self::$modelPath.PHP_EOL.PHP_EOL;
            exit;
        }

        if(file_exists($handlers_path)) {
            new Self($handlers_path,$dbName,$regStore,$mainPath);
           self::parseHandlersContent();
           self::createHandlersModels();
        }
    }

    static private function parseHandlersContent()
    {
        self::removeUnwantedChars();
        $modelsArray = explode('$$',self::$modelContents);
        if((isset($modelsArray[0]) && trim($modelsArray[0]) === "") || empty($modelsArray)) {
            // echo "\n- No handler is contained in ".self::$modelPath."/handlers.sql\n\n";
        } else {
            foreach($modelsArray as $key => $hand) {
                $model = str_replace('#fmlsqt',"'",$hand);
                $model = str_replace('#fmldqt','"',$hand);
                if(preg_match('/^\s*DELIMITER\s*/',$model,$match)) {
                    unset($modelsArray[$key]);
                    continue;
                }
                $modelsArray[$key] = $model; 
            }
        }
        self::setConfigs($modelsArray);
    }
    
    static private function setConfigs($modelsArray)
    {
        self::$modelsArray = $modelsArray;
        if(
            !isset(self::$regStore['list']['APP_MODELS']['default']) || 
            trim(self::$regStore['list']['APP_MODELS']['default']) === ""
        ) {
            self::$regStore['list']['APP_MODELS']['default'] = self::$dbName;
        } else if(!in_array(self::$dbName,self::$regStore['list']['APP_MODELS']))
            self::$regStore['list']['APP_MODELS']['md_'.self::$dbName] = self::$dbName;
        
        self::$model_configs = [
            'host'     => self::$regStore['list']['SERVER_CONFIG_HOSTS']['default'],
            'user'     => self::$regStore['list']['SERVER_CONFIG_USERS']['default'],
            'password' => self::$regStore['list']['SERVER_CONFIG_PASSWORDS']['default'],
            'database' => self::$dbName
        ];
    }

    static private function parseModelContent()
    {
        self::removeUnwantedChars();
        $modelsArray = explode(';',self::$modelContents);
        if((isset($modelsArray[0]) && trim($modelsArray[0]) === "") || empty($modelsArray)) {
            echo "\n- No models is contained in ".self::$modelPath."/models.sql\n\n";
            exit;
        } else {
            foreach($modelsArray as $key => $model) {
                $model = str_replace('#fmlsqt',"'",$model);
                $model = str_replace('#fmldqt','"',$model);
                self::setAlters($model);
                self::setDrops($model);
                $modelsArray[$key] = trim(self::rewriteTableQuery($model));
                self::initModelsFields($model);
            }
            self::setConfigs($modelsArray);
        }
    }

    static private function initModelsFields($model)
    {
        if(preg_match(Patterns::modelQueryPattern(),$model,$match)) {
            self::setModelFields($model,$match['tableName']);
        }
    }

    static private function setDrops($model)
    {
        preg_match(Patterns::dropTablePattern(),$model,$match);
        if(preg_match(Patterns::dropDBPattern(),$model,$match)) {
            array_push(self::$droppedDBs,$match['tableName']); 
        } else if(preg_match(Patterns::dropTablePattern(),$model,$match)) {
            array_push(self::$droppedTables,$match['tableName']);
        } else if(preg_match(Patterns::modelQueryPattern(),$model,$match)) {
            $index = array_search($match['tableName'],self::$droppedTables);
            if(is_int($index)) {
                unset(self::$droppedTables[$index]);
            }
        } else if(preg_match(Patterns::createDatabasePattern(),$model,$match)) {
            $index = array_search($match['dbName'],self::$droppedDBs);
            if(is_int($index)) {
                unset(self::$droppedDBs[$index]);
            }
        }
    }

    static private function setAlters($modelQry) 
    {
        if(preg_match(Patterns::alterAddPattern(),$modelQry)) 
            array_push(self::$alterAddQry,$modelQry);
        else if(preg_match(Patterns::alterDropPattern(),$modelQry)) 
            array_push(self::$alterDropQry,$modelQry);
    }

    static private function dropTables()
    {
        foreach(self::$droppedTables as $tableName) {
            unset(self::$fields[$tableName]);
            dropStore($tableName,self::$dbName,self::$mainPath);
        }
    }

    static private function droppedCurrentDB()
    {
        $flag = false;
        if(in_array(self::$dbName,self::$droppedDBs)){
            $flag = true;
            self::$fields = [];
        }
        return $flag;
    }

    static private function alterFields()
    {
        foreach(self::$alterAddQry as $qry) {
            if(preg_match(Patterns::alterAddPattern(),$qry,$match)) {
                $tableName = trim($match['tableName']);
                if(isset(self::$fields[$tableName])) {
                    $columnName = trim($match['columnName']);
                    if(!in_array($columnName,self::$fields[$tableName]))
                        array_push(self::$fields[$tableName],$columnName);
                }
            }
        }

        foreach(self::$alterDropQry as $qry) {
            if(preg_match(Patterns::alterDropPattern(),$qry,$match)) {
                $tableName = trim($match['tableName']);
                if(isset(self::$fields[$tableName])) {
                    $search_index = array_search(trim($match['columnName']),self::$fields[$tableName]);
                    if(is_int($search_index)) {
                        unset(self::$fields[trim($match['tableName'])][$search_index]);
                    }
                }
            }
        }
    }
    
    static private function rewriteTableQuery($payload)
    {
        $pattern = '/(?:CREATE(?:\s+)TABLE(?:\s+)(?:IF(?:\s+)NOT(?:\s+)EXISTS(?:\s+))?)/i';
        $payload = preg_replace($pattern,'CREATE TABLE IF NOT EXISTS ',$payload);
        return $payload;
    }

    static private function query_has_database_query($payload)
    {
        $pattern = '/CREATE(?:\s+)DATABASE(?:\s+)(?:IF(?:\s+)NOT(?:\s+)EXISTS(?:\s+)(?:[a-zA-Z_][a-zA-Z0-9_]*)|';
        $pattern .= '(?:[a-zA-Z_][a-zA-Z0-9]*))/i';
        $create_matched = preg_match($pattern,$payload);
        $pattern = '/USE(?:\s+)(?:[a-zA-Z_][a-zA-Z0-9_]*)/i';
        $use_matched = preg_match($pattern,$payload);
        return $create_matched || $use_matched;
    }

    static private function createDBModels()
    {
        $sql = null;

        foreach(self::$modelsArray as  $query) {
            if($query === "") continue;
            self::$tableName = "";
            if(preg_match(Patterns::createTable(),$query,$match) || preg_match(Patterns::dropTablePattern(),$query,$match)) 
                self::$tableName = $match['tableName'];

            if(!self::query_has_database_query($query)) {
                $sql = new SQLCreateModel(
                    self::$tableName,
                    $query,
                    self::$model_configs
                );
                if($sql->create_database() || $sql->dbExists()) 
                    $sql->create_models();
            }
        }
        return $sql;
    }

    static private function createModels()
    {
        $sql = self::createDBModels();

        if($sql <> null && $sql->tables_created()) {
            self::saveSettings();
            echo PHP_EOL."# Model '".self::$dbName."' was successfully migrated.".PHP_EOL.PHP_EOL;
        } else if($sql <> null) {
            self::saveSettings();
            echo PHP_EOL."# Model '".self::$dbName."' is already migrated.".PHP_EOL.PHP_EOL;
        }
        SQLCreateModel::resetMigrationStateFlag();
    }

    static private function createHandlersModels()
    {
        $sql = null;

        foreach(self::$modelsArray as  $query) {
            if($query === "") continue;
            if(!self::query_has_database_query($query)) {
                $sql = new SQLCreateHand(
                    $query,
                    self::$model_configs
                );
                $sql->create_handlers();
                
            }
        }
        return $sql;
    }

    static private function saveSettings()
    {
        self::dropTables();
        self::alterFields();
        if(!self::droppedCurrentDB()) {
            self::setProtocols();
            self::createClassModels();
        }
        self::createINI();

        foreach(self::$regStore['list']['APP_MODELS'] as $key => $modelName) {
            if(!file_exists(self::$mainPath.'app/models/'.$modelName)) {
                unset(self::$regStore['list']['APP_MODELS'][$key]);
            }
        }
        self::createINI();
    }

    static private function setProtocols()
    {
        $serverHost = self::$regStore['list']['SERVER_CONFIG_HOSTS'];
        $serverUser = self::$regStore['list']['SERVER_CONFIG_USERS'];
        $serverPass = self::$regStore['list']['SERVER_CONFIG_PASSWORDS'];
        $serverDB   = self::$regStore['list']['APP_MODELS'];
        self::$protocols['host'] = (
            isset($serverHost['md_'.self::$dbName]) && !empty($serverHost['md_'.self::$dbName])
            ? 'md_'.self::$dbName : 'default'
        );

        self::$protocols['username'] = (
            isset($serverUser['md_'.self::$dbName]) && !empty($serverUser['md_'.self::$dbName])
            ? 'md_'.self::$dbName : 'default'
        );

        self::$protocols['password'] = (
            isset($serverPass['md_'.self::$dbName]) && !empty($serverPass['md_'.self::$dbName])
            ? 'md_'.self::$dbName : 'default'
        );

        self::$protocols['database'] = (
            isset($serverDB['md_'.self::$dbName]) && !empty($serverDB['md_'.self::$dbName])
            ? 'md_'.self::$dbName : 'default'
        );
    }

    static public function setAppReg($regvalue,$mainPath)
    {
        self::$regStore = $regvalue;
        self::$mainPath = $mainPath;
        self::$iniCounter = 0;
    }

    static public function createINI()
    {
        $path = self::$mainPath.'app/.app_reg';
        if(file_exists($path)) {
            $file = fopen($path,'w');
            fwrite($file,self::stringifyArrayToINI(self::$regStore['list']));
            fclose($file);
            self::$iniCounter = 0;
        }
    }

    static private function stringifyArrayToINI(array $a, array $parent = [])
    {
        $out = '';
        $comments = [
            ";;; Hosts / Server Names".PHP_EOL,
            ";;; Users".PHP_EOL,
            ";;; Access Passwords".PHP_EOL,
            ";;; Models".PHP_EOL
        ];
        foreach($a as $k => $v) {
            if(is_array($v)) {
                $sec = array_merge((array) $parent,(array) $k);
                $out .= PHP_EOL.$comments[self::$iniCounter++].PHP_EOL;
                $out .= '['.join('.',$sec).']'.PHP_EOL;
                $out .= self::stringifyArrayToINI($v,$sec);
            } else {
                $out .="$k = '$v'".PHP_EOL;
            }
        }
        return $out;
    }

    static private function createClassModels()
    {
        foreach(self::$fields as $className => $fields) {
            ClassModelGen::createModels(
                self::$dbName,
                $className,
                $fields,
                self::$primaryKeys[$className],
                self::$foreignKeys[$className],
                self::$protocols,
                self::$modelPath
            );
        }
    }

    static private function setModelFields($model,$tableName)
    {
        self::$primaryKeys[$tableName] = [];
        self::$foreignKeys[$tableName] = [];
        self::$fields[$tableName] = [];

        while(preg_match(Patterns::primaryKeys(),$model,$match)) {
            $keys = explode(',',$match['keys']);
            foreach($keys as $key) {
                $key = trim($key);
                if($key <> "") array_push(self::$primaryKeys[$tableName],$key);
                $model = str_replace($match[0],'',$model); 
            }
        }

        while(preg_match(Patterns::foreignKey(),$model,$match)) {
            self::$foreignKeys[$tableName][trim($match['fkey'])] = trim($match['ref']);
            $fqueryLen = strlen($match[0]);
            $fqueryStr = "";
            if($match[0][$fqueryLen - 1] === ')') {
                for($i = 0; $i < ($fqueryLen - 1); $i++) {
                    $fqueryStr .= $match[0][$i];
                }
            } else $fqueryStr = $match[0];
            $model = str_replace($fqueryStr,'',$model); 
        }

        while(preg_match(Patterns::modelQueryPattern(),$model,$match)) {

            $fieldName = trim($match['fieldName']);
            array_push(self::$fields[$tableName],$fieldName); 
            if(isset($match['keyType']) &&  preg_match('%^primary\s+key$%',strtolower(trim($match['keyType'])))) {
                array_push(self::$primaryKeys[$tableName],$fieldName);
            }
            $model = str_replace($match['fieldQuery'],'',$model);
        }
    }


    static private function removeUnwantedChars()
    {
        self::$modelContents = preg_replace('%^[/][/](?:.*)$%','',self::$modelContents);
        self::$modelContents = preg_replace('%^[-](?:.*)$%','',self::$modelContents);
        self::$modelContents = preg_replace('%[`]%','',self::$modelContents);
        self::$modelContents = str_replace("\\'",'#fmlsqt',self::$modelContents);
        self::$modelContents = str_replace('\\"','#fmldqt',self::$modelContents);
    }
}
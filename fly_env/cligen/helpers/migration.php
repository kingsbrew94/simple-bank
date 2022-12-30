<?php
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @version 2.0.0
 */
function migrate_models($argv_length,$general_path)
{
    try{
        if($argv_length === 1) {
            $dir = get_model_dir($general_path.'app/models/'); 
            if(!empty($dir))
                echo "\nMigrating models ...".PHP_EOL.PHP_EOL; 
            else echo "\n- No models available at {$general_path}'app/models/'\n\n\n";
            foreach($dir as $key => $d) {
                $content = fetch_app_reg($general_path.'app/.app_reg');
                if(!validateAppRegFile($content['list'],$general_path)) break;
                $mgtn = new Migration($content,$d,$key,$general_path);
                $mgtn->createRegistries();
            }
        } else throw new Exception(">> fly-env: migrate:models -> command is an empty type. No preceeding arguments needed");
    } catch(Exception $err) {
        echo PHP_EOL.$err->getMessage().PHP_EOL.PHP_EOL;
    }
}

function migrate($cmd,$general_path)
{
    try{
        if(isset($cmd[0]) && count($cmd) === 1&& !empty($cmd[0])) {
            $modelPath = "{$general_path}app/models/{$cmd[0]}"; 
            $dbName = $cmd[0];
            if(is_dir($modelPath)) {
                echo "\nMigrating model {$cmd[0]} ...".PHP_EOL.PHP_EOL;
                $content = fetch_app_reg($general_path.'app/.app_reg');
                if(validateAppRegFile($content['list'],$general_path)) {
                    $mgtn = new Migration($content,$modelPath,$dbName,$general_path);
                    $mgtn->createRegistries();
                } 
            } else throw new Exception("\n>> Warning: Model '{$dbName}' does not exists at {$general_path}app/models/\n\n");
            
        } else throw new Exception(">> fly-env: migrate -> command expects model name. HINT: migrate [MODEL NAME HERE]");
    } catch(Exception $err) {
        echo PHP_EOL.$err->getMessage().PHP_EOL.PHP_EOL;
    }
}

function fetch_app_reg($path)
{
    $content = [];
    if(file_exists($path)) {
        $content['list'] = parse_ini_file($path,true);
    } else throw new Exception(">> .app_reg file is not found in {$path}app/.app_reg.".PHP_EOL.PHP_EOL);
    return $content;
}

function validateAppRegFile(array $content, $path)
{   $flag = true;
    if(empty($content)) {
        $flag = false;
        die(">> .app_reg file at {$path}app/.app_reg is empty.".PHP_EOL.PHP_EOL);
    } else if(!isset($content['SERVER_CONFIG_HOSTS']['default'])) {
        $flag = false;
        die(">> .app_reg file at {$path}app/.app_reg does not have a default host name.".PHP_EOL.PHP_EOL);
    } else if(!isset($content['SERVER_CONFIG_USERS']['default'])) {
        $flag = false;
        die(">> .app_reg file at {$path}app/.app_reg does not have a default user.".PHP_EOL.PHP_EOL);
    } else if(!isset($content['SERVER_CONFIG_PASSWORDS']['default'])) {
        $flag = false;
        die(">> .app_reg file at {$path}app/.app_reg does not have a default password.".PHP_EOL.PHP_EOL);
    } else if(empty($content['SERVER_CONFIG_HOSTS']['default'])) {
        $flag = false;
        die(">> .app_reg file at {$path}app/.app_reg does not have a default host name.".PHP_EOL.PHP_EOL);
    } else if(empty($content['SERVER_CONFIG_USERS']['default'])) {
        $flag = false;
        die(">> .app_reg file at {$path}app/.app_reg does not have a default user.".PHP_EOL.PHP_EOL);
    } 
    return $flag;
}

function get_model_dir($path)
{
    $payload = [];
    $dir = dir($path);
    while(false !== ($entry = $dir->read())) {
        
        if(is_dir($path.$entry) && !preg_match('/[.]+/',$entry)) {
            $payload[$entry] = $path.$entry;
        }
    }
    return $payload;
}

function get_model_files($path)
{
    $payload = [];
    if(is_dir($path)) {
        $dir = dir($path);
        while(false !== ($entry = $dir->read())) {
            if(file_exists($path.$entry) && preg_match(Patterns::modelFile(),$entry)) {
                $payload[] = $path.$entry;
            }
        }
    }
    return $payload;
}

function make_model($cmd, $general_path)
{
    try {
        if(isset($cmd[0]) && count($cmd) === 1) {
            if(preg_match(Patterns::modelNamePattern(),$cmd[0])) {
                createModel($cmd[0],$general_path);
            } else throw new Exception(">> Warning: Model name must contain characters like [a-zA-Z_][a-zA-Z_0-9]*");
        } else throw new Exception(">> fly-env: make:model -> command expects model name.\nHINT: make_model [Model NAME HERE].");
    } catch(Exception $err) {
        echo PHP_EOL.$err->getMessage().PHP_EOL.PHP_EOL;
    }
}

function createModel($modelName, $general_path)
{
    if(!is_dir("{$general_path}app/models/$modelName/")) {
        mkdir("{$general_path}app/models/$modelName/");        
    }

    if(!file_exists("{$general_path}app/models/$modelName/models.sql")) {
        $file = fopen("{$general_path}app/models/$modelName/models.sql",'w');
        fwrite($file,"-- Write your models here\n");
        fclose($file);
        echo "\n # Model '{$modelName}' is successfully made ready.\n\n";
    } else {
        echo "\n - Model '{$modelName}' is already made ready.\n\n";
    }

    if(!file_exists("{$general_path}app/models/$modelName/handlers.sql")) {
        $file = fopen("{$general_path}app/models/$modelName/handlers.sql",'w');
        fwrite($file,"-- Write your routines and procedures here\n");
        fclose($file);
    }
}
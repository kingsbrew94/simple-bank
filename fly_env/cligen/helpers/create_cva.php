<?php 
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @version 2.0.0
 */
function create_controller_view($argv_length,$argv,$general_path)
{
    try {
        if($argv_length === 2) {
            CVA_Gen::createControllerView($argv[0],$general_path);
            echo PHP_EOL."# Controller and View of '$argv[0]' was successfully Created.".PHP_EOL;
            echo "- Controller Name: ".CVA_Gen::controllerName().PHP_EOL;
            echo "- View Name: ".CVA_Gen::viewName().PHP_EOL.PHP_EOL;
        } else throw new Exception(">> fly-env: create:cv or create:controller-view -> command expects class name. HINT: create:cv [CLASS NAME HERE] or create:controller-view [CLASS NAME HERE]");
    } catch(Exception $err) {
        echo PHP_EOL.$err->getMessage().PHP_EOL.PHP_EOL;
    }
}

function create_controller($argv_length,$argv,$general_path)
{
    try {
        if($argv_length === 2) {
            CVA_Gen::createController($argv[0],$general_path);
            echo PHP_EOL."# Controller of '$argv[0]' was successfully Created.".PHP_EOL;
            echo "- Controller Name: ".CVA_Gen::controllerName().PHP_EOL.PHP_EOL;
        } else throw new Exception(">> fly-env: create:c or create:controller -> command expects controller name. HINT: create:c [CONTROLLER NAME HERE] or create:controller [CONTROLLER NAME HERE]");
    } catch(Exception $err) {
        echo PHP_EOL.$err->getMessage().PHP_EOL.PHP_EOL;
    }
}

function create_view($argv_length,$argv,$general_path)
{
    try {
        if($argv_length === 2) {
            CVA_Gen::createView($argv[0],$general_path);
            echo PHP_EOL."# View of '$argv[0]' was successfully Created.".PHP_EOL;
            echo "- View Name: ".CVA_Gen::viewName().PHP_EOL.PHP_EOL;
        } else throw new Exception(">> fly-env: create:v or create:view -> command expects view name. HINT: create:v [VIEW NAME HERE] or create:view [VIEW NAME HERE]");
    } catch(Exception $err) {
        echo PHP_EOL.$err->getMessage().PHP_EOL.PHP_EOL;
    }
}

function create_widget($argv_length,$argv,$general_path)
{
    try {
        if($argv_length === 2) {
            CVA_Gen::createWidget($argv[0],$general_path);
            echo PHP_EOL."# Widget of '$argv[0]' was successfully Created.".PHP_EOL;
            echo "- Widget Name: ".CVA_Gen::widgetName().PHP_EOL.PHP_EOL;
        } else throw new Exception(">> fly-env: create:w or create:widget or create:fml -> command expects view name. HINT: create:w [WIDGET NAME HERE] or create:fml [WIDGET NAME HERE] or create:widget [WIDGET NAME HERE]");
    } catch(Exception $err) {
        echo PHP_EOL.$err->getMessage().PHP_EOL.PHP_EOL;
    }
}

function create_validator($argv_length,$argv,$general_path)
{
    try {
        if($argv_length === 2) {
            CVA_Gen::createValidator($argv[0],$general_path);
            echo PHP_EOL."# Validator of '$argv[0]' was successfully Created.".PHP_EOL;
            echo "- Validator Name: ".CVA_Gen::validatorName().PHP_EOL.PHP_EOL;
        } else throw new Exception(">> fly-env: create:validator or create:vldt -> command expects activity name.\nHINT: create:activity [VALIIDATOR NAME HERE] or create:a [VALIDATOR NAME HERE]");
    } catch(Exception $err) {
        echo PHP_EOL.$err->getMessage().PHP_EOL.PHP_EOL;
    }
}

function create_class($argv_length,$argv,$general_path)
{
    try {
        if($argv_length === 2) {
            CVA_Gen::createClass($argv[0],$general_path);
            echo PHP_EOL."# Class of '$argv[0]' was successfully Created.".PHP_EOL;
            echo "- Class Name: ".CVA_Gen::actionClassName().PHP_EOL.PHP_EOL;
        } else throw new Exception(">> fly-env: create:class -> command expects class name. HINT: create:class [CLASS NAME HERE]");
    } catch(Exception $err) {
        echo PHP_EOL.$err->getMessage().PHP_EOL.PHP_EOL;
    }
}

function create_activity($argv_length,$argv,$general_path)
{
    try {
        
        if($argv_length === 2) {
            $explode = explode('@',$argv[0]);
            if(count($explode) === 3 && !empty($explode[0]) && !empty($explode[1]) && !is_numeric($explode[0]) && !is_numeric($explode[1])) {
                ActivityGen::set($general_path.'app/actors',trim($explode[0]),trim($explode[1]), trim($explode[2]))->generateActivity();
                CVA_Gen::createValidator($explode[0],$general_path);
                echo PHP_EOL."# Activity of '$argv[0]' was successfully Created.".PHP_EOL;
                echo "- Activity Name: ".$explode[0].''.PHP_EOL.PHP_EOL;
            } else throw new Exception(">> fly-env: create:activity or create:a -> command expects activity name.\nHINT: create:activity [ACTIVITY NAME HERE]@[MODEL NAME HERE]@[DATABASE NAME HERE]  or create:a [ACTIVITY NAME HERE]@[MODEL NAME HERE]@[DATABASE NAME HERE]");
                
        } else throw new Exception(">> fly-env: create:activity or create:a -> command expects activity name.\nHINT: create:activity [ACTIVITY NAME HERE]@[MODEL NAME HERE]@[DATABASE NAME HERE] or create:a [ACTIVITY NAME HERE]@[MODEL NAME HERE]@[DATABASE NAME HERE]");
    } catch(Exception $err) {
        echo PHP_EOL.$err->getMessage().PHP_EOL.PHP_EOL;
    }
}

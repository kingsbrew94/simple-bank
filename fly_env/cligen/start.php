<?php

require_once 'generators/cva.gen.php';
require_once 'generators/md.gen.php';
require_once 'generators/sqlcreate.gen.php';
require_once 'generators/sqlcreatehand.gen.php';
require_once 'generators/classmd.gen.php';
require_once 'generators/registry.gen.php';
require_once "templates/activity.tem.php";
require_once 'generators/activity.gen.php';
require_once 'helpers/create_cva.php';
require_once 'helpers/migration.php';
require_once 'helpers/patterns.php';
require_once 'helpers/drops.php';

/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @version 2.0.0
 */
define('FLY_ENV_CLI_PATH','fly_env'.DIRECTORY_SEPARATOR.'cligen');

function init_cmds($argc="", array $argv=[]) {
    array_shift($argv);
    $general_path = str_replace(FLY_ENV_CLI_PATH,'',__DIR__);
    if(!empty($argv)) {
        route_cli_commands($argc,$argv,$general_path);
    }
}

function route_cli_commands($argc, $argv,$general_path)
{   $argv_length = count($argv);
    $unshift_argvs = ['migrate:models'];
    $command_name = $argv[0];

    if(!in_array($command_name,$unshift_argvs)) array_shift($argv);

    switch($command_name) {
        case 'migrate:models':
            migrate_models($argv_length,$general_path);
        break;
        case 'migrate':
            migrate($argv,$general_path);
        break;
        case 'create:cv': case 'create:controller-view':
            create_controller_view($argv_length,$argv,$general_path);
        break;
        case 'create:c': case 'create:controller':
            create_controller($argv_length,$argv,$general_path);
        break;
        case 'create:v': case 'create:view':
            create_view($argv_length,$argv,$general_path);
        break;
        case 'create:validator': case 'create:vldt':
            create_validator($argv_length,$argv,$general_path);
        break;
        case 'create:activity': case 'create:ac':
            create_activity($argv_length,$argv,$general_path);
        break;
        case 'create:class':
            create_class($argv_length,$argv,$general_path);
        break;
        case 'create:w': case 'create:fml': case 'create:widget':
            create_widget($argv_length,$argv,$general_path);
        break;
        case 'drop:table':
            dropModelStore($argv,$general_path);
        break;
        case 'drop:db': case 'drop:database':
            dropModel($argv,$general_path);
        break;
        case 'make:model':
            make_model($argv,$general_path);
        break;
        default:
          echo PHP_EOL.">> fly-env: $command_name: command not found".PHP_EOL.PHP_EOL;
        break;
    }
}
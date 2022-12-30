<?php
define('FLY_ENV',DIRECTORY_SEPARATOR.'fly_env'.DIRECTORY_SEPARATOR.'initializer');
define('FLY_ENV_APP_CONTROLLERS','app/controllers/');
define('FLY_ENV_APP_MODELS','app/models/');
define('FLY_ENV_APP_VIEWS','app/views/');
define('FLY_ENV_INTERFACES','FLY_ENV\Util\Gene');
define('FLY_ENV_CORE_NAMESPACE','FLY_ENV/Util');
define('FLY_ENV_SQLPDO','SQLPDO');
define('FLY_ENV_MYSQLI','SQLI');
define('EXT','.php');

define('FLY_ENV_STATIC_HTMLS_PATH', 'app/statics/public/');
define('FLY_ENV_STATIC_FML_PORTAL_PATH','fly_env/util/dom/fml_portal.wave.php');
define('FLY_ENV_STATIC', 'app/statics/');

define('FLY_ENV_CONFIG','app/.app_reg');

define('FLY_ENV_UTIL_MVC_PATH','fly_env'.DIRECTORY_SEPARATOR.'util'.DIRECTORY_SEPARATOR.'mvc');

define('FLY_APP_ROOT_DIR', str_replace('fly_env'.DIRECTORY_SEPARATOR.'initializer','',__DIR__));

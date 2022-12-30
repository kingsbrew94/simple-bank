<?php
/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @version 2.0.0
 */

$app_uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);


if($app_uri !== '/' && file_exists(__DIR__.'/app'.$app_uri)) return false;


require_once('process.class.php');

?>

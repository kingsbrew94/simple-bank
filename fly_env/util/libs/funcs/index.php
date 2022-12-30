<?php
use FLY\DSource\App;
use FLY_ENV\Util\Routers\Pipe;
use FLY\Security\Sessions;
use FLY_ENV\Util\Wave_Engine\Pattern;

function import($url) {
    return require_once FLY_ENV_STATIC_HTMLS_PATH.$url.EXT;
}

function is_empty($var) {

    $flag1 = !is_array($var) && !is_object($var)? (!isset($var) xor (trim((string) $var) === "" || $var === NULL)): false;

    $flag2 = is_array($var)  && count($var) === 0;
    $flag3 = is_object($var) && count((array) $var) === 0;
    return $flag1 || $flag2 || $flag3;
}

function hex_str($hex_token)
{
  $val='';
  foreach(explode("\n",trim(chunk_split($hex_token,2))) as $h) $val.=chr(hexdec($h));
  return($val);
}

function url(string $path) {

    if($path === '/') $path = '';
    $url = App::http_referer().App::host_directory().$path;
    $flag_num = strpos($path,':');
    if($flag_num  === 0 && is_int($flag_num)) {
        $url_payload = Pipe::url_payload($path);
        if($url_payload !== null) {
            if($url_payload === '/') {
                $path = '';
                $url = App::http_referer();
            } else {
                if(strpos($url_payload,'/') === 0) {
                    $url_payload = str_replace('/','',App::host_directory()).rearrange_url_separators($url_payload);
                }
                $url = App::http_referer().$url_payload;
            }
        }
        if($url === App::http_referer()) $url .= App::host_directory();
    }
   
    return htmlentities($url);
}

function cdnurl(string $url)
{
    if(preg_match('/^(?:https:\/\/)/',$url)) return $url;
    return "https://".preg_replace('/^(?:http:\/\/)/',"",$url);
}

function rearrange_url_separators($url_payload) {
    if(App::host_directory() === "") {
        $new_url_payload_arr = [];

        $url_payload_arr = explode('/',$url_payload);
        foreach($url_payload_arr as $url_tip) {
            if(!preg_match('/^(?:\s*)$/',$url_tip)) {
                $new_url_payload_arr[] = $url_tip;
            } 
        }
        $url_payload = implode('/',$new_url_payload_arr);
    }
    return $url_payload;
}

function statics(string $path,$error_log = true) {
    $url = App::http_referer().FLY_ENV_STATIC.$path;
    if(isset($_SERVER['DOCUMENT_ROOT'])) {
        $doc_root_arr = explode('/',$_SERVER['DOCUMENT_ROOT']);
        $doc_root = $doc_root_arr[count($doc_root_arr) - 1];
        $self_file_arr = explode('/',$_SERVER['PHP_SELF']);
        
        if($doc_root === 'htdocs' || (count($self_file_arr) > 2 && $self_file_arr[2] === App::root_file()))
            $url = App::http_referer().App::host_directory().FLY_ENV_STATIC.$path;
    }
    if(file_exists(FLY_ENV_STATIC.$path)) return $url;
    else if(!$error_log) { return $url; }
    throw new Exception('The file at path '.$url.'does not exists');
}

function usecss(string $href_path)
{
    $href_path = trim($href_path.'.css');
    if(file_exists(FLY_ENV_STATIC.'css/'.$href_path))
        $href_path = 'css/'.$href_path;

    $statics = statics($href_path);
    return <<<CSS
        <link type="text/css" rel="stylesheet" href="{$statics}" media="screen,projection"/>        
CSS;
}

function usejs(string $src_path, $attr = []) {
    $src_path = trim($src_path.'.js');
    if(file_exists(FLY_ENV_STATIC.'js/'.$src_path))
        $src_path = 'js/'.$src_path;
    $statics = statics($src_path);
    $attr = set_static_attributes($attr);
    return <<<JS
        <script src="{$statics}"$attr></script>
JS;
}

function usecdnjs(string $src_path, $attr = []) 
{
    $src_path = trim($src_path);
    if(!preg_match('%^.+[.]js$%',$src_path)) 
        $src_path .= '.js';
    
    $attr = set_static_attributes($attr);
    return <<<JS
        <script src="{$src_path}"$attr></script>
JS;
}

function usecdncss(string $src_path, $attr = []) 
{
    $src_path = trim($src_path);
    if(!preg_match('%^.+[.]css$%',$src_path)) 
        $src_path .='.css';
    
    $attr = set_static_attributes($attr);
    return <<<CSS
        <link rel="stylesheet" type="text/css" href="{$src_path}"$attr/>
CSS;
}

function set_static_attributes(array $attrs)  
{
    $payload = " ";
    foreach($attrs as $attr => $val) {
        $payload .= $attr.'='.'"'.$val.'" ';
    }

    return $payload;
}

function csrf_token()
{
    $token = x_csrf();
    
    return <<<TKN
      <input type="hidden" name="csrf_token" value="{$token}"/>
TKN;
}

function x_csrf() 
{
    $token = create_token();
    Sessions::tokens('csrf_token',$token);
    return $token;
}

function create_token()
{
    return bin2hex(openssl_random_pseudo_bytes(strlen(uniqid(rand(),true))));

}

function dynamic_baseurl() {
    return statics('', false);
}

function thisYear() {
    return date('Y');
}

function thisMonth() {
    return date('M');
}

function dateQuery(string $dateText,string $dateQuery) {
    return date_format(date_create($dateText),$dateQuery);
}

function char_lmt(string $chars, int $limit=0) {
    
    if(strlen($chars) > $limit && $limit > 0) {
        $output ='';
        for($index = 0; $index < $limit; $index++) {
            $output .= $chars[$index];
        }
        return $output;
    }
    return $chars;
}

function word_lmt(string $words,int $limit=0) {
    $words = preg_replace(Pattern::htmlFullDataTag(),' $2',$words);
    $words = preg_replace(Pattern::htmlEmptyDataTag(),' ',$words);

    $words_array = preg_split('/\s+/',$words);

    $output = $limit === 0 ? $words: "";

    for($index = 0; $index < $limit; $index++) {
        if(!isset($words_array[$index])) break;
        $output .= $words_array[$index].' ';
    }
    return $output;
}

function str_capitalize(string $text,string $delimiter = "", $output_delimiter = "")
{
    $word_array = $delimiter === "" ? preg_split('/\s+/',$text) : explode($delimiter,$text);
    $output_array = [];

    foreach($word_array as $word) {
        array_push($output_array,ucfirst(trim($word)));
    }
    return implode($output_delimiter,$output_array);
}


function str_camel(string $text,string $delimiter = "", $output_delimiter = "")
{
    $word_array = $delimiter === "" ? preg_split('/\s+/',$text) : explode($delimiter,$text);
    $output_array = [];
    $count = 1;
    foreach($word_array as $word) {
        if($count === 1) {
            ++$count;
            array_push($output_array,strtolower(trim($word)));
            continue;
        }
        array_push($output_array,ucfirst(trim($word)));
    }
    return implode($output_delimiter,$output_array);
}

function srcurl($path)
{
    return FLY_APP_ROOT_DIR.$path;
}

function staticurl($path)
{
    return FLY_APP_ROOT_DIR.FLY_ENV_STATIC.$path;
}

/*
    *****************
    * Model Helpers *
    *****************
 */

/**
 * @function QIF
 * @todo   Build model field if condition
 * @return string
 */

function QIF(string $if, string $else): string 
{
    return "if:{{$if},{$else}}";
}

/**
 * @function QF
 * @todo   Build model field functions
 * @return string
 */

function QF(string $query_function_name,...$args): string 
{
    return $query_function_name.':{'.implode(',',$args).'}';
}
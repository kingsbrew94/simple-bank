<?php namespace FLY\Libs;

use FLY\Libs\Restmodels\Dto;
use FLY\Security\Verify;
use FLY_ENV\Util\Wave_Engine\Pattern;

class FLYFormValidator extends Verify {

    private static $request;

    private static $has_error = false;

    private static $error_data = [];

    private static $data_fields = [];

    private static $error_flags = [];

    public function __construct(Request $request = null)
    {
        self::$request = $request;
    }

    static public function check(Request $request,array &$error_messages = []): FLYFormValidator
    {
        $validator = new Self($request);
        $error_found = false;
        foreach($error_messages as $errKey => $errValue) {
            if($error_found === true) {
                break;
            }
            self::setRequestTypes(self::$request::all(),[$errKey => $errValue]);
            $request = self::$request::all();
            foreach($request as $data_type => $payload) {
                if(
                (self::strict_validate($data_type,$payload,self::$error_flags) === FALSE ||
                    self::optional_validate($data_type,$payload,self::$error_flags) === FALSE
                )
                ) {
                    $error_found = true;
                    break;
                } else {
                    $error_found = false;
                }
            }
        }
        $error_messages = null;
        unset($error_messages);
        self::$has_error = !empty(self::$error_data);
        self::reset_request();

        return $validator;
    }

    static private function setRequestTypes(array $requests, array $error_messages)
    {
        self::setDataFields($error_messages);
        $requestKeys = array_keys($requests);
        foreach($requestKeys as $keyValue) {
            $keyValue = self::sanitizeRequestKeys($keyValue);
            if(array_key_exists($keyValue,self::$data_fields)) {
                self::$request::change_key($keyValue,$keyValue.':'.self::$data_fields[$keyValue]);
            }
        }
        foreach(self::$data_fields as $field => $data_type) {
            if(!array_key_exists($field,$requests)) {
                self::$request::set("{$field}:{$data_type}",'');
            }
        }
    }

    static private function sanitizeRequestKeys($currentKeyValue): string
    {
        $newKey = trim(preg_replace('/[:](?:.*)/','', $currentKeyValue));
        self::$request::change_key($currentKeyValue,$newKey);
        return $newKey;
    }

    static private function setDataFields(array $error_messages)
    {
        $keys = array_keys($error_messages);
        foreach($keys as $value) {
            $fields = preg_split('/(?:\s*)[:](?:\s*)/',$value);
            if(count($fields) === 2) {
                if(!array_key_exists($fields[0],Request::all())) continue;
                self::$data_fields[$fields[0]] = $fields[1];
            }
            self::$error_flags[$fields[0]] = $error_messages[$value];
        }
    }

    public function has_error(): bool
    {
        return self::$has_error;
    }

    public function get_error_message(): Dto
    {
        return new Dto(self::$error_data['state']?? false,self::$error_data['payload']??'',self::$error_data['field_type']??'');
    }

    public function get_request(): Request
    {
        return self::$request;
    }

    static private function strict_validate($data_type,$payload,array $error_messages = []): bool
    {
        $response = self::check_strictly($data_type,$payload);
        self::set_strict_error($response,$data_type,$payload,$error_messages);
        return $response;
    }

    static private function optional_validate($data_type,$payload,array $error_messages = []): bool
    {
        $response = self::check_optionally($data_type,$payload);
        self::set_optional_error($response,$data_type,$payload,$error_messages);
        return $response;
    }

    static private function check_strictly($data_type,$payload)
    {
        $response = true;
        if(preg_match('%(?:\s*)\:(?:\s*)text%i',$data_type)):
            $response = self::raw_text_valid($payload);
        elseif(preg_match('%(?:\s*)\:(?:\s*)min\s*[|]\s*([0-9]*)\s*[|]%i',$data_type,$match)):
            $response = self::value_is_minimum($payload,$match[1]);
        elseif(preg_match('%(?:\s*)\:(?:\s*)max\s*[|]\s*([0-9]*)\s*[|]%i',$data_type,$match)):
            $response = self::value_is_maximum($payload,$match[1]);
        elseif(preg_match('%(?:\s*)\:(?:\s*)[|]\s*([0-9]*)\s*[|]%i',$data_type,$match)):
            $response = self::absolute_length($payload,$match[1]);
        elseif (preg_match('%(?:\s*)\:(?:\s*)email%i',$data_type)):
            $response = self::email_is_valid($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)url%i',$data_type)):
            $response = self::url_is_valid($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)ip%i',$data_type)):
            $response = self::ip_is_valid($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)salphaNum%i',$data_type)):
            $response = self::is_strict_alphaNumeric($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)alphaNum%i',$data_type)):
            $response = self::is_alphaNumeric($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)alpha%i',$data_type)):
            $response = self::is_alpha($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)tel%i',$data_type)):
            $response = self::is_telNumber($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)unum%i',$data_type)):
            $response = self::is_unsigned_number($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)snum%i',$data_type)):
            $response = self::is_signed_number($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)natNum%i',$data_type)):
            $response = self::is_natural_number($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)num%i',$data_type)):
            $response = self::is_number($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)int%i',$data_type)):
            $response = self::is_int_val($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)float%i',$data_type)):
            $response = self::is_float_val($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)double%i',$data_type)):
            $response = self::is_double_val($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)date%i',$data_type)):
            $response = self::is_date($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)time%i',$data_type)):
            $response = self::is_time($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)dateTime%i',$data_type)):
            $response = self::is_datetime($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)bool%i',$data_type)):
            $response = self::is_boolean($payload);
        elseif (preg_match('%(?:\s*)(?:\:)(?:\s*)\%([\w\d\s\W\S\D]*)%i',$data_type,$match)):
            $response = self::pattern_matched($payload,$match[1]);
        elseif (preg_match('%(?:\s*)(?:\:)(?:\s*)\(((?:[\w\d\s\W\S\D]*?))\)$%i',$data_type,$match)):
            $response = self::enum_is_matched($payload,explode(',',$match[1]));
        elseif (preg_match('%(?:\s*)(?:\:)(?:\s*)\{((?:[\w\d\s\W\S\D]*?))\}$%i',$data_type,$match)):
            $response = self::compare($payload,explode(',',$match[1]));
        endif;
        return $response;
    }

    static private function check_optionally($data_type,$payload)
    {
        $response = true;
        if(preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)text%i',$data_type)):
            $response = self::raw_text_valid($payload);
        elseif(preg_match('%(?:\s*)\:(?:\s*)[?]\s*min\s*[|]\s*([0-9]*)\s*[|]%i',$data_type,$match)):
            $response = self::value_is_minimum($payload,$match[1]);
        elseif(preg_match('%(?:\s*)\:(?:\s*)[?]\s*max\s*[|]\s*([0-9]*)\s*[|]%i',$data_type,$match)):
            $response = self::value_is_maximum($payload,$match[1]);
        elseif(preg_match('%(?:\s*)\:(?:\s*)[?]\s*[|]\s*([0-9]*)\s*[|]%i',$data_type,$match)):
            $response = self::absolute_length($payload,$match[1]);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)email%i',$data_type)):
            $response = self::email_is_valid($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)url%i',$data_type)):
            $response = self::url_is_valid($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)ip%i',$data_type)):
            $response = self::ip_is_valid($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)salphaNum%i',$data_type)):
            $response = self::is_strict_alphaNumeric($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)alphaNum%i',$data_type)):
            $response = self::is_alphaNumeric($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)alpha%i',$data_type)):
            $response = self::is_alpha($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)tel%i',$data_type)):
            $response = self::is_telNumber($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)unum%i',$data_type)):
            $response = self::is_unsigned_number($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)snum%i',$data_type)):
            $response = self::is_signed_number($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)natNum%i',$data_type)):
            $response = self::is_natural_number($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)num%i',$data_type)):
            $response = self::is_number($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)int%i',$data_type)):
            $response = self::is_int_val($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)float%i',$data_type)):
            $response = self::is_float_val($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)double%i',$data_type)):
            $response = self::is_double_val($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)date%i',$data_type)):
            $response = self::is_date($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)time%i',$data_type)):
            $response = self::is_time($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)dateTime%i',$data_type)):
            $response = self::is_datetime($payload);
        elseif (preg_match('%(?:\s*)\:(?:\s*)[?](?:\s*)bool%i',$data_type)):
            $response = self::is_boolean($payload);
        elseif (preg_match('%(?:\s*)(?:\:)(?:\s*)(?:[?])(?:\s*)\%([\w\d\s\W\S\D]*)%i',$data_type,$match)):
            $response = self::pattern_matched($payload,$match[1]);
        elseif (preg_match('%(?:\s*)(?:\:)(?:\s*)(?:[?])(?:\s*)\(((?:[\w\d\s\W\S\D]*?))\)$%i',$data_type,$match)):
            $response = self::enum_is_matched($payload,explode(',',$match[1]));
        elseif (preg_match('%(?:\s*)(?:\:)(?:\s*)(?:[?])(?:\s*)\{((?:[\w\d\s\W\S\D]*?))\}$%i',$data_type,$match)):
            $response = self::compare($payload,explode(',',$match[1]));
        endif;
        return $response;
    }

    static private function set_optional_error(&$response,$data_type,$payload,$error_messages)
    {
        if(($payload === "" && $response === FALSE) || ($payload !== "" && $response)) {
            self::$request->{self::get_data_field($data_type)} = $payload;
            $response = TRUE;
            self::$request::set_error(false);
        } else if(array_key_exists(self::get_data_field_var($data_type),$error_messages) && self::$has_error && !$response){
            self::$error_data = ['state' => false,'payload' => $error_messages[self::get_data_field_var($data_type)],'field_type' => $data_type];
            self::$request::set_error(true);
        } else if(self::$has_error && !$response) {
            self::$error_data = ['state' => false,'field_type' => $data_type];
            self::$request::set_error(true);
        }
        
    }

    static private function set_strict_error($response,$data_type,$payload,$error_messages)
    {
        if($response === TRUE) {
            Request::set(self::get_data_field($data_type), $payload);
            self::$request::set_error(false);
        } else if(array_key_exists(self::get_data_field_var($data_type),$error_messages) && self::$has_error){
            self::$error_data = ['state' => false,'payload' => $error_messages[self::get_data_field_var($data_type)],'field_type' => $data_type];
            self::$request::set_error(true);
        } else if(self::$has_error) {
            self::$error_data = ['state' => false,'field_type' => $data_type];
            self::$request::set_error(true);
        }
    }

    static private function get_data_field_var($data_type): string
    {
        return trim(preg_replace(Pattern::validationDataFields(),'',$data_type));
    }

    static private function get_data_field($data_type): string
    {
        $pattern  = Pattern::validationDataFields();

        if(isset($_REQUEST[$data_type]) && preg_match($pattern,$data_type)) unset($_REQUEST[$data_type]);
        return trim(preg_replace($pattern,'',$data_type));
    }

    static private function reset_request()
    {
        switch(self::$request::get_request_method()) {
            case 'post':
                $_POST = $_REQUEST;
                break;
            case 'get':
                $_GET = $_REQUEST;
                break;
        }
    }

    static private function absolute_length(&$data,$length): bool
    {
        return self::checker(function($data) use($length){
            return self::is_absolute($data,$length);
        },$data);
    }

    static private function value_is_minimum(&$data, $length): bool
    {
        return self::checker(function($data) use($length){
            return self::is_minimum($data,$length);
        },$data);
    }

    static private function value_is_maximum(&$data, $length): bool
    {
        return self::checker(function($data) use($length){
            return self::is_maximum($data,$length);
        },$data);
    }

    static private function raw_text_valid(&$data): bool
    {
        return self::checker(function($data){
            return self::rawText($data);
        },$data);
    }

    static private function compare(string $payload, array $requestKeys): bool
    {
        foreach($requestKeys as $key) {
            $key = trim($key);
            if(!self::compareRequests($payload,$key)) return false;
        }
        return true;
    }

    static private function compareRequests(string $payload, string $requestKey): bool
    {
        $requestKey = self::getCurrentRequestKey($requestKey);
        if(self::$request::exists($requestKey) &&  self::$request::get($requestKey) === $payload) {
            self::$has_error = false;
            return true;
        }
        self::$has_error = true;
        return false;
    }

    static private function getCurrentRequestKey(string $requestKey): string
    {
        $currentRequestKeys = array_keys(self::$request::all());

        $pattern = "/^({$requestKey}[:]?[\w\d\s\W\S\D]*)$/";
        foreach($currentRequestKeys as $key) {
            if(preg_match($pattern,$key,$match)) {
                return $match[1];
            }
        }
        return $requestKey;
    }

    static private function is_boolean(&$data): bool
    {
        return self::checker(function($data){
            return self::boolean($data);
        },$data);
    }
    static private function pattern_matched(&$data, string $pattern): bool
    {
        return self::checker(function($data) use($pattern){
            return self::pattern("%{$pattern}%xm",$data);
        },$data);
    }

    static private function enum_is_matched(&$data,array $enum): bool
    {
        return self::checker(function($data) use($enum){
            return self::enum($enum,$data);
        },$data);
    }

    static private function url_is_valid(&$data): bool
    {
        return self::checker(function($data){
            return self::url($data);
        },$data);
    }

    static private function ip_is_valid(&$data): bool
    {
        return self::checker(function($data){
            return self::ip($data);
        },$data);
    }

    static private function email_is_valid(&$data): bool
    {
        return self::checker(function($data){
            return self::email($data);
        },$data);
    }

    static private function is_alpha(&$data): bool
    {
        return self::checker(function($data){
            return self::alphaText($data);
        },$data);
    }


    static private function is_strict_alphaNumeric(&$data): bool
    {
        return self::checker(function($data){
            return self::strictAlphaNumeric($data);
        },$data);
    }

    static private function is_alphaNumeric(&$data): bool
    {
        return self::checker(function($data){
            return self::alphaNumeric($data);
        },$data);
    }

    static private function is_telNumber(&$data): bool
    {
        return self::checker(function($data){
            return self::telNumber($data);
        },$data);
    }

    static private function is_number(&$data): bool
    {
        return self::checker(function($data){
            return self::numeric($data);
        },$data);
    }

    static private function is_signed_number(&$data): bool
    {
        return self::checker(function($data){
            return self::signed_numeric($data);
        },$data);
    }

    static private function is_unsigned_number(&$data): bool
    {
        return self::checker(function($data){
            return self::unsigned_numeric($data);
        },$data);
    }

    static private function is_natural_number(&$data): bool
    {
        return self::checker(function($data){
            return self::natural_numeric($data);
        },$data);
    }

    static private function is_float_val(&$data)
    {
        return self::checker(function($data){
            return self::float($data);
        },$data);
    }

    static private function is_double_val(&$data)
    {
        return self::checker(function($data){
            return self::double($data);
        },$data);
    }

    static private function is_int_val(&$data)
    {
        return self::checker(function($data){
            return self::integer($data);
        },$data);
    }

    static private function is_date(&$data)
    {
        return self::checker(function($data){
            return self::date($data);
        },$data);
    }

    static private function is_time(&$data)
    {
        return self::checker(function($data){
            return self::time($data);
        },$data);
    }

    static private function is_datetime(&$data)
    {
        return self::checker(function($data){
            return self::float($data);
        },$data);
    }

    static private function checker($callback,&$data): bool
    {
        if($callback($data)) {
            $data = self::getValue();
            self::$has_error = false;
            return true;
        }
        self::$has_error = true;
        return false;
    }
}
<?php namespace FLY\Security;
define('STANDARD_EMAIL_PATTERN','/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD');
define('STANDARD_WEB_PATTERN','%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu');
define('STANDARD_IP_PATTERN','/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/');

class Verify {

    private static $validValue;

    private static $flag = false;

    public static function email(string $email)
    {
       self::$flag = false;
       if(self::checkEmail($email)) {
           $mail = filter_var(self::cleanData($email), FILTER_SANITIZE_EMAIL);
           if(filter_var($mail, FILTER_VALIDATE_EMAIL)) {
               self::$flag = true;
               self::$validValue = $mail;
           }
       }
       return self::$flag;
    }

    public static function url(string $url)
    {
        self::$flag = false;
        if(preg_match(STANDARD_WEB_PATTERN,$url)) {
            $url = filter_var($url, FILTER_SANITIZE_URL);
            if(filter_var($url, FILTER_VALIDATE_URL)) {
                self::$flag = true;
                self::$validValue = $url;
            }
        }
        return self::$flag;
    }

    public static function is_absolute(string $strvalue, int $value)
    {
        self::$flag = (strlen($strvalue) === $value);
        return self::$flag;
    }

    public static function is_minimum(string $strvalue, int $value)
    {
        self::$flag = (strlen($strvalue) >= $value);
        return self::$flag;
    }

    public static function is_maximum(string $strvalue, int $value)
    {
        self::$flag = (strlen($strvalue) <= $value);
        return self::$flag;
    }

    public static function ip(string $ip)
    {
        self::$flag = false;
        if(preg_match(STANDARD_IP_PATTERN,$ip)) {
            if(filter_var($ip, FILTER_VALIDATE_IP)) {
                self::$flag = true;
                self::$validValue = $ip;
            }
        }
        return self::$flag;
    }

    public static function alphaText(string $text)
    {
        $cleanText = self::cleanData($text);
        $cleanText = filter_var($cleanText, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        self::$flag      = self::checkText($cleanText);
        if(self::$flag) self::$validValue = $cleanText;
        return self::$flag;
    }

    private static function checkText(string $text)
    {
        if(!empty($text) && !(strpos($text," ") === 0)) {
            $pattern = '/^[A-Za-z\s]+$/';
            if(preg_match($pattern, $text)) {
                return true;
            }
        }
        return false;
    }

    private static function number(string $number, $is_tel = false)
    {
        $cleanNumber = self::cleanData($number);
        $cleanNumber = filter_var((int) $cleanNumber, FILTER_SANITIZE_NUMBER_INT);
        self::$flag  = filter_var($cleanNumber, FILTER_VALIDATE_INT);
        
        if(self::$flag) {
            $numberHasPlus = strpos($number,'+'); 
            $hasFrontZero = strpos($number,'0');
            self::$validValue = ($is_tel) ? ( 
                (is_int($numberHasPlus) && $numberHasPlus === 0) ? '+'.$cleanNumber 
                    : (
                        !((int) $hasFrontZero > 0 ) ? '0'.$cleanNumber : $cleanNumber
                    ) 
                ) : $cleanNumber;
        }
        return self::$flag;
    }

    private static function numIsInt($number)
    {
        $number = (string) $number;
        return preg_match('/^[-]?[0-9]+$/',$number) && is_int((int) $number);
    }

    public static function numeric(string $number)
    {
        self::$flag = is_numeric($number);
        return self::getDataFlag($number);        
    }

    public static function integer(string $number) 
    {
        return self::numeric($number) && self::numIsInt($number);
    }

    public static function float(string $number)
    {
        return self::numeric($number) && preg_match('/^[-]?[0-9]*[.][0-9]+$/',$number) && is_float((float)$number);
    }

    public static function double(string $number)
    {
        return self::numeric($number) &&  preg_match('/^[-]?[0-9]*[.][0-9]+$/',$number) && is_double((double) $number);
    }

    public static function signed_numeric(string $number)
    {
        return self::numeric($number) && preg_match('/^[-][0-9]*[.]?[0-9]+?$/',$number) && ((double) $number < 0);
    }

    public static function unsigned_numeric(string $number)
    {
        return self::numeric($number) && preg_match('/^[0-9]*[.]?[0-9]+?$/',$number) && ((double) $number >= 0);
    }

    public static function natural_numeric(string $number)
    {
        return self::integer($number) && preg_match('/^[1-9]+$/',$number) && ((int) $number > 0);
    }

    public static function date(string $date)
    {
        self::$flag = false;
        if(!is_empty($date)) {
            $day   = dateQuery($date,'d');
            $month = dateQuery($date,'m');
            $year  = dateQuery($date,'y');
            self::$flag = checkdate($month,$day,$year);
            self::$validValue = dateQuery($date,'Y-m-d');
        }
        return self::$flag;
    }

    public static function time(string $time)
    {
        self::$flag = false;
        $time = preg_replace('/.*([0-9][0-9][:][0-9][0-9][:][0-9][0-9]\s*(?:AM|PM)?).*/i','$1',$time);
        $time_flag = (
            !preg_match('/^[a-zA-Z]+$/',$time) &&
            preg_match('/.*((?:[0-9][0-9]|[0-9])[:][0-9][0-9][:][0-9][0-9]\s*(?:AM|PM)?).*/',$time,$match)
        );
        if(!is_empty($time) && $time_flag) {
            $hrs = dateQuery($time,'H');
            $min = dateQuery($time,'i');
            $sec = dateQuery($time,'s');
            self::$flag =  self::integer($hrs) && self::integer($min) && self::integer($sec);
        }
        return self::getDataFlag($time);
    }

    public static function datetime(string $datetime)
    {
        self::$flag = false;
        if(!is_empty($datetime)) {
            self::$flag = self::date($datetime) && self::time($datetime);
            self::$validValue = dateQuery($datetime,'Y-m-d h:i:s');
        }
        return self::$flag;
    }

    public static function telNumber(string $tel)
    {
        $len = strlen($tel);
        self::$validValue = str_replace('+','00',$tel);

        if(self::number($tel, true) && ($len >= 10 && $len <= 15)) {
           return true;
        }
        return false; 
    }

    public static function boolean(&$data) 
    {
        self::$flag = is_bool($data);
        return self::getDataFlag($data); 
    }

    public static function enum(array $enum, string &$data)
    {
        foreach($enum as $index => $num) {
            $enum[$index] = trim($num);
        }
        self::$flag = \in_array(trim($data),$enum);
        return self::getDataFlag($data);       
    }

    public static function pattern(string $pattern, string &$data)
    {
        self::$flag = preg_match($pattern,$data);
        return self::getDataFlag($data);        
    }

    public static function rawText(string &$rawtext)
    {
        return self::checkRawText($rawtext);
    }

    public static function alphaNumeric(string $text) 
    {
        $cleanText = self::cleanData($text);
        $cleanText = filter_var($cleanText, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        self::$flag = self::checkAlphaNumeric($cleanText);
        
        return self::getDataFlag($cleanText);
    }

    public static function strictAlphaNumeric(string $text) 
    {
        $cleanText = self::cleanData($text);
        $cleanText = filter_var($cleanText, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        self::$flag = self::checkAlphaNumeric($cleanText) && self::checkStrictAlphaNumeric($cleanText);
        return self::getDataFlag($cleanText);
    }

    private static function checkStrictAlphaNumeric(string $text)
    {
        $textLen = strlen($text);
        $isAlpha = false;
        $isNum = false;
        for($index = 0; $index < ($textLen - 1); $index++) {
            if(ctype_alpha($text[$index])) {
                $isAlpha = true;
                break;
            }
        }
        for($index = 0; $index < ($textLen - 1); $index++) {
            if(is_numeric($text[$index])) {
                $isNum = true;
                break;
            }
        }
        return $isAlpha && $isNum;
    }

    private static function checkAlphaNumeric(string $text) 
    {
        return (!empty($text) && !(strpos($text," ") === 0)) && 
               ((ctype_alnum($text) || preg_match('%^[a-zA-Z0-9][a-zA-Z0-9\s]*$%',$text)));
    }

    private static function getDataFlag($data): bool
    {
        if(self::$flag) self::$validValue = self::cleanData($data);
        return self::$flag;
    }

    private static function checkRawText(string $rawtext)
    {
        if(!empty($rawtext)) {
            self::$validValue = self::cleanData($rawtext);
            return true;
        }
        return false;
    }

    private static function checkEmail(string $email)
    {
        $atIndex = strpos($email,"@");
        $dotIndex = strpos($email,".");
        $partOne = explode("@",$email);
       
        if(count($partOne) === 2) {
            $partTwo = explode(".",$partOne[1]);

            if(count($partTwo) === 2) {
                if($atIndex >= 1 && ($atIndex < ($dotIndex - 1) || ($dotIndex - 1) > 0) && ($dotIndex >($atIndex + 1) || ($atIndex+1) > 1)) {
                    $pattern =  '/^[A-Za-z0-9\._]+$/';
                    $firstPart  = $partOne[0];
                    $secondPart = $partTwo[0];
                    $thirdPart  = $partTwo[1];
                    if(
                        preg_match($pattern, $firstPart) && preg_match($pattern, $secondPart) &&
                        preg_match($pattern, $thirdPart) && preg_match(STANDARD_EMAIL_PATTERN, $email)
                    ) {
                        return true;
                    } 
                } 
            } 
        }
        return false;
    }

    public static function getValue() 
    {
        return self::$validValue;
    }

    private static function cleanData(string $data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = filter_var($data, FILTER_SANITIZE_STRING);
        $data = htmlentities($data);
        return $data;
    }
}
<?php namespace FLY\Security;

class KeyGen 
{
    private static $charbank = [];

    private static $tokenbank = [];

    private static $primaryKey = "";

    private static $charbankLength;

    private static $tokenbankLength;

    private static $tokenLength;

    private static $token;

    private static $charlength;

    public function __construct()
    {
        self::$charbank = [
            "QWERTYUIOP123",
            "ASDFGHJKL4560",
            "ZXCVBNM789479"
        ];

        self::$charbankLength = count(self::$charbank) - 1;
        self::$charlength = strlen(self::$charbank[0]) - 1;
        self::$primaryKey = "";
    }

    private static function setTknLen()
    {
        self::$tokenbankLength = count(self::$tokenbank) - 1;
        self::$tokenLength = strlen(self::$tokenbank[0]) - 1;
        self::$token = "";
    }

    private static function set_tokens()
    {
        self::$tokenbank = [
            "QqWwEeRrTtYyUuIiOoP123$",
            "AaSsDdFfGgHhJjKkLl4560.",
            "ZzXxCcVvBbNnMm78947t95_"
        ];
        self::setTknLen();
    }

    private static function set_numberTokens()
    {
        self::$tokenbank = [
            "013579111021517192123002527",
            "024681012104161820222420068",
            "000025711131719233137414751"
        ];
        self::setTknLen();
    }

    private static function set_alphNumTokens()
    {
        self::$tokenbank = [
            "QqWwEeRrTtYyUuIiOoP123",
            "AaSsDdFfGgHhJjKkLl4560",
            "ZzXxCcVvBbNnMm78947t95"
        ];
        self::setTknLen();
    }

    private static function set_smallAlphNumTokens()
    {
        self::$tokenbank = [
            "abcdefghijklm",
            "0123456789101",
            "nopqrstuvwxyz",
            "1213241516178"
        ];
        self::setTknLen();
    }

    private static function set_bigAlphNumTokens()
    {
        self::$tokenbank = [
            "ABCDEFGHIJKLM",
            "0123456789101",
            "NOPQRSTUVWXWZ",
            "1213241516178"
        ];
        self::setTknLen();
    }

    public static function primary_digits(int $MAX_RANGE,string $format="",bool $set_time=false,string $sep="", int $sep_sequence=-1)
    {
        self::set_numberTokens();
        return self::addSeparator(self::tokenGenerate($MAX_RANGE,$format,$set_time),$sep,$sep_sequence);
    }

    public static function primary_key(int $MAX_RANGE,string $format="",bool $set_time=false,string $sep="", int $sep_sequence=-1)
    {
        new Self;

        if(isset($MAX_RANGE)) {
            
            for($i = 0; $i < $MAX_RANGE; $i++) {
                $randIndexcontroller = mt_rand(0, self::$charbankLength);
                $randIndex = mt_rand(0, self::$charlength); 
                self::$primaryKey .= self::$charbank[$randIndexcontroller][$randIndex];

                if(strlen(self::$primaryKey) === $MAX_RANGE) break;
            }
           
            $temp = self::$primaryKey;
            if($set_time === true)
                self::$primaryKey = self::get_time().self::$primaryKey;
            $temp = str_replace('%key',self::$primaryKey,$format);
            return self::addSeparator(is_empty($temp) ? (self::$primaryKey) : $temp,$sep,$sep_sequence);
        }else {
            throw new \Exception("Key range not set");
        }
    }

    public static function alpha_num_token(int $MAX_RANGE,string $format="",bool $set_time=false,string $sep="", int $sep_sequence=-1)
    {
        self::set_alphNumTokens();
        return self::addSeparator(self::tokenGenerate($MAX_RANGE,$format,$set_time),$sep,$sep_sequence);
    }

    public static function alpha_num_lc_token(int $MAX_RANGE,string $format="",bool $set_time=false,string $sep="", int $sep_sequence=-1)
    {
        self::set_smallAlphNumTokens();
        return self::addSeparator(self::tokenGenerate($MAX_RANGE,$format,$set_time),$sep,$sep_sequence);
    }

    public static function alpha_num_uc_token(int $MAX_RANGE,string $format="",bool $set_time=false,string $sep="", int $sep_sequence=-1)
    {
        self::set_bigAlphNumTokens();
        return self::addSeparator(self::tokenGenerate($MAX_RANGE,$format,$set_time),$sep,$sep_sequence);
    }

    public static function token(int $MAX_RANGE,string $format="",bool $set_time=false,string $sep="", int $sep_sequence=-1)
    {
        self::set_tokens();
        return self::addSeparator(self::tokenGenerate($MAX_RANGE,$format,$set_time),$sep,$sep_sequence);
    }

    static private function tokenGenerate(int $MAX_RANGE,string $format="",bool $set_time=false)
    {
        if(isset($MAX_RANGE)) {
            for($i = 0; $i < $MAX_RANGE; $i++) {
                $randIndexcontroller = mt_rand(0, self::$tokenbankLength);
                $randIndex = mt_rand(0, self::$tokenLength); 
                self::$token .= self::$tokenbank[$randIndexcontroller][$randIndex];
                if(strlen(self::$token) === $MAX_RANGE) break;
            }
           
            if($set_time === true)
                self::$token = self::get_time().self::$token;
            $temp = str_replace('%key',self::$token,$format);
            self::$token = is_empty($temp) ? self::$token : $temp;
            return self::$token;
        }else {
            throw new \Exception("Key range not set");
        }
    }

    static private function addSeparator(string $token,string $sep,int $seqNum)
    {
        if(is_empty($sep) || $seqNum <= 0) return $token;

        $tokenLen = strlen($token);
        $temp = "";
        for($i = 0; $i < $tokenLen; $i++) {
            $temp .= $token[$i];
            if(($i + 1) === $seqNum && ($i + 1) < $tokenLen) $temp .= $sep;
        }
        return $temp;
    }

    static private function get_time()
    {
        $datetime = new \DateTime;
        $date_str = preg_replace('/\-|(?:\s+)|\:|0/','',$datetime->format('Y-m-d H:i:s.u'));
        $date_str = explode('.',$date_str)[0];
        return $date_str;
    }
}
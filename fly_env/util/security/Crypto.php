<?php namespace FLY\Security;

/**
 * Description of Crypto
 *
 * @author K.B Brew
 */

class Crypto  {
    
    private static $data;

    private static $password;

    private static $salt;

    public function __construct($raw_value, $salt)
    {
        self::$password = $raw_value;
        self::$salt     = $salt;
    }

    public static function lock($raw_value, $salt,$algorithm = PASSWORD_BCRYPT)
    {
        new Self($raw_value, $salt);

        return self::encrypt($algorithm);
    }

    private static function encrypt($algorithm)
    {
        self::set_mds_sha_crypt();

        self::$data = password_hash(self::$data,$algorithm,[strlen(self::$data)]);

        return self::$data;
    }

    private static function set_mds_sha_crypt()
    {
        self::$data = \md5(self::$password);
        self::$data =  sha1(self::$data);
        self::$data =  crypt(self::$data, sha1(self::$data));
    }

    public static function verify($raw_value, string $encrypted_value,  $key)
    {
        new Self($raw_value, $key);
        return self::check($encrypted_value);
    }

    private static function check(string $encrypted_value)  
    {
        self::set_mds_sha_crypt();
        return password_verify(self::$data,$encrypted_value);
    }

}

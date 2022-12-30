<?php namespace FLY_ENV\Util\Syncs;

abstract class Config {

    private static $config;

    private static $dbase;

    private static $host;

    private static $user;

    private static $password;

    private $access_type;

    public function __construct($access_type ,$get = true) 
    {
       self::$config = parse_ini_file(FLY_ENV_CONFIG, $get);
        if($access_type !== null && $access_type !== 'USE_OVERRIDE') {
            $this->access_type = $access_type;
        }
    }

    protected static function getConfig() 
    {
        return self::$config;
    }

    protected function assign_configs($config_type)
    {
        switch($this->access_type) {
            case 'set_host':
                $this->setHost(self::$config['SERVER_CONFIG_HOSTS'][$config_type]);
            break;
            case 'set_user':
                $this->setUser(self::$config['SERVER_CONFIG_USERS'][$config_type]);
            break;
            case 'set_password':
                $this->setPassword(self::$config['SERVER_CONFIG_PASSWORDS'][$config_type]);
            break;
            case 'set_model':
                $this->setDBase(self::$config['APP_MODELS'][$config_type]);
            break;
        }
    }

    static public function host($host_name = 'default')
    {
        self::$host = self::$config['SERVER_CONFIG_HOSTS'][$host_name];
    }

    static public function password($password = 'default')
    {
        self::$password = self::$config['SERVER_CONFIG_PASSWORDS'][$password];
    }

    static public function user($user_name = 'default')
    {
        self::$user = self::$config['SERVER_CONFIG_USERS'][$user_name];
    }

    static public function name($name = 'default')
    {
        self::$dbase = self::$config['APP_MODELS'][$name];
    }

    private function setHost($host)
    {
        self::$host = $host;
    }
    
    private function setUser($user)
    {
        self::$user = $user;
    }
    
    private function setPassword($password)
    {
        self::$password = $password;
    }
    
    private static function setDBase($dbase) 
    {
        self::$dbase = $dbase;
    }

    protected static function getDBase()
    {
        return self::$dbase === null ? self::$config['APP_MODELS']['default'] : self::$dbase;
    }

    protected static function getHost() 
    { 
        return self::$host === null ? self::$config['SERVER_CONFIG_HOSTS']['default'] : self::$host; 
    }

    protected static function getUser() 
    { 
        return self::$user === null ? self::$config['SERVER_CONFIG_USERS']['default'] : self::$user; 
    }

    protected static function getPassword() 
    { 
        return self::$password === null ? self::$config['SERVER_CONFIG_PASSWORDS']['default'] : self::$password; 
    }
    

    protected function get_config_keys($name) {
        $config_access_keys = array(
            'set_host',
            'set_user',
            'set_password',
            'set_model'
        );
        
        if(in_array($name,$config_access_keys)) {
            return $this->route_config_access($name);
        }
        
        throw new \Exception('Undefined config access key: "'.$name.'"');
    }

    private function route_config_access($name)
    {
        return new class($name) extends Config {

            public function __construct($name) {
                parent::__construct($name);
            }

            public function __get($config_type)
            {
                $this->assign_configs($config_type);
            }
        };
    }
}
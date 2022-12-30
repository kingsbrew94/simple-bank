<?php

/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model\Algorithm
 * @version 3.0.0
 */

namespace FLY\Model\Algorithm;

/**
 * @class Config
 * @todo Set database configuration
 */

 
abstract class Config {

    /**
     * @var $config
     * @todo Stores configurations
     */
    private static array $config;


    /**
     * @var $dbase
     * @todo Stores database name
     */
    private string $dbase;


    /**
     * @var $host
     * @todo Stores database host name
     */
    private string $host;


    /**
     * @var $user
     * @todo Stores database username
     */
    private string $user;


    /**
     * @var $password
     * @todo Stores database password
     */
    private string $password;


    /**
     * @method void setUp()
     * @param boolean $get
     * @param array $credentials
     * @return void
     * @todo Initializes database configurations 
     */

    protected function setUp(array $credentials,bool $get=true)
    {
       self::$config   = parse_ini_file(FLY_ENV_CONFIG, $get);
       $this->host     = self::$config['SERVER_CONFIG_HOSTS'][$credentials['host']?? 'default'];
       $this->user     = self::$config['SERVER_CONFIG_USERS'][$credentials['user']?? 'default'];
       $this->password = self::$config['SERVER_CONFIG_PASSWORDS'][$credentials['password']?? 'default'];
       $this->dbase    = self::$config['APP_MODELS'][$credentials['model']?? 'default'];
    }

    
    /**
     * @method string getHost()
     * @return string
     */

    protected function getHost(): string
    {
        return $this->host;
    }


    /**
     * @method string getUser()
     * @return string
     */

    protected function getUser(): string
    {
        return $this->user;
    }


    /**
     * @method string getPassword()
     * @return string
     */

    protected function getPassword(): string
    {
        return $this->password;
    }


    /**
     * @method string getModel()
     * @return string
     */

    protected function getModel(): string
    {
        return $this->dbase;
    }
}
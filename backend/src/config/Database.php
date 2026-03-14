<?php

namespace App\config;


use Simplon\Mysql\PDOConnector;
use Simplon\Mysql\Mysql;

/**
 * Class Database
 * @package App\config
 */
class Database
{
    /**
     * @var array
     */
    private static $instances = [];
    
    /**
     * Database constructor.
     */
    protected function __construct() { }
    
    protected function __clone() { }
    
    public function __wakeup()
    {
        throw new \RuntimeException('Cannot unserialize a singleton.');
    }
    
    /**
     * @return Database
     */
    public static function getInstance(): Database
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }
        
        return self::$instances[$cls];
    }
    
    /**
     * @return Mysql
     * @throws \Exception
     */
    public function dbConn(): Mysql
    {
        $pdo = new PDOConnector(
            getenv('DB_HOST'),            // docker name of mysql container
            getenv('DB_USER_NAME'),      // user
            getenv('DB_PASSWORD'),      // password
            getenv('DB_NAME')          // database
        );
        
        $pdoConn = $pdo->connect('utf8', []); // charset, options
        return new Mysql($pdoConn);
    }
}

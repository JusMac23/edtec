<?php

// Include the config file
require_once dirname(__DIR__) . '/config.php';

class Database 
{
    private static $instance = null;
    private $pdo;

    private function __construct() 
    {
        $host    = DB_HOST;
        $db      = DB_NAME;
        $user    = DB_USER;
        $pass    = DB_PASS;
        $charset = DB_CHARSET;

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     
            PDO::ATTR_EMULATE_PREPARES   => false,                  
            PDO::ATTR_STRINGIFY_FETCHES  => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $charset COLLATE {$charset}_unicode_ci"
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            error_log("DB Connection Error: " . $e->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
            die("Database connection failed: " . $e->getMessage()); // Displays exact error while testing
        }
    }

    private function __clone() {}
    public function __wakeup() { throw new \Exception("Cannot unserialize a singleton."); }

    public static function getInstance() 
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() 
    {
        return $this->pdo;
    }
}
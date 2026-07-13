<?php

// Step up one directory level to find the root vendor folder
require_once dirname(__DIR__) . '/vendor/autoload.php';

class Database 
{
    private static $instance = null;
    private $pdo;

    private function __construct() 
    {
        try {
            // Look for the .env file at the project root level
            $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
            $dotenv->load();

            $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);
        } catch (\Exception $e) {
            error_log("Environment configuration error: " . $e->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
            die("Server configuration error.");
        }

        $host    = $_ENV['DB_HOST'];
        $db      = $_ENV['DB_NAME'];
        $user    = $_ENV['DB_USER'];
        $pass    = $_ENV['DB_PASS'];
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        // 1. DSN includes the strict charset
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            // 2. Strict Error Mode: Never leak SQL syntax errors to the browser
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
            
            // 3. Default Fetch Mode: Associative arrays are safest and cleanest
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     
            
            // 4. THE ULTIMATE DEFENSE: Forces real native prepared statements
            PDO::ATTR_EMULATE_PREPARES   => false,                  
            
            // 5. ENHANCEMENT: Maintain correct data types (integers stay integers, not strings)
            PDO::ATTR_STRINGIFY_FETCHES  => false,
            
            // 6. ENHANCEMENT: Force the charset handshake on connection to prevent encoding-based SQLi
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $charset COLLATE {$charset}_unicode_ci"
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            // Secure error logging: We log the real error silently, but show a safe, generic message to the user
            error_log("DB Connection Error: " . $e->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
            die("Database connection failed. Please try again later.");
        }
    }

    // Prevent cloning and unserializing of the Singleton instance
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
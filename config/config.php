<?php
declare(strict_types=1);

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));   // абсолютный путь на диске до /statistics

// Настройки подключения к БД
define('DB_HOST', 'SQL2008');
define('DB_NAME', 'test_statistics');
define('DB_USER', 'sa');
define('DB_PASS', 'afrp324b');
define('DB_PORT', '1433');
define('DB_DRIVER', 'sqlsrv'); // 'sqlsrv' или 'odbc'

// Установка кодировки
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Europe/Moscow');

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            if (DB_DRIVER === 'sqlsrv') {
                // Подключение через PDO SQLSRV
                $dsn = "sqlsrv:Server=" . DB_HOST . "," . DB_PORT . ";Database=" . DB_NAME;
                
                // Отключаем проверку SSL для самоподписанных сертификатов
                $dsn .= ";TrustServerCertificate=1;";
                
                $this->connection = new PDO(
                    $dsn,
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
                
                // Настройка кодировки
                $this->connection->exec("SET ANSI_NULLS ON");
                $this->connection->exec("SET ANSI_WARNINGS ON");
                $this->connection->exec("SET QUOTED_IDENTIFIER ON");
                
            } elseif (DB_DRIVER === 'odbc') {
                // Подключение через ODBC
                $dsn = "Driver={ODBC Driver 18 for SQL Server};" .
                       "Server=" . DB_HOST . "," . DB_PORT . ";" .
                       "Database=" . DB_NAME . ";" .
                       "TrustServerCertificate=yes;";
                
                $this->connection = odbc_connect($dsn, DB_USER, DB_PASS);
                
                if (!$this->connection) {
                    throw new Exception("ODBC Connection failed: " . odbc_errormsg());
                }
                
            } else {
                throw new Exception("Unsupported database driver");
            }
            
            
        } catch (Exception $e) {
            // Логируем ошибку но не показываем детали пользователю
            $this->logError($e->getMessage());
            die("Ошибка подключения к базе данных. Пожалуйста, обратитесь к администратору.");
        }
    }
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            if (DB_DRIVER === 'sqlsrv') {
                $stmt = $this->connection->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Ошибка подготовки запроса");
                }
                
                if (!empty($params)) {
                    $stmt->execute($params);
                } else {
                    $stmt->execute();
                }
                
                return $stmt;
                
            } elseif (DB_DRIVER === 'odbc') {
                $stmt = odbc_prepare($this->connection, $sql);
                if (!$stmt) {
                    throw new Exception("ODBC Prepare error: " . odbc_errormsg());
                }
                
                if (!empty($params)) {
                    odbc_execute($stmt, $params);
                } else {
                    odbc_execute($stmt);
                }
                
                return $stmt;
            }
            
        } catch (Exception $e) {
            $this->logError($e->getMessage() . " SQL: " . $sql);
            return false;
        }
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        
        if (!$stmt) {
            return [];
        }
        
        if (DB_DRIVER === 'sqlsrv') {
            return $stmt->fetchAll();
        } elseif (DB_DRIVER === 'odbc') {
            $result = [];
            while ($row = odbc_fetch_array($stmt)) {
                $result[] = $row;
            }
            return $result;
        }
        
        return [];
    }
    
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        
        if (!$stmt) {
            return null;
        }
        
        if (DB_DRIVER === 'sqlsrv') {
            return $stmt->fetch();
        } elseif (DB_DRIVER === 'odbc') {
            return odbc_fetch_array($stmt);
        }
        
        return null;
    }
    
    private function logError($message) {
        $logFile = 'sql_errors.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    
    public function testConnection() {
        try {
            $result = $this->fetchOne("SELECT @@VERSION as version");
            return $result ? true : false;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Инициализация базы данных
$db = Database::getInstance();
$pdo = $db->getConnection();

// Функция для безопасного вывода
function safeEcho($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/* Проверка соединения (опционально)
if (!$db->testConnection()) {
    // Можно добавить обработку ошибки
    error_log("Database connection test failed");
}
?> */
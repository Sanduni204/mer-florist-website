<?php

/**
 * Config loader
 * - Loads a local `.env` file (if present) and injects values into environment
 * - Reads common DB/APP variables from environment with sensible defaults
 */

// Load a simple .env parser (no external dependency)
if (!function_exists('load_dotenv_file')) {
    function load_dotenv_file($path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            return;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') === false) continue;
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            // remove surrounding quotes
            if ((substr($value,0,1) === '"' && substr($value,-1) === '"') || (substr($value,0,1) === "'" && substr($value,-1) === "'")) {
                $value = substr($value,1,-1);
            }
            // only set when not already set in environment
            if (getenv($name) === false) {
                putenv("$name=$value");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

// Look for a .env in project root (one level up from Config)
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    load_dotenv_file($envPath);
}

// Define APPURL if not already defined; allow override via APPURL env var
if (!defined('APPURL')) {
    $envAppUrl = getenv('APPURL');
    define('APPURL', $envAppUrl ? rtrim($envAppUrl, '/') . '/' : 'http://localhost/mer_ecommerce/');
}

// Guard constant definitions but prefer environment variables (common names)
if (!defined('HOSTNAME')) {
    $hostFromEnv = getenv('DB_HOST') ?: getenv('HOSTNAME');
    define('HOSTNAME', $hostFromEnv ? $hostFromEnv : 'localhost');
}
if (!defined('DBNAME')) {
    $dbFromEnv = getenv('DB_NAME') ?: getenv('DBNAME');
    define('DBNAME', $dbFromEnv ? $dbFromEnv : 'mer');
}
if (!defined('USER')) {
    $userFromEnv = getenv('DB_USER') ?: getenv('USER');
    define('USER', $userFromEnv ? $userFromEnv : 'root');
}
if (!defined('PASS')) {
    $passFromEnv = getenv('DB_PASS') ?: getenv('PASS');
    define('PASS', $passFromEnv ? $passFromEnv : '');
}
// Optional port override
if (!defined('DB_PORT')) {
    $portEnv = getenv('DB_PORT');
    if ($portEnv) {
        define('DB_PORT', (int) $portEnv);
    }
}

// If a connection already exists, don't create another one
if (!isset($conn) || !($conn instanceof PDO)) {
    $ports = [3308, 3307, 3306];
    $hosts = ["127.0.0.1", HOSTNAME];
    $lastException = null;

    foreach ($hosts as $HOST) {
        foreach ($ports as $PORT) {
            try {
                $dsn = "mysql:host=" . $HOST . ";port=" . $PORT . ";dbname=" . DBNAME . ";charset=utf8mb4";
                $conn = new PDO($dsn, USER, PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                // Connected successfully; break out
                $lastException = null;
                break 2;
            } catch (PDOException $e) {
                $lastException = $e;
            }
        }
    }

    // Ensure cart_items table exists
    try {
        $conn->exec("CREATE TABLE IF NOT EXISTS cart_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            item_id INT NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY user_item (user_id, item_id)
        )");
    } catch (PDOException $e) {}

    if (!isset($conn) || !($conn instanceof PDO)) {
        $message = "Database connection failed. Tried hosts: " . implode(", ", $hosts) . "; ports: " . implode(", ", $ports) . ". Error: " . ($lastException ? $lastException->getMessage() : 'unknown');
        die($message);
    }
}

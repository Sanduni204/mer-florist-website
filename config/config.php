<?php

// Define APPURL if not already defined
if (!defined('APPURL')) {
    define('APPURL', 'http://localhost/mer_ecommerce/');
}

// Guard constant definitions to avoid redefinition notices
if (!defined('HOSTNAME')) { define('HOSTNAME', 'localhost'); }
if (!defined('DBNAME'))   { define('DBNAME',   'mer'); }
if (!defined('USER'))     { define('USER',     'root'); }
if (!defined('PASS'))     { define('PASS',     ''); }

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

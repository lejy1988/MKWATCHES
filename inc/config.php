<?php

declare(strict_types=1);

session_start();

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'mk_watches');
define('DB_USER', 'root');
define('DB_PASS', '');


$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
   
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}
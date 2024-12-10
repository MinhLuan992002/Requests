<?php
if (!defined('DB_HOST')) {
    define("DB_HOST", "localhost:3309");
}

if (!defined('DB_USER')) {
    define("DB_USER", "root");
}

if (!defined('DB_PASS')) {
    define("DB_PASS", "");
}

if (!defined('DB_NAME')) {
    define("DB_NAME", "requestsapp");
}
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage();
    exit();
}
?>

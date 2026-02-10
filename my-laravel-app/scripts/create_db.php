<?php

try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;charset=utf8mb4", 'root', '');
    $pdo->exec("CREATE DATABASE IF NOT EXISTS laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "CREATED\n";
} catch (Exception $e) {
    echo 'ERR: ' . $e->getMessage() . "\n";
    exit(1);
}

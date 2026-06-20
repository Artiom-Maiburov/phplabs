<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    // Безопасные настройки сессий (Защита от Session Hijacking)
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();
}

function getDBConnection(): PDO {
    $host = 'localhost';
    $db   = 'strange_portal';
    $user = 'root';
    $pass = ''; 
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}

// Вспомогательная функция защиты от XSS (Требования безопасности)
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
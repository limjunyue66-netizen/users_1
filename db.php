<?php
declare(strict_types=1);

function db_connect(): PDO
{
    $host = 'localhost';
    $db_name = 'users_db';
    $username = 'root';
    $password = '070817';

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$db_name;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return $pdo;

    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
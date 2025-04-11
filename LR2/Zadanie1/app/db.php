<?php
$host = 'db'; // имя сервиса из docker-compose
$db   = 'mydb';
$user = 'user';
$pass = 'userpass';
$charset = 'utf8';

$dsn = "pgsql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Ошибка подключения к БД: " . $e->getMessage();
    exit;
}
?>

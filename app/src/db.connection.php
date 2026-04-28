<?php
$host = 'localhost';
$dbname = 'canalti';
$user = 'root';
$pass = 'root';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} 
catch (PDOException $e) {
    error_log("Erro na conexao: " . $e->getMessage());
    http_response_code(500);
    exit;
}
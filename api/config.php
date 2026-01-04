<?php
// Configuration PDO minimal — modifiez les valeurs ci-dessous selon votre environnement
header('Content-Type: application/json; charset=utf-8');

$DB_HOST = '127.0.0.1';
$DB_NAME = 'artricot';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

return $pdo;

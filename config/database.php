<?php

$databaseUrl = getenv('MYSQL_URL');

if (!$databaseUrl) {
    die('MYSQL_URL não definida');
}

$db = parse_url($databaseUrl);

$host = $db['host'];
$port = $db['port'] ?? 3306;
$user = $db['user'];
$pass = $db['pass'];
$name = ltrim($db['path'], '/');

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die('Erro ao conectar ao banco de dados');
}

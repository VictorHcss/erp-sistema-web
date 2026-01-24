<?php
require __DIR__ . '/../config/database.php';

$sql = file_get_contents(__DIR__ . '/../dbschema/schema.sql');

try {
    $pdo->exec($sql);
    echo "Banco instalado com sucesso!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
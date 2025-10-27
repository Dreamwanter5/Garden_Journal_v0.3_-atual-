<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$dsn = "mysql:host=localhost;port=3306;dbname=gardenjournal;charset=utf8";
$user = "root";
$pass = "";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    echo "Conexão OK. Versão MySQL: " . $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION);
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
    error_log("TEST DB CONNECTION ERROR: " . $e->getMessage());
}
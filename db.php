<?php
// db.php : connexion PDO à la base via config.php généré par Ansible

require __DIR__ . '/config.php';  // fourni par Ansible

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASSWORD, $options);
} catch (PDOException $e) {
    die('Erreur de connexion à la base : ' . htmlspecialchars($e->getMessage()));
}

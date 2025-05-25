<?php
$_ENV = parse_ini_file(__DIR__ . '/.env');

function getPDO(): string|PDO
{
    if (!isset($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'])) {
        return json_encode(['success' => false, 'message' => 'Erreur serveur.']);
    }

    try {
        $pdo = new PDO(
            "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8",
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD']
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        return json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données.']);
    }
}

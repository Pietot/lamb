<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

$_ENV = parse_ini_file(__DIR__ . '/.env');

if (!isset($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'])) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur.']);
    exit;
}

try {
    $pdo = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id_employee = $_SESSION['id_employee'] ?? null;

    if ($id_employee === null) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
        exit;
    }

    logout($pdo, $id_employee);
    
    echo json_encode(['success' => true, 'message' => 'Déconnexion réussie.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur.']);
}

function logout(PDO $pdo, int $id_employee): void
{
    $stmt = $pdo->prepare('UPDATE employee SET token = NULL, token_init = NULL WHERE id_employee = :id_employee');
    $stmt->bindValue(':id_employee', $id_employee);
    $stmt->execute();
}

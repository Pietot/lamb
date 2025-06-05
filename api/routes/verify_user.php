<?php

$_ENV = parse_ini_file(__DIR__ . '/utils/.env');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: ' . $_ENV['DOMAIN']);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    exit(0);
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . $_ENV['DOMAIN']);
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/utils/pdo.php';
require_once __DIR__ . '/utils/token.php';

$pdo = getPDO();

$stmt = $pdo->prepare('SELECT id_employee FROM employee WHERE id_employee = :id_employee AND token = :token AND token_init > NOW() - INTERVAL 1 HOUR');
$stmt->bindValue(':id_employee', $_GET['id_employee']);
$stmt->bindValue(':token', $_GET['token']);
$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $token = setToken($pdo, $result['id_employee']);
    echo json_encode([
        'success' => true,
        'user' => [
            'id_employee' => $result['id_employee'],
            'token' => $token,
        ]
    ]);
} else if ($result === false) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé ou token invalide.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur.']);
}

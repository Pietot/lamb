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

$stmt = $pdo->prepare('SELECT id_utilisateur FROM utilisateur WHERE id_utilisateur = :id_utilisateur AND token = :token AND token_init > NOW() - INTERVAL 1 HOUR');
$stmt->bindValue(':id_utilisateur', $_GET['id_utilisateur']);
$stmt->bindValue(':token', $_GET['token']);
$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $token = setToken($pdo, $result['id_utilisateur']);
    echo json_encode([
        'success' => true,
        'user' => [
            'id_utilisateur' => $result['id_utilisateur'],
            'token' => $token,
        ]
    ]);
} else if ($result === false) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé ou token invalide.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur.']);
}

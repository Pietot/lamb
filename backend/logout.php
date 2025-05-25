<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/pdo.php';
include_once __DIR__ . '/token.php';

$pdo = getPDO();

try {
    $id_employee = verifyToken($pdo, $_COOKIE['token']);

    if ($id_employee === null) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
        exit;
    }

    removeToken($pdo, $id_employee);

    echo json_encode(['success' => true, 'message' => 'Déconnexion réussie.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur.']);
}

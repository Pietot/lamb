<?php

$_ENV = parse_ini_file(__DIR__ . '/utils/.env');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . $_ENV['DOMAIN']);
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/utils/pdo.php';
require_once __DIR__ . '/utils/token.php';

$pdo = getPDO();

$id_utilisateur = $_POST['id_utilisateur'] ?? null;

try {
    if ($id_utilisateur === null) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    removeToken($pdo, $id_utilisateur);
    echo json_encode(['success' => true, 'message' => 'Déconnexion réussie.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
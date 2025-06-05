<?php
$_ENV = parse_ini_file(__DIR__ . '/utils/.env');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . $_ENV['DOMAIN']);
header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/utils/pdo.php';

$pdo = getPDO();

$table = $_GET['table'] ?? null;

if (!$table) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Paramètre manquant.']);
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table");
$stmt->execute(['table' => $table]);
$tableExists = $stmt->fetchColumn() > 0;

if (!$tableExists) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => "Paramètre invalide."]);
    exit;
}

try {
    $articles = $pdo->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'articles' => $articles]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur.']);
}

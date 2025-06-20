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
    echo json_encode(['success' => false, 'message' => 'Paramètre manquant.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table");
$stmt->execute(['table' => $table]);
$tableExists = $stmt->fetchColumn() > 0;

if (!$tableExists) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => "Paramètre invalide."], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    if ($table === 'utilisateur') {
        $stmt = $pdo->prepare("SELECT id_utilisateur, nom, prenom, email, id_role FROM $table");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM $table");
        $stmt->execute();
    }
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

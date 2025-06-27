<?php
require_once __DIR__ . '/utils/cors.php';
require_once __DIR__ . '/utils/pdo.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));

    if (empty($nom) || empty($description)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (strlen($nom) > 50 || strlen($description) > 50) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPDO();

    $stmt = $pdo->prepare('INSERT INTO categorie (nom, description) VALUES (:nom, :description)');
    $stmt->bindValue(':nom', $nom);
    $stmt->bindValue(':description', $description);
    $stmt->execute();
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Catégorie créée avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
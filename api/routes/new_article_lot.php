<?php
require_once __DIR__ . '/utils/cors.php';
require_once __DIR__ . '/utils/pdo.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id_article = htmlspecialchars(trim($_POST['id_article'] ?? ''));
    $id_lot = htmlspecialchars(trim($_POST['id_lot'] ?? ''));
    $quantite = htmlspecialchars(trim($_POST['quantite'] ?? ''));

    if (empty($id_article) || empty($id_lot) || empty($quantite)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($id_article) || !is_numeric($id_lot) || !is_numeric($quantite) || $quantite < 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO article_lot (id_article, id_lot, quantite) VALUES (:id_article, :id_lot, :quantite)');
    $stmt->bindValue(':id_article', $id_article);
    $stmt->bindValue(':id_lot', $id_lot);
    $stmt->bindValue(':quantite', $quantite);
    $stmt->execute();
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Article lot créé avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

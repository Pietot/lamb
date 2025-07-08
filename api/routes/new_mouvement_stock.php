<?php
require_once __DIR__ . '/utils/cors.php';
require_once __DIR__ . '/utils/pdo.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $quantite = htmlspecialchars(trim($_POST['quantite'] ?? ''));
    $date_mouvement = htmlspecialchars(trim($_POST['date_mouvement'] ?? ''));
    $id_type = htmlspecialchars(trim($_POST['id_type'] ?? ''));
    $id_article = htmlspecialchars(trim($_POST['id_article'] ?? ''));

    if (empty($quantite) || empty($date_mouvement) || empty($id_type) || empty($id_article)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($quantite) || !strtotime($date_mouvement) || !is_numeric($id_type) || !is_numeric($id_article) || $quantite < 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO mouvement_stock (quantite, date_mouvement, id_type, id_article) VALUES (:quantite, :date_mouvement, :id_type, :id_article)');
    $stmt->bindValue(':quantite', $quantite);
    $stmt->bindValue(':date_mouvement', date('Y-m-d H:i:s', strtotime($date_mouvement)));
    $stmt->bindValue(':id_type', $id_type);
    $stmt->bindValue(':id_article', $id_article);
    $stmt->execute();
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Mouvement de stock créé avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

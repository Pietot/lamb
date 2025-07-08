<?php
require_once __DIR__ . '/utils/cors.php';
require_once __DIR__ . '/utils/pdo.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id_lot = htmlspecialchars(trim($_POST['id_lot'] ?? ''));
    $id_commande = htmlspecialchars(trim($_POST['id_commande'] ?? ''));
    $quantite = htmlspecialchars(trim($_POST['quantite'] ?? ''));

    if (empty($id_lot) || empty($id_commande) || empty($quantite)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($id_lot) || !is_numeric($id_commande) || !is_numeric($quantite) || $quantite < 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO commande_lot (id_lot, id_commande, quantite) VALUES (:id_lot, :id_commande, :quantite)');
    $stmt->bindValue(':id_lot', $id_lot);
    $stmt->bindValue(':quantite', $quantite);
    $stmt->execute();
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Commande lot créé avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

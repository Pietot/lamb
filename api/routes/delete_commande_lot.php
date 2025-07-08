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

    if (empty($id_lot) || empty($id_commande)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($id_lot) || !is_numeric($id_commande)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM commande_lot WHERE id_lot = :id_lot AND id_commande = :id_commande');
    $stmt->bindValue(':id_lot', $id_lot);
    $stmt->bindValue(':id_commande', $id_commande);
    $stmt->execute();
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Commande lot supprimée avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

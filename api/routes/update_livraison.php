<?php
require_once __DIR__ . '/utils/cors.php';
require_once __DIR__ . '/utils/pdo.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id_livraison = htmlspecialchars(trim($_POST['id_livraison'] ?? ''));
    $statut = htmlspecialchars(trim($_POST['statut'] ?? ''));

    if (empty($id_livraison) || empty($statut)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID livraison et statut requis'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($id_livraison)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID livraison invalide'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPDO();

    // Mettre à jour uniquement le statut et la date de modification
    $stmt = $pdo->prepare('UPDATE livraison SET statut = :statut, date_modification = :date_modification WHERE id_livraison = :id_livraison');
    $stmt->bindValue(':statut', $statut);
    $stmt->bindValue(':date_modification', date('Y-m-d H:i:s'));
    $stmt->bindValue(':id_livraison', $id_livraison);
    $stmt->execute();
    
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Statut mis à jour avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur', 'error' => $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
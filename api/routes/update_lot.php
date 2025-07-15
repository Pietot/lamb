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
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $date_creation = htmlspecialchars(trim($_POST['date_creation'] ?? ''));
    $quantite_stock = htmlspecialchars(trim($_POST['quantite_stock'] ?? ''));
    $seuil_alerte = htmlspecialchars(trim($_POST['seuil_alerte'] ?? ''));

    if (empty($nom) || empty($description) || empty($date_creation) || empty($quantite_stock) || empty($seuil_alerte)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($id_lot) || strlen($nom) > 50 || strlen($description) > 200 || !strtotime($date_creation) || !is_numeric($quantite_stock) || $quantite_stock < 0 || !is_numeric($seuil_alerte) || $seuil_alerte < 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPDO();

    $stmt = $pdo->prepare('UPDATE lot SET nom = :nom, description = :description, date_creation = :date_creation, quantite_stock = :quantite_stock, seuil_alerte = :seuil_alerte WHERE id_lot = :id_lot');
    $stmt->bindValue(':nom', $nom);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':date_creation', date('Y-m-d H:i:s', strtotime($date_creation)));
    $stmt->bindValue(':quantite_stock', $quantite_stock);
    $stmt->bindValue(':seuil_alerte', $seuil_alerte);
    $stmt->bindValue(':id_lot', $id_lot);
    $stmt->execute();
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Lot mis à jour avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

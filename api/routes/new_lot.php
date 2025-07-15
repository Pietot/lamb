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
    $date_creation = htmlspecialchars(trim($_POST['date_creation'] ?? ''));
    $quantite_stock = htmlspecialchars(trim($_POST['quantite_stock'] ?? ''));
    $seuil_alerte = htmlspecialchars(trim($_POST['seuil_alerte'] ?? ''));

    if (empty($nom) || empty($description) || empty($date_creation) || empty($quantite_stock) || empty($seuil_alerte)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (strlen($nom) > 50 || strlen($description) > 200 || !strtotime($date_creation) || !is_numeric($quantite_stock) || $quantite_stock < 0 || !is_numeric($seuil_alerte) || $seuil_alerte < 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPDO();

    $stmt = $pdo->prepare('INSERT INTO lot (nom, description, date_creation, quantite_stock, seuil_alerte) VALUES (:nom, :description, :date_creation, :quantite_stock, :seuil_alerte)');
    $stmt->bindValue(':nom', $nom);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':date_creation', date('Y-m-d H:i:s', strtotime($date_creation)));
    $stmt->bindValue(':quantite_stock', $quantite_stock);
    $stmt->bindValue(':seuil_alerte', $seuil_alerte);
    $stmt->execute();
    
    $id_lot = $pdo->lastInsertId();
    
    http_response_code(201);
    echo json_encode([
        'success' => true, 
        'message' => 'Lot créé avec succès',
        'id_lot' => $id_lot
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
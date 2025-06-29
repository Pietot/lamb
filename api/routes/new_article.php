<?php
require_once __DIR__ . '/utils/cors.php';
require_once __DIR__ . '/utils/pdo.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $reference = htmlspecialchars(trim($_POST['reference'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $quantite = htmlspecialchars(trim($_POST['quantite'] ?? ''));
    $seuil_alerte = htmlspecialchars(trim($_POST['seuil_alerte'] ?? ''));
    $id_categorie = htmlspecialchars(trim($_POST['id_categorie'] ?? ''));
    $id_fournisseur = htmlspecialchars(trim($_POST['id_fournisseur'] ?? ''));
    $reference_fournisseur = htmlspecialchars(trim($_POST['reference_fournisseur'] ?? ''));
    $prix_achat = htmlspecialchars(trim($_POST['prix_achat'] ?? ''));

    if (empty($reference) || empty($description) || empty($nom) || empty($quantite) || empty($seuil_alerte) || empty($id_categorie) || empty($id_fournisseur) || empty($reference_fournisseur) || empty($prix_achat)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (strlen($reference) > 20 || strlen($description) > 200 || strlen($nom) > 50 || !is_numeric($quantite) || !is_numeric($seuil_alerte) || $seuil_alerte < 0 || !is_numeric($id_categorie) || !is_numeric($id_fournisseur) || strlen($reference_fournisseur) > 20 || !is_numeric($prix_achat)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPDO();
    // Vérifier si la référence existe déjà
    $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM article WHERE reference = :reference');
    $checkStmt->bindValue(':reference', $reference);
    $checkStmt->execute();
    if ($checkStmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Référence déjà utilisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO article (reference, description, nom, quantite_stock, seuil_alerte, id_categorie, id_fournisseur_principal, reference_fournisseur, prix_achat) VALUES (:reference, :description, :nom, :quantite_stock, :seuil_alerte, :id_categorie, :id_fournisseur_principal, :reference_fournisseur, :prix_achat)');
    $stmt->bindValue(':reference', $reference);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':nom', $nom);
    $stmt->bindValue(':quantite_stock', $quantite);
    $stmt->bindValue(':seuil_alerte', $seuil_alerte);
    $stmt->bindValue(':id_categorie', $id_categorie);
    $stmt->bindValue(':id_fournisseur_principal', $id_fournisseur);
    $stmt->bindValue(':reference_fournisseur', $reference_fournisseur);
    $stmt->bindValue(':prix_achat', $prix_achat);

    $stmt->execute();
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Article créé avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

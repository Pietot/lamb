<?php
require_once __DIR__ . '/utils/cors.php';
require_once __DIR__ . '/utils/pdo.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id_fournisseur = htmlspecialchars(trim($_POST['id_fournisseur'] ?? ''));
    $id_commande_fournisseur = htmlspecialchars(trim($_POST['id_commande_fournisseur'] ?? ''));
    $date_livraison = htmlspecialchars(trim($_POST['date_livraison'] ?? ''));
    $numero_bon_livraison = htmlspecialchars(trim($_POST['numero_bon_livraison'] ?? ''));
    $statut = htmlspecialchars(trim($_POST['statut'] ?? ''));
    $id_utilisateur_reception = htmlspecialchars(trim($_POST['id_utilisateur_reception'] ?? ''));
    $notes = htmlspecialchars(trim($_POST['notes'] ?? ''));
    $date_creation = htmlspecialchars(trim($_POST['date_creation'] ?? ''));
    $date_modification = htmlspecialchars(trim($_POST['date_modification'] ?? ''));

    if (empty($id_fournisseur) || empty($id_commande_fournisseur) || empty($date_livraison) || empty($numero_bon_livraison) || empty($statut) || empty($id_utilisateur_reception) || empty($notes) || empty($date_creation) || empty($date_modification)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($id_fournisseur) || !is_numeric($id_commande_fournisseur) || !is_numeric($id_utilisateur_reception) || !strtotime($date_livraison) || !strtotime($date_creation) || !strtotime($date_modification)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO livraison (id_fournisseur, numero_livraison, id_commande_fournisseur, date_livraison, numero_bon_livraison, statut, id_utilisateur_reception, notes, date_creation, date_modification) VALUES (:id_fournisseur, :numero_livraison, :id_commande_fournisseur, :date_livraison, :numero_bon_livraison, :statut, :id_utilisateur_reception, :notes, :date_creation, :date_modification)');
    $stmt->bindValue(':id_fournisseur', $id_fournisseur);
    // Générer un code aléatoire à 5 lettres majuscules
    $randomLetters = strtoupper(substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 5)), 0, 5));
    $stmt->bindValue(':numero_livraison', 'LIV-' . date('Y-m-d') . '-' . $id_fournisseur . '-' . $randomLetters);
    $stmt->bindValue(':id_commande_fournisseur', $id_commande_fournisseur);
    $stmt->bindValue(':date_livraison', date('Y-m-d H:i:s', strtotime($date_livraison)));
    $stmt->bindValue(':numero_bon_livraison', $numero_bon_livraison);
    $stmt->bindValue(':statut', $statut);
    $stmt->bindValue(':id_utilisateur_reception', $id_utilisateur_reception);
    $stmt->bindValue(':notes', $notes);
    $stmt->bindValue(':date_creation', date('Y-m-d H:i:s', strtotime($date_creation)));
    $stmt->bindValue(':date_modification', date('Y-m-d H:i:s', strtotime($date_modification)));
    $stmt->execute();
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Livraison créée avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

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
    $id_commande_fournisseur = !empty($_POST['id_commande_fournisseur']) ? htmlspecialchars(trim($_POST['id_commande_fournisseur'])) : null;
    $date_livraison = htmlspecialchars(trim($_POST['date_livraison'] ?? ''));
    $numero_bon_livraison = htmlspecialchars(trim($_POST['numero_bon_livraison'] ?? ''));
    $statut = htmlspecialchars(trim($_POST['statut'] ?? ''));
    $id_utilisateur_reception = htmlspecialchars(trim($_POST['id_utilisateur_reception'] ?? ''));
    $notes = htmlspecialchars(trim($_POST['notes'] ?? ''));
    
    $date_creation = !empty($_POST['date_creation']) ? htmlspecialchars(trim($_POST['date_creation'])) : date('Y-m-d H:i:s');
    $date_modification = !empty($_POST['date_modification']) ? htmlspecialchars(trim($_POST['date_modification'])) : date('Y-m-d H:i:s');

    if (empty($id_fournisseur) || empty($date_livraison) || empty($statut)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Champs obligatoires manquants : id_fournisseur, date_livraison et statut sont requis'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($id_fournisseur)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'id_fournisseur doit être numérique'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPDO();

    if ($id_commande_fournisseur !== null) {
        $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM commande_fournisseur WHERE id_commande_fournisseur = :id');
        $checkStmt->bindValue(':id', $id_commande_fournisseur);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn() == 0) {
            $id_commande_fournisseur = null;
        }
    }
    
    if (empty($id_utilisateur_reception)) {
        $id_utilisateur_reception = 1; 
    }
    
    if (empty($numero_bon_livraison)) {
        $numero_bon_livraison = 'BL-' . date('Ymd') . '-' . rand(1000, 9999);
    }
    
    if (empty($notes)) {
        $notes = '';
    }

    $randomLetters = strtoupper(substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 5)), 0, 5));
    $numero_livraison = 'LIV-' . date('Y-m-d') . '-' . $id_fournisseur . '-' . $randomLetters;

    $stmt = $pdo->prepare('
        INSERT INTO livraison (
            id_fournisseur, 
            numero_livraison, 
            id_commande_fournisseur, 
            date_livraison, 
            numero_bon_livraison, 
            statut, 
            id_utilisateur_reception, 
            notes, 
            date_creation, 
            date_modification
        ) VALUES (
            :id_fournisseur, 
            :numero_livraison, 
            :id_commande_fournisseur, 
            :date_livraison, 
            :numero_bon_livraison, 
            :statut, 
            :id_utilisateur_reception, 
            :notes, 
            :date_creation, 
            :date_modification
        )
    ');
    
    $stmt->bindValue(':id_fournisseur', $id_fournisseur);
    $stmt->bindValue(':numero_livraison', $numero_livraison);
    $stmt->bindValue(':id_commande_fournisseur', $id_commande_fournisseur, PDO::PARAM_INT);
    $stmt->bindValue(':date_livraison', $date_livraison);
    $stmt->bindValue(':numero_bon_livraison', $numero_bon_livraison);
    $stmt->bindValue(':statut', $statut);
    $stmt->bindValue(':id_utilisateur_reception', $id_utilisateur_reception);
    $stmt->bindValue(':notes', $notes);
    $stmt->bindValue(':date_creation', $date_creation);
    $stmt->bindValue(':date_modification', $date_modification);
    
    $stmt->execute();
    
    $lastId = $pdo->lastInsertId();
    
    http_response_code(201);
    echo json_encode([
        'success' => true, 
        'message' => 'Livraison créée avec succès',
        'id_livraison' => $lastId,
        'numero_livraison' => $numero_livraison
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur base de données',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur du serveur',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
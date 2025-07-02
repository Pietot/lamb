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
    $contact_nom = htmlspecialchars(trim($_POST['contact_nom'] ?? ''));
    $contact_prenom = htmlspecialchars(trim($_POST['contact_prenom'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $telephone = htmlspecialchars(trim($_POST['telephone'] ?? ''));
    $adresse = htmlspecialchars(trim($_POST['adresse'] ?? ''));
    $ville = htmlspecialchars(trim($_POST['ville'] ?? ''));
    $code_postal = htmlspecialchars(trim($_POST['code_postal'] ?? ''));
    $pays = htmlspecialchars(trim($_POST['pays'] ?? ''));
    
    // Champs optionnels
    $site_web = htmlspecialchars(trim($_POST['site_web'] ?? ''));
    $conditions_paiement = htmlspecialchars(trim($_POST['conditions_paiement'] ?? ''));
    $delai_livraison = isset($_POST['delai_livraison']) && $_POST['delai_livraison'] !== '' ? intval($_POST['delai_livraison']) : null;
    $note_qualite = isset($_POST['note_qualite']) && $_POST['note_qualite'] !== '' ? floatval($_POST['note_qualite']) : null;
    $actif = isset($_POST['actif']) && $_POST['actif'] === 'true' ? 1 : 0;

    // Validation des champs requis
    if (empty($nom) || empty($contact_nom) || empty($contact_prenom) || empty($email) || 
        empty($telephone) || empty($adresse) || empty($ville) || empty($code_postal) || empty($pays)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validation des longueurs et formats
    if (strlen($nom) > 100 || strlen($contact_nom) > 50 || strlen($contact_prenom) > 50 || 
        !verifyPhoneNumber($telephone) || !filter_var($email, FILTER_VALIDATE_EMAIL) || 
        strlen($adresse) > 255 || !verifyPostalCode($code_postal)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validation des champs optionnels
    if ($site_web && !filter_var($site_web, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPDO();
    // Vérifier si l'email existe déjà
    $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM fournisseur WHERE email = :email');
    $checkStmt->bindValue(':email', $email);
    $checkStmt->execute();
    if ($checkStmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email déjà utilisé'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare('
        INSERT INTO fournisseur (
            nom, contact_nom, contact_prenom, email, telephone, 
            adresse, ville, code_postal, pays, site_web, 
            conditions_paiement, delai_livraison, note_qualite, actif,
            date_creation, date_modification
        ) VALUES (
            :nom, :contact_nom, :contact_prenom, :email, :telephone, 
            :adresse, :ville, :code_postal, :pays, :site_web, 
            :conditions_paiement, :delai_livraison, :note_qualite, :actif,
            NOW(), NOW()
        )
    ');
    
    $stmt->bindValue(':nom', $nom);
    $stmt->bindValue(':contact_nom', $contact_nom);
    $stmt->bindValue(':contact_prenom', $contact_prenom);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':telephone', $telephone);
    $stmt->bindValue(':adresse', $adresse);
    $stmt->bindValue(':ville', $ville);
    $stmt->bindValue(':code_postal', $code_postal);
    $stmt->bindValue(':pays', $pays);
    $stmt->bindValue(':site_web', $site_web ?: null);
    $stmt->bindValue(':conditions_paiement', $conditions_paiement ?: null);
    $stmt->bindValue(':delai_livraison', $delai_livraison);
    $stmt->bindValue(':note_qualite', $note_qualite);
    $stmt->bindValue(':actif', $actif);
    
    $stmt->execute();
    
    $newId = $pdo->lastInsertId();
    
    http_response_code(201);
    echo json_encode([
        'success' => true, 
        'message' => 'Fournisseur créé avec succès',
        'id' => intval($newId)
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Erreur du serveur"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function verifyPhoneNumber($phone): bool
{
    return preg_match('/^0[1-9][0-9]{8}$/', $phone);
}

function verifyPostalCode($postalCode): bool
{
    return preg_match('/^[0-9]{5}$/', $postalCode);
}
?>
<?php
require_once __DIR__ . '/utils/cors.php';
require_once __DIR__ . '/utils/pdo.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id_client = htmlspecialchars(trim($_POST['id_client'] ?? ''));
    $raison_sociale = htmlspecialchars(trim($_POST['raison_sociale'] ?? ''));
    $nom_commercial = htmlspecialchars(trim($_POST['nom_commercial'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $telephone = htmlspecialchars(trim($_POST['telephone'] ?? ''));
    $adresse = htmlspecialchars(trim($_POST['adresse'] ?? ''));
    $code_postal = htmlspecialchars(trim($_POST['code_postal'] ?? ''));
    $ville = htmlspecialchars(trim($_POST['ville'] ?? ''));
    $siret = htmlspecialchars(trim($_POST['siret'] ?? ''));
    $secteur_activite = htmlspecialchars(trim($_POST['secteur_activite'] ?? ''));
    $contact_principal = htmlspecialchars(trim($_POST['contact_principal'] ?? ''));

    if (empty($id_client) || empty($raison_sociale) || empty($email) || empty($telephone) || empty($adresse) || 
        empty($code_postal) || empty($ville) || empty($siret) || empty($contact_principal)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($id_client) || !verifyPhoneNumber($telephone) || !filter_var($email, FILTER_VALIDATE_EMAIL) || 
        strlen($adresse) > 255 || strlen($raison_sociale) > 100 || 
        strlen($nom_commercial) > 100 || !verifyCodePostal($code_postal) || 
        !verifySiret($siret) || strlen($ville) > 100 || 
        strlen($secteur_activite) > 100 || strlen($contact_principal) > 100) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPDO();
    
    // Vérifier si l'email existe déjà
    $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM client WHERE email = :email AND id_client != :id_client');
    $checkStmt->bindValue(':email', $email);
    $checkStmt->bindValue(':id_client', $id_client);
    $checkStmt->execute();
    if ($checkStmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email déjà utilisé'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE client SET raison_sociale = :raison_sociale, nom_commercial = :nom_commercial, email = :email, telephone = :telephone, adresse = :adresse, code_postal = :code_postal, ville = :ville, siret = :siret, secteur_activite = :secteur_activite, contact_principal = :contact_principal WHERE id_client = :id_client');

    $stmt->bindValue(':id_client', $id_client);
    $stmt->bindValue(':raison_sociale', $raison_sociale);
    $stmt->bindValue(':nom_commercial', $nom_commercial);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':telephone', $telephone);
    $stmt->bindValue(':adresse', $adresse);
    $stmt->bindValue(':code_postal', $code_postal);
    $stmt->bindValue(':ville', $ville);
    $stmt->bindValue(':siret', $siret);
    $stmt->bindValue(':secteur_activite', $secteur_activite);
    $stmt->bindValue(':contact_principal', $contact_principal);
    
    $stmt->execute();

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Client mis à jour avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Erreur du serveur"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function verifyPhoneNumber($phone): bool
{
    return preg_match('/^0[1-9][0-9]{8}$/', $phone);
}

function verifyCodePostal($code): bool
{
    return preg_match('/^\d{5}$/', $code);
}

function verifySiret($siret): bool
{
    return preg_match('/^\d{14}$/', $siret);
}
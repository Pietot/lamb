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
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $telephone = htmlspecialchars(trim($_POST['telephone'] ?? ''));
    $adresse = htmlspecialchars(trim($_POST['adresse'] ?? ''));
    $raison_sociale = htmlspecialchars(trim($_POST['raison_sociale'] ?? ''));
    $siren = htmlspecialchars(trim($_POST['siren'] ?? ''));
    $siret = htmlspecialchars(trim($_POST['siret'] ?? ''));
    $fonction_contact = htmlspecialchars(trim($_POST['fonction_contact'] ?? ''));

    if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($adresse) || empty($raison_sociale) || empty($siren) || empty($siret) || empty($fonction_contact)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (strlen($nom) > 50 || strlen($prenom) > 50 || !verifyPhoneNumber($telephone) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($adresse) > 100 || strlen($raison_sociale) > 100 || !verifySiren($siren) || !verifySiret($siret) || strlen($fonction_contact) > 50) {
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

    $stmt = $pdo->prepare('INSERT INTO fournisseur (nom, prenom, email, telephone, adresse, raison_sociale, siren, siret, fonction_contact) VALUES (:nom, :prenom, :email, :telephone, :adresse, :raison_sociale, :siren, :siret, :fonction_contact)');
    $stmt->bindValue(':nom', $nom);
    $stmt->bindValue(':prenom', $prenom);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':telephone', $telephone);
    $stmt->bindValue(':adresse', $adresse);
    $stmt->bindValue(':raison_sociale', $raison_sociale);
    $stmt->bindValue(':siren', $siren);
    $stmt->bindValue(':siret', $siret);
    $stmt->bindValue(':fonction_contact', $fonction_contact);
    $stmt->execute();
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Fournisseur créé avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Erreur du serveur"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function verifyPhoneNumber($phone): bool
{
    return preg_match('/^0[1-9][0-9]{8}$/', $phone);
}

function verifySiren($siren): bool
{
    return preg_match('/^\d{9}$/', $siren);
}

function verifySiret($siret): bool
{
    return preg_match('/^\d{14}$/', $siret);
}

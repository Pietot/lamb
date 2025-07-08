<?php
require_once __DIR__ . '/utils/cors.php';
require_once __DIR__ . '/utils/pdo.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id_utilisateur = htmlspecialchars(trim($_POST['id_utilisateur'] ?? ''));
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $login = htmlspecialchars(trim($_POST['login'] ?? ''));
    $id_role = htmlspecialchars(trim($_POST['id_role'] ?? ''));

    if (empty($id_utilisateur) || empty($nom) || empty($prenom) || empty($email) || empty($login) || empty($id_role)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($id_utilisateur) || strlen($nom) > 50 || strlen($prenom) > 50 || strlen($login) > 30 || !filter_var($email, FILTER_VALIDATE_EMAIL) || !is_numeric($id_role)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPDO();
    // Vérifier si l'email existe déjà
    $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM utilisateur WHERE email = :email AND id_utilisateur != :id_utilisateur');
    $checkStmt->bindValue(':email', $email);
    $checkStmt->bindValue(':id_utilisateur', $id_utilisateur);
    $checkStmt->execute();

    if ($checkStmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email déjà utilisé'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email, login = :login, id_role = :id_role WHERE id_utilisateur = :id_utilisateur');
    $stmt->bindValue(':nom', $nom);
    $stmt->bindValue(':prenom', $prenom);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':login', $login);
    $stmt->bindValue(':id_role', $id_role);
    $stmt->bindValue(':id_utilisateur', $id_utilisateur);
    $stmt->execute();
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Utilisateur mis à jour avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Erreur du serveur"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
<?php
$_ENV = parse_ini_file(__DIR__ . '/utils/.env');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . $_ENV['DOMAIN']);
header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/utils/pdo.php';

try {
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permission refusée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $login = htmlspecialchars(trim($_POST['login'] ?? ''));
    $password = htmlspecialchars(trim($_POST['password'] ?? ''));
    $id_role = htmlspecialchars(trim($_POST['id_role'] ?? ''));

    if (empty($nom) || empty($prenom) || empty($email) || empty($login) || empty($password) || empty($id_role)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (strlen($nom) >= 50 || strlen($prenom) >= 50 || strlen($login) >= 30 || !filter_var($email, FILTER_VALIDATE_EMAIL) || !isPasswordValid($password) || !is_numeric($id_role)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPDO();
    // Vérifier si l'email existe déjà
    $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM utilisateur WHERE email = :email');
    $checkStmt->bindValue(':email', $email);
    $checkStmt->execute();

    if ($checkStmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email déjà utilisé'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO utilisateur (nom, prenom, email, login, pwd_hash, id_role) VALUES (:nom, :prenom, :email, :login, :pwd_hash, :id_role)');
    $stmt->bindValue(':nom', $nom);
    $stmt->bindValue(':prenom', $prenom);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':login', $login);
    $stmt->bindValue(':pwd_hash', password_hash($password, PASSWORD_BCRYPT));
    $stmt->bindValue(':id_role', $id_role, PDO::PARAM_INT);
    $stmt->execute();
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Utilisateur créé avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function isPasswordValid($password)
{
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,30}$/', $password);
}
<?php
require_once __DIR__ . '/utils/cors.php';
require_once __DIR__ . '/utils/pdo.php';

$_ENV = parse_ini_file(__DIR__ . '/utils/.env');

$pdo = getPDO();

try {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = getUser($pdo, $login);

    if ($user && password_verify($password, $user['pwd_hash'])) {
        // Stocker l'utilisateur et le rôle dans la session
        $_SESSION['user'] = $user['id_utilisateur'];
        $_SESSION['role'] = $user['role'];
        session_regenerate_id(true);
        addLastLogin($pdo, $user['id_utilisateur']);
        echo json_encode([
            'success' => true,
            'user' => [
                'id_utilisateur' => $user['id_utilisateur'],
                'role' => $user['role'],
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nom d\u2019utilisateur ou mot de passe incorrect.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function getUser(PDO $pdo, string $login): array
{
    $stmt = $pdo->prepare('SELECT id_utilisateur, pwd_hash, id_role as role FROM utilisateur WHERE login = :login');
    $stmt->bindValue(':login', $login);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result !== false ? $result : [];
}

function addLastLogin(PDO $pdo, int $userId): void
{
    $stmt = $pdo->prepare('UPDATE utilisateur SET last_login = NOW() WHERE id_utilisateur = :id');
    $stmt->bindValue(':id', $userId);
    $stmt->execute();
}

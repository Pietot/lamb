<?php

$_ENV = parse_ini_file(__DIR__ . '/utils/.env');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . $_ENV['DOMAIN']);
header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/utils/pdo.php';
require_once __DIR__ . '/utils/token.php';

$pdo = getPDO();

try {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = getUser($pdo, $login);

    if ($user && password_verify($password, $user['pwd_hash'])) {
        $token = setToken($pdo, $user['id_utilisateur']);
        echo json_encode([
            'success' => true,
            'user' => [
                'id_utilisateur' => $user['id_utilisateur'],
                'token' => $token,
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nom d’utilisateur ou mot de passe incorrect.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function getUser(PDO $pdo, string $login): array
{
    $stmt = $pdo->prepare('SELECT id_utilisateur, pwd_hash FROM utilisateur WHERE login = :login');
    $stmt->bindValue(':login', $login);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result !== false ? $result : [];
}

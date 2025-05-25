<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/pdo.php';
include_once __DIR__ . '/token.php';

$pdo = getPDO();

try {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = getUser($pdo, $login);

    if ($user && password_verify($password, $user['pwd_hash'])) {
        $token = setToken($pdo, $user['id_employee']);
        echo json_encode([
            'success' => true,
            'user' => [
                'id_employee' => $user['id_employee'],
                'token' => $token,
            ]
        ]);

    } else {
        echo json_encode(['success' => false, 'message' => 'Nom d’utilisateur ou mot de passe incorrect.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur.' . $e->getMessage()]);
}

function getUser(PDO $pdo, string $login): array
{
    $stmt = $pdo->prepare('SELECT id_employee, pwd_hash FROM employee WHERE login = :login');
    $stmt->bindValue(':login', $login);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result !== false ? $result : [];
}

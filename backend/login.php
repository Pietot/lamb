<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

$_ENV = parse_ini_file(__DIR__ . '/.env');

if (!isset($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'])) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur.']);
}

try {
    $pdo = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = getUser($pdo, $login);

    if ($user && password_verify($password, $user['pwd_hash'])) {
        $token = setToken($pdo, $user['id_employee']);
        setcookie('token', $token, time() + 3600, '/');
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

function setToken(PDO $pdo, int $id_employee): string
{
    $token = bin2hex(random_bytes(30));
    $stmt = $pdo->prepare('UPDATE employee SET token = :token, token_init = NOW() WHERE id_employee = :id_employee');
    $stmt->bindValue(':id_employee', $id_employee);
    $stmt->bindValue(':token', $token);
    $stmt->execute();
    return $token;
}
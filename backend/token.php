<?php

function setToken(PDO $pdo, int $id_employee): string
{
    $token = bin2hex(random_bytes(30));
    $stmt = $pdo->prepare('UPDATE employee SET token = :token, token_init = NOW() WHERE id_employee = :id_employee');
    $stmt->bindValue(':id_employee', $id_employee);
    $stmt->bindValue(':token', $token);
    $stmt->execute();
    setcookie('token', $token, time() + 3600, '/');
    return $token;
}

function removeToken(PDO $pdo, int $id_employee): void
{
    $stmt = $pdo->prepare('UPDATE employee SET token = NULL, token_init = NULL WHERE id_employee = :id_employee');
    $stmt->bindValue(':id_employee', $id_employee);
    $stmt->execute();
    setcookie('token', '', -1, '/');
}

function verifyToken(PDO $pdo, string $token): ?int
{
    $stmt = $pdo->prepare('SELECT id_employee FROM employee WHERE token = :token AND token_init > NOW() - INTERVAL 1 HOUR');
    $stmt->bindValue(':token', $token);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        setToken($pdo, $result['id_employee']);
        return $result['id_employee'];
    } else {
        setcookie('token', '', -1, '/');
        return null;
    }
}
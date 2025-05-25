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

function verifyToken(PDO $pdo, string $token): bool
{
    $stmt = $pdo->prepare('SELECT id_employee FROM employee WHERE token = :token AND token_init > NOW() - INTERVAL 1 HOUR');
    $stmt->bindValue(':token', $token);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        setToken($pdo, $result['id_employee']);
        return true;
    } else {
        setcookie('token', '', time() - 3600, '/');
        return false;
    }
}
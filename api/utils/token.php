<?php

function setToken(PDO $pdo, int $id_employee): string
{
    $token = bin2hex(random_bytes(30));
    $stmt = $pdo->prepare('UPDATE employee SET token = :token, token_init = NOW() WHERE id_employee = :id_employee');
    $stmt->bindValue(':id_employee', $id_employee);
    $stmt->bindValue(':token', $token);
    $stmt->execute();
    return $token;
}

function removeToken(PDO $pdo, int $id_employee): void
{
    $stmt = $pdo->prepare('UPDATE employee SET token = NULL, token_init = NULL WHERE id_employee = :id_employee');
    $stmt->bindValue(':id_employee', $id_employee);
    $stmt->execute();
}

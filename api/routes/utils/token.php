<?php
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    http_response_code(403);
    exit('Access denied.');
}
function setToken(PDO $pdo, int $id_utilisateur): string
{
    $token = bin2hex(random_bytes(30));
    $stmt = $pdo->prepare('UPDATE utilisateur SET token = :token, token_init = NOW() WHERE id_utilisateur = :id_utilisateur');
    $stmt->bindValue(':id_utilisateur', $id_utilisateur);
    $stmt->bindValue(':token', $token);
    $stmt->execute();
    return $token;
}

function removeToken(PDO $pdo, int $id_utilisateur): void
{
    $stmt = $pdo->prepare('UPDATE utilisateur SET token = NULL, token_init = NULL WHERE id_utilisateur = :id_utilisateur');
    $stmt->bindValue(':id_utilisateur', $id_utilisateur);
    $stmt->execute();
}

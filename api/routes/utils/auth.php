<?php

require_once __DIR__ . '/cors.php';

// Middleware d'authentification et de gestion des rôles
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    http_response_code(403);
    exit('Access denied.');
}

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $_ENV['COOKIE_SECURE'] === 'true',
    'httponly' => false,
]);

function require_auth(?array $roles = null)
{
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Non authentifié.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($roles !== null && (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles))) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permission refusée.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

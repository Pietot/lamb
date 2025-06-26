<?php
// Middleware d'authentification et de gestion des rôles
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    http_response_code(403);
    exit('Access denied.');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

function require_auth($role = null) {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Non authentifié.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($role !== null && (!isset($_SESSION['role']) || $_SESSION['role'] !== $role)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permission refusée.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function get_user_id() {
    return $_SESSION['user'] ?? null;
}

function get_user_role() {
    return $_SESSION['role'] ?? null;
} 
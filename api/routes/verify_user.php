<?php
require_once __DIR__ . '/utils/cors.php';

if (isset($_SESSION['user']) && isset($_SESSION['role'])) {
    echo json_encode([
        'success' => true,
        'user' => [
            'id_utilisateur' => $_SESSION['user'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'],
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

<?php
require_once __DIR__ . '/utils/cors.php';

// Détruit la session
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
session_unset();
session_destroy();
echo json_encode(['success' => true, 'message' => 'Déconnexion réussie.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
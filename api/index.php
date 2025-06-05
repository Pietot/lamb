<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include_once __DIR__ . '/routes/utils/verify_auth_api.php';

if (!verifyAuthAPI()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

switch ($path) {
    case '/lamb/api/verify_user':
        require __DIR__ . '/routes/verify_user.php';
        exit;

    case '/lamb/api/login':
        require __DIR__ . '/routes/login.php';
        exit;

    case '/lamb/api/logout':
        require __DIR__ . '/routes/logout.php';
        exit;

    case '/lamb/api/get_table':
        require __DIR__ . '/routes/get_table.php';
        exit;
}

http_response_code(404);
echo json_encode(['success' => false, 'message' => 'Route introuvable.', 'route' => $path]);

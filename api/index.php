<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include_once __DIR__ . '/routes/utils/verify_auth_api.php';

if (verifyAuthAPI()) {
    $_ENV = parse_ini_file(__DIR__ . '/routes/utils/.env');

    $requestUri = $_SERVER['REQUEST_URI'];
    $path = parse_url($requestUri, PHP_URL_PATH);

    switch ($path) {
        case $_ENV['API_ROUTE'] . 'verify_user':
            require __DIR__ . '/routes/verify_user.php';
            exit;

        case $_ENV['API_ROUTE'] . 'login':
            require __DIR__ . '/routes/login.php';
            exit;

        case $_ENV['API_ROUTE'] . 'logout':
            require __DIR__ . '/routes/logout.php';
            exit;

        case $_ENV['API_ROUTE'] . 'get_table':
            require __DIR__ . '/routes/get_table.php';
            exit;
    }

    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Route introuvable.', 'route' => $path, 'referer' => $_ENV['API_ROUTE'] . 'login'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

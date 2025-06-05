<?php
$_ENV = parse_ini_file(__DIR__ . '/.env');

function verifyAuthAPI(): bool
{
    $token = null;

    if (function_exists('getallheaders')) {
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        if (isset($headers['authorization'])) {
            $token = $headers['authorization'];
        }
    }

    if (!$token && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $token = $_SERVER['HTTP_AUTHORIZATION'];
    }

    if (!$token && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $token = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }

    if (!$token) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'En-tête Authorization manquante']);
        exit;
    }

    $token = trim(str_replace('Bearer', '', $token));

    if ($token !== $_ENV['API_KEY']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Clé API invalide']);
        exit;
    }

    return true;
}

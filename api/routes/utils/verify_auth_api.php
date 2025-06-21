<?php
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    http_response_code(403);
    exit('Access denied.');
}

header('Content-Type: application/json');

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

    if (!$token || trim(str_replace('Bearer', '', $token)) !== $_ENV['API_KEY']) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Permission refusée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    return true;
}

function isAdmin(): bool
{
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    return isset($headers['authorization-admin']) && $headers['authorization-admin'] === $_ENV['API_KEY_ADMIN'];
}

<?php

$_ENV = parse_ini_file(__DIR__ . '/.env');

header('Content-Type: application/json');
$allowed_origins = [
    $_ENV['DOMAIN'],
    $_ENV['DOMAIN_PROXY'],
    $_ENV['DOMAIN_PC_AZALEE']
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
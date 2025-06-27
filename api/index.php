<?php

$_ENV = parse_ini_file(__DIR__ . '/routes/utils/.env');

require_once __DIR__ . '/routes/utils/cors.php';
require_once __DIR__ . '/routes/utils/pdo.php';
require_once __DIR__ . '/routes/utils/auth.php';

const ADMIN_ROLE = 1;
const COMMERCIAL_ROLE = 2;
const GESTIONNAIRE_ROLE = 3;

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

session_start();

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
        require_auth();
        require __DIR__ . '/routes/get_table.php';
        exit;

    case $_ENV['API_ROUTE'] . 'new_user':
        require_auth([ADMIN_ROLE]);
        require __DIR__ . '/routes/new_user.php';
        exit;

    case $_ENV['API_ROUTE'] . 'new_client':
        require_auth([ADMIN_ROLE, COMMERCIAL_ROLE]);
        require __DIR__ . '/routes/new_client.php';
        exit;

    case $_ENV['API_ROUTE'] . 'new_article':
        require_auth([ADMIN_ROLE, COMMERCIAL_ROLE]);
        require __DIR__ . '/routes/new_article.php';
        exit;

    case $_ENV['API_ROUTE'] . 'new_commande':
        require_auth([ADMIN_ROLE, COMMERCIAL_ROLE]);
        require __DIR__ . '/routes/new_commande.php';
        exit;

    case $_ENV['API_ROUTE'] . 'new_fournisseur':
        require_auth([ADMIN_ROLE, COMMERCIAL_ROLE]);
        require __DIR__ . '/routes/new_fournisseur.php';
        exit;

    case $_ENV['API_ROUTE'] . 'new_article_lot':
        require_auth([ADMIN_ROLE, COMMERCIAL_ROLE]);
        require __DIR__ . '/routes/new_article_lot.php';
        exit;
}

http_response_code(404);
echo json_encode(['success' => false, 'message' => 'Route introuvable.', 'route' => $path, 'referer' => $_ENV['API_ROUTE'] . 'login'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// function tooManyAttempts($ip)
// {
//     $pdo = getPDO();
//     $now = new DateTime();
//     $oneMinuteAgo = (new DateTime())->modify('-1 minute');

//     // Rechercher l'entrée IP
//     $stmt = $pdo->prepare("SELECT * FROM adresse_ip WHERE ip = :ip");
//     $stmt->execute(['ip' => $ip]);
//     $entry = $stmt->fetch(PDO::FETCH_ASSOC);

//     if ($entry) {
//         $windowStart = new DateTime($entry['window_start']);

//         if ($windowStart >= $oneMinuteAgo) {
//             // Même fenêtre => incrémenter ou bloquer
//             if ($entry['request_count'] >= 60) {
//                 return true;
//             }

//             $stmt = $pdo->prepare("UPDATE adresse_ip SET request_count = request_count + 1 WHERE ip = :ip");
//             $stmt->execute(['ip' => $ip]);
//         } else {
//             // Nouvelle fenêtre => reset compteur
//             $stmt = $pdo->prepare("UPDATE adresse_ip SET request_count = 1, window_start = :now WHERE ip = :ip");
//             $stmt->execute([
//                 'ip' => $ip,
//                 'now' => $now->format('Y-m-d H:i:s')
//             ]);
//         }
//     } else {
//         // Nouvelle IP => insérer
//         $stmt = $pdo->prepare("INSERT INTO adresse_ip (ip, request_count, window_start) VALUES (:ip, 1, :now)");
//         $stmt->execute([
//             'ip' => $ip,
//             'now' => $now->format('Y-m-d H:i:s')
//         ]);
//     }

//     return false;
// }

<?php
require_once __DIR__ . '/utils/cors.php';
require_once __DIR__ . '/utils/pdo.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id_mouvement = htmlspecialchars(trim($_POST['id_mouvement'] ?? ''));

    if (empty($id_mouvement)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($id_mouvement)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    $pdo = getPDO();

    $stmt = $pdo->prepare('DELETE FROM mouvement_stock WHERE id_mouvement = :id_mouvement');
    $stmt->bindValue(':id_mouvement', $id_mouvement);
    $stmt->execute();
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Mouvement de stock supprimé avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

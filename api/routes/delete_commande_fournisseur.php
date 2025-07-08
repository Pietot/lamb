    <?php
require_once __DIR__ . '/utils/cors.php';
require_once __DIR__ . '/utils/pdo.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id_commande_fournisseur = htmlspecialchars(trim($_POST['id_commande_fournisseur'] ?? ''));

    if (empty($id_commande_fournisseur)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($id_commande_fournisseur)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM commande_fournisseur WHERE id_commande_fournisseur = :id_commande_fournisseur');
    $stmt->bindValue(':id_commande_fournisseur', $id_commande_fournisseur);
    $stmt->execute();
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Commande fournisseur supprimée avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

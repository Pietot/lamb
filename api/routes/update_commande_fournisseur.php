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
    $numero_commande = htmlspecialchars(trim($_POST['numero_commande'] ?? ''));
    $id_fournisseur = htmlspecialchars(trim($_POST['id_fournisseur'] ?? ''));
    $date_commande = htmlspecialchars(trim($_POST['date_commande'] ?? ''));
    $date_livraison_prevue = htmlspecialchars(trim($_POST['date_livraison_prevue'] ?? ''));
    $statut = htmlspecialchars(trim($_POST['statut'] ?? ''));
    $montant_ht = htmlspecialchars(trim($_POST['montant_ht'] ?? ''));
    $montant_tva = htmlspecialchars(trim($_POST['montant_tva'] ?? ''));
    $montant_ttc = htmlspecialchars(trim($_POST['montant_ttc'] ?? ''));
    $id_utilisateur = htmlspecialchars(trim($_POST['id_utilisateur'] ?? ''));
    $notes = htmlspecialchars(trim($_POST['notes'] ?? ''));
    $date_creation = htmlspecialchars(trim($_POST['date_creation'] ?? ''));
    $date_modification = htmlspecialchars(trim($_POST['date_modification'] ?? ''));

    if (empty($id_commande_fournisseur) || empty($numero_commande) || empty($id_fournisseur) || empty($date_commande) || empty($date_livraison_prevue) || empty($statut) || empty($montant_ht) || empty($montant_tva) || empty($montant_ttc) || empty($id_utilisateur) || empty($notes) || empty($date_creation) || empty($date_modification)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($id_commande_fournisseur) || !is_numeric($id_fournisseur) || !is_numeric($montant_ht) || $montant_ht < 0 || !is_numeric($montant_tva) || $montant_tva < 0 || !is_numeric($montant_ttc) || $montant_ttc < 0 || !is_numeric($id_utilisateur) || !strtotime($date_commande) || !strtotime($date_livraison_prevue) || !strtotime($date_creation) || !strtotime($date_modification) || $montant_ht > $montant_tva || $montant_ht + $montant_tva != $montant_ttc) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE commande_fournisseur SET numero_commande = :numero_commande, id_fournisseur = :id_fournisseur, date_commande = :date_commande, date_livraison_prevue = :date_livraison_prevue, statut = :statut, montant_ht = :montant_ht, montant_tva = :montant_tva, montant_ttc = :montant_ttc, id_utilisateur = :id_utilisateur, notes = :notes, date_creation = :date_creation, date_modification = :date_modification WHERE id_commande_fournisseur = :id_commande_fournisseur');
    $stmt->bindValue(':id_commande_fournisseur', $id_commande_fournisseur);
    $stmt->bindValue(':numero_commande', $numero_commande);
    $stmt->bindValue(':id_fournisseur', $id_fournisseur);
    $stmt->bindValue(':date_commande', date('Y-m-d H:i:s', strtotime($date_commande)));
    $stmt->bindValue(':date_livraison_prevue', date('Y-m-d H:i:s', strtotime($date_livraison_prevue)));
    $stmt->bindValue(':statut', $statut);
    $stmt->bindValue(':montant_ht', $montant_ht);
    $stmt->bindValue(':montant_tva', $montant_tva);
    $stmt->bindValue(':montant_ttc', $montant_ttc);
    $stmt->bindValue(':id_utilisateur', $id_utilisateur);
    $stmt->bindValue(':notes', $notes);
    $stmt->bindValue(':date_creation', $date_creation);
    $stmt->bindValue(':date_modification', $date_modification);
    $stmt->execute();
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Commande fournisseur mise à jour avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

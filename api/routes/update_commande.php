<?php
require_once __DIR__ . '/utils/cors.php';
require_once __DIR__ . '/utils/pdo.php';


const ATTENTE = 0;
const PREPARATION = 1;
const EXPEDIE = 2;

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id_commande = htmlspecialchars(trim($_POST['id_commande'] ?? ''));
    $id_client = htmlspecialchars(trim($_POST['id_client'] ?? ''));
    $date_commande = htmlspecialchars(trim($_POST['date_commande'] ?? ''));
    $statut = htmlspecialchars(trim($_POST['statut'] ?? ''));
    $montant_ht = htmlspecialchars(trim($_POST['montant_ht'] ?? ''));

    if (empty($id_commande) || empty($id_client) || empty($date_commande) || empty($statut) || empty($montant_ht)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs manquants'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!is_numeric($id_commande) || !is_numeric($id_client) || !isValidDate($date_commande) || !in_array($statut, [ATTENTE, PREPARATION, EXPEDIE]) || !is_numeric($montant_ht) || $montant_ht <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Champs invalides'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = getPDO();

    $stmt = $pdo->prepare('UPDATE commande SET id_client = :id_client, date_commande = :date_commande, statut = :statut, montant_ht = :montant_ht, montant_ttc = :montant_ttc WHERE id_commande = :id_commande');
    $stmt->bindValue(':id_commande', $id_commande);
    $stmt->bindValue(':id_client', $id_client);
    $stmt->bindValue(':date_commande', $date_commande);
    $stmt->bindValue(':statut', $statut);
    $stmt->bindValue(':montant_ht', $montant_ht);
    $stmt->bindValue(':montant_ttc', $montant_ht * 1.2);
    $stmt->execute();
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Commande créée avec succès'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur du serveur'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function isValidDate($date): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

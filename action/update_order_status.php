<?php
// update_order_status.php

require_once __DIR__ . '/../inc/core.php';

header('Content-Type: application/json');

// Vérifier les paramètres
if (!isset($_GET['id']) || !isset($_GET['status'])) {
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
    exit;
}

$orderId = (int)$_GET['id'];
$newStatus = $_GET['status'];

// Valider le statut en fonction de l'ENUM de la base de données
$allowedStatuses = ['pending', 'in_progress', 'completed', 'cancelled', 'served'];
if (!in_array($newStatus, $allowedStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Statut invalide']);
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Préparer la requête en fonction du statut
    $query = "UPDATE orderlist SET status = :status";
    if ($newStatus === 'in_progress') {
        $query .= ", progress_at = NOW()";
    } elseif ($newStatus === 'completed') {
        $query .= ", completed_at = NOW()";
    } elseif ($newStatus === 'served') {
        $query .= ", served_at = NOW()";
    }

    $query .= " WHERE id = :id";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':status' => $newStatus,
        ':id' => $orderId
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Statut de la commande mis à jour avec succès'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
<?php
// ca_validate_command.php

require_once __DIR__ . '/../inc/core.php';

// Vérifier qu'il y a une commande en cours
if (empty($_COOKIE['commandPreparation'])) {
    die(json_encode(['status' => 'error', 'message' => 'Aucune commande à valider']));
}

// Récupérer la commande actuelle
$currentCommand = json_decode($_COOKIE['commandPreparation'], true);
if (empty($currentCommand)) {
    die(json_encode(['status' => 'error', 'message' => 'Commande invalide']));
}

// Calculer le prix total
$totalPrice = 0;
foreach ($currentCommand as $item) {
    $totalPrice += floatval($item['price']);
}

// Préparer les données pour la base de données
try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Commencer une transaction
    $pdo->beginTransaction();

    // 1. Récupérer le dernier numéro de commande du jour
    $dayAbbr = strtoupper(substr(date('l'), 0, 2));
    $today = date('Y-m-d');
    
    $stmt = $pdo->prepare("
        SELECT MAX(CAST(SUBSTRING_INDEX(id_commande, '_', -1) AS UNSIGNED)) as max_num 
        FROM orderlist 
        WHERE DATE(created_at) = :today
        AND id_commande LIKE :day_prefix
    ");
    
    $stmt->execute([
        ':today' => $today,
        ':day_prefix' => $dayAbbr . '_%'
    ]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextNum = ($result['max_num'] ?? 0) + 1;

    // 2. Insérer la commande avec les identifiants générés
    $stmt = $pdo->prepare("
        INSERT INTO orderlist 
        (status, contents, price, created_at, uid_commande, id_commande) 
        VALUES 
        ('pending', :contents, :price, NOW(), :uid_commande, :id_commande)
    ");

    // Générer les identifiants
    $dateParts = [
        'Y' => date('Y'),
        'm' => date('m'),
        'd' => date('d')
    ];

    // uid_commande format: NUMERO.JOUR.ANNEE.MOIS.JOUR
    $uidCommande = sprintf(
        "%d.%s.%s.%s.%s",
        $nextNum,
        $dayAbbr,
        $dateParts['Y'],
        $dateParts['m'],
        $dateParts['d']
    );

    // id_commande format: JOUR_NUMERO (en décimal, pas en hexa)
    $idCommande = sprintf("%s_%03d", $dayAbbr, $nextNum);

    $stmt->execute([
        ':contents' => json_encode($currentCommand),
        ':price' => $totalPrice,
        ':uid_commande' => $uidCommande,
        ':id_commande' => $idCommande
    ]);

    // Récupérer l'ID généré
    $orderId = $pdo->lastInsertId();

    // Valider la transaction
    $pdo->commit();

    // Vider le cookie de commande
    setcookie('commandPreparation', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    // Rediriger vers la page d'accueil
    header('Location: /caisse?msg=commandeEnvoyeeOk&id=' . $idCommande);
    exit();
} catch (PDOException $e) {
    // Annuler la transaction en cas d'erreur
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Journaliser l'erreur
    error_log("Erreur validation commande: " . $e->getMessage());

    // Rediriger vers la page d'accueil
    header('Location: /caisse?msg=commandeEnvoyeeKo');
    exit();
}
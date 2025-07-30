<?php
// ca_copy_item.php

require_once __DIR__ . '/../inc/core.php';

// Vérifier que l'UID est présent
if (!isset($_GET['iuid'])) {
    header("HTTP/1.1 400 Bad Request");
    die("UID manquant");
}

$itemUid = $_GET['iuid'];

// Récupérer la commande actuelle depuis le cookie
$currentCommand = [];
if (!empty($_COOKIE['commandPreparation'])) {
    $currentCommand = json_decode($_COOKIE['commandPreparation'], true);
}

// Trouver l'item à dupliquer
$itemToCopy = null;
foreach ($currentCommand as $item) {
    if ($item['uid'] === $itemUid) {
        $itemToCopy = $item;
        break;
    }
}

if (!$itemToCopy) {
    header("HTTP/1.1 404 Not Found");
    die("Item non trouvé");
}

// Générer un nouvel UID pour la copie
$newUid = 'cmd_' . uniqid();

// Créer la copie avec un nouvel UID
$copiedItem = $itemToCopy;
$copiedItem['uid'] = $newUid;

// Ajouter la copie à la commande
$currentCommand[] = $copiedItem;

// Mettre à jour le cookie
setcookie('commandPreparation', json_encode($currentCommand), [
    'expires' => time() + 86400, // 1 jour
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Rediriger vers la page précédente
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    // Fallback si HTTP_REFERER n'est pas disponible
    header("Location: /caisse");
}
exit;
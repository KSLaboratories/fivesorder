<?php
// ca_delete_item.php

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

// Trouver et supprimer l'item
$found = false;
foreach ($currentCommand as $key => $item) {
    if ($item['uid'] === $itemUid) {
        unset($currentCommand[$key]);
        $found = true;
        break;
    }
}

if (!$found) {
    header("HTTP/1.1 404 Not Found");
    die("Item non trouvé");
}

// Réindexer le tableau (important après un unset)
$currentCommand = array_values($currentCommand);

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

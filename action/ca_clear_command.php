<?php
// ca_clear_command.php

require_once __DIR__ . '/../inc/core.php';

// Vider complètement la commande en initialisant un tableau vide
$currentCommand = [];

// Mettre à jour le cookie avec un tableau vide
setcookie('commandPreparation', json_encode($currentCommand), [
    'expires' => time() + 86400, // 1 jour
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Optionnel : Ajouter un message flash
session_start();
$_SESSION['flash_message'] = [
    'type' => 'success',
    'message' => 'La commande a été vidée avec succès'
];

// Rediriger vers la page précédente ou la caisse par défaut
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: /caisse");
}
exit;
<?php

require_once __DIR__.'/../inc/core.php';

// Récupération du cookie actuel
$arrayCommmandPreparation = [];

if (isset($_COOKIE['commandPreparation'])) {
    $decoded = json_decode($_COOKIE['commandPreparation'], true);
    if (is_array($decoded)) {
        $arrayCommmandPreparation = $decoded;
    }
}

// Copie des données POST sans "urlReference"
$newCommand = $_POST;
unset($newCommand['urlReference']);

// Ajout d’un identifiant unique
$newCommand['uid'] = uniqid('cmd_', true); // Ex: cmd_64af72f97a7f83.36564291

// Ajout à la liste
$arrayCommmandPreparation[] = $newCommand;

// Réenregistrement du cookie
setcookie('commandPreparation', json_encode($arrayCommmandPreparation), [
    'expires' => time() + 3600 * 24 * 7,
    'path' => '/',
    'secure' => true,
    'httponly' => false,
    'samesite' => 'Lax'
]);

if (!empty($_POST['urlReference'])) {
    $url = $_POST['urlReference'];

    // Supprime le "URL: " au début s’il est présent
    $url = str_replace('URL: ', '', $url);

    // Supprime &item=XXX peu importe la valeur
    $url = preg_replace('/&item=\d+/', '', $url);

    // Redirection propre
    header('Location: ' . $url);
    exit;
} else {
    echo 'Pas de paramètre urlReference reçu.';
}



?>

<pre><?php var_dump($_POST); ?></pre>

<hr>

<pre><?php var_dump($arrayCommmandPreparation); ?></pre>

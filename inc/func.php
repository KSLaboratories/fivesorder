<?php

/**
 * Vérifie si l'utilisateur est sur la page autorisée en fonction de son type de compte.
 * Si ce n'est pas le cas, redirige l'utilisateur vers la page appropriée.
 *
 * @param string $session_account_type Type de compte de l'utilisateur.
 */
function securitySection($session_account_type)
{
    $pageAuth = [
        'sa' => [
            "page" => "super_admin.php",
            "redirect" => "/super_admin"
        ],
        'cu' => [
            "page" => "cuca.php",
            "redirect" => "/restauration"
        ],
        "ca" => [
            "page" => "cuca.php",
            "redirect" => "/restauration"
        ]
    ];

    // Vérifie si le type de compte existe dans le tableau
    if (array_key_exists($session_account_type, $pageAuth)) {
        $currentPage = basename($_SERVER['PHP_SELF']);
        $allowedPage = $pageAuth[$session_account_type]['page'];

        // Si l'utilisateur n'est pas sur la page autorisée
        if ($currentPage !== $allowedPage) {
            header("Location: " . $pageAuth[$session_account_type]['redirect']);
            exit();
        }
    } else {
        // Type de compte non reconnu - redirection par défaut
        header("Location: /login");
        exit();
    }
}

/**
 * Chiffre une valeur avec AES-256-CBC
 * @param string $value - Valeur à chiffrer
 * @return string - Valeur chiffrée (format: iv:payload)
 */
function encryptValue($value)
{
    // Récupère la clé depuis .env
    $key = base64_decode(explode(':', $_ENV['ENCRYPTION_KEY'])[1]);

    // Génère un IV (Vecteur d'Initialisation)
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

    // Chiffrement
    $encrypted = openssl_encrypt($value, 'aes-256-cbc', $key, 0, $iv);

    // Combine IV et données chiffrées (stockage au format base64)
    return base64_encode($iv) . ':' . $encrypted;
}

/**
 * Déchiffre une valeur chiffrée avec AES-256-CBC
 * @param string $encryptedValue - Valeur chiffrée (format: iv:payload)
 * @return string|false - Valeur déchiffrée ou false en cas d'échec
 */
function decryptValue($encryptedValue)
{
    // Récupère la clé depuis .env
    $key = base64_decode(explode(':', $_ENV['ENCRYPTION_KEY'])[1]);

    // Sépare IV et données
    $parts = explode(':', $encryptedValue);
    if (count($parts) !== 2) return false;

    $iv = base64_decode($parts[0]);
    $payload = $parts[1];

    // Déchiffrement
    return openssl_decrypt($payload, 'aes-256-cbc', $key, 0, $iv);
}

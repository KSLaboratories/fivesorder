<?php
require_once __DIR__ . '/../inc/core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée');
}

$label = trim($_POST['label'] ?? '');

if ($label === '') {
    exit("Le nom du filtre est requis.");
}

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->prepare("INSERT INTO filters (label) VALUES (:label)");
    $stmt->execute([':label' => $label]);

    header('Location: ../super_admin?&filter=filter&for=filters_a');
    exit;
} catch (PDOException $e) {
    exit('Erreur : ' . $e->getMessage());
}

<?php
require_once __DIR__ . '/../inc/core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Méthode non autorisée.");
}

$filterId = isset($_POST['filter_id']) ? (int)$_POST['filter_id'] : 0;

if ($filterId <= 0) {
    exit("ID de filtre invalide.");
}

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Suppression du filtre (les items associés seront mis à NULL automatiquement)
    $stmt = $pdo->prepare("DELETE FROM filters WHERE id = :id");
    $stmt->execute([':id' => $filterId]);

    header('Location: ../super_admin?filte=filter&for=filters_r');
    exit;

} catch (PDOException $e) {
    exit("Erreur : " . $e->getMessage());
}

<?php
require_once __DIR__ . '/../inc/core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée');
}

$itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

if ($itemId <= 0) {
    exit('ID invalide');
}

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Suppression de l’image si existante
    $stmt = $pdo->prepare("SELECT image FROM items WHERE id = :id");
    $stmt->execute([':id' => $itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item && !empty($item['image'])) {
        $imagePath = __DIR__ . '/../' . $item['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Suppression de l’item (les `items_additionnal` sont supprimés automatiquement par ON DELETE CASCADE)
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = :id");
    $stmt->execute([':id' => $itemId]);

    header('Location: ../super_admin?success=item_deleted');
    exit;
} catch (PDOException $e) {
    exit('Erreur DB : ' . $e->getMessage());
}

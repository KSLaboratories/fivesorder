<?php
require_once __DIR__ . '/../inc/core.php';

$category = $_POST['category'];

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute([':id' => $category]);

    $pdo->commit();

    header('Location: ../super_admin?success=category_removed');
    exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    die('Erreur DB : ' . $e->getMessage());
}
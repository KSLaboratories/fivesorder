<?php
require_once __DIR__ . '/../inc/core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée');
}

$itemName   = trim($_POST['item'] ?? '');
$categoryId = $_POST['category'] !== 'no_category' ? (int)$_POST['category'] : null;
$price      = str_replace(',', '.', $_POST['price'] ?? '0');
$color      = $_POST['color'] ?? null;
$additionals = $_POST['additionals'] ?? [];
$imagePath  = null;
$filterId = isset($_POST['filter_id']) && $_POST['filter_id'] !== '' ? (int)$_POST['filter_id'] : null;


if ($itemName === '' || $price === '' || !is_numeric($price)) {
    exit('Champ requis manquant ou invalide.');
}

// Gestion de l'image
if (!empty($_FILES['image']['tmp_name'])) {
    $uploadDir = __DIR__ . '/../uploads/';
    $filename = uniqid('item_') . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fullPath = $uploadDir . $filename;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $fullPath)) {
        $imagePath = 'uploads/' . $filename;
    } else {
        exit("Échec de l'upload de l'image.");
    }
}

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $pdo->beginTransaction();

    // Insère l'item
    $stmt = $pdo->prepare("INSERT INTO items (name, category_id, additional, image, price, color, filter_id)
                       VALUES (:name, :category, NULL, :image, :price, :color, :filter)");
    $stmt->execute([
        ':name'     => $itemName,
        ':category' => $categoryId,
        ':image'    => $imagePath,
        ':price'    => $price,
        ':color'    => $color,
        ':filter'   => $filterId
    ]);

    $itemId = $pdo->lastInsertId();

    // Ajoute les options additionnelles
    $insertAdd = $pdo->prepare("INSERT INTO items_additionnal (item_id, label) VALUES (:item, :label)");
    foreach ($additionals as $label) {
        $clean = trim($label);
        if ($clean !== '') {
            $insertAdd->execute([
                ':item'  => $itemId,
                ':label' => $clean
            ]);
        }
    }

    $pdo->commit();

    header('Location: ../super_admin');
    exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    exit('Erreur DB : ' . $e->getMessage());
}

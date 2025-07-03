<?php
require_once __DIR__ . '/../inc/core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée');
}

// Récupération des données avec des valeurs par défaut
$itemName = trim($_POST['item'] ?? '');
$categoryId = ($_POST['category'] ?? 'no_category') !== 'no_category' ? (int)$_POST['category'] : null;
$price = str_replace(',', '.', $_POST['price'] ?? '0');
$color = $_POST['color'] ?? 'red'; // Valeur par défaut
$additionals = $_POST['additionals'] ?? []; // Correction de la variable
$imagePath = null;
$filterId = !empty($_POST['filter_id']) ? (int)$_POST['filter_id'] : null;

// Validation plus complète
if (empty($itemName)) {
    exit('Le nom de l\'item est requis.');
}

if (!is_numeric($price) || $price <= 0) {
    exit('Le prix doit être un nombre positif.');
}

// Gestion de l'image (inchangée)
if (!empty($_FILES['image']['tmp_name'])) {
    $uploadDir = __DIR__ . '/../uploads/';
    $filename = uniqid('item_') . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fullPath = $uploadDir . $filename;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $fullPath)) {
        exit("Échec de l'upload de l'image.");
    }
    $imagePath = 'uploads/' . $filename;
}

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $pdo->beginTransaction();

    // Insertion de l'item
    $stmt = $pdo->prepare("INSERT INTO items (name, category_id, additional, image, price, color, filter_id)
                         VALUES (:name, :category, NULL, :image, :price, :color, :filter)");
    $stmt->execute([
        ':name' => $itemName,
        ':category' => $categoryId,
        ':image' => $imagePath,
        ':price' => $price,
        ':color' => $color,
        ':filter' => $filterId
    ]);

    $itemId = $pdo->lastInsertId();

    // Insertion des options additionnelles
    if (!empty($additionals)) {
        // Dans la partie traitement des additionals
        $insertAdd = $pdo->prepare("INSERT INTO items_additionnal 
                          (item_id, label, price, default_quantity, created_at)
                          VALUES (:item, :label, :price, :quantity, NOW())");

        foreach ($additionals as $additional) {
            $label = trim($additional['label'] ?? '');
            $price = (float)($additional['price'] ?? 0);
            $quantity = isset($additional['default_quantity']) ? (int)$additional['default_quantity'] : 1;

            // Validation du prix
            if ($price < 0 || $price > 50) {
                $pdo->rollBack();
                exit("Le prix doit être entre 0.00 et 50.00 €");
            }

            if (!empty($label)) {
                $insertAdd->execute([
                    ':item' => $itemId,
                    ':label' => $label,
                    ':price' => $price,
                    ':quantity' => $quantity
                ]);
            }
        }
    }

    $pdo->commit();
    header('Location: ../super_admin?filter=items&for=item_a');
    exit;
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    exit('Erreur DB : ' . $e->getMessage());
}

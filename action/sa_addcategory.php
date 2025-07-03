<?php
require_once __DIR__ . '/../inc/core.php';

// Vérification de la méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée');
}

// Récupération et nettoyage des données du formulaire
$category = trim($_POST['category'] ?? '');
$subCategory = $_POST['sub_category'] ?? 'no_category';
$color = $_POST['color'] ?? '';

// Validation minimale
if ($category === '' || $color === '') {
    exit('Tous les champs requis ne sont pas remplis.');
}

try {
    // Connexion à la base de données via PDO
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Requête SQL pour insérer la catégorie
    $stmt = $pdo->prepare("INSERT INTO categories (category_name, category_color, sub_category) VALUES (:name, :color, :parent)");
    $stmt->execute([
        ':name'   => $category,
        ':parent' => $subCategory === 'no_category' ? null : $subCategory,
        ':color'  => $color
    ]);

    // Redirection ou message de succès
    header('Location: ../super_admin?filter=category&for=category_a');
    exit;

} catch (PDOException $e) {
    exit('Erreur lors de l’insertion : ' . $e->getMessage());
}

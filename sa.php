<?php

require_once __DIR__ . '/inc/core.php';

// remove error
error_reporting(0);

// categories
try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $categories = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);

    $subcategories = $pdo->query("SELECT * FROM categories WHERE sub_category IS NULL ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);

    $items = $pdo->query("SELECT id, name FROM items ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

    $filters = $pdo->query("SELECT id, label FROM filters ORDER BY label ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erreur DB : ' . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FO | SA</title>
    <link rel="stylesheet" href="./src/css/style.css">
</head>

<body>

    <main class="sa">
        <h1>Page administrateur</h1>

        <div class="navigation">
            <a <?php if ($_GET['filter'] == '') echo 'class="active"' ?> href="?filter=">Toutes les catégories</a>
            <a <?php if ($_GET['filter'] == 'category') echo 'class="active"' ?> href="?filter=category">Catégories</a>
            <a <?php if ($_GET['filter'] == 'items') echo 'class="active"' ?> href="?filter=items">Items</a>
            <a <?php if ($_GET['filter'] == 'filter') echo 'class="active"' ?> href="?filter=filter">Filters</a>
        </div>

        <!-- category -->
        <?php if ($_GET['filter'] == 'category' || $_GET['filter'] == '') { ?>
            <form action="action/sa_addcategory.php" method="post">
                <h2>Ajouter une catégorie</h2>
                <p class="helpMessage">
                    Les catégories sert à trier les différents items. Vous pouvez également réaliser des sous catégories.
                </p>
                <select name="sub_category" id="">
                    <option value="no_category">Pas une sous catégorie</option>
                    <?php

                    foreach ($subcategories as $category) {
                        echo '<option value="' . $category['id'] . '">' . $category['category_name'] . '</option>';
                    }

                    ?>
                </select>
                <input type="text" name="category" id="" placeholder="Nom de la catégorie">
                <select name="color" id="">
                    <option value="red">Rouge</option>
                    <option value="blue">Bleu</option>
                    <option value="green">Vert</option>
                    <option value="yellow">Jaune</option>
                    <option value="orange">Orange</option>
                    <option value="pink">Rose</option>
                    <option value="black">Noir</option>
                    <option value="purple">Violet</option>
                </select>
                <button type="submit">Ajouter</button>
            </form>

            <form action="action/sa_removecategory.php" method="post" onsubmit="return confirm('Supprimer cette catégorie ?')">
                <h2>Supprimer une catégorie</h2>
                <p class="helpMessage">
                    Si c'est une sous catégorie (indiquer entre parenthèse avant le nom), nous ne supprimerons que la sous catégorie.
                </p>
                <select name="category" id="">
                    <?php

                    foreach ($categories as $category) {
                        if ($category['sub_category'] !== null) {
                            // echo '<option value="' . $category['id'] . '">(' . $category['sub_category'] . ') ' . $category['category_name'] . '</option>';
                            // continue;
                            // ? get sub category name
                            $subCategory = $pdo->query("SELECT category_name FROM categories WHERE id = " . $category['sub_category'])->fetch(PDO::FETCH_ASSOC);
                            echo '<option value="' . $category['id'] . '">' . $category['category_name'] . ' (' . $subCategory['category_name'] . ')</option>';
                            continue;
                        }
                        echo '<option value="' . $category['id'] . '">' . $category['category_name'] . '</option>';
                    }

                    ?>
                </select>
                <button type="submit">Supprimer</button>
            </form>
        <?php } ?>

        <?php if ($_GET['filter'] == 'items' || $_GET['filter'] == '') { ?>
            <form action="action/sa_add_item.php" method="post" enctype="multipart/form-data">
                <h2>Ajouter un item</h2>
                <p class="helpMessage">
                    Un item peut être de la nourriture, une boisson, etc. C'est ce qui sera commandé.
                </p>

                <select name="category">
                    <option value="no_category">Pas dans une catégorie/sous-catégorie</option>
                    <?php
                    foreach ($categories as $category) {
                        if ($category['sub_category'] !== null) {
                            $subCategory = $pdo->query("SELECT category_name FROM categories WHERE id = " . (int)$category['sub_category'])->fetch(PDO::FETCH_ASSOC);
                            echo '<option value="' . $category['id'] . '">' . htmlspecialchars($category['category_name']) . ' (' . htmlspecialchars($subCategory['category_name']) . ')</option>';
                            continue;
                        }
                        echo '<option value="' . $category['id'] . '">' . htmlspecialchars($category['category_name']) . '</option>';
                    }
                    ?>
                </select>

                <input type="text" name="item" placeholder="Nom de l'item" required>
                <input type="text" name="price" placeholder="Prix (€)" required>

                <!-- Image -->
                <input type="file" name="image">

                <!-- Couleur -->
                <select name="color">
                    <option value="red">Rouge</option>
                    <option value="blue">Bleu</option>
                    <option value="green">Vert</option>
                    <option value="yellow">Jaune</option>
                    <option value="orange">Orange</option>
                    <option value="pink">Rose</option>
                    <option value="black">Noir</option>
                    <option value="purple">Violet</option>
                </select>

                <!-- Additionnels -->
                <div id="additionals">
                    <label>Ajouts disponibles :</label>
                    <div>
                        <input type="text" name="additionals[]" placeholder="Ex: sucre">
                        <button type="button" onclick="addAdditional()">+</button>
                    </div>
                </div>

                <select name="filter_id">
                    <option value="">Aucun filtre</option>
                    <?php foreach ($filters as $filter): ?>
                        <option value="<?= $filter['id'] ?>"><?= htmlspecialchars($filter['label']) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Ajouter l'item</button>
            </form>

            <script>
                function addAdditional() {
                    const container = document.getElementById('additionals');
                    const div = document.createElement('div');
                    div.innerHTML = `
            <input type="text" name="additionals[]" placeholder="Ex: sucre">
            <button type="button" onclick="this.parentElement.remove()">-</button>
        `;
                    container.appendChild(div);
                }
            </script>


            <form action="action/sa_delete_item.php" method="post">

                <form action="action/sa_delete_item.php" method="post" onsubmit="return confirm('Supprimer cet item ?');">
                    <h2>Supprimer un item</h2>
                    <select name="item_id" required>
                        <option value="">Choisir un item</option>
                        <?php foreach ($items as $item): ?>
                            <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Supprimer</button>
                </form>
            </form>
        <?php } ?>

        <?php if ($_GET['filter'] == 'filter') { ?>
            <form action="action/sa_add_filter.php" method="post">
                <h2>Ajouter un filtre</h2>
                <input type="text" name="label" placeholder="Nom du filtre" required>
                <button type="submit">Ajouter le filtre</button>
            </form>

            <form action="action/sa_delete_filter.php" method="post" onsubmit="return confirm('Supprimer ce filtre ?')">
                <select name="filter_id" required>
                    <h2>Supprimer un filtre</h2>
                    <option value="">Choisir un filtre</option>
                    <?php foreach ($filters as $filter): ?>
                        <option value="<?= $filter['id'] ?>"><?= htmlspecialchars($filter['label']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Supprimer</button>
            </form>

        <?php } ?>

    </main>

</body>

</html>
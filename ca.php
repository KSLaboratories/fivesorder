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

    $categories = $pdo->query("SELECT * FROM categories WHERE sub_category IS NULL ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['category'])) {
        $subcategories = $pdo->query("SELECT * FROM categories WHERE sub_category IS NOT NULL AND sub_category = {$_GET['category']} ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);
    }

    // ? items
    if (empty($_GET['category']) && empty($_GET['sub_category'])) {
        $items = $pdo->query("SELECT id, name, image, color FROM items WHERE category_id IS NULL ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $filters = $pdo->query("SELECT id, label FROM filters ORDER BY label ASC")->fetchAll(PDO::FETCH_ASSOC);
    } elseif(!empty($_GET['category']) && empty($_GET['sub_category'])) {
        $items = $pdo->query("SELECT id, name, image, color FROM items WHERE category_id = {$_GET['category']} ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $filters = $pdo->query("SELECT id, label FROM filters ORDER BY label ASC")->fetchAll(PDO::FETCH_ASSOC);
    } elseif(!empty($_GET['category']) && !empty($_GET['sub_category'])) {
        $items = $pdo->query("SELECT id, name, image, color FROM items WHERE category_id = {$_GET['sub_category']} ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $filters = $pdo->query("SELECT id, label FROM filters ORDER BY label ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // itemShow
    if (!empty($_GET['item'])) {
        $itemsadditionals = $pdo->query("SELECT * FROM items_additionnal WHERE item_id = {$_GET['item']} ORDER BY label ASC")->fetchAll(PDO::FETCH_ASSOC);
        $itemInfo = $pdo->query("SELECT * FROM items WHERE id = {$_GET['item']}")->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die('Erreur DB : ' . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FO | CA</title>
    <link rel="stylesheet" href="./src/css/style.css">
</head>

<body class="flex dark">
    <div class="overlay"></div>
    <main class="ca">
        <header>
            <ul>
                <a href="?">
                    <li>Accueil</li>
                </a>
                <?php foreach ($categories as $category) { ?>
                    <a href="?category=<?= $category['id'] ?>">
                        <li data-id="<?= $category['id'] ?>" data-sub_category="<?= $category['sub_category'] ?>" style="border-bottom: 3px solid <?= $category['category_color'] ?>">
                            <?= $category['category_name'] ?>
                        </li>
                    </a>
                <?php } ?>
            </ul>
        </header>
        <header>
            <?php if (isset($_GET['category']) && !empty($subcategories)) { ?>
                <ul class="subcat">
                    <?php foreach ($subcategories as $subcategory) { ?>
                        <a href="?category=<?= $_GET['category'] ?>&sub_category=<?= $subcategory['id'] ?>">
                            <li data-id="<?= $subcategory['id'] ?>" data-sub_category="<?= $subcategory['sub_category'] ?>" style="border-bottom: 3px solid <?= $subcategory['category_color'] ?>">
                                <?= $subcategory['category_name'] ?>
                            </li>
                        </a>
                    <?php } ?>
                </ul>
            <?php } ?>
        </header>

        <section class="productList">
            <!-- product list -->
            <div class="filter">
                <h2>Nourriture</h2>
                <div class="productList__inside">
                    <?php foreach ($items as $item) { ?>
                        <div style="border-bottom:10px solid <?= $item['color'] ?>;" data-id="<?= $item['id'] ?>" class="box">
                            <?php if (!empty($item['image'])) { ?>
                                <img src="<?= $item['image'] ?>" alt="">
                            <?php } else { ?>
                                <div class="replaceImage">
                                    <p><?= $item['name'] ?></p>
                                </div>
                            <?php } ?>
                            <div class="info">
                                <h3><?= htmlspecialchars($item['name']) ?></h3>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <?php if (!empty($_GET['item'])) { ?>
                    <div id="ccenter-<?= $_GET['item'] ?>" style="border:3px solid <?= $item['color'] ?>;" class="ccenter active">
                        <?php if (isset($itemInfo['image'])) { ?>
                            <img src="<?= $itemInfo['image'] ?>" alt="">
                        <?php } else { ?>
                            <div class="replaceImage" style="border-bottom:10px solid <?= $itemInfo['color'] ?>;">
                                <p><?= $itemInfo['name'] ?></p>
                            </div>
                        <?php } ?>
                        <div class="contents">
                            <form class="prepareList" action="" method="post">
                                <input type="hidden" name="item_id" value="<?= $_GET['item'] ?>">
                                <div class="additional">
                                    <div class="additional__item">
                                        <?php foreach ($itemsadditionals as $itemadditionnal) { ?>
                                            <div class="group">
                                                <label>
                                                    <div class="utils">

                                                    </div>
                                                    <?= htmlspecialchars($itemadditionnal['label']) ?>
                                                </label>
                                                <div class="priceAdd">
                                                    <input data-price="<?= $itemadditionnal['price'] ?>" type="number"
                                                        name="additional[<?= $itemadditionnal['id'] ?>]"
                                                        value="<?= $itemadditionnal['default_quantity'] ?>" min="0">

                                                    <p class="additional-price"><?= number_format($itemadditionnal['price'], 2) ?> €</p>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="send">
                                    <div class="priceTotal">
                                        <span class="price"><?= $itemInfo['price'] ?></span>
                                        <span class="currency">€</span>
                                    </div>
                                    <button type="submit" class="btn">Valider</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    </main>
    <aside class="ca"></aside>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du clic sur un item
            document.querySelectorAll('.box').forEach(box => {
                box.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const itemId = this.getAttribute('data-id');

                    // Masquer tous les ccenter
                    document.querySelectorAll('.ccenter').forEach(cc => cc.classList.remove('active'));
                    document.querySelector('.overlay').classList.add('active');

                    // Afficher le ccenter correspondant
                    const ccenter = document.querySelector(`#ccenter-${itemId}`);
                    if (ccenter) {
                        ccenter.classList.add('active');
                    } else {
                        // Charger l'item via AJAX si nécessaire
                        window.location.href = `?<?php if (isset($_GET['category'])) echo "category={$_GET['category']}&"; if (isset($_GET['sub_category'])) echo "sub_category={$_GET['sub_category']}&"; ?>item=${itemId}`;
                    }

                    // Activer le panier si premier clic
                    if (!document.querySelector('.ca').classList.contains('active')) {
                        document.querySelector('.ca').classList.add('active');
                    }
                });
            });

            // Fermer au clic en dehors
            document.querySelector('.overlay').addEventListener('click', function() {
                document.querySelectorAll('.ccenter').forEach(cc => cc.classList.remove('active'));
                this.classList.remove('active');

                // Sauvegarder le formulaire dans le panier
                const activeForm = document.querySelector('.ccenter.active .prepareList');
                if (activeForm) {
                    const formData = new FormData(activeForm);
                    const jsonData = {};
                    formData.forEach((value, key) => jsonData[key] = value);

                    document.querySelector('aside.ca').dataset.order = JSON.stringify(jsonData);
                    console.log('Données sauvegardées:', jsonData);
                }
            });

            // Calcul du prix en temps réel
            document.querySelectorAll('.additional__item input[type="number"]').forEach(input => {
                input.addEventListener('change', updateTotalPrice);
            });

            function updateTotalPrice() {
                let total = parseFloat(document.querySelector('.priceTotal .price').textContent) || 0;
                const basePrice = parseFloat('<?= $itemInfo['price'] ?? 0 ?>'); // Prix de base de l'item

                // Réinitialiser le total avec le prix de base
                total = basePrice;

                // Ajouter le prix des options additionnelles
                document.querySelectorAll('.additional__item input[type="number"]').forEach(input => {
                    const quantity = parseInt(input.value) || 0;
                    const price = parseFloat(input.getAttribute('data-price')) || 0;
                    total += quantity * price;
                });

                document.querySelector('.priceTotal .price').textContent = total.toFixed(2);
            }

            // Initialiser le prix total avec le prix de base au chargement
            updateTotalPrice();
        });
    </script>

</body>

</html>
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
    } elseif (!empty($_GET['category']) && empty($_GET['sub_category'])) {
        $items = $pdo->query("SELECT id, name, image, color FROM items WHERE category_id = {$_GET['category']} ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $filters = $pdo->query("SELECT id, label FROM filters ORDER BY label ASC")->fetchAll(PDO::FETCH_ASSOC);
    } elseif (!empty($_GET['category']) && !empty($_GET['sub_category'])) {
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
    <link rel="stylesheet" href="./src/css/nwd.css">
    <script src="node_modules/hyperscript.org/dist/_hyperscript.min.js"></script>

    <!-- sweetalert2 -->
    <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
</head>

<body class="dark">
    <?php require_once __DIR__ . '/inc/nav.php' ?>
    <?php if (!empty($_GET['item'])) { ?>
        <div _="on click set my.style.display to 'none' then set .ccenter.style.display to 'none'" class="overlay phoneActive"></div>
    <?php } ?>

    <main>

        <?php if (!empty($_GET['item'])) { ?>
            <form class="ccenter" action="action/ca_add_item.php" method="post">

                <input type="hidden" name="item_id" value="<?= $_GET['item'] ?>">
                <input name="urlReference" type="hidden" id="pageInfo" readonly style="width: 100%;">
                <script>
                    const fullUrl = window.location.href;
                    const queryString = window.location.search; // inclut le `?`
                    const input = document.getElementById('pageInfo');

                    input.value = `URL: ${fullUrl}`;
                </script>

                <div class="header">
                    <?php if (isset($itemInfo['image'])) { ?>
                        <div class="img">
                            <img src="<?= $itemInfo['image'] ?>" alt="">
                        </div>
                    <?php } ?>
                    <div class="info">
                        <p class="name">
                            <?= $itemInfo['name'] ?>
                        </p>
                        <p class="desc">
                            Une fois le bouton envoyé appuyé, il sera ajouté au panier
                        </p>
                    </div>
                </div>
                <div class="additionnal">
                    <?php foreach ($itemsadditionals as $itemadditionnal) { ?>
                        <div class="box">
                            <div>
                                <p class="name">
                                    <?= htmlspecialchars($itemadditionnal['label']) ?>
                                </p>
                                <p class="price">
                                    <?= number_format($itemadditionnal['price'], 2) ?> €
                                </p>
                            </div>
                            <div>
                                <input data-price="<?= $itemadditionnal['price'] ?>" type="number"
                                    name="additional[<?= $itemadditionnal['id'] ?>]"
                                    value="<?= $itemadditionnal['default_quantity'] ?>" min="0">
                                <div class="btn">
                                    <button class="add">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="lucide lucide-plus-icon lucide-plus">
                                            <path d="M5 12h14" />
                                            <path d="M12 5v14" />
                                        </svg>
                                    </button>
                                    <button class="remove">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="lucide lucide-minus-icon lucide-minus">
                                            <path d="M5 12h14" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="actions">
                    <button class="dismiss" _="on click set .overlay.style.display to 'none' then set .ccenter.style.display to 'none'" onclick="event.preventDefault()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-undo-dot-icon lucide-undo-dot">
                            <path d="M21 17a9 9 0 0 0-15-6.7L3 13" />
                            <path d="M3 7v6h6" />
                            <circle cx="12" cy="17" r="1" />
                        </svg>
                    </button>
                    <input type="number" name="price" id="" value="<?= $itemInfo['price'] ?>" readonly>
                    <button class="send">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-circle-plus-icon lucide-circle-plus">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M8 12h8" />
                            <path d="M12 8v8" />
                        </svg>
                    </button>
                </div>
            </form>


            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialiser le prix de base
                    const basePrice = parseFloat(document.querySelector('input[name="price"]').value);

                    // Fonction pour mettre à jour le prix total
                    function updateTotalPrice() {
                        let total = basePrice;

                        // Parcourir tous les inputs d'additionnels
                        document.querySelectorAll('.additionnal input[type="number"]').forEach(input => {
                            const quantity = parseInt(input.value) || 0;
                            const price = parseFloat(input.dataset.price) || 0;
                            total += quantity * price;
                        });

                        // Mettre à jour le champ prix total
                        document.querySelector('input[name="price"]').value = total.toFixed(2);
                    }

                    // Gérer les clics sur les boutons d'ajout
                    document.querySelectorAll('.additionnal .btn .add').forEach(button => {
                        button.addEventListener('click', function(e) {
                            e.preventDefault();
                            const input = this.closest('.box').querySelector('input[type="number"]');
                            input.value = parseInt(input.value) + 1;
                            updateTotalPrice();
                        });
                    });

                    // Gérer les clics sur les boutons de retrait
                    document.querySelectorAll('.additionnal .btn .remove').forEach(button => {
                        button.addEventListener('click', function(e) {
                            e.preventDefault();
                            const input = this.closest('.box').querySelector('input[type="number"]');
                            const currentValue = parseInt(input.value);
                            if (currentValue > 0) {
                                input.value = currentValue - 1;
                                updateTotalPrice();
                            }
                        });
                    });

                    // Mettre à jour aussi quand on change directement l'input
                    document.querySelectorAll('.additionnal input[type="number"]').forEach(input => {
                        input.addEventListener('change', updateTotalPrice);
                    });
                });
            </script>
        <?php } ?>

        <div class="search">
            <button id="buttonNavigation"
                _="on click toggle .phoneActive on .navigation then toggle .phoneActive on .overlay then toggle .phoneActive on me">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-menu-icon lucide-menu">
                    <path d="M4 12h16" />
                    <path d="M4 18h16" />
                    <path d="M4 6h16" />
                </svg>
            </button>
            <form action="" method="get">
                <div class="group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-package-search-icon lucide-package-search">
                        <path
                            d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                        <path d="m7.5 4.27 9 5.15" />
                        <polyline points="3.29 7 12 12 20.71 7" />
                        <line x1="12" x2="12" y1="22" y2="12" />
                        <circle cx="18.5" cy="15.5" r="2.5" />
                        <path d="M20.27 17.27 22 19" />
                    </svg>
                    <input type="search" name="q" placeholder="Rechercher un item" id="">
                </div>
                <button type="submit">Rechercher</button>
            </form>
            <button id="buttonCommands"
                _="on click toggle .commandsActive on .command then toggle .phoneActive on .overlay then toggle .commandsActive on me">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-scroll-text-icon lucide-scroll-text">
                    <path d="M15 12h-5" />
                    <path d="M15 8h-5" />
                    <path d="M19 17V5a2 2 0 0 0-2-2H4" />
                    <path
                        d="M8 21h12a2 2 0 0 0 2-2v-1a1 1 0 0 0-1-1H11a1 1 0 0 0-1 1v1a2 2 0 1 1-4 0V5a2 2 0 1 0-4 0v2a1 1 0 0 0 1 1h3" />
                </svg>
            </button>
        </div>
        <header>
            <nav>
                <a href="?">
                    <button <?= empty($_GET['category']) ? 'class="cbtn__bck__black"' : 'class="cbtn__ctrh__black"'; ?>>Accueil</button>
                </a>
                <?php foreach ($categories as $category) { ?>
                    <a href="?category=<?= $category['id'] ?>">
                        <button class="<?= ($_GET['category'] != $category['id']) ? 'cbtn__ctrh__' . $category['category_color'] : 'cbtn__bck__' . $category['category_color'] ?>">
                            <?= $category['category_name'] ?>
                        </button>
                    </a>
                <?php } ?>
            </nav>
            <?php if (isset($_GET['category']) && !empty($subcategories)) { ?>
                <nav class="small">
                    <?php foreach ($subcategories as $subcategory) { ?>
                        <a href="?&category=<?= $_GET['category'] ?>&sub_category=<?= $subcategory['id'] ?>">
                            <button class="<?= ($_GET['sub_category'] != $subcategory['id']) ? 'cbtn__ctrh__' . $subcategory['category_color'] : 'cbtn__bck__' . $subcategory['category_color'] ?>">
                                <?= $subcategory['category_name'] ?>
                            </button>
                        </a>
                    <?php } ?>
                </nav>
            <?php } ?>
        </header>

        <div class="pos">
            <div class="pos__title sec__title--left">
                <p>Espresso</p>
            </div>

            <div class="pos__area">



                <?php foreach ($items as $item) { ?>
                    <a href="?<?php
                                $params = [];

                                if (!empty($_GET['category'])) {
                                    $params[] = 'category=' . urlencode($_GET['category']);
                                }

                                if (!empty($_GET['sub_category'])) {
                                    $params[] = 'sub_category=' . urlencode($_GET['sub_category']);
                                }

                                $params[] = 'item=' . urlencode($item['id']);

                                echo implode('&', $params);
                                ?>">
                        <div class="item">
                            <?php if (!empty($item['image'])) { ?>
                                <div class="img">
                                    <img src="<?= $item['image'] ?>" alt="">
                                </div>
                            <?php } else { ?>
                                <div class="img cbtn__bck__<?= $item['color'] ?>">
                                    <p><?= htmlspecialchars($item['name']) ?></p>
                                </div>
                            <?php } ?>
                            <p class="title">
                                <?= htmlspecialchars($item['name']) ?>
                            </p>
                            <button class="cbtn__bck__blue">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-package-plus-icon lucide-package-plus">
                                    <path d="M16 16h6" />
                                    <path d="M19 13v6" />
                                    <path
                                        d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                                    <path d="m7.5 4.27 9 5.15" />
                                    <polyline points="3.29 7 12 12 20.71 7" />
                                    <line x1="12" x2="12" y1="22" y2="12" />
                                </svg>
                                Ajouter
                            </button>
                        </div>
                    </a>
                <?php } ?>


            </div>
        </div>
    </main>

    <aside class="command">
        <form action="action/ca_validate_command.php" method="post">
            <div class="list">
                <?php
                if (!empty($_COOKIE['commandPreparation'])) {
                    $commandItems = json_decode($_COOKIE['commandPreparation'], true);

                    try {
                        $pdo = new PDO(
                            "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
                            $_ENV['DB_USER'],
                            $_ENV['DB_PASS'],
                            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                        );

                        foreach ($commandItems as $item) {
                            // Récupérer les infos de l'item depuis la DB
                            $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
                            $stmt->execute([$item['item_id']]);
                            $itemInfo = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($itemInfo) {
                ?>
                                <div class="item">
                                    <?php if (!empty($itemInfo['image'])) { ?>
                                        <div class="img">
                                            <img src="<?= htmlspecialchars($itemInfo['image']) ?>" alt="">
                                        </div>
                                    <?php } else { ?>
                                        <div class="img cbtn__bck__<?= htmlspecialchars($itemInfo['color']) ?>">
                                            <p><?= htmlspecialchars($itemInfo['name']) ?></p>
                                        </div>
                                    <?php } ?>
                                    <div class="info">
                                        <h3 class="title">
                                            <?= htmlspecialchars($itemInfo['name']) ?>
                                        </h3>
                                        <p class="price">
                                            <?= number_format($item['price'], 2) ?><span>€</span>
                                        </p>
                                    </div>
                                    <div class="btnActions">
                                        <a href="action/ca_copy_item.php?iuid=<?= htmlspecialchars($item['uid']) ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-copy-plus-icon lucide-copy-plus">
                                                <line x1="15" x2="15" y1="12" y2="18" />
                                                <line x1="12" x2="18" y1="15" y2="15" />
                                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                                            </svg>
                                        </a>
                                        <a href="action/ca_delete_item.php?iuid=<?= htmlspecialchars($item['uid']) ?>" class="delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-minus-icon lucide-badge-minus">
                                                <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" />
                                                <line x1="8" x2="16" y1="12" y2="12" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                <?php
                            }
                        }
                    } catch (PDOException $e) {
                        echo '<p>Erreur de connexion à la base de données</p>';
                    }
                } else {
                    echo '<p>Aucun item dans la commande</p>';
                }
                ?>
            </div>
            <?php if (!empty($_COOKIE['commandPreparation'])) { ?>
                <div class="detail">
                    <a href="caisse_details">
                        Détails de la commande
                    </a>
                </div>
                <div class="send">
                    <button type="submit" id="submitCommand">Valider et envoyer</button>
                </div>
            <?php } ?>
        </form>
    </aside>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const submitButton = document.getElementById('submitCommand');

            if (submitButton) {
                submitButton.addEventListener('click', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Confirmer la commande',
                        html: `<p>Êtes-vous sûr de vouloir valider cette commande ?</p>`,
                        icon: 'question',
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonColor: 'var(--blue-10)',
                        cancelButtonColor: 'var(--red-10)',
                        denyButtonColor: 'var(--gray-10)',
                        confirmButtonText: 'Oui, valider !',
                        cancelButtonText: 'Annuler',
                        denyButtonText: 'Voir détails',
                        customClass: {
                            popup: 'swal-wide'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Correction ici : window au lieu de windows
                            window.location.href = 'action/ca_validate_command.php';
                        } else if (result.isDenied) {
                            window.location.href = '/caisse_details';
                        }
                    });
                });
            }
        });
    </script>

    <?php if ($_GET['msg'] === "commandeEnvoyeeOk") { ?>
        <!-- command OK -->

        <script>
            Swal.fire({
                title: 'Commande envoyé',
                html: `<p>La commande a bien été envoyé</p> <p>Commande : <b><?= $_GET['id']; ?></b></p>`,
                icon: 'success',
                showCancelButton: false,
                showDenyButton: false, // Ajout du troisième bouton
                confirmButtonColor: 'var(--blue-10)',
                cancelButtonColor: 'var(--red-10)',
                denyButtonColor: 'var(--gray-10)', // Couleur pour le nouveau bouton
                confirmButtonText: 'd\'accord',
                customClass: {
                    popup: 'swal-wide'
                }
            })
        </script>

    <?php } ?>

</body>

</html>
<?php
// cuisine.php

require_once __DIR__ . '/inc/core.php';

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Récupérer les commandes triées (in_progress d'abord, puis pending, et par date)
    $stmt = $pdo->query("
        SELECT o.*, 
               CASE 
                 WHEN o.status = 'in_progress' THEN 0 
                 WHEN o.status = 'pending' THEN 1 
                 ELSE 2 
               END AS status_order
        FROM orderlist o
        WHERE o.status IN ('pending', 'in_progress', 'completed') 
        ORDER BY status_order ASC, o.created_at ASC
    ");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Précharger les informations des items et suppléments
    $itemIds = [];
    $additionalIds = [];

    foreach ($orders as $order) {
        $contents = json_decode($order['contents'], true);
        foreach ($contents as $item) {
            if (isset($item['item_id'])) $itemIds[] = $item['item_id'];
            if (!empty($item['additional'])) {
                $additionalIds = array_merge($additionalIds, array_keys($item['additional']));
            }
        }
    }

    // Récupérer les noms des items
    $itemsInfo = [];
    if (!empty($itemIds)) {
        $in = implode(',', array_unique($itemIds));
        $stmt = $pdo->query("SELECT id, name FROM items WHERE id IN ($in)");
        $itemsInfo = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // Récupérer les noms des suppléments
    $additionalsInfo = [];
    if (!empty($additionalIds)) {
        $in = implode(',', array_unique($additionalIds));
        $stmt = $pdo->query("SELECT id, label FROM items_additionnal WHERE id IN ($in)");
        $additionalsInfo = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
} catch (PDOException $e) {
    die('Erreur DB : ' . $e->getMessage());
}

// Stocker les items complétés en session
$completedItems = $_SESSION['completed_items'] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Cuisine</title>
    <script src="node_modules/hyperscript.org/dist/_hyperscript.min.js"></script>

    <script src="node_modules/notyf/notyf.min.js"></script>
    <link rel="stylesheet" href="node_modules/notyf/notyf.min.css">

    <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="node_modules/sweetalert2/dist/sweetalert2.css">
    
    <link rel="stylesheet" href="./src/css/nwd.css">
    <style>

    </style>
</head>

<body class="dark">
    <?php require_once __DIR__ . '/inc/nav.php' ?>



    <main class="sa no_aside">
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
        </div>








        <div class="cuisine">
            <div class="header">
                <h1>Commandes en cuisine</h1>
                <button class="refresh-btn" onclick="window.location.reload()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                        <path d="M3 3v5h5" />
                        <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16" />
                        <path d="M16 16h5v5" />
                    </svg>
                    Actualiser
                </button>
            </div>

            <div class="orders-grid">
                <?php foreach ($orders as $order): ?>
                    <?php
                    $contents = json_decode($order['contents'], true);
                    $statusClass = str_replace(' ', '_', strtolower($order['status']));
                    ?>
                    <div class="order-card <?= $statusClass ?>">
                        <button class="cancel-order" onclick="updateOrderStatus(<?= $order['id'] ?>, 'cancelled')" title="Annuler cette commande">
                            ×
                        </button>

                        <div class="order-header">
                            <span class="order-id">#<?= htmlspecialchars($order['id_commande']) ?></span>
                            <span class="order-time">
                                <?= date('H:i', strtotime($order['created_at'])) ?>
                            </span>
                        </div>

                        <div class="order-status <?= $statusClass ?>">
                            <?= match ($order['status']) {
                                'pending' => 'En attente',
                                'in_progress' => 'En préparation',
                                'completed' => 'Prêt à servir',
                                default => $order['status']
                            } ?>
                        </div>

                        <div class="order-items" data-order-id="<?= $order['id'] ?>">
                            <?php foreach ($contents as $item): ?>
                                <?php
                                $itemName = $itemsInfo[$item['item_id']] ?? 'Item inconnu';
                                $itemKey = $order['id'] . '_' . $item['uid'];
                                ?>
                                <div class="item <?= isset($completedItems[$itemKey]) ? 'completed' : '' ?>"
                                    data-item-id="<?= htmlspecialchars($item['uid']) ?>"
                                    onclick="toggleCompleted('<?= $itemKey ?>', this)">
                                    <div class="item-name"><?= htmlspecialchars($itemName) ?></div>
                                    <?php if (!empty($item['additional'])): ?>
                                        <div class="item-additionals">
                                            <?php foreach ($item['additional'] as $addId => $qty): ?>
                                                <?php if ($qty > 0): ?>
                                                    <?php
                                                    $addName = $additionalsInfo[$addId] ?? 'Suppl. inconnu';
                                                    $addKey = $itemKey . '_' . $addId;
                                                    ?>
                                                    <div class="additional <?= isset($completedItems[$addKey]) ? 'completed' : '' ?>"
                                                        data-add-id="<?= $addId ?>"
                                                        onclick="event.stopPropagation(); toggleCompleted('<?= $addKey ?>', this)">
                                                        + <?= htmlspecialchars($addName) ?> (x<?= $qty ?>)
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="order-actions">
                            <?php if ($order['status'] === 'pending'): ?>
                                <button class="status-btn start-btn"
                                    onclick="updateOrderStatus(<?= $order['id'] ?>, 'in_progress')">
                                    Commencer la préparation
                                </button>
                            <?php elseif ($order['status'] === 'in_progress'): ?>
                                <button class="status-btn complete-btn"
                                    onclick="updateOrderStatus(<?= $order['id'] ?>, 'completed')">
                                    Commande prête
                                </button>
                                <button class="status-btn back-btn"
                                    onclick="updateOrderStatus(<?= $order['id'] ?>, 'pending')">
                                    Revenir en attente
                                </button>
                            <?php elseif ($order['status'] === 'completed'): ?>
                                <button class="status-btn served-btn"
                                    onclick="updateOrderStatus(<?= $order['id'] ?>, 'served')">
                                    Marquer comme servi
                                </button>
                                <button class="status-btn back-btn"
                                    onclick="updateOrderStatus(<?= $order['id'] ?>, 'in_progress')">
                                    Revenir en préparation
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script>
            // Stocker les items complétés
            function toggleCompleted(itemKey, element) {
                fetch('/action/toggle_completed.php?key=' + itemKey, {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            element.classList.toggle('completed');
                        }
                    });
            }

            // Mettre à jour le statut d'une commande
            function updateOrderStatus(orderId, newStatus) {
                let confirmText = "";
                let confirmIcon = "warning";

                if (newStatus === 'cancelled') {
                    confirmText = "Êtes-vous sûr de vouloir annuler cette commande ?";
                    confirmIcon = "error";
                } else if (newStatus === 'pending') {
                    confirmText = "Revenir cette commande à l'état 'En attente' ?";
                } else {
                    // Pas de confirmation pour les autres changements
                    sendStatusUpdate(orderId, newStatus);
                    return;
                }

                Swal.fire({
                    title: 'Confirmation',
                    text: confirmText,
                    icon: confirmIcon,
                    showCancelButton: true,
                    confirmButtonText: 'Oui',
                    cancelButtonText: 'Non'
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendStatusUpdate(orderId, newStatus);
                    }
                });
            }

            function sendStatusUpdate(orderId, newStatus) {
                fetch(`/action/update_order_status.php?id=${orderId}&status=${newStatus}`, {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Succès',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Erreur',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Erreur',
                            text: 'Une erreur réseau est survenue',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            }

            // Actualisation automatique toutes les 3 secondes
            setInterval(() => {
                fetch(window.location.href, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        cache: 'no-store'
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.querySelector('.orders-grid').innerHTML;
                        document.querySelector('.orders-grid').innerHTML = newContent;
                    })
                    .catch(error => console.error('Erreur d\'actualisation:', error));
            }, 3000);
        </script>
    </main>
</body>

</html>
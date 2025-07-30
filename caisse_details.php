<?php
require_once __DIR__ . '/inc/core.php';

// remove error
error_reporting(0);

// Initialisation PDO
try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('Erreur DB : ' . $e->getMessage());
}

// Récupérer la commande en cours depuis le cookie
$currentCommand = [];
if (!empty($_COOKIE['commandPreparation'])) {
    $currentCommand = json_decode($_COOKIE['commandPreparation'], true);
}

// Calculer le total de la commande
$total = 0;
foreach ($currentCommand as $item) {
    $total += floatval($item['price']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la commande | FO | CA</title>
    <link rel="stylesheet" href="./src/css/nwd.css">
    <script src="node_modules/hyperscript.org/dist/_hyperscript.min.js"></script>
    <!-- SweetAlert2 CSS & JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Votre CSS existant ici */
    </style>
</head>

<body class="dark">
    <div class="caisse_details">
        <header>
            <h1>Détails de la commande</h1>
            <a href="/caisse" class="back-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Retour
            </a>
        </header>

        <div class="command-items">
            <?php if (empty($currentCommand)): ?>
                <div class="empty-message">
                    <p>Aucun article dans la commande</p>
                </div>
            <?php else: ?>
                <?php foreach ($currentCommand as $item): ?>
                    <?php
                    // Récupérer les infos de l'item depuis la DB
                    $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
                    $stmt->execute([$item['item_id']]);
                    $itemInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Récupérer les infos des suppléments
                    $additionnals = [];
                    if (!empty($item['additional'])) {
                        $inIds = implode(',', array_keys($item['additional']));
                        $stmt = $pdo->query("SELECT * FROM items_additionnal WHERE id IN ($inIds)");
                        $additionnals = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    ?>
                    
                    <div class="command-item">
                        <div class="item-header">
                            <?php if (!empty($itemInfo['image'])): ?>
                                <div class="item-image">
                                    <img src="<?= htmlspecialchars($itemInfo['image']) ?>" alt="<?= htmlspecialchars($itemInfo['name']) ?>">
                                </div>
                            <?php else: ?>
                                <div class="item-image cbtn__bck__<?= htmlspecialchars($itemInfo['color']) ?>">
                                    <p><?= htmlspecialchars($itemInfo['name']) ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="item-info">
                                <h2><?= htmlspecialchars($itemInfo['name']) ?></h2>
                                <p class="price"><?= number_format($item['price'], 2) ?> €</p>
                            </div>
                            
                            <div class="item-actions">
                                <a href="action/ca_copy_item.php?iuid=<?= htmlspecialchars($item['uid']) ?>" class="action-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="15" x2="15" y1="12" y2="18" />
                                        <line x1="12" x2="18" y1="15" y2="15" />
                                        <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                                        <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                                    </svg>
                                </a>
                                <a href="#" onclick="confirmDeleteItem('<?= htmlspecialchars($item['uid']) ?>')" class="action-btn delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        
                        <?php if (!empty($additionnals)): ?>
                            <div class="item-additionnals">
                                <h3>Suppléments :</h3>
                                <ul>
                                    <?php foreach ($additionnals as $add): ?>
                                        <?php if ($item['additional'][$add['id']] > 0): ?>
                                            <li>
                                                <span class="add-name"><?= htmlspecialchars($add['label']) ?></span>
                                                <span class="add-quantity">x<?= $item['additional'][$add['id']] ?></span>
                                                <span class="add-price"><?= number_format($add['price'] * $item['additional'][$add['id']], 2) ?> €</span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <div class="command-total">
                    <h3>Total :</h3>
                    <p class="total-price"><?= number_format($total, 2) ?> €</p>
                </div>
                
                <div class="command-actions">
                    <a href="#" onclick="confirmClearCommand()" class="btn clear-btn">Vider la commande</a>
                    <a href="#" onclick="confirmValidateCommand()" class="btn validate-btn">Valider la commande</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // Confirmation pour supprimer un item
    function confirmDeleteItem(itemUid) {
        event.preventDefault();
        Swal.fire({
            title: 'Supprimer cet article ?',
            text: "Cette action est irréversible !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `action/ca_delete_item.php?iuid=${itemUid}`;
            }
        });
    }

    // Confirmation pour vider la commande
    function confirmClearCommand() {
        event.preventDefault();
        Swal.fire({
            title: 'Vider toute la commande ?',
            text: "Tous les articles seront supprimés !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, tout vider !',
            cancelButtonText: 'Annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'action/ca_clear_command.php';
            }
        });
    }

    // Confirmation pour valider la commande
    function confirmValidateCommand() {
        event.preventDefault();
        Swal.fire({
            title: 'Confirmer la commande',
            html: `<p>Êtes-vous sûr de vouloir valider cette commande ?</p><p class="total-confirm">Total: <?= number_format($total, 2) ?> €</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, valider !',
            cancelButtonText: 'Annuler',
            customClass: {
                popup: 'swal-wide'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'action/ca_validate_command.php';
            }
        });
    }
    </script>
</body>
</html>
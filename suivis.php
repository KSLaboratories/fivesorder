<?php
// suivis.php

require_once __DIR__ . '/inc/core.php';

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Récupérer toutes les commandes triées par date (plus récente en premier)
    $orders = $pdo->query("
        SELECT * FROM orderlist 
        ORDER BY created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Calculer les statistiques de temps
    $stats = $pdo->query("
        SELECT 
            AVG(TIMESTAMPDIFF(MINUTE, created_at, progress_at)) AS avg_pending_to_progress,
            AVG(TIMESTAMPDIFF(MINUTE, progress_at, completed_at)) AS avg_progress_to_completed,
            AVG(TIMESTAMPDIFF(MINUTE, completed_at, served_at)) AS avg_completed_to_served,
            AVG(TIMESTAMPDIFF(MINUTE, created_at, served_at)) AS avg_total_time,
            COUNT(*) AS total_served
        FROM orderlist
        WHERE status = 'served'
    ")->fetch(PDO::FETCH_ASSOC);

    // Données pour les graphiques (24h par défaut)
    $timeRange = $_GET['time_range'] ?? '24h';
    $interval = match ($timeRange) {
        '1h' => '1 HOUR',
        '2h' => '2 HOUR',
        '8h' => '8 HOUR',
        '24h' => '24 HOUR',
        '48h' => '48 HOUR',
        '72h' => '72 HOUR',
        default => '24 HOUR'
    };

    // Données pour le graphique de timeline
    $timelineData = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m-%d %H:00') AS hour_created,
            COUNT(*) AS count
        FROM orderlist
        WHERE created_at >= NOW() - INTERVAL $interval
        GROUP BY hour_created
        ORDER BY hour_created
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Données pour le graphique de statuts
    $statusData = $pdo->query("
        SELECT 
            status,
            COUNT(*) AS count
        FROM orderlist
        WHERE created_at >= NOW() - INTERVAL $interval
        GROUP BY status
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Préparer les données pour ApexCharts
    $timelineChartData = [
        'categories' => [],
        'series' => [
            [
                'name' => 'Commandes',
                'data' => []
            ]
        ]
    ];

    foreach ($timelineData as $data) {
        $timelineChartData['categories'][] = date('H:i', strtotime($data['hour_created']));
        $timelineChartData['series'][0]['data'][] = $data['count'];
    }

    $statusChartData = [
        'labels' => [],
        'series' => []
    ];

    foreach ($statusData as $data) {
        $statusChartData['labels'][] = match ($data['status']) {
            'pending' => 'En attente',
            'in_progress' => 'En préparation',
            'completed' => 'Prête',
            'served' => 'Servie',
            'cancelled' => 'Annulée',
            default => $data['status']
        };
        $statusChartData['series'][] = $data['count'];
    }
} catch (PDOException $e) {
    die('Erreur DB : ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi des Commandes</title>
    <link rel="stylesheet" href="./src/css/nwd.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.css">
</head>

<body class="dark suivis">

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

        <div class="suivi-container">
            <h1>Suivi des Commandes</h1>

            <!-- Graphiques -->
            <div class="charts-grid">
                <div class="chart-container">
                    <div class="chart-header">
                        <h2>Commandes par heure</h2>
                        <div class="time-range-selector">
                            <button class="time-range-btn <?= $timeRange === '1h' ? 'active' : '' ?>" onclick="changeTimeRange('1h')">1h</button>
                            <button class="time-range-btn <?= $timeRange === '2h' ? 'active' : '' ?>" onclick="changeTimeRange('2h')">2h</button>
                            <button class="time-range-btn <?= $timeRange === '8h' ? 'active' : '' ?>" onclick="changeTimeRange('8h')">8h</button>
                            <button class="time-range-btn <?= $timeRange === '24h' ? 'active' : '' ?>" onclick="changeTimeRange('24h')">24h</button>
                            <button class="time-range-btn <?= $timeRange === '48h' ? 'active' : '' ?>" onclick="changeTimeRange('48h')">48h</button>
                            <button class="time-range-btn <?= $timeRange === '72h' ? 'active' : '' ?>" onclick="changeTimeRange('72h')">72h</button>
                        </div>
                    </div>
                    <div id="timelineChart"></div>
                </div>

                <div class="chart-container">
                    <h2>Répartition des statuts</h2>
                    <div id="statusChart"></div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="stats-container">
                <h2>Statistiques de Préparation</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?= round($stats['avg_pending_to_progress'] ?? 0) ?> min</div>
                        <div class="stat-label">Moyenne attente → préparation</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= round($stats['avg_progress_to_completed'] ?? 0) ?> min</div>
                        <div class="stat-label">Moyenne préparation → prêt</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= round($stats['avg_completed_to_served'] ?? 0) ?> min</div>
                        <div class="stat-label">Moyenne prêt → servi</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= round($stats['avg_total_time'] ?? 0) ?> min</div>
                        <div class="stat-label">Temps moyen total</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $stats['total_served'] ?? 0 ?></div>
                        <div class="stat-label">Commandes servies</div>
                    </div>
                </div>
            </div>

            <!-- Liste des commandes -->
            <div class="orders-list">
                <h2>Historique des Commandes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID Commande</th>
                            <th>Statut</th>
                            <th>Créée le</th>
                            <th>En préparation</th>
                            <th>Prête le</th>
                            <th>Servie le</th>
                            <th>Durée totale</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['id_commande']) ?></td>
                                <td>
                                    <span class="status-badge <?= strtolower($order['status']) ?>">
                                        <?= match ($order['status']) {
                                            'pending' => 'En attente',
                                            'in_progress' => 'En préparation',
                                            'completed' => 'Prête à servir',
                                            'served' => 'Servie',
                                            'cancelled' => 'Annulée',
                                            default => $order['status']
                                        } ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                <td><?= $order['progress_at'] ? date('d/m/Y H:i', strtotime($order['progress_at'])) : '-' ?></td>
                                <td><?= $order['completed_at'] ? date('d/m/Y H:i', strtotime($order['completed_at'])) : '-' ?></td>
                                <td><?= $order['served_at'] ? date('d/m/Y H:i', strtotime($order['served_at'])) : '-' ?></td>
                                <td>
                                    <?php if ($order['served_at']): ?>
                                        <?= round((strtotime($order['served_at']) - strtotime($order['created_at'])) / 60) ?> min
                                    <?php elseif ($order['completed_at']): ?>
                                        <?= round((strtotime($order['completed_at']) - strtotime($order['created_at'])) / 60) ?> min
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.js"></script>
        <script>
            // Fonction pour changer la plage horaire
            function changeTimeRange(range) {
                window.location.href = `?time_range=${range}`;
            }

            // Graphique timeline
            const timelineOptions = {
                series: <?= json_encode($timelineChartData['series']) ?>,
                chart: {
                    type: 'area',
                    height: 350,
                    zoom: {
                        enabled: false
                    },
                    foreColor: '#000'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                    }
                },
                xaxis: {
                    categories: <?= json_encode($timelineChartData['categories']) ?>,
                    labels: {
                        style: {
                            colors: '#000'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#000'
                        }
                    }
                },
                tooltip: {
                    theme: 'dark'
                },
                colors: ['#6daef8ff']
            };

            const timelineChart = new ApexCharts(document.querySelector("#timelineChart"), timelineOptions);
            timelineChart.render();

            // Graphique de statuts
            const statusOptions = {
                series: <?= json_encode($statusChartData['series']) ?>,
                labels: <?= json_encode($statusChartData['labels']) ?>,
                chart: {
                    type: 'donut',
                    height: 350
                },
                colors: ['#FFC107', '#2196F3', '#4CAF50', '#6c757d', '#dc3545'],
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: '#ccc'
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    color: '#ccc'
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    theme: 'dark'
                }
            };

            const statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
            statusChart.render();
        </script>
    </main>
</body>

</html>
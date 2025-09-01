<?php
$title = 'Dashboard Admin - GazExpress';
$includeMap = false;

ob_start();
?>

<header class="header">
    <nav class="navbar">
        <div class="nav-brand">
            <i class="fas fa-fire"></i>
            <span>GazExpress</span>
        </div>
        <div class="nav-links">
            <span>Admin: <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="/logout" class="btn-login">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </nav>
</header>

<div class="dashboard">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-cog"></i> Administration
        </div>
        <ul class="sidebar-menu">
            <li><a href="/admin/dashboard" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="/admin/stations"><i class="fas fa-gas-pump"></i> Stations</a></li>
            <li><a href="/admin/orders"><i class="fas fa-list"></i> Commandes</a></li>
            <li><a href="/admin/analytics"><i class="fas fa-chart-bar"></i> Analyses</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1>Tableau de bord administrateur</h1>
            <p class="breadcrumb">Admin / Dashboard</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $stats['total_users'] ?></h3>
                <p><i class="fas fa-users"></i> Clients inscrits</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['total_stations'] ?></h3>
                <p><i class="fas fa-gas-pump"></i> Stations actives</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['pending_stations'] ?></h3>
                <p><i class="fas fa-clock"></i> En attente d'approbation</p>
            </div>
            <div class="stat-card">
                <h3><?= number_format($stats['total_revenue'], 0, ',', ' ') ?> FCFA</h3>
                <p><i class="fas fa-money-bill-wave"></i> Revenus (commissions)</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-top: 2rem;">
            <div class="table-container">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    <h3><i class="fas fa-list"></i> Commandes récentes</h3>
                </div>
                <div style="max-height: 500px; overflow-y: auto;">
                    <?php if (empty($recentOrders)): ?>
                        <div style="padding: 2rem; text-align: center; color: #666;">
                            <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                            <p>Aucune commande pour le moment</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Station</th>
                                    <th>Produit</th>
                                    <th>Total</th>
                                    <th>Commission</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($order['customer_nom']) ?></td>
                                        <td><?= htmlspecialchars($order['station_nom']) ?></td>
                                        <td><?= htmlspecialchars($order['produit_nom']) ?> (<?= $order['quantite'] ?>)</td>
                                        <td><?= number_format($order['total'], 0, ',', ' ') ?> FCFA</td>
                                        <td><?= number_format($order['commission_admin'], 0, ',', ' ') ?> FCFA</td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch($order['statut']) {
                                                case 'pending': $statusClass = 'badge-warning'; $statusText = 'En attente'; break;
                                                case 'confirmed': $statusClass = 'badge-info'; $statusText = 'Confirmée'; break;
                                                case 'preparing': $statusClass = 'badge-info'; $statusText = 'Préparation'; break;
                                                case 'delivering': $statusClass = 'badge-warning'; $statusText = 'En livraison'; break;
                                                case 'delivered': $statusClass = 'badge-success'; $statusText = 'Livrée'; break;
                                                case 'cancelled': $statusClass = 'badge-danger'; $statusText = 'Annulée'; break;
                                            }
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-container">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    <h3><i class="fas fa-chart-pie"></i> Statistiques rapides</h3>
                </div>
                <div style="padding: 2rem;">
                    <div class="quick-stats">
                        <div style="margin-bottom: 1.5rem;">
                            <h4>Commandes aujourd'hui</h4>
                            <p style="font-size: 2rem; color: var(--primary-color); margin: 0;">
                                <?= count(array_filter($recentOrders, fn($o) => date('Y-m-d', strtotime($o['date_commande'])) === date('Y-m-d'))) ?>
                            </p>
                        </div>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <h4>Revenus du mois</h4>
                            <p style="font-size: 1.5rem; color: var(--success-color); margin: 0;">
                                <?= number_format(array_sum(array_map(fn($o) => 
                                    date('Y-m', strtotime($o['date_commande'])) === date('Y-m') ? $o['commission_admin'] : 0, 
                                    $recentOrders
                                )), 0, ',', ' ') ?> FCFA
                            </p>
                        </div>
                        
                        <?php if ($stats['pending_stations'] > 0): ?>
                        <div style="margin-bottom: 1.5rem;">
                            <div class="alert alert-warning">
                                <strong><?= $stats['pending_stations'] ?></strong> station(s) en attente d'approbation
                                <div style="margin-top: 1rem;">
                                    <a href="/admin/stations" class="btn btn-primary btn-sm">
                                        Gérer les approbations
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div>
                            <a href="/admin/analytics" class="btn btn-secondary" style="width: 100%;">
                                <i class="fas fa-chart-bar"></i> Voir toutes les analyses
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php
$content = ob_get_clean();
include '../app/Views/layouts/app.php';
?>
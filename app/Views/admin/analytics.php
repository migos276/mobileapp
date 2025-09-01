<?php
$title = 'Analyses - GazExpress';
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
            <li><a href="/admin/dashboard"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="/admin/stations"><i class="fas fa-gas-pump"></i> Stations</a></li>
            <li><a href="/admin/orders"><i class="fas fa-list"></i> Commandes</a></li>
            <li><a href="/admin/analytics" class="active"><i class="fas fa-chart-bar"></i> Analyses</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1>Analyses et Statistiques</h1>
            <p class="breadcrumb">Admin / Analyses</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
            <!-- Commandes mensuelles -->
            <div class="table-container">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    <h3><i class="fas fa-calendar-alt"></i> Commandes par mois</h3>
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($monthlyOrders)): ?>
                        <div style="padding: 2rem; text-align: center; color: #666;">
                            <i class="fas fa-chart-line" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                            <p>Aucune donnée disponible</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mois</th>
                                    <th>Nombre de commandes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monthlyOrders as $data): ?>
                                    <tr>
                                        <td><?= date('M Y', strtotime($data['month'] . '-01')) ?></td>
                                        <td><?= $data['count'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Revenus mensuels -->
            <div class="table-container">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    <h3><i class="fas fa-money-bill-wave"></i> Revenus mensuels (commissions)</h3>
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($monthlyRevenue)): ?>
                        <div style="padding: 2rem; text-align: center; color: #666;">
                            <i class="fas fa-chart-bar" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                            <p>Aucune donnée disponible</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mois</th>
                                    <th>Revenus (FCFA)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monthlyRevenue as $data): ?>
                                    <tr>
                                        <td><?= date('M Y', strtotime($data['month'] . '-01')) ?></td>
                                        <td><?= number_format($data['revenue'], 0, ',', ' ') ?> FCFA</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Produits les plus vendus -->
        <div class="table-container" style="margin-top: 2rem;">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                <h3><i class="fas fa-trophy"></i> Produits les plus vendus</h3>
            </div>
            <div style="max-height: 400px; overflow-y: auto;">
                <?php if (empty($topProducts)): ?>
                    <div style="padding: 2rem; text-align: center; color: #666;">
                        <i class="fas fa-star" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <p>Aucune donnée disponible</p>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité totale vendue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topProducts as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['nom']) ?></td>
                                    <td><?= $product['total_vendu'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php
$content = ob_get_clean();
include '../app/Views/layouts/app.php';
?>

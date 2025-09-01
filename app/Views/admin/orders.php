<?php
$title = 'Gestion des Commandes - GazExpress';
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
            <li><a href="/admin/orders" class="active"><i class="fas fa-list"></i> Commandes</a></li>
            <li><a href="/admin/analytics"><i class="fas fa-chart-bar"></i> Analyses</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1>Gestion des Commandes</h1>
            <p class="breadcrumb">Admin / Commandes</p>
        </div>

        <div class="table-container">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                <h3><i class="fas fa-list"></i> Toutes les commandes</h3>
            </div>
            <div style="max-height: 600px; overflow-y: auto;">
                <?php if (empty($orders)): ?>
                    <div style="padding: 2rem; text-align: center; color: #666;">
                        <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <p>Aucune commande trouvée</p>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Station</th>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Total</th>
                                <th>Commission</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['date_commande'])) ?></td>
                                    <td><?= htmlspecialchars($order['customer_nom']) ?></td>
                                    <td><?= htmlspecialchars($order['station_nom']) ?> (<?= htmlspecialchars($order['nom_entreprise']) ?>)</td>
                                    <td><?= htmlspecialchars($order['produit_nom']) ?></td>
                                    <td><?= $order['quantite'] ?></td>
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
                                            default: $statusClass = 'badge-secondary'; $statusText = ucfirst($order['statut']); break;
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
    </main>
</div>

<?php
$content = ob_get_clean();
include '../app/Views/layouts/app.php';
?>

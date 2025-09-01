<?php
$title = 'Dashboard Station - GazExpress';
$includeMap = true;

ob_start();
?>

<header class="header">
    <nav class="navbar">
        <div class="nav-brand">
            <i class="fas fa-fire"></i>
            <span>GazExpress</span>
        </div>
        <div class="nav-links">
            <span>Station: <?= htmlspecialchars($station['nom_entreprise']) ?></span>
            <a href="/logout" class="btn-login">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </nav>
</header>

<div class="dashboard">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-gas-pump"></i> Espace Station
        </div>
        <ul class="sidebar-menu">
            <li><a href="/station/dashboard" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="/station/orders"><i class="fas fa-list"></i> Commandes</a></li>
            <li><a href="/station/stock"><i class="fas fa-boxes"></i> Gestion Stock</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1>Tableau de bord</h1>
            <p class="breadcrumb">Station / Dashboard</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $stats['total_orders'] ?></h3>
                <p><i class="fas fa-shopping-cart"></i> Commandes totales</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['pending_orders'] ?></h3>
                <p><i class="fas fa-clock"></i> En attente</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['delivered_orders'] ?></h3>
                <p><i class="fas fa-check-circle"></i> Livrées</p>
            </div>
            <div class="stat-card">
                <h3><?= number_format($stats['total_revenue'], 0, ',', ' ') ?> FCFA</h3>
                <p><i class="fas fa-money-bill-wave"></i> Revenus</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
            <div class="table-container">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    <h3><i class="fas fa-list"></i> Commandes récentes</h3>
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($orders)): ?>
                        <div style="padding: 2rem; text-align: center; color: #666;">
                            <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                            <p>Aucune commande pour le moment</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($order['customer_nom']) ?></td>
                                        <td><?= htmlspecialchars($order['produit_nom']) ?></td>
                                        <td><?= $order['quantite'] ?></td>
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
                                        <td>
                                            <?php if ($order['statut'] === 'pending'): ?>
                                                <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'confirmed')" class="btn btn-primary btn-sm">
                                                    Confirmer
                                                </button>
                                            <?php elseif ($order['statut'] === 'confirmed'): ?>
                                                <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'preparing')" class="btn btn-primary btn-sm">
                                                    Préparer
                                                </button>
                                            <?php elseif ($order['statut'] === 'preparing'): ?>
                                                <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'delivering')" class="btn btn-primary btn-sm">
                                                    Livrer
                                                </button>
                                            <?php elseif ($order['statut'] === 'delivering'): ?>
                                                <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'delivered')" class="btn btn-success btn-sm">
                                                    Terminé
                                                </button>
                                            <?php endif; ?>
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
                    <h3><i class="fas fa-boxes"></i> État des stocks</h3>
                </div>
                <div style="padding: 2rem;">
                    <?php if (empty($stocks)): ?>
                        <div style="text-align: center; color: #666;">
                            <i class="fas fa-boxes" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                            <p>Aucun stock configuré</p>
                            <a href="/station/stock" class="btn btn-primary" style="margin-top: 1rem;">
                                Gérer les stocks
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="stock-grid">
                            <?php foreach ($stocks as $stock): ?>
                                <div class="stock-item" style="border: 1px solid var(--border-color); border-radius: var(--radius); padding: 1rem; margin-bottom: 1rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <h4><?= htmlspecialchars($stock['produit_nom']) ?></h4>
                                            <p style="color: #666; margin: 0.5rem 0;">
                                                Stock: <strong><?= $stock['quantite'] ?></strong> <?= $stock['unite'] ?>
                                            </p>
                                            <?php if ($stock['prix_station']): ?>
                                                <p style="color: var(--primary-color); margin: 0;">
                                                    Prix: <?= formatPrice($stock['prix_station']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <?php if ($stock['quantite'] > 0): ?>
                                                <span class="badge badge-success">Disponible</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Rupture</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="/station/stock" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-edit"></i> Gérer les stocks
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function updateOrderStatus(orderId, status) {
    fetch(`/station/orders/${orderId}/update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Statut mis à jour', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message || 'Erreur', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion', 'error');
    });
}
</script>

<?php
$content = ob_get_clean();
include '../app/Views/layouts/app.php';
?>
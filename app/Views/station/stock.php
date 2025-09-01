<?php
$title = 'Gestion des Stocks - GazExpress';
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
            <li><a href="/station/dashboard"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="/station/orders"><i class="fas fa-list"></i> Commandes</a></li>
            <li><a href="/station/stock" class="active"><i class="fas fa-boxes"></i> Gestion Stock</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1>Gestion des Stocks</h1>
            <p class="breadcrumb">Station / Stocks</p>
        </div>

        <div class="table-container">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                <h3><i class="fas fa-boxes"></i> Stocks des produits</h3>
            </div>
            <div style="padding: 2rem;">
                <div class="stock-management">
                    <?php foreach ($products as $product): ?>
                        <?php
                        $currentStock = null;
                        foreach ($stocks as $stock) {
                            if ($stock['produit_id'] == $product['id']) {
                                $currentStock = $stock;
                                break;
                            }
                        }
                        ?>
                        <div class="stock-card" style="border: 1px solid var(--border-color); border-radius: var(--radius); padding: 1.5rem; margin-bottom: 1.5rem;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; align-items: center;">
                                <div>
                                    <h4><?= htmlspecialchars($product['nom']) ?></h4>
                                    <p style="color: #666; margin: 0.5rem 0;"><?= htmlspecialchars($product['description']) ?></p>
                                    <p style="color: var(--primary-color); margin: 0;">
                                        Prix de base: <?= number_format($product['prix_unitaire'], 0, ',', ' ') ?> FCFA
                                    </p>
                                </div>
                                
                                <div>
                                    <label for="quantite_<?= $product['id'] ?>">Quantité en stock</label>
                                    <input type="number" id="quantite_<?= $product['id'] ?>" 
                                           class="form-control" min="0" 
                                           value="<?= $currentStock['quantite'] ?? 0 ?>"
                                           onchange="updateStockQuantity(<?= $product['id'] ?>, this.value)">
                                </div>
                                
                                <div>
                                    <label for="prix_<?= $product['id'] ?>">Votre prix (FCFA)</label>
                                    <input type="number" id="prix_<?= $product['id'] ?>" 
                                           class="form-control" min="0" step="100"
                                           value="<?= $currentStock['prix_station'] ?? $product['prix_unitaire'] ?>"
                                           onchange="updateStockPrice(<?= $product['id'] ?>, this.value)">
                                </div>
                                
                                <div style="text-align: center;">
                                    <?php if (($currentStock['quantite'] ?? 0) > 0): ?>
                                        <span class="badge badge-success">Disponible</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Rupture</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 2rem; padding: 1rem; background: var(--light-color); border-radius: var(--radius);">
                    <h4><i class="fas fa-info-circle"></i> Informations importantes</h4>
                    <ul style="margin: 1rem 0; padding-left: 2rem;">
                        <li>Mettez à jour vos stocks régulièrement pour éviter les ruptures</li>
                        <li>Vous pouvez ajuster vos prix selon votre marge souhaitée</li>
                        <li>Les clients verront vos prix lors de la commande</li>
                        <li>Les modifications sont appliquées immédiatement</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function updateStockQuantity(productId, quantity) {
    updateStock(productId, { quantite: quantity });
}

function updateStockPrice(productId, price) {
    updateStock(productId, { prix_station: price });
}

function updateStock(productId, data) {
    const formData = new FormData();
    formData.append('produit_id', productId);
    
    if (data.quantite !== undefined) {
        formData.append('quantite', data.quantite);
    }
    if (data.prix_station !== undefined) {
        formData.append('prix_station', data.prix_station);
    }
    
    fetch('/station/stock', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Stock mis à jour', 'success');
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
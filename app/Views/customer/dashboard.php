<?php
$title = 'Dashboard Client - GazExpress';
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
            <span>Bonjour, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="/logout" class="btn-login">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </nav>
</header>

<div class="dashboard">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-user"></i> Espace Client
        </div>
        <ul class="sidebar-menu">
            <li><a href="/customer/dashboard" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="#" onclick="showOrderForm()"><i class="fas fa-shopping-cart"></i> Commander</a></li>
            <li><a href="/customer/orders"><i class="fas fa-list"></i> Mes commandes</a></li>
            <li><a href="/customer/profile"><i class="fas fa-user-edit"></i> Mon profil</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1>Tableau de bord</h1>
            <p class="breadcrumb">Accueil / Dashboard</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $stats['total_orders'] ?></h3>
                <p><i class="fas fa-shopping-cart"></i> Commandes totales</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['delivered_orders'] ?></h3>
                <p><i class="fas fa-check-circle"></i> Commandes livrées</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['pending_orders'] ?></h3>
                <p><i class="fas fa-clock"></i> Commandes en cours</p>
            </div>
            <div class="stat-card">
                <h3><?= number_format($stats['total_spent'], 0, ',', ' ') ?> FCFA</h3>
                <p><i class="fas fa-money-bill-wave"></i> Total dépensé</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
            <div class="table-container">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    <h3><i class="fas fa-shopping-cart"></i> Commandes récentes</h3>
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($orders)): ?>
                        <div style="padding: 2rem; text-align: center; color: #666;">
                            <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                            <p>Aucune commande pour le moment</p>
                            <button onclick="showOrderForm()" class="btn btn-primary" style="margin-top: 1rem;">
                                Commander maintenant
                            </button>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($order['produit_nom']) ?></td>
                                        <td><?= $order['quantite'] ?></td>
                                        <td><?= number_format($order['total'], 0, ',', ' ') ?> FCFA</td>
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
                    <h3><i class="fas fa-map-marker-alt"></i> Votre position</h3>
                </div>
                <div style="padding: 2rem;">
                    <button onclick="showOrderForm()" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                        <i class="fas fa-shopping-cart"></i> Nouvelle commande
                    </button>
                    <div id="map" style="height: 300px; border-radius: var(--radius);"></div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal de commande -->
<div id="orderModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3><i class="fas fa-shopping-cart"></i> Nouvelle commande</h3>
            <button onclick="closeModal('orderModal')" class="modal-close">&times;</button>
        </div>
        <div class="modal-body" id="orderFormContainer">
            <!-- Le formulaire sera chargé ici -->
        </div>
    </div>
</div>

<script>
// Initialiser la carte
const userLat = <?= $user['latitude'] ?>;
const userLng = <?= $user['longitude'] ?>;
const map = initMap('map', userLat, userLng, 15);

// Ajouter le marqueur de l'utilisateur
addMarker(map, userLat, userLng, 'Votre position', 'blue');

// Charger les stations proches
loadNearbyStations();

function loadNearbyStations() {
    fetch(`/api/stations/nearby?lat=${userLat}&lng=${userLng}`)
        .then(response => response.json())
        .then(data => {
            data.stations.forEach(station => {
                const popupText = `<strong>${station.nom_entreprise}</strong><br>
                                   Distance: ${station.distance} km<br>
                                   ${station.heures_ouverture || 'Horaires non spécifiés'}`;
                addMarker(map, station.latitude, station.longitude, popupText, 'red');
            });
        })
        .catch(error => console.error('Erreur:', error));
}

function showOrderForm() {
    openModal('orderModal');
    loadOrderForm();
}

function loadOrderForm() {
    const container = document.getElementById('orderFormContainer');
    container.innerHTML = '<div style="text-align: center; padding: 2rem;"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>';
    
    // Charger le formulaire de commande
    fetch('/api/products')
        .then(response => response.json())
        .then(data => {
            container.innerHTML = createOrderForm(data.products);
            loadStationsForOrder();
        })
        .catch(error => {
            container.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement</div>';
        });
}

function createOrderForm(products) {
    return `
        <form id="orderForm">
            <div class="form-group">
                <label for="produit">Produit</label>
                <select id="produit" name="produit_id" class="form-select" required onchange="updateStationsForProduct()">
                    <option value="">Sélectionner un produit</option>
                    ${products.map(p => `<option value="${p.id}" data-prix="${p.prix_unitaire}">${p.nom} - ${formatPrice(p.prix_unitaire)}</option>`).join('')}
                </select>
            </div>

            <div class="form-group">
                <label for="quantite">Quantité</label>
                <input type="number" id="quantite" name="quantite" class="form-control" min="1" value="1" required onchange="calculateOrderTotal()">
            </div>

            <div class="form-group">
                <label for="station">Station de service</label>
                <select id="station" name="station_id" class="form-select" required onchange="calculateOrderTotal()">
                    <option value="">Sélectionner d'abord un produit</option>
                </select>
            </div>

            <div class="form-group">
                <label for="methode_paiement">Méthode de paiement</label>
                <select id="methode_paiement" name="methode_paiement" class="form-select" required>
                    <option value="">Sélectionner</option>
                    <option value="orange_money">Orange Money</option>
                    <option value="mtn_money">MTN Money</option>
                </select>
            </div>

            <div class="form-group">
                <label for="telephone_paiement">Numéro de paiement</label>
                <input type="tel" id="telephone_paiement" name="telephone_paiement" class="form-control" 
                       placeholder="+237 6XX XXX XXX" required>
            </div>

            <div class="form-group">
                <label for="adresse_livraison">Adresse de livraison</label>
                <textarea id="adresse_livraison" name="adresse_livraison" class="form-control" rows="3" required><?= htmlspecialchars($user['adresse']) ?></textarea>
            </div>

            <div style="background: var(--light-color); padding: 1rem; border-radius: var(--radius); margin: 1rem 0;">
                <h4>Récapitulatif de la commande</h4>
                <div id="orderSummary">
                    <p>Sélectionnez un produit et une station pour voir le récapitulatif</p>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-credit-card"></i> Commander et Payer
            </button>
        </form>
    `;
}

let nearbyStations = [];

function loadStationsForOrder() {
    fetch(`/api/stations/nearby?lat=${userLat}&lng=${userLng}`)
        .then(response => response.json())
        .then(data => {
            nearbyStations = data.stations;
        })
        .catch(error => console.error('Erreur:', error));
}

function updateStationsForProduct() {
    const produitId = document.getElementById('produit').value;
    const stationSelect = document.getElementById('station');
    
    if (!produitId) {
        stationSelect.innerHTML = '<option value="">Sélectionner d\'abord un produit</option>';
        return;
    }
    
    stationSelect.innerHTML = '<option value="">Sélectionner une station</option>';
    nearbyStations.forEach(station => {
        const option = document.createElement('option');
        option.value = station.id;
        option.textContent = `${station.nom_entreprise} (${station.distance} km)`;
        option.dataset.distance = station.distance;
        stationSelect.appendChild(option);
    });
    
    calculateOrderTotal();
}

function calculateOrderTotal() {
    const produitSelect = document.getElementById('produit');
    const quantite = parseInt(document.getElementById('quantite').value) || 0;
    const stationSelect = document.getElementById('station');
    const summaryDiv = document.getElementById('orderSummary');
    
    if (!produitSelect.value || !quantite || !stationSelect.value) {
        summaryDiv.innerHTML = '<p>Sélectionnez un produit, une quantité et une station pour voir le récapitulatif</p>';
        return;
    }
    
    const prixUnitaire = parseFloat(produitSelect.selectedOptions[0].dataset.prix);
    const distance = parseFloat(stationSelect.selectedOptions[0].dataset.distance);
    
    const sousTotal = prixUnitaire * quantite;
    const fraisLivraison = 500 + (distance * 100);
    const total = sousTotal + fraisLivraison;
    
    summaryDiv.innerHTML = `
        <table style="width: 100%; font-size: 0.9rem;">
            <tr><td>Produit:</td><td>${produitSelect.selectedOptions[0].text.split(' - ')[0]}</td></tr>
            <tr><td>Quantité:</td><td>${quantite}</td></tr>
            <tr><td>Prix unitaire:</td><td>${formatPrice(prixUnitaire)}</td></tr>
            <tr><td>Sous-total:</td><td>${formatPrice(sousTotal)}</td></tr>
            <tr><td>Distance:</td><td>${distance} km</td></tr>
            <tr><td>Frais de livraison:</td><td>${formatPrice(fraisLivraison)}</td></tr>
            <tr style="border-top: 1px solid var(--border-color); font-weight: bold;">
                <td>Total à payer:</td><td style="color: var(--primary-color);">${formatPrice(total)}</td>
            </tr>
        </table>
    `;
}

// Gestion de la soumission du formulaire de commande
document.addEventListener('submit', function(e) {
    if (e.target.id === 'orderForm') {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const orderData = {
            station_id: formData.get('station_id'),
            produit_id: formData.get('produit_id'),
            quantite: formData.get('quantite'),
            methode_paiement: formData.get('methode_paiement'),
            telephone_paiement: formData.get('telephone_paiement'),
            adresse_livraison: formData.get('adresse_livraison'),
            latitude_livraison: userLat,
            longitude_livraison: userLng
        };
        
        fetch('/api/orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Commande passée avec succès !', 'success');
                closeModal('orderModal');
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showNotification(data.message || 'Erreur lors de la commande', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur de connexion', 'error');
        });
    }
});
</script>

<?php
$content = ob_get_clean();
include '../app/Views/layouts/app.php';
?>
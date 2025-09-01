<?php
$title = 'Inscription - GazExpress';
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
            <a href="/">Accueil</a>
            <a href="/login">Connexion</a>
        </div>
    </nav>
</header>

<div class="form-container" style="max-width: 600px;">
    <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary-color);">
        <i class="fas fa-user-plus"></i> Inscription
    </h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= $success ?>
            <div style="margin-top: 1rem;">
                <a href="/login" class="btn btn-primary">Se connecter</a>
            </div>
        </div>
    <?php else: ?>

    <form method="POST" action="/register" id="registerForm">
        <div class="form-group">
            <label for="role">Type de compte</label>
            <select id="role" name="role" class="form-select" required onchange="toggleStationFields()">
                <option value="customer" <?= ($userType ?? 'customer') === 'customer' ? 'selected' : '' ?>>Client</option>
                <option value="station" <?= ($userType ?? 'customer') === 'station' ? 'selected' : '' ?>>Station de Service</option>
            </select>
        </div>

        <div class="form-group">
            <label for="nom">Nom complet</label>
            <input type="text" id="nom" name="nom" class="form-control" required
                   value="<?= htmlspecialchars($formData['nom'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required
                   value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" class="form-control" required
                   placeholder="+237 6XX XXX XXX"
                   value="<?= htmlspecialchars($formData['telephone'] ?? '') ?>">
        </div>

        <div id="stationFields" style="display: <?= ($userType ?? 'customer') === 'station' ? 'block' : 'none' ?>;">
            <div class="form-group">
                <label for="nom_entreprise">Nom de l'entreprise</label>
                <input type="text" id="nom_entreprise" name="nom_entreprise" class="form-control"
                       value="<?= htmlspecialchars($formData['nom_entreprise'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="numero_licence">Numéro de licence</label>
                <input type="text" id="numero_licence" name="numero_licence" class="form-control"
                       value="<?= htmlspecialchars($formData['numero_licence'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="heures_ouverture">Heures d'ouverture</label>
                <input type="text" id="heures_ouverture" name="heures_ouverture" class="form-control"
                       placeholder="Ex: 08h00 - 18h00"
                       value="<?= htmlspecialchars($formData['heures_ouverture'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="adresse">Adresse complète</label>
            <textarea id="adresse" name="adresse" class="form-control" rows="3" required><?= htmlspecialchars($formData['adresse'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-secondary" onclick="getLocationForRegistration()" style="width: 100%;">
                <i class="fas fa-map-marker-alt"></i> Obtenir ma position
            </button>
            <input type="hidden" id="latitude" name="latitude" value="<?= $formData['latitude'] ?? '' ?>">
            <input type="hidden" id="longitude" name="longitude" value="<?= $formData['longitude'] ?? '' ?>">
            <div id="locationStatus" style="margin-top: 0.5rem; font-size: 0.9rem;"></div>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirmer le mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fas fa-user-plus"></i> S'inscrire
        </button>
    </form>

    <?php endif; ?>

    <div style="text-align: center; margin-top: 2rem;">
        <p>Déjà un compte ? 
            <a href="/login" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">
                Connectez-vous
            </a>
        </p>
    </div>
</div>

<script>
function toggleStationFields() {
    const role = document.getElementById('role').value;
    const stationFields = document.getElementById('stationFields');
    const requiredFields = stationFields.querySelectorAll('input, textarea');
    
    if (role === 'station') {
        stationFields.style.display = 'block';
        requiredFields.forEach(field => {
            if (field.id === 'nom_entreprise') {
                field.required = true;
            }
        });
    } else {
        stationFields.style.display = 'none';
        requiredFields.forEach(field => field.required = false);
    }
}

function getLocationForRegistration() {
    const statusDiv = document.getElementById('locationStatus');
    statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Obtention de votre position...';
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                statusDiv.innerHTML = '<i class="fas fa-check-circle" style="color: var(--success-color);"></i> Position obtenue avec succès';
            },
            function(error) {
                statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle" style="color: var(--error-color);"></i> Erreur: Veuillez autoriser la géolocalisation';
            }
        );
    } else {
        statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle" style="color: var(--error-color);"></i> La géolocalisation n\'est pas supportée';
    }
}

// Auto-obtenir la position au chargement
window.addEventListener('load', function() {
    setTimeout(getLocationForRegistration, 1000);
});
</script>

<?php
$content = ob_get_clean();
include '../app/Views/layouts/app.php';
?>
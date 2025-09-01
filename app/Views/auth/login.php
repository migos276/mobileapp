<?php
$title = 'Connexion - GazExpress';
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
        </div>
    </nav>
</header>

<div class="form-container">
    <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary-color);">
        <i class="fas fa-sign-in-alt"></i> Connexion
    </h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required 
                   value="<?= htmlspecialchars($email ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fas fa-sign-in-alt"></i> Se connecter
        </button>
    </form>

    <div style="text-align: center; margin-top: 2rem;">
        <p>Pas encore de compte ? 
            <a href="/register" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">
                Inscrivez-vous
            </a>
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../app/Views/layouts/app.php';
?>
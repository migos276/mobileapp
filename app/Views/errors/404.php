<?php
$title = 'Page non trouvée - GazExpress';
$includeMap = false;

ob_start();
?>

<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; text-align: center; padding: 2rem;">
    <i class="fas fa-exclamation-triangle" style="font-size: 5rem; color: var(--warning-color); margin-bottom: 2rem;"></i>
    <h1 style="color: var(--dark-color); margin-bottom: 1rem;">Page non trouvée</h1>
    <p style="color: #666; margin-bottom: 2rem; max-width: 400px;">
        La page que vous recherchez n'existe pas ou a été déplacée.
    </p>
    <a href="/" class="btn btn-primary">
        <i class="fas fa-home"></i> Retour à l'accueil
    </a>
</div>

<?php
$content = ob_get_clean();
include '../app/Views/layouts/app.php';
?>
<?php
// Démarrer la session au tout début
session_start();

// Autoloader PSR-4 pour charger les classes automatiquement
spl_autoload_register(function ($class) {
    // Préfixe de l'espace de nom du projet
    $prefix = 'App\\';

    // Répertoire de base pour le préfixe de l'espace de nom
    $base_dir = __DIR__ . '/../app/';

    // Est-ce que la classe utilise le préfixe de l'espace de nom ?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Non, passer au prochain autoloader enregistré
        return;
    }

    // Obtenir le nom de la classe relative
    $relative_class = substr($class, $len);

    // Remplacer le préfixe de l'espace de nom par le répertoire de base,
    // remplacer les séparateurs d'espace de nom par des séparateurs de répertoire
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Si le fichier existe, le charger
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\App;

// Initialiser l'application
$app = new App();
$app->run();
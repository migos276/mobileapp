<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'GazExpress - Livraison de Gaz de Cuisine' ?></title>
    
    <!-- PWA Meta Tags -->
    <meta name="description" content="Application de livraison de gaz de cuisine à domicile">
    <meta name="theme-color" content="#FF6B35">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="GazExpress">
    
    <!-- Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/icons/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/icons/icon-16x16.png">
    <link rel="apple-touch-icon" href="/assets/icons/icon-192x192.png">
    
    <!-- Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- Styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?php if (isset($includeMap) && $includeMap): ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <?php endif; ?>
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <?= $additionalHead ?? '' ?>
</head>
<body>
    <?= $content ?>
    
    <!-- Scripts -->
    <?php if (isset($includeMap) && $includeMap): ?>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <?php endif; ?>
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/pwa.js"></script>
    
    <?= $additionalScripts ?? '' ?>
</body>
</html>
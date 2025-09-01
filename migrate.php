<?php
// Script de migration pour créer les tables
$dbFile = __DIR__ . '/database/gazexpress.db';

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents(__DIR__ . '/supabase/migrations/20250901162837_square_summit.sql');
    $pdo->exec($sql);

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}

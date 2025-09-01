<?php
namespace App\Core;

use PDO;
use PDOException;

class Database extends PDO {
    private static $instance = null;
    private $host = __DIR__ . '/../../database/gazexpress.db'; // Utiliser un chemin absolu pour plus de fiabilité

    // Le constructeur est maintenant privé pour empêcher l'instanciation directe
    private function __construct() {
        try {
            // Créer le dossier de la base de données s'il n'existe pas
            $dbDir = dirname($this->host);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0777, true);
            }

            // Appeler le constructeur parent de PDO
            parent::__construct("sqlite:{$this->host}");
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Activer les clés étrangères
            $this->exec('PRAGMA foreign_keys = ON');
            
            // Initialiser le schéma de la base de données si nécessaire
            $this->initDatabase();
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    private function initDatabase() {
        // Vérifier si la table 'users' existe pour éviter de ré-exécuter le schéma
        $stmt = $this->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
        if ($stmt->fetch() !== false) {
            return; // Les tables existent déjà, on ne fait rien
        }

        // Le chemin correct vers le fichier schema.sql
        $sqlFile = __DIR__ . '/../../database/schema.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            // S'assurer que le fichier n'est pas vide avant de l'exécuter
            if (!empty(trim($sql))) {
                $this->exec($sql);
            }
        }
    }

    // La méthode statique qui contrôle l'accès à l'instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Empêcher le clonage de l'instance
    private function __clone() {}

    // Empêcher la désérialisation de l'instance
    public function __wakeup() {}
}
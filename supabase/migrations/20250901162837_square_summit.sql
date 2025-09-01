-- Base de données GazExpress SQLite
-- Création des tables

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role TEXT CHECK(role IN ('admin', 'station', 'customer')) NOT NULL,
    latitude REAL,
    longitude REAL,
    adresse TEXT,
    statut TEXT CHECK(statut IN ('pending', 'approved', 'rejected')) DEFAULT 'pending',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des stations de service
CREATE TABLE IF NOT EXISTS stations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    nom_entreprise VARCHAR(150) NOT NULL,
    numero_licence VARCHAR(50),
    description TEXT,
    heures_ouverture VARCHAR(100),
    statut_ouverture BOOLEAN DEFAULT 1,
    commission_rate DECIMAL(5,2) DEFAULT 5.00,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des produits (types de gaz)
CREATE TABLE IF NOT EXISTS produits (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    unite VARCHAR(20) DEFAULT 'kg',
    image VARCHAR(255),
    actif BOOLEAN DEFAULT 1
);

-- Table des stocks
CREATE TABLE IF NOT EXISTS stocks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    station_id INTEGER NOT NULL,
    produit_id INTEGER NOT NULL,
    quantite INTEGER NOT NULL DEFAULT 0,
    prix_station DECIMAL(10,2),
    derniere_maj DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (station_id) REFERENCES stations(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE,
    UNIQUE(station_id, produit_id)
);

-- Table des commandes
CREATE TABLE IF NOT EXISTS commandes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_id INTEGER NOT NULL,
    station_id INTEGER NOT NULL,
    produit_id INTEGER NOT NULL,
    quantite INTEGER NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    frais_livraison DECIMAL(10,2) NOT NULL,
    commission_admin DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    statut TEXT CHECK(statut IN ('pending', 'confirmed', 'preparing', 'delivering', 'delivered', 'cancelled')) DEFAULT 'pending',
    methode_paiement TEXT CHECK(methode_paiement IN ('orange_money', 'mtn_money')) NOT NULL,
    telephone_paiement VARCHAR(20) NOT NULL,
    statut_paiement TEXT CHECK(statut_paiement IN ('pending', 'paid', 'failed')) DEFAULT 'pending',
    adresse_livraison TEXT NOT NULL,
    latitude_livraison REAL NOT NULL,
    longitude_livraison REAL NOT NULL,
    distance_km DECIMAL(8,2),
    temps_estime INTEGER, -- en minutes
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_livraison DATETIME NULL,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (station_id) REFERENCES stations(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
);

-- Table des transactions
CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    commande_id INTEGER NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    commission DECIMAL(10,2) NOT NULL,
    type TEXT CHECK(type IN ('payment', 'commission', 'refund')) NOT NULL,
    statut TEXT CHECK(statut IN ('pending', 'completed', 'failed')) DEFAULT 'pending',
    reference_transaction VARCHAR(100),
    date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
);

-- Insertion des produits par défaut
INSERT OR IGNORE INTO produits (id, nom, description, prix_unitaire, unite) VALUES
(1, 'Gaz 6kg', 'Bouteille de gaz butane 6kg', 3500, 'bouteille'),
(2, 'Gaz 12kg', 'Bouteille de gaz butane 12kg', 6500, 'bouteille'),
(3, 'Gaz 35kg', 'Bouteille de gaz butane 35kg', 18000, 'bouteille');

-- Création de l'administrateur par défaut
INSERT OR IGNORE INTO users (id, nom, email, telephone, mot_de_passe, role, statut) VALUES
(1, 'Administrateur', 'admin@gazexpress.com', '+237000000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved');

-- Index pour optimiser les performances
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_users_location ON users(latitude, longitude);
CREATE INDEX IF NOT EXISTS idx_commandes_customer ON commandes(customer_id);
CREATE INDEX IF NOT EXISTS idx_commandes_station ON commandes(station_id);
CREATE INDEX IF NOT EXISTS idx_commandes_statut ON commandes(statut);
CREATE INDEX IF NOT EXISTS idx_stocks_station_produit ON stocks(station_id, produit_id);
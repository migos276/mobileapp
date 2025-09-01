<?php
namespace App\Models;

use App\Core\Model;

class Stock extends Model {
    protected $table = 'stocks';

    public function getStationStock($stationId) {
        $sql = "SELECT s.*, p.nom as produit_nom, p.unite, p.prix_unitaire
                FROM stocks s
                JOIN produits p ON s.produit_id = p.id
                WHERE s.station_id = ?
                ORDER BY p.nom";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$stationId]);
        return $stmt->fetchAll();
    }

    public function updateStock($stationId, $produitId, $quantite, $prixStation = null) {
        // Vérifier si le stock existe
        $existing = $this->db->prepare("SELECT id FROM stocks WHERE station_id = ? AND produit_id = ?");
        $existing->execute([$stationId, $produitId]);
        $stock = $existing->fetch();

        $data = [
            'quantite' => $quantite,
            'derniere_maj' => date('Y-m-d H:i:s')
        ];

        if ($prixStation !== null) {
            $data['prix_station'] = $prixStation;
        }

        if ($stock) {
            return $this->update($stock['id'], $data);
        } else {
            $data['station_id'] = $stationId;
            $data['produit_id'] = $produitId;
            return $this->create($data);
        }
    }

    public function checkAvailability($stationId, $produitId, $quantiteRequise) {
        $sql = "SELECT quantite FROM stocks WHERE station_id = ? AND produit_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$stationId, $produitId]);
        $stock = $stmt->fetch();
        
        return $stock && $stock['quantite'] >= $quantiteRequise;
    }

    public function decreaseStock($stationId, $produitId, $quantite) {
        $sql = "UPDATE stocks SET quantite = quantite - ?, derniere_maj = CURRENT_TIMESTAMP
                WHERE station_id = ? AND produit_id = ? AND quantite >= ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantite, $stationId, $produitId, $quantite]);
    }

    public function getStationsWithProduct($produitId, $userLat = null, $userLng = null, $maxDistance = 20) {
        $sql = "SELECT DISTINCT
                    u.id, u.nom_entreprise, u.adresse, u.telephone, u.email,
                    u.latitude, u.longitude, u.heures_ouverture,
                    s.quantite, s.prix_station,
                    p.nom as produit_nom, p.prix_unitaire,
                    (
                        6371 * acos(
                            cos(radians(?)) * cos(radians(u.latitude)) *
                            cos(radians(u.longitude) - radians(?)) +
                            sin(radians(?)) * sin(radians(u.latitude))
                        )
                    ) as distance
                FROM stocks s
                JOIN users u ON s.station_id = u.id
                JOIN produits p ON s.produit_id = p.id
                WHERE s.produit_id = ? AND s.quantite > 0 AND u.role = 'station'
                HAVING distance <= ?
                ORDER BY distance";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userLat ?: 0, $userLng ?: 0, $userLat ?: 0, $produitId, $maxDistance]);
        return $stmt->fetchAll();
    }
}

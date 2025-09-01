<?php
namespace App\Models;

use App\Core\Model;

class Order extends Model {
    protected $table = 'commandes';

    public function createOrder($data) {
        // Calculer les frais et commission
        $distance = $this->calculateDistance(
            $data['customer_latitude'], $data['customer_longitude'],
            $data['station_latitude'], $data['station_longitude']
        );
        
        $fraisLivraison = $this->calculateDeliveryFee($distance);
        $commission = $fraisLivraison * 0.05; // 5% de commission
        $sousTotal = $data['prix_unitaire'] * $data['quantite'];
        $total = $sousTotal + $fraisLivraison;

        $orderData = [
            'customer_id' => $data['customer_id'],
            'station_id' => $data['station_id'],
            'produit_id' => $data['produit_id'],
            'quantite' => $data['quantite'],
            'prix_unitaire' => $data['prix_unitaire'],
            'frais_livraison' => $fraisLivraison,
            'commission_admin' => $commission,
            'total' => $total,
            'methode_paiement' => $data['methode_paiement'],
            'telephone_paiement' => $data['telephone_paiement'],
            'adresse_livraison' => $data['adresse_livraison'],
            'latitude_livraison' => $data['latitude_livraison'],
            'longitude_livraison' => $data['longitude_livraison'],
            'distance_km' => $distance,
            'temps_estime' => $this->calculateEstimatedTime($distance),
            'date_commande' => date('Y-m-d H:i:s')
        ];

        return $this->create($orderData);
    }

    public function getCustomerOrders($customerId) {
        $sql = "SELECT c.*, p.nom as produit_nom, s.nom_entreprise, u.nom as station_nom 
                FROM commandes c 
                JOIN produits p ON c.produit_id = p.id 
                JOIN stations s ON c.station_id = s.id 
                JOIN users u ON s.user_id = u.id 
                WHERE c.customer_id = ? 
                ORDER BY c.date_commande DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }

    public function getStationOrders($stationId) {
        $sql = "SELECT c.*, p.nom as produit_nom, u.nom as customer_nom, u.telephone as customer_telephone
                FROM commandes c 
                JOIN produits p ON c.produit_id = p.id 
                JOIN users u ON c.customer_id = u.id 
                WHERE c.station_id = ? 
                ORDER BY c.date_commande DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$stationId]);
        return $stmt->fetchAll();
    }

    public function updateStatus($orderId, $status) {
        $data = ['statut' => $status];
        
        if ($status === 'delivered') {
            $data['date_livraison'] = date('Y-m-d H:i:s');
        }
        
        return $this->update($orderId, $data);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Rayon de la Terre en km
        
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);
        
        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;
        
        $a = sin($deltaLat/2) * sin($deltaLat/2) + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon/2) * sin($deltaLon/2);
        $c = 2 * asin(sqrt($a));
        
        return round($earthRadius * $c, 2);
    }

    private function calculateDeliveryFee($distance) {
        $baseFee = 500; // Frais de base en FCFA
        $perKmFee = 100; // Frais par km en FCFA
        
        return $baseFee + ($distance * $perKmFee);
    }

    private function calculateEstimatedTime($distance) {
        // Estimation: 30 km/h en moyenne + 10 minutes de préparation
        return round(($distance / 30) * 60) + 10;
    }
}
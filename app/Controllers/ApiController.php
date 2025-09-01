<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Stock;
use Exception;

class ApiController extends Controller {
    private $userModel;
    private $productModel;
    private $orderModel;
    private $stockModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->productModel = new Product();
        $this->orderModel = new Order();
        $this->stockModel = new Stock();
    }

    public function nearbyStations() {
        $latitude = $_GET['lat'] ?? null;
        $longitude = $_GET['lng'] ?? null;
        $maxDistance = $_GET['distance'] ?? 20;

        if (!$latitude || !$longitude) {
            $this->json(['error' => 'Coordonnées requises'], 400);
            return;
        }

        $stations = $this->userModel->getNearbyStations($latitude, $longitude, $maxDistance);
        $this->json(['stations' => $stations]);
    }

    public function stationsByProduct($productId) {
        $latitude = $_GET['lat'] ?? null;
        $longitude = $_GET['lng'] ?? null;
        $maxDistance = $_GET['distance'] ?? 20;

        if (!$productId) {
            $this->json(['error' => 'ID produit requis'], 400);
            return;
        }

        // Get stations that have this product in stock
        $stations = $this->stockModel->getStationsWithProduct($productId, $latitude, $longitude, $maxDistance);
        $this->json(['stations' => $stations]);
    }

    public function products() {
        $products = $this->productModel->getActiveProducts();
        $this->json(['products' => $products]);
    }

    public function createOrder() {
        $this->requireAuth();
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validation des données
        $required = ['station_id', 'produit_id', 'quantite', 'methode_paiement', 'telephone_paiement', 'adresse_livraison', 'latitude_livraison', 'longitude_livraison'];
        
        foreach ($required as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                $this->json(['success' => false, 'message' => "Champ requis: {$field}"], 400);
                return;
            }
        }

        // Vérifier le stock
        if (!$this->stockModel->checkAvailability($input['station_id'], $input['produit_id'], $input['quantite'])) {
            $this->json(['success' => false, 'message' => 'Stock insuffisant'], 400);
            return;
        }

        // Récupérer les informations nécessaires
        $product = $this->productModel->find($input['produit_id']);
        $station = $this->userModel->find($input['station_id']);
        $customer = $this->getCurrentUser();

        $orderData = array_merge($input, [
            'customer_id' => $_SESSION['user_id'],
            'prix_unitaire' => $product['prix_unitaire'],
            'customer_latitude' => $customer['latitude'],
            'customer_longitude' => $customer['longitude'],
            'station_latitude' => $station['latitude'],
            'station_longitude' => $station['longitude']
        ]);

        try {
            $orderId = $this->orderModel->createOrder($orderData);
            
            // Décrémenter le stock
            $this->stockModel->decreaseStock($input['station_id'], $input['produit_id'], $input['quantite']);
            
            // Simuler le processus de paiement
            $paymentResult = $this->processPayment($input['methode_paiement'], $input['telephone_paiement'], $orderData['total']);
            
            if ($paymentResult['success']) {
                $this->orderModel->update($orderId, ['statut_paiement' => 'paid']);
                $this->json(['success' => true, 'order_id' => $orderId, 'message' => 'Commande créée avec succès']);
            } else {
                $this->orderModel->update($orderId, ['statut_paiement' => 'failed']);
                $this->json(['success' => false, 'message' => 'Échec du paiement'], 400);
            }
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Erreur lors de la création de la commande'], 500);
        }
    }

    public function orderStatus($orderId) {
        $order = $this->orderModel->find($orderId);
        
        if (!$order) {
            $this->json(['error' => 'Commande non trouvée'], 404);
            return;
        }

        $this->json(['order' => $order]);
    }

    private function processPayment($method, $telephone, $amount) {
        // Simulation du processus de paiement
        // En production, intégrer les APIs Orange Money et MTN Money
        
        $success = rand(0, 1); // Simulation aléatoire pour la démo
        
        return [
            'success' => $success,
            'transaction_id' => $success ? 'TXN_' . time() . '_' . rand(1000, 9999) : null,
            'message' => $success ? 'Paiement effectué avec succès' : 'Échec du paiement'
        ];
    }
}
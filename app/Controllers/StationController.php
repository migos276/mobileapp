<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Station;
use App\Models\Stock;
use App\Models\Order;
use App\Models\Product;

class StationController extends Controller {
    private $stationModel;
    private $stockModel;
    private $orderModel;
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->requireRole('station');
        $this->stationModel = new Station();
        $this->stockModel = new Stock();
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    public function dashboard() {
        $station = $this->stationModel->getStationByUserId($_SESSION['user_id']);
        $orders = $this->orderModel->getStationOrders($station['id']);
        $stocks = $this->stockModel->getStationStock($station['id']);
        
        $stats = [
            'total_orders' => count($orders),
            'pending_orders' => count(array_filter($orders, fn($o) => in_array($o['statut'], ['pending', 'confirmed']))),
            'delivered_orders' => count(array_filter($orders, fn($o) => $o['statut'] === 'delivered')),
            'total_revenue' => array_sum(array_map(fn($o) => $o['total'] - $o['commission_admin'], $orders)),
            'products_in_stock' => count(array_filter($stocks, fn($s) => $s['quantite'] > 0))
        ];

        $this->view('station/dashboard', [
            'station' => $station,
            'orders' => array_slice($orders, 0, 10),
            'stocks' => $stocks,
            'stats' => $stats
        ]);
    }

    public function orders() {
        $station = $this->stationModel->getStationByUserId($_SESSION['user_id']);
        $orders = $this->orderModel->getStationOrders($station['id']);
        
        $this->view('station/orders', ['orders' => $orders]);
    }

    public function stock() {
        $station = $this->stationModel->getStationByUserId($_SESSION['user_id']);
        $stocks = $this->stockModel->getStationStock($station['id']);
        $products = $this->productModel->getActiveProducts();
        
        $this->view('station/stock', [
            'stocks' => $stocks,
            'products' => $products,
            'station' => $station
        ]);
    }

    public function updateStock() {
        $station = $this->stationModel->getStationByUserId($_SESSION['user_id']);
        $produitId = $_POST['produit_id'] ?? null;
        $quantite = $_POST['quantite'] ?? 0;
        $prixStation = $_POST['prix_station'] ?? null;

        if ($produitId && $quantite >= 0) {
            if ($this->stockModel->updateStock($station['id'], $produitId, $quantite, $prixStation)) {
                $this->json(['success' => true, 'message' => 'Stock mis à jour']);
            } else {
                $this->json(['success' => false, 'message' => 'Erreur lors de la mise à jour'], 500);
            }
        } else {
            $this->json(['success' => false, 'message' => 'Données invalides'], 400);
        }
    }

    public function updateOrder($orderId) {
        $station = $this->stationModel->getStationByUserId($_SESSION['user_id']);
        $newStatus = $_POST['status'] ?? '';
        
        // Vérifier que la commande appartient à cette station
        $order = $this->orderModel->find($orderId);
        if (!$order || $order['station_id'] != $station['id']) {
            $this->json(['success' => false, 'message' => 'Commande non trouvée'], 404);
            return;
        }

        if ($this->orderModel->updateStatus($orderId, $newStatus)) {
            $this->json(['success' => true, 'message' => 'Statut mis à jour']);
        } else {
            $this->json(['success' => false, 'message' => 'Erreur lors de la mise à jour'], 500);
        }
    }
}
<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Station;
use App\Models\Order;
use App\Models\Product;

class AdminController extends Controller {
    private $userModel;
    private $stationModel;
    private $orderModel;
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->requireRole('admin');
        $this->userModel = new User();
        $this->stationModel = new Station();
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    public function dashboard() {
        // Statistiques générales
        $totalUsers = count($this->userModel->where(['role' => 'customer']));
        $totalStations = count($this->userModel->where(['role' => 'station', 'statut' => 'approved']));
        $pendingStations = count($this->userModel->where(['role' => 'station', 'statut' => 'pending']));
        $totalOrders = count($this->orderModel->all());
        
        // Commandes récentes
        $recentOrders = $this->db->query("
            SELECT c.*, p.nom as produit_nom, u1.nom as customer_nom, u2.nom as station_nom
            FROM commandes c
            JOIN produits p ON c.produit_id = p.id
            JOIN users u1 ON c.customer_id = u1.id
            JOIN stations s ON c.station_id = s.id
            JOIN users u2 ON s.user_id = u2.id
            ORDER BY c.date_commande DESC
            LIMIT 10
        ")->fetchAll();

        // Revenus totaux (commissions)
        $totalRevenue = $this->db->query("SELECT SUM(commission_admin) as total FROM commandes WHERE statut = 'delivered'")->fetch()['total'] ?? 0;

        $stats = [
            'total_users' => $totalUsers,
            'total_stations' => $totalStations,
            'pending_stations' => $pendingStations,
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue
        ];

        $this->view('admin/dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders
        ]);
    }

    public function stations() {
        $pendingStations = $this->userModel->getPendingStations();
        $approvedStations = $this->stationModel->getAllStationsWithUsers();
        
        $this->view('admin/stations', [
            'pendingStations' => $pendingStations,
            'approvedStations' => array_filter($approvedStations, fn($s) => $s['statut'] === 'approved')
        ]);
    }

    public function approveStation($stationId) {
        $action = $_POST['action'] ?? '';

        if ($action === 'approve') {
            $this->userModel->approveUser($stationId);
            $_SESSION['message'] = 'Station approuvée avec succès';
        } elseif ($action === 'reject') {
            $this->userModel->rejectUser($stationId);
            $_SESSION['message'] = 'Station rejetée';
        } else {
            $_SESSION['error'] = 'Action invalide';
        }

        header('Location: /admin/stations');
        exit;
    }

    public function orders() {
        $orders = $this->db->query("
            SELECT c.*, p.nom as produit_nom, u1.nom as customer_nom, u2.nom as station_nom, s.nom_entreprise
            FROM commandes c
            JOIN produits p ON c.produit_id = p.id
            JOIN users u1 ON c.customer_id = u1.id
            JOIN stations s ON c.station_id = s.id
            JOIN users u2 ON s.user_id = u2.id
            ORDER BY c.date_commande DESC
        ")->fetchAll();

        $this->view('admin/orders', ['orders' => $orders]);
    }

    public function analytics() {
        // Données pour les graphiques
        $monthlyOrders = $this->db->query("
            SELECT strftime('%Y-%m', date_commande) as month, COUNT(*) as count
            FROM commandes
            GROUP BY strftime('%Y-%m', date_commande)
            ORDER BY month DESC
            LIMIT 12
        ")->fetchAll();

        $monthlyRevenue = $this->db->query("
            SELECT strftime('%Y-%m', date_commande) as month, SUM(commission_admin) as revenue
            FROM commandes
            WHERE statut = 'delivered'
            GROUP BY strftime('%Y-%m', date_commande)
            ORDER BY month DESC
            LIMIT 12
        ")->fetchAll();

        $topProducts = $this->db->query("
            SELECT p.nom, SUM(c.quantite) as total_vendu
            FROM commandes c
            JOIN produits p ON c.produit_id = p.id
            WHERE c.statut = 'delivered'
            GROUP BY p.id, p.nom
            ORDER BY total_vendu DESC
            LIMIT 5
        ")->fetchAll();

        $this->view('admin/analytics', [
            'monthlyOrders' => $monthlyOrders,
            'monthlyRevenue' => $monthlyRevenue,
            'topProducts' => $topProducts
        ]);
    }
}
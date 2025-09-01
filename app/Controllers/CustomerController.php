<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;

class CustomerController extends Controller {
    private $userModel;
    private $orderModel;
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->requireRole('customer');
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    public function dashboard() {
        $user = $this->getCurrentUser();
        $orders = $this->orderModel->getCustomerOrders($_SESSION['user_id']);
        
        $stats = [
            'total_orders' => count($orders),
            'delivered_orders' => count(array_filter($orders, fn($o) => $o['statut'] === 'delivered')),
            'pending_orders' => count(array_filter($orders, fn($o) => in_array($o['statut'], ['pending', 'confirmed', 'preparing', 'delivering']))),
            'total_spent' => array_sum(array_column($orders, 'total'))
        ];

        $this->view('customer/dashboard', [
            'user' => $user,
            'orders' => array_slice($orders, 0, 5),
            'stats' => $stats
        ]);
    }

    public function orders() {
        $orders = $this->orderModel->getCustomerOrders($_SESSION['user_id']);
        $this->view('customer/orders', ['orders' => $orders]);
    }

    public function profile() {
        $user = $this->getCurrentUser();
        $this->view('customer/profile', ['user' => $user]);
    }

    public function updateProfile() {
        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'telephone' => trim($_POST['telephone'] ?? ''),
            'adresse' => trim($_POST['adresse'] ?? '')
        ];

        if (!empty($_POST['password'])) {
            if ($_POST['password'] === $_POST['confirm_password']) {
                $data['mot_de_passe'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } else {
                $this->view('customer/profile', [
                    'user' => $this->getCurrentUser(),
                    'error' => 'Les mots de passe ne correspondent pas'
                ]);
                return;
            }
        }

        if ($this->userModel->update($_SESSION['user_id'], $data)) {
            $_SESSION['user_name'] = $data['nom'];
            $this->view('customer/profile', [
                'user' => $this->getCurrentUser(),
                'success' => 'Profil mis à jour avec succès'
            ]);
        } else {
            $this->view('customer/profile', [
                'user' => $this->getCurrentUser(),
                'error' => 'Erreur lors de la mise à jour'
            ]);
        }
    }
}
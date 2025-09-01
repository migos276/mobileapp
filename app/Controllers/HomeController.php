<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    
    public function index() {
        // Vérifier si l'utilisateur est connecté et rediriger
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
            $role = $_SESSION['user_role'];
            switch ($role) {
                case 'admin':
                    $this->redirect('/admin/dashboard');
                    break;
                case 'station':
                    $this->redirect('/station/dashboard');
                    break;
                case 'customer':
                    $this->redirect('/customer/dashboard');
                    break;
            }
        } elseif (isset($_SESSION['user_id']) && !isset($_SESSION['user_role'])) {
            // Session inconsistante, nettoyer et rediriger vers login
            session_destroy();
            $this->redirect('/auth/login');
        }

        $this->view('home/index');
    }
}
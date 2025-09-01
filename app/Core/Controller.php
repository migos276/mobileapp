<?php
namespace App\Core;

use Exception;

class Controller {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function view($view, $data = []) {
        extract($data);
        
        $viewFile = "../app/Views/{$view}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("Vue non trouvée: {$view}");
        }
    }

    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    protected function requireRole($role) {
        $this->requireAuth();
        if ($_SESSION['user_role'] !== $role) {
            $this->redirect('/');
        }
    }

    protected function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
}

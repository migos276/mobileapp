<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Station;
use Exception;

use App\Core\Controller;

class AuthController extends Controller {
    private $userModel;
    private $stationModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->stationModel = new Station();
    }

    public function login() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        $this->view('auth/login');
    }

    public function processLogin() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $this->view('auth/login', ['error' => 'Veuillez remplir tous les champs']);
            return;
        }

        $user = $this->userModel->verifyPassword($email, $password);
        
        if ($user) {
            if ($user['statut'] === 'approved' || $user['role'] === 'customer') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                switch ($user['role']) {
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
            } else {
                $this->view('auth/login', [
                    'error' => 'Votre compte est en attente d\'approbation',
                    'email' => $email
                ]);
            }
        } else {
            $this->view('auth/login', [
                'error' => 'Email ou mot de passe incorrect',
                'email' => $email
            ]);
        }
    }

    public function register() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        $userType = $_GET['type'] ?? 'customer';
        $this->view('auth/register', ['userType' => $userType]);
    }

    public function processRegister() {
        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telephone' => trim($_POST['telephone'] ?? ''),
            'mot_de_passe' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? 'customer',
            'adresse' => trim($_POST['adresse'] ?? ''),
            'latitude' => $_POST['latitude'] ?? null,
            'longitude' => $_POST['longitude'] ?? null
        ];

        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        $errors = $this->validateRegistration($data, $confirmPassword);
        
        if (!empty($errors)) {
            $this->view('auth/register', [
                'error' => implode('<br>', $errors),
                'userType' => $data['role'],
                'formData' => $data
            ]);
            return;
        }

        // Vérifier si l'email existe déjà
        if ($this->userModel->findByEmail($data['email'])) {
            $this->view('auth/register', [
                'error' => 'Cette adresse email est déjà utilisée',
                'userType' => $data['role'],
                'formData' => $data
            ]);
            return;
        }

        try {
            $userId = $this->userModel->createUser($data);
            
            // Si c'est une station, créer l'entrée station
            if ($data['role'] === 'station') {
                $stationData = [
                    'user_id' => $userId,
                    'nom_entreprise' => trim($_POST['nom_entreprise'] ?? ''),
                    'numero_licence' => trim($_POST['numero_licence'] ?? ''),
                    'description' => trim($_POST['description'] ?? ''),
                    'heures_ouverture' => trim($_POST['heures_ouverture'] ?? '')
                ];
                
                $this->stationModel->createStation($stationData);
            }

            $message = 'Inscription réussie ! ';
            if ($data['role'] === 'customer') {
                $message .= 'Vous pouvez maintenant vous connecter.';
            } else {
                $message .= 'Votre compte est en attente d\'approbation par l\'administrateur.';
            }

            $this->view('auth/register', [
                'success' => $message,
                'userType' => $data['role']
            ]);
            
        } catch (Exception $e) {
            $this->view('auth/register', [
                'error' => 'Erreur lors de l\'inscription: ' . $e->getMessage(),
                'userType' => $data['role'],
                'formData' => $data
            ]);
        }
    }

    public function logout() {
        // Clear all session variables
        $_SESSION = [];

        // Delete the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();

        // Redirect to home page with direct header
        header('Location: /');
        exit;
    }

    private function validateRegistration($data, $confirmPassword) {
        $errors = [];

        if (empty($data['nom'])) $errors[] = 'Le nom est requis';
        if (empty($data['email'])) $errors[] = 'L\'email est requis';
        if (empty($data['telephone'])) $errors[] = 'Le téléphone est requis';
        if (empty($data['mot_de_passe'])) $errors[] = 'Le mot de passe est requis';
        if (empty($data['adresse'])) $errors[] = 'L\'adresse est requise';
        
        if ($data['mot_de_passe'] !== $confirmPassword) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }
        
        if (strlen($data['mot_de_passe']) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide';
        }
        
        if (!$data['latitude'] || !$data['longitude']) {
            $errors[] = 'La géolocalisation est requise';
        }

        return $errors;
    }
}
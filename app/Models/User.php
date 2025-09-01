<?php
namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected $table = 'users';

    public function findByEmail($email) {
        return $this->findBy('email', $email);
    }

    public function createUser($data) {
        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        $data['date_creation'] = date('Y-m-d H:i:s');
        
        if ($data['role'] !== 'admin') {
            $data['statut'] = 'pending';
        } else {
            $data['statut'] = 'approved';
        }
        
        return $this->create($data);
    }

    public function verifyPassword($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            return $user;
        }
        return false;
    }

    public function getNearbyStations($latitude, $longitude, $maxDistance = 20) {
        $sql = "SELECT s.*, u.nom, u.telephone, u.adresse, u.latitude, u.longitude,
                       (6371 * acos(cos(radians(?)) * cos(radians(u.latitude)) * 
                       cos(radians(u.longitude) - radians(?)) + sin(radians(?)) * 
                       sin(radians(u.latitude)))) AS distance
                FROM stations s 
                JOIN users u ON s.user_id = u.id 
                WHERE u.statut = 'approved' AND s.statut_ouverture = 1
                HAVING distance <= ?
                ORDER BY distance";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$latitude, $longitude, $latitude, $maxDistance]);
        return $stmt->fetchAll();
    }

    public function getPendingStations() {
        $sql = "SELECT u.*, s.nom_entreprise, s.numero_licence, s.description 
                FROM users u 
                LEFT JOIN stations s ON u.id = s.user_id 
                WHERE u.role = 'station' AND u.statut = 'pending'
                ORDER BY u.date_creation DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function approveUser($id) {
        return $this->update($id, ['statut' => 'approved']);
    }

    public function rejectUser($id) {
        return $this->update($id, ['statut' => 'rejected']);
    }
}
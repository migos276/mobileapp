<?php
namespace App\Models;

use App\Core\Model;

class Station extends Model {
    protected $table = 'stations';

    public function createStation($data) {
        $data['commission_rate'] = 5.00;
        return $this->create($data);
    }

    public function getStationByUserId($userId) {
        return $this->findBy('user_id', $userId);
    }

    public function getStationWithUser($stationId) {
        $sql = "SELECT s.*, u.nom, u.email, u.telephone, u.adresse, u.latitude, u.longitude
                FROM stations s
                JOIN users u ON s.user_id = u.id
                WHERE s.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$stationId]);
        return $stmt->fetch();
    }

    public function getAllStationsWithUsers() {
        $sql = "SELECT s.*, u.nom, u.email, u.telephone, u.statut, u.date_creation
                FROM stations s
                JOIN users u ON s.user_id = u.id
                ORDER BY u.date_creation DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function updateStatus($stationId, $status) {
        return $this->update($stationId, ['statut_ouverture' => $status]);
    }
}
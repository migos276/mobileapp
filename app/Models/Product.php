<?php
namespace App\Models;

use App\Core\Model;

class Product extends Model {
    protected $table = 'produits';

    public function getActiveProducts() {
        return $this->where(['actif' => 1]);
    }

    public function createProduct($data) {
        $data['actif'] = 1;
        return $this->create($data);
    }

    public function toggleStatus($id) {
        $product = $this->find($id);
        if ($product) {
            return $this->update($id, ['actif' => !$product['actif']]);
        }
        return false;
    }
}
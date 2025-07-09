<?php
/**
 * Router Model
 * StarRent.vip - Starlink Router Rental Platform
 */

class Router {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT * FROM routers WHERE status = 1";
        $params = [];
        
        if (!empty($filters['availability'])) {
            $sql .= " AND availability_status = :availability";
            $params['availability'] = $filters['availability'];
        }
        
        if (!empty($filters['featured'])) {
            $sql .= " AND featured = 1";
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE :search OR model LIKE :search OR description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY featured DESC, created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . intval($filters['limit']);
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM routers WHERE id = :id AND status = 1";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function getFeatured($limit = 6) {
        return $this->getAll(['featured' => true, 'limit' => $limit]);
    }
    
    public function getAvailable($startDate, $endDate, $excludeRentalId = null) {
        $sql = "SELECT r.* FROM routers r 
                WHERE r.status = 1 
                AND r.availability_status = 'available'
                AND r.available_units > 0
                AND r.id NOT IN (
                    SELECT DISTINCT router_id FROM rentals 
                    WHERE rental_status IN ('confirmed', 'active')
                    AND (
                        (start_date <= :start_date AND end_date >= :start_date)
                        OR (start_date <= :end_date AND end_date >= :end_date)
                        OR (start_date >= :start_date AND end_date <= :end_date)
                    )";
        
        $params = [
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        if ($excludeRentalId) {
            $sql .= " AND id != :exclude_rental_id";
            $params['exclude_rental_id'] = $excludeRentalId;
        }
        
        $sql .= ") ORDER BY featured DESC, name ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('routers', $data);
    }
    
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('routers', $data, 'id = :id', ['id' => $id]);
    }
    
    public function delete($id) {
        return $this->db->update('routers', ['status' => 0], 'id = :id', ['id' => $id]);
    }
    
    public function updateAvailability($id, $status) {
        return $this->db->update('routers', 
            ['availability_status' => $status, 'updated_at' => date('Y-m-d H:i:s')], 
            'id = :id', 
            ['id' => $id]
        );
    }
    
    public function updateRating($id) {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                FROM reviews 
                WHERE router_id = :id AND status = 1";
        $result = $this->db->fetchOne($sql, ['id' => $id]);
        
        if ($result) {
            $this->db->update('routers', [
                'rating' => round($result['avg_rating'], 2),
                'total_reviews' => $result['total_reviews'],
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = :id', ['id' => $id]);
        }
    }
    
    public function calculateRentalCost($routerId, $rentalType, $days) {
        $router = $this->getById($routerId);
        if (!$router) return false;
        
        $dailyRate = $router['daily_rate'];
        $weeklyRate = $router['weekly_rate'];
        $monthlyRate = $router['monthly_rate'];
        
        switch ($rentalType) {
            case 'daily':
                return $dailyRate * $days;
            case 'weekly':
                $weeks = ceil($days / 7);
                return $weeklyRate * $weeks;
            case 'monthly':
                $months = ceil($days / 30);
                return $monthlyRate * $months;
            default:
                return $dailyRate * $days;
        }
    }
}
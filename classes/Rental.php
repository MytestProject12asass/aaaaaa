<?php
/**
 * Rental Model
 * StarRent.vip - Starlink Router Rental Platform
 */

class Rental {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $data['rental_number'] = $this->generateRentalNumber();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('rentals', $data);
    }
    
    public function getById($id) {
        $sql = "SELECT r.*, rt.name as router_name, rt.model as router_model, rt.photo as router_photo,
                       u.name as user_name, u.email as user_email, u.phone as user_phone
                FROM rentals r 
                JOIN routers rt ON r.router_id = rt.id 
                JOIN users u ON r.user_id = u.id 
                WHERE r.id = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function getByRentalNumber($rentalNumber) {
        $sql = "SELECT r.*, rt.name as router_name, rt.model as router_model, rt.photo as router_photo,
                       u.name as user_name, u.email as user_email, u.phone as user_phone
                FROM rentals r 
                JOIN routers rt ON r.router_id = rt.id 
                JOIN users u ON r.user_id = u.id 
                WHERE r.rental_number = :rental_number";
        return $this->db->fetchOne($sql, ['rental_number' => $rentalNumber]);
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT r.*, rt.name as router_name, rt.model as router_model,
                       u.name as user_name, u.email as user_email
                FROM rentals r 
                JOIN routers rt ON r.router_id = rt.id 
                JOIN users u ON r.user_id = u.id 
                WHERE 1=1";
        $params = [];
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND r.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }
        
        if (!empty($filters['router_id'])) {
            $sql .= " AND r.router_id = :router_id";
            $params['router_id'] = $filters['router_id'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND r.rental_status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['payment_status'])) {
            $sql .= " AND r.payment_status = :payment_status";
            $params['payment_status'] = $filters['payment_status'];
        }
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND r.start_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND r.end_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }
        
        $sql .= " ORDER BY r.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . intval($filters['limit']);
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('rentals', $data, 'id = :id', ['id' => $id]);
    }
    
    public function updateStatus($id, $status) {
        return $this->update($id, ['rental_status' => $status]);
    }
    
    public function updatePaymentStatus($id, $status) {
        return $this->update($id, ['payment_status' => $status]);
    }
    
    public function getActiveRentals() {
        return $this->getAll(['status' => 'active']);
    }
    
    public function getOverdueRentals() {
        $sql = "SELECT r.*, rt.name as router_name, u.name as user_name, u.email as user_email
                FROM rentals r 
                JOIN routers rt ON r.router_id = rt.id 
                JOIN users u ON r.user_id = u.id 
                WHERE r.rental_status = 'active' 
                AND r.end_date < CURDATE()";
        return $this->db->fetchAll($sql);
    }
    
    public function getPendingRentals() {
        return $this->getAll(['status' => 'pending']);
    }
    
    public function calculateLateFee($rentalId) {
        $rental = $this->getById($rentalId);
        if (!$rental) return 0;
        
        $endDate = new DateTime($rental['end_date']);
        $currentDate = new DateTime();
        
        if ($currentDate <= $endDate) return 0;
        
        $daysLate = $currentDate->diff($endDate)->days;
        $lateFeePercentage = LATE_FEE_PERCENTAGE / 100;
        
        return $rental['total_cost'] * $lateFeePercentage * $daysLate;
    }
    
    public function extendRental($rentalId, $newEndDate, $additionalCost) {
        $rental = $this->getById($rentalId);
        if (!$rental) return false;
        
        $startDate = new DateTime($rental['start_date']);
        $endDate = new DateTime($newEndDate);
        $totalDays = $endDate->diff($startDate)->days + 1;
        
        return $this->update($rentalId, [
            'end_date' => $newEndDate,
            'total_days' => $totalDays,
            'total_cost' => $rental['total_cost'] + $additionalCost
        ]);
    }
    
    public function checkAvailability($routerId, $startDate, $endDate, $excludeRentalId = null) {
        $sql = "SELECT COUNT(*) as count FROM rentals 
                WHERE router_id = :router_id 
                AND rental_status IN ('confirmed', 'active')
                AND (
                    (start_date <= :start_date AND end_date >= :start_date)
                    OR (start_date <= :end_date AND end_date >= :end_date)
                    OR (start_date >= :start_date AND end_date <= :end_date)
                )";
        
        $params = [
            'router_id' => $routerId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        if ($excludeRentalId) {
            $sql .= " AND id != :exclude_rental_id";
            $params['exclude_rental_id'] = $excludeRentalId;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] == 0;
    }
    
    private function generateRentalNumber() {
        do {
            $number = 'RNT' . date('Ymd') . mt_rand(1000, 9999);
            $exists = $this->db->fetchOne(
                "SELECT id FROM rentals WHERE rental_number = :number", 
                ['number' => $number]
            );
        } while ($exists);
        
        return $number;
    }
    
    public function getStats() {
        $stats = [];
        
        // Total rentals
        $result = $this->db->fetchOne("SELECT COUNT(*) as count FROM rentals");
        $stats['total_rentals'] = $result['count'];
        
        // Active rentals
        $result = $this->db->fetchOne("SELECT COUNT(*) as count FROM rentals WHERE rental_status = 'active'");
        $stats['active_rentals'] = $result['count'];
        
        // Pending rentals
        $result = $this->db->fetchOne("SELECT COUNT(*) as count FROM rentals WHERE rental_status = 'pending'");
        $stats['pending_rentals'] = $result['count'];
        
        // Overdue rentals
        $result = $this->db->fetchOne("SELECT COUNT(*) as count FROM rentals WHERE rental_status = 'active' AND end_date < CURDATE()");
        $stats['overdue_rentals'] = $result['count'];
        
        // Total revenue
        $result = $this->db->fetchOne("SELECT SUM(total_cost) as revenue FROM rentals WHERE payment_status = 'paid'");
        $stats['total_revenue'] = $result['revenue'] ?: 0;
        
        // This month revenue
        $result = $this->db->fetchOne("SELECT SUM(total_cost) as revenue FROM rentals WHERE payment_status = 'paid' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $stats['monthly_revenue'] = $result['revenue'] ?: 0;
        
        return $stats;
    }
}
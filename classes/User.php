<?php
/**
 * User Model
 * StarRent.vip - Starlink Router Rental Platform
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], HASH_ALGO);
        }
        
        // Generate affiliate code
        $data['affilate_code'] = $this->generateAffiliateCode();
        
        // Generate verification link if email verification is enabled
        $data['verification_link'] = bin2hex(random_bytes(32));
        
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('users', $data);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM users WHERE id = :id AND is_banned = 0";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function getByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email AND is_banned = 0";
        return $this->db->fetchOne($sql, ['email' => $email]);
    }
    
    public function getByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username AND is_banned = 0";
        return $this->db->fetchOne($sql, ['username' => $username]);
    }
    
    public function authenticate($email, $password) {
        $user = $this->getByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->db->update('users', 
                ['updated_at' => date('Y-m-d H:i:s')], 
                'id = :id', 
                ['id' => $user['id']]
            );
            return $user;
        }
        
        return false;
    }
    
    public function update($id, $data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], HASH_ALGO);
        }
        
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('users', $data, 'id = :id', ['id' => $id]);
    }
    
    public function verifyEmail($verificationLink) {
        $sql = "SELECT id FROM users WHERE verification_link = :link";
        $user = $this->db->fetchOne($sql, ['link' => $verificationLink]);
        
        if ($user) {
            return $this->db->update('users', [
                'email_verified' => 'Yes',
                'verification_link' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = :id', ['id' => $user['id']]);
        }
        
        return false;
    }
    
    public function updateBalance($userId, $amount, $type = 'add') {
        $user = $this->getById($userId);
        if (!$user) return false;
        
        $newBalance = $type === 'add' 
            ? $user['balance'] + $amount 
            : $user['balance'] - $amount;
        
        if ($newBalance < 0) return false;
        
        return $this->db->update('users', 
            ['balance' => $newBalance, 'updated_at' => date('Y-m-d H:i:s')], 
            'id = :id', 
            ['id' => $userId]
        );
    }
    
    public function getRentals($userId, $limit = null) {
        $sql = "SELECT r.*, rt.name as router_name, rt.model as router_model 
                FROM rentals r 
                JOIN routers rt ON r.router_id = rt.id 
                WHERE r.user_id = :user_id 
                ORDER BY r.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }
    
    public function getTransactions($userId, $limit = null) {
        $sql = "SELECT * FROM transactions 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }
    
    public function getSupportTickets($userId, $limit = null) {
        $sql = "SELECT * FROM support_tickets 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }
    
    private function generateAffiliateCode() {
        do {
            $code = substr(md5(uniqid(mt_rand(), true)), 0, 8);
            $exists = $this->db->fetchOne(
                "SELECT id FROM users WHERE affilate_code = :code", 
                ['code' => $code]
            );
        } while ($exists);
        
        return $code;
    }
    
    public function generateUsername($name) {
        $username = strtolower(str_replace(' ', '', $name));
        $username = preg_replace('/[^a-z0-9]/', '', $username);
        
        $counter = 1;
        $originalUsername = $username;
        
        while ($this->getByUsername($username)) {
            $username = $originalUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
}
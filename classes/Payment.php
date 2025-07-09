<?php
/**
 * Payment Model
 * StarRent.vip - Starlink Router Rental Platform
 */

class Payment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $data['payment_number'] = $this->generatePaymentNumber();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('payments', $data);
    }
    
    public function getById($id) {
        $sql = "SELECT p.*, r.rental_number, u.name as user_name, u.email as user_email
                FROM payments p 
                LEFT JOIN rentals r ON p.rental_id = r.id 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.id = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function getByPaymentNumber($paymentNumber) {
        return $this->getById($this->getIdByPaymentNumber($paymentNumber));
    }
    
    public function getByGatewayTransactionId($transactionId) {
        $sql = "SELECT * FROM payments WHERE gateway_transaction_id = :transaction_id";
        return $this->db->fetchOne($sql, ['transaction_id' => $transactionId]);
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT p.*, r.rental_number, u.name as user_name, u.email as user_email
                FROM payments p 
                LEFT JOIN rentals r ON p.rental_id = r.id 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE 1=1";
        $params = [];
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND p.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }
        
        if (!empty($filters['rental_id'])) {
            $sql .= " AND p.rental_id = :rental_id";
            $params['rental_id'] = $filters['rental_id'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['gateway'])) {
            $sql .= " AND p.gateway = :gateway";
            $params['gateway'] = $filters['gateway'];
        }
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(p.created_at) >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(p.created_at) <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . intval($filters['limit']);
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('payments', $data, 'id = :id', ['id' => $id]);
    }
    
    public function updateStatus($id, $status, $gatewayResponse = null) {
        $data = ['status' => $status];
        if ($gatewayResponse) {
            $data['gateway_response'] = json_encode($gatewayResponse);
        }
        return $this->update($id, $data);
    }
    
    public function markAsCompleted($id, $transactionId = null, $gatewayResponse = null) {
        $data = ['status' => 'completed'];
        
        if ($transactionId) {
            $data['gateway_transaction_id'] = $transactionId;
        }
        
        if ($gatewayResponse) {
            $data['gateway_response'] = json_encode($gatewayResponse);
        }
        
        return $this->update($id, $data);
    }
    
    public function markAsFailed($id, $gatewayResponse = null) {
        return $this->updateStatus($id, 'failed', $gatewayResponse);
    }
    
    public function createPlisioPayment($amount, $currency, $orderNumber, $callbackUrl) {
        $plisioApi = new PlisioAPI();
        
        $params = [
            'source_currency' => $currency,
            'source_amount' => $amount,
            'order_number' => $orderNumber,
            'callback_url' => $callbackUrl,
            'success_callback_url' => APP_URL . '/payment/success',
            'fail_callback_url' => APP_URL . '/payment/failed',
            'cancel_callback_url' => APP_URL . '/payment/cancelled'
        ];
        
        return $plisioApi->createInvoice($params);
    }
    
    public function processPlisioCallback($data) {
        // Verify the callback signature
        if (!$this->verifyPlisioCallback($data)) {
            return false;
        }
        
        $payment = $this->getByGatewayTransactionId($data['txn_id']);
        if (!$payment) {
            return false;
        }
        
        switch ($data['status']) {
            case 'completed':
                $this->markAsCompleted($payment['id'], $data['txn_id'], $data);
                $this->processSuccessfulPayment($payment['id']);
                break;
            case 'cancelled':
            case 'failed':
                $this->markAsFailed($payment['id'], $data);
                break;
        }
        
        return true;
    }
    
    private function verifyPlisioCallback($data) {
        $receivedSign = $data['verify_hash'] ?? '';
        unset($data['verify_hash']);
        
        ksort($data);
        $postData = json_encode($data, JSON_UNESCAPED_UNICODE);
        $verifyHash = hash_hmac('sha1', $postData, PLISIO_SECRET_KEY);
        
        return hash_equals($verifyHash, $receivedSign);
    }
    
    private function processSuccessfulPayment($paymentId) {
        $payment = $this->getById($paymentId);
        if (!$payment || !$payment['rental_id']) {
            return false;
        }
        
        // Update rental payment status
        $rental = new Rental();
        $rental->updatePaymentStatus($payment['rental_id'], 'paid');
        
        // Create transaction record
        $transaction = new Transaction();
        $transaction->create([
            'user_id' => $payment['user_id'],
            'rental_id' => $payment['rental_id'],
            'payment_id' => $paymentId,
            'email' => $payment['user_email'],
            'amount' => $payment['amount'],
            'type' => 'Rental Payment',
            'status' => 'completed',
            'description' => 'Payment for rental #' . $payment['rental_number'],
            'txnid' => $payment['gateway_transaction_id']
        ]);
        
        // Send confirmation email
        $this->sendPaymentConfirmationEmail($payment);
        
        return true;
    }
    
    private function sendPaymentConfirmationEmail($payment) {
        // Implementation for sending email confirmation
        // You can use PHPMailer or similar library
    }
    
    private function generatePaymentNumber() {
        do {
            $number = 'PAY' . date('Ymd') . mt_rand(1000, 9999);
            $exists = $this->db->fetchOne(
                "SELECT id FROM payments WHERE payment_number = :number", 
                ['number' => $number]
            );
        } while ($exists);
        
        return $number;
    }
    
    private function getIdByPaymentNumber($paymentNumber) {
        $result = $this->db->fetchOne(
            "SELECT id FROM payments WHERE payment_number = :number", 
            ['number' => $paymentNumber]
        );
        return $result ? $result['id'] : null;
    }
    
    public function getStats() {
        $stats = [];
        
        // Total payments
        $result = $this->db->fetchOne("SELECT COUNT(*) as count, SUM(amount) as total FROM payments");
        $stats['total_payments'] = $result['count'];
        $stats['total_amount'] = $result['total'] ?: 0;
        
        // Completed payments
        $result = $this->db->fetchOne("SELECT COUNT(*) as count, SUM(amount) as total FROM payments WHERE status = 'completed'");
        $stats['completed_payments'] = $result['count'];
        $stats['completed_amount'] = $result['total'] ?: 0;
        
        // Pending payments
        $result = $this->db->fetchOne("SELECT COUNT(*) as count FROM payments WHERE status = 'pending'");
        $stats['pending_payments'] = $result['count'];
        
        // Failed payments
        $result = $this->db->fetchOne("SELECT COUNT(*) as count FROM payments WHERE status = 'failed'");
        $stats['failed_payments'] = $result['count'];
        
        return $stats;
    }
}
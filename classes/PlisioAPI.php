<?php
/**
 * Plisio API Integration
 * StarRent.vip - Starlink Router Rental Platform
 */

class PlisioAPI {
    private $apiKey;
    private $secretKey;
    private $baseUrl = 'https://plisio.net/api/v1/';
    
    public function __construct() {
        $this->apiKey = PLISIO_API_KEY;
        $this->secretKey = PLISIO_SECRET_KEY;
    }
    
    public function createInvoice($params) {
        $params['api_key'] = $this->apiKey;
        
        $response = $this->makeRequest('invoices/new', $params);
        
        if ($response && $response['status'] === 'success') {
            return $response['data'];
        }
        
        return false;
    }
    
    public function getInvoice($invoiceId) {
        $params = [
            'api_key' => $this->apiKey,
            'id' => $invoiceId
        ];
        
        $response = $this->makeRequest('invoices/' . $invoiceId, $params, 'GET');
        
        if ($response && $response['status'] === 'success') {
            return $response['data'];
        }
        
        return false;
    }
    
    public function getCurrencies() {
        $params = ['api_key' => $this->apiKey];
        
        $response = $this->makeRequest('currencies', $params, 'GET');
        
        if ($response && $response['status'] === 'success') {
            return $response['data'];
        }
        
        return [];
    }
    
    public function getBalance($currency = null) {
        $params = ['api_key' => $this->apiKey];
        
        if ($currency) {
            $params['psys_cid'] = $currency;
        }
        
        $response = $this->makeRequest('balances', $params, 'GET');
        
        if ($response && $response['status'] === 'success') {
            return $response['data'];
        }
        
        return [];
    }
    
    public function createWithdrawal($params) {
        $params['api_key'] = $this->apiKey;
        
        $response = $this->makeRequest('operations/withdraw', $params);
        
        if ($response && $response['status'] === 'success') {
            return $response['data'];
        }
        
        return false;
    }
    
    public function getOperations($params = []) {
        $params['api_key'] = $this->apiKey;
        
        $response = $this->makeRequest('operations', $params, 'GET');
        
        if ($response && $response['status'] === 'success') {
            return $response['data'];
        }
        
        return [];
    }
    
    public function verifyCallback($data) {
        $receivedSign = $data['verify_hash'] ?? '';
        unset($data['verify_hash']);
        
        ksort($data);
        $postData = json_encode($data, JSON_UNESCAPED_UNICODE);
        $verifyHash = hash_hmac('sha1', $postData, $this->secretKey);
        
        return hash_equals($verifyHash, $receivedSign);
    }
    
    private function makeRequest($endpoint, $params = [], $method = 'POST') {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init();
        
        if ($method === 'GET') {
            $url .= '?' . http_build_query($params);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'User-Agent: StarRent.vip/1.0'
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            error_log("Plisio API cURL error: " . $error);
            return false;
        }
        
        if ($httpCode !== 200) {
            error_log("Plisio API HTTP error: " . $httpCode);
            return false;
        }
        
        $decodedResponse = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Plisio API JSON decode error: " . json_last_error_msg());
            return false;
        }
        
        return $decodedResponse;
    }
    
    public function getSupportedCurrencies() {
        return [
            'BTC' => 'Bitcoin',
            'ETH' => 'Ethereum',
            'LTC' => 'Litecoin',
            'BCH' => 'Bitcoin Cash',
            'USDT' => 'Tether (USDT)',
            'USDC' => 'USD Coin',
            'SHIB' => 'Shiba Inu',
            'DOGE' => 'Dogecoin',
            'TRX' => 'TRON',
            'BNB' => 'Binance Coin',
            'ADA' => 'Cardano',
            'XRP' => 'Ripple',
            'DOT' => 'Polkadot',
            'UNI' => 'Uniswap',
            'LINK' => 'Chainlink',
            'MATIC' => 'Polygon',
            'SOL' => 'Solana',
            'AVAX' => 'Avalanche'
        ];
    }
    
    public function formatAmount($amount, $decimals = 8) {
        return number_format($amount, $decimals, '.', '');
    }
    
    public function convertCurrency($amount, $fromCurrency, $toCurrency) {
        $params = [
            'api_key' => $this->apiKey,
            'source_currency' => $fromCurrency,
            'source_amount' => $amount,
            'target_currency' => $toCurrency
        ];
        
        $response = $this->makeRequest('operations/convert', $params);
        
        if ($response && $response['status'] === 'success') {
            return $response['data']['target_amount'];
        }
        
        return false;
    }
}
<?php
/**
 * MongoDB Connection Class using REST API
 * This class provides MongoDB functionality without requiring the MongoDB PHP extension
 */

class MongoDBConnection {
    private $baseUrl;
    private $database;
    private $apiKey;
    
    public function __construct($host = 'localhost', $port = 27017, $database = 'schogms') {
        $this->baseUrl = "http://{$host}:{$port}";
        $this->database = $database;
    }
    
    /**
     * Get collection reference
     */
    public function collection($collectionName) {
        return new MongoDBCollection($this->baseUrl, $this->database, $collectionName);
    }
    
    /**
     * Test connection
     */
    public function testConnection() {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl . "/admin");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            return $httpCode === 200;
        } catch (Exception $e) {
            return false;
        }
    }
}

class MongoDBCollection {
    private $baseUrl;
    private $database;
    private $collection;
    
    public function __construct($baseUrl, $database, $collection) {
        $this->baseUrl = $baseUrl;
        $this->database = $database;
        $this->collection = $collection;
    }
    
    /**
     * Insert a document
     */
    public function insertOne($document) {
        $document['_id'] = $this->generateObjectId();
        if (!isset($document['created_at'])) {
            $document['created_at'] = new MongoDB_BSON_UTCDateTime();
        }
        if (!isset($document['updated_at'])) {
            $document['updated_at'] = new MongoDB_BSON_UTCDateTime();
        }
        
        return $this->makeRequest('POST', $document);
    }
    
    /**
     * Insert multiple documents
     */
    public function insertMany($documents) {
        $results = [];
        foreach ($documents as $document) {
            $results[] = $this->insertOne($document);
        }
        return $results;
    }
    
    /**
     * Find documents
     */
    public function find($filter = [], $options = []) {
        $query = [
            'filter' => $filter,
            'options' => $options
        ];
        
        return $this->makeRequest('GET', $query);
    }
    
    /**
     * Find one document
     */
    public function findOne($filter = []) {
        $results = $this->find($filter, ['limit' => 1]);
        return !empty($results) ? $results[0] : null;
    }
    
    /**
     * Update documents
     */
    public function updateOne($filter, $update, $options = []) {
        // Add updated_at timestamp
        if (isset($update['$set'])) {
            $update['$set']['updated_at'] = new MongoDB_BSON_UTCDateTime();
        } else {
            $update['updated_at'] = new MongoDB_BSON_UTCDateTime();
        }
        
        $query = [
            'filter' => $filter,
            'update' => $update,
            'options' => $options
        ];
        
        return $this->makeRequest('PUT', $query);
    }
    
    /**
     * Update multiple documents
     */
    public function updateMany($filter, $update, $options = []) {
        $query = [
            'filter' => $filter,
            'update' => $update,
            'options' => array_merge($options, ['multi' => true])
        ];
        
        return $this->makeRequest('PUT', $query);
    }
    
    /**
     * Delete documents
     */
    public function deleteOne($filter) {
        $query = [
            'filter' => $filter
        ];
        
        return $this->makeRequest('DELETE', $query);
    }
    
    /**
     * Delete multiple documents
     */
    public function deleteMany($filter) {
        $query = [
            'filter' => $filter,
            'options' => ['multi' => true]
        ];
        
        return $this->makeRequest('DELETE', $query);
    }
    
    /**
     * Count documents
     */
    public function count($filter = []) {
        $query = [
            'filter' => $filter
        ];
        
        $result = $this->makeRequest('GET', $query, '/count');
        return $result['count'] ?? 0;
    }
    
    /**
     * Make HTTP request to MongoDB REST API
     */
    private function makeRequest($method, $data = [], $endpoint = '') {
        $url = $this->baseUrl . "/{$this->database}/{$this->collection}" . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'GET':
                if (!empty($data)) {
                    $url .= '?' . http_build_query($data);
                    curl_setopt($ch, CURLOPT_URL, $url);
                }
                break;
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        } else {
            throw new Exception("MongoDB request failed: HTTP {$httpCode} - {$response}");
        }
    }
    
    /**
     * Generate ObjectId
     */
    private function generateObjectId() {
        return bin2hex(random_bytes(12));
    }
}

/**
 * MongoDB BSON Types
 */
class MongoDB_BSON_UTCDateTime {
    private $timestamp;
    
    public function __construct($timestamp = null) {
        $this->timestamp = $timestamp ?: time() * 1000;
    }
    
    public function toDateTime() {
        return new DateTime('@' . ($this->timestamp / 1000));
    }
    
    public function __toString() {
        return date('Y-m-d H:i:s', $this->timestamp / 1000);
    }
}

// Create global MongoDB connection instance
$mongodb = new MongoDBConnection('localhost', 27017, 'schogms');

// Test connection
if (!$mongodb->testConnection()) {
    error_log("Warning: MongoDB connection failed. Please ensure MongoDB is running on localhost:27017");
}
?>

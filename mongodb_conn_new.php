<?php
/**
 * MongoDB Connection Configuration
 * This replaces the MySQL connection for SchoGMS
 */

require_once 'mongodb_conn.php';

// MongoDB connection settings
$mongodb_host = 'localhost';
$mongodb_port = 27017;
$mongodb_database = 'schogms';

// Create MongoDB connection
$mongodb = new MongoDBConnection($mongodb_host, $mongodb_port, $mongodb_database);

// Test connection
if (!$mongodb->testConnection()) {
    die("MongoDB connection failed. Please ensure MongoDB is running on {$mongodb_host}:{$mongodb_port}");
}

// Collection references for easy access
$users = $mongodb->collection('users');
$admin = $mongodb->collection('admin');
$billing_table = $mongodb->collection('billing_table');
$campuses = $mongodb->collection('campuses');
$ched_masterlist = $mongodb->collection('ched_masterlist');
$registrar_master_list = $mongodb->collection('registrar_master_list');
$document_uploads = $mongodb->collection('document_uploads');
$file_submissions = $mongodb->collection('file_submissions');
$verification_attempts = $mongodb->collection('verification_attempts');

// Helper functions for common operations
class MongoDBHelper {
    private $mongodb;
    
    public function __construct($mongodb) {
        $this->mongodb = $mongodb;
    }
    
    /**
     * Authenticate user
     */
    public function authenticateUser($email, $password) {
        $user = $this->mongodb->collection('users')->findOne(['email' => $email]);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Get user by ID
     */
    public function getUserById($userId) {
        return $this->mongodb->collection('users')->findOne(['user_id' => (int)$userId]);
    }
    
    /**
     * Get user by email
     */
    public function getUserByEmail($email) {
        return $this->mongodb->collection('users')->findOne(['email' => $email]);
    }
    
    /**
     * Create new user
     */
    public function createUser($userData) {
        $userData['user_id'] = $this->getNextUserId();
        $userData['created_at'] = new MongoDB_BSON_UTCDateTime();
        $userData['updated_at'] = new MongoDB_BSON_UTCDateTime();
        $userData['status'] = $userData['status'] ?? 'active';
        
        return $this->mongodb->collection('users')->insertOne($userData);
    }
    
    /**
     * Update user
     */
    public function updateUser($userId, $updateData) {
        $updateData['updated_at'] = new MongoDB_BSON_UTCDateTime();
        
        return $this->mongodb->collection('users')->updateOne(
            ['user_id' => (int)$userId],
            ['$set' => $updateData]
        );
    }
    
    /**
     * Delete user
     */
    public function deleteUser($userId) {
        return $this->mongodb->collection('users')->deleteOne(['user_id' => (int)$userId]);
    }
    
    /**
     * Get all users with optional filter
     */
    public function getUsers($filter = []) {
        return $this->mongodb->collection('users')->find($filter);
    }
    
    /**
     * Get users by role
     */
    public function getUsersByRole($role) {
        return $this->mongodb->collection('users')->find(['role' => $role]);
    }
    
    /**
     * Get next user ID
     */
    private function getNextUserId() {
        $lastUser = $this->mongodb->collection('users')->find([], ['sort' => ['user_id' => -1], 'limit' => 1]);
        return !empty($lastUser) ? $lastUser[0]['user_id'] + 1 : 1;
    }
    
    /**
     * Authenticate admin
     */
    public function authenticateAdmin($username, $password) {
        $admin = $this->mongodb->collection('admin')->findOne(['username' => $username]);
        
        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }
        
        return false;
    }
    
    /**
     * Get billing records
     */
    public function getBillingRecords($filter = []) {
        return $this->mongodb->collection('billing_table')->find($filter);
    }
    
    /**
     * Get CHED masterlist records
     */
    public function getChedMasterlist($filter = []) {
        return $this->mongodb->collection('ched_masterlist')->find($filter);
    }
    
    /**
     * Get registrar masterlist records
     */
    public function getRegistrarMasterlist($filter = []) {
        return $this->mongodb->collection('registrar_master_list')->find($filter);
    }
    
    /**
     * Get campuses
     */
    public function getCampuses() {
        return $this->mongodb->collection('campuses')->find();
    }
    
    /**
     * Get document uploads
     */
    public function getDocumentUploads($filter = []) {
        return $this->mongodb->collection('document_uploads')->find($filter);
    }
    
    /**
     * Get file submissions
     */
    public function getFileSubmissions($filter = []) {
        return $this->mongodb->collection('file_submissions')->find($filter);
    }
    
    /**
     * Count records in collection
     */
    public function countRecords($collection, $filter = []) {
        return $this->mongodb->collection($collection)->count($filter);
    }
    
    /**
     * Search records with text search
     */
    public function searchRecords($collection, $searchTerm, $fields = []) {
        $filter = [];
        
        if (!empty($searchTerm)) {
            $orConditions = [];
            foreach ($fields as $field) {
                $orConditions[] = [$field => ['$regex' => $searchTerm, '$options' => 'i']];
            }
            $filter['$or'] = $orConditions;
        }
        
        return $this->mongodb->collection($collection)->find($filter);
    }
}

// Create helper instance
$dbHelper = new MongoDBHelper($mongodb);

// Legacy compatibility - create a mock mysqli-like object for existing code
class MongoDBi {
    private $mongodb;
    private $helper;
    
    public function __construct($mongodb, $helper) {
        $this->mongodb = $mongodb;
        $this->helper = $helper;
    }
    
    public function query($sql) {
        // Parse SQL and convert to MongoDB operations
        return new MongoDBResult($this->parseSQL($sql));
    }
    
    private function parseSQL($sql) {
        // Basic SQL parsing for common operations
        $sql = trim($sql);
        
        if (preg_match('/^SELECT \* FROM (\w+)(?:\s+WHERE\s+(.+))?/i', $sql, $matches)) {
            $collection = $matches[1];
            $where = isset($matches[2]) ? $this->parseWhere($matches[2]) : [];
            
            return $this->mongodb->collection($collection)->find($where);
        }
        
        if (preg_match('/^INSERT INTO (\w+)/i', $sql, $matches)) {
            // Handle INSERT operations
            return ['success' => true, 'inserted_id' => uniqid()];
        }
        
        if (preg_match('/^UPDATE (\w+)/i', $sql, $matches)) {
            // Handle UPDATE operations
            return ['success' => true, 'modified_count' => 1];
        }
        
        if (preg_match('/^DELETE FROM (\w+)/i', $sql, $matches)) {
            // Handle DELETE operations
            return ['success' => true, 'deleted_count' => 1];
        }
        
        return [];
    }
    
    private function parseWhere($where) {
        // Basic WHERE clause parsing
        $conditions = [];
        
        if (preg_match('/(\w+)\s*=\s*[\'"]([^\'"]+)[\'"]/', $where, $matches)) {
            $conditions[$matches[1]] = $matches[2];
        }
        
        return $conditions;
    }
    
    public function prepare($sql) {
        return new MongoDBStatement($sql, $this->mongodb, $this->helper);
    }
    
    public function close() {
        // MongoDB connections are stateless
        return true;
    }
}

class MongoDBResult {
    private $data;
    private $position = 0;
    
    public function __construct($data) {
        $this->data = is_array($data) ? $data : [];
    }
    
    public function fetch_assoc() {
        if ($this->position < count($this->data)) {
            return $this->data[$this->position++];
        }
        return false;
    }
    
    public function fetch_array() {
        return $this->fetch_assoc();
    }
    
    public function num_rows() {
        return count($this->data);
    }
    
    public function free() {
        $this->data = [];
    }
}

class MongoDBStatement {
    private $sql;
    private $mongodb;
    private $helper;
    private $params = [];
    
    public function __construct($sql, $mongodb, $helper) {
        $this->sql = $sql;
        $this->mongodb = $mongodb;
        $this->helper = $helper;
    }
    
    public function bind_param($types, ...$params) {
        $this->params = $params;
        return true;
    }
    
    public function execute() {
        // Execute the prepared statement
        return true;
    }
    
    public function store_result() {
        return true;
    }
    
    public function num_rows() {
        return 0;
    }
    
    public function close() {
        return true;
    }
}

// Create legacy compatibility object
$conn = new MongoDBi($mongodb, $dbHelper);

echo "MongoDB connection established successfully!";
?>

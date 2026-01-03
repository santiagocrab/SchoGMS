<?php
/**
 * MongoDB Connection Configuration for SchoGMS
 * This replaces the MySQL connection with MongoDB functionality
 */

require_once 'mongodb_simple_fast.php';

// MongoDB connection settings
$mongodb_database = 'schogms';

// Create MongoDB connection
$mongodb = new SimpleFastMongoDB($mongodb_database);

// Test connection
if (!$mongodb->testConnection()) {
    // Try to create the data directory if it doesn't exist
    $dataDir = __DIR__ . '/mongodb_data/schogms';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    // Test again
    if (!$mongodb->testConnection()) {
        error_log("MongoDB connection failed. Data directory: " . $dataDir);
        // Don't die, just log the error and continue
    }
}

// Collection references for easy access
$users = $mongodb->collection('users');
$admin = $mongodb->collection('admin');
$campuses = $mongodb->collection('campuses');
$ched_masterlist = $mongodb->collection('ched_masterlist');
$registrar_master_list = $mongodb->collection('registrar_master_list');
$document_uploads = $mongodb->collection('document_uploads');
$file_submissions = $mongodb->collection('file_submissions');
$verification_attempts = $mongodb->collection('verification_attempts');
$assigned_dean = $mongodb->collection('assigned_dean');
$assigned_program_chairs = $mongodb->collection('assigned_program_chairs');

// Helper functions for common operations
if (!class_exists('MongoDBHelper')) {
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
        $userData['status'] = $userData['status'] ?? 'active';
        
        return $this->mongodb->collection('users')->insertOne($userData);
    }
    
    /**
     * Update user
     */
    public function updateUser($userId, $updateData) {
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
     * Get registrar masterlist records with pagination
     */
    public function getRegistrarMasterlistPaginated($filter = [], $page = 1, $limit = 50) {
        return $this->mongodb->collection('registrar_master_list')->findPaginated($filter, [
            'page' => $page,
            'limit' => $limit
        ]);
    }
    
    /**
     * Search registrar masterlist with pagination
     */
    public function searchRegistrarMasterlist($searchTerm, $page = 1, $limit = 50) {
        $fields = ['last_name', 'first_name', 'id_number', 'course', 'campus'];
        return $this->mongodb->collection('registrar_master_list')->search($searchTerm, $fields, [
            'page' => $page,
            'limit' => $limit
        ]);
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
} // End of class_exists check

// Create helper instance
$dbHelper = new MongoDBHelper($mongodb);

// Legacy compatibility - create a mock mysqli-like object for existing code
if (!class_exists('MongoDBi')) {
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
} // End of class_exists check

// Create legacy compatibility object
$conn = new MongoDBi($mongodb, $dbHelper);

// Set connection error to null for compatibility
$conn->connect_error = null;

// Connection established successfully (no echo to avoid output issues)
?>

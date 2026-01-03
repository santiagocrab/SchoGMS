<?php
/**
 * Migration Script: MySQL to MongoDB
 * This script migrates data from MySQL to MongoDB
 */

require_once 'mongodb_conn.php';

// MySQL connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "schogms";

$mysqlConn = new mysqli($servername, $username, $password, $dbname);

if ($mysqlConn->connect_error) {
    die("MySQL connection failed: " . $mysqlConn->connect_error);
}

class MySQLToMongoDBMigration {
    private $mysqlConn;
    private $mongodb;
    
    public function __construct($mysqlConn) {
        $this->mysqlConn = $mysqlConn;
        $this->mongodb = new MongoDBConnection();
    }
    
    /**
     * Run the complete migration
     */
    public function migrate() {
        echo "Starting MySQL to MongoDB migration...\n";
        
        try {
            // Test connections
            if (!$this->testConnections()) {
                throw new Exception("Database connections failed");
            }
            
            // Migrate tables
            $this->migrateUsers();
            $this->migrateAdmin();
            $this->migrateBillingTable();
            $this->migrateCampuses();
            $this->migrateChedMasterlist();
            $this->migrateRegistrarMasterList();
            $this->migrateDocumentUploads();
            $this->migrateFileSubmissions();
            $this->migrateVerificationAttempts();
            
            echo "Migration completed successfully!\n";
            
        } catch (Exception $e) {
            echo "Migration failed: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Test database connections
     */
    private function testConnections() {
        echo "Testing database connections...\n";
        
        // Test MySQL
        if ($this->mysqlConn->connect_error) {
            echo "MySQL connection failed: " . $this->mysqlConn->connect_error . "\n";
            return false;
        }
        echo "MySQL connection: OK\n";
        
        // Test MongoDB
        if (!$this->mongodb->testConnection()) {
            echo "MongoDB connection failed\n";
            return false;
        }
        echo "MongoDB connection: OK\n";
        
        return true;
    }
    
    /**
     * Migrate users table
     */
    private function migrateUsers() {
        echo "Migrating users table...\n";
        
        $result = $this->mysqlConn->query("SELECT * FROM users");
        $usersCollection = $this->mongodb->collection('users');
        
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            $document = [
                'user_id' => (int)$row['user_id'],
                'name' => $row['name'],
                'email' => $row['email'],
                'role' => $row['role'],
                'password' => $row['password'],
                'campus' => $row['campus'] ?? null,
                'verification_code' => $row['verification_code'] ?? null,
                'verification_expires' => $row['verification_expires'] ?? null,
                'email_verified' => (bool)($row['email_verified'] ?? false),
                'status' => $row['status'] ?? 'active',
                'created_at' => new MongoDB_BSON_UTCDateTime(strtotime($row['created_at']) * 1000),
                'updated_at' => new MongoDB_BSON_UTCDateTime(strtotime($row['updated_at']) * 1000)
            ];
            
            $usersCollection->insertOne($document);
            $count++;
        }
        
        echo "Migrated {$count} users\n";
    }
    
    /**
     * Migrate admin table
     */
    private function migrateAdmin() {
        echo "Migrating admin table...\n";
        
        $result = $this->mysqlConn->query("SELECT * FROM admin");
        $adminCollection = $this->mongodb->collection('admin');
        
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            $document = [
                'admin_id' => (int)$row['admin_id'],
                'username' => $row['username'],
                'password' => $row['password'],
                'created_at' => new MongoDB_BSON_UTCDateTime(strtotime($row['created_at']) * 1000)
            ];
            
            $adminCollection->insertOne($document);
            $count++;
        }
        
        echo "Migrated {$count} admin records\n";
    }
    
    /**
     * Migrate billing_table
     */
    private function migrateBillingTable() {
        echo "Migrating billing_table...\n";
        
        $result = $this->mysqlConn->query("SELECT * FROM billing_table");
        $billingCollection = $this->mongodb->collection('billing_table');
        
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            $document = [
                'id' => (int)$row['id'],
                'last_name' => $row['last_name'],
                'first_name' => $row['first_name'],
                'scholarship_type' => $row['scholarship_type'],
                'units_enrolled' => (int)($row['units_enrolled'] ?? 0),
                'course' => $row['course'],
                'campus' => $row['campus'],
                'year_and_date_submitted_ched' => $row['year_and_date_submitted_ched'] ? new MongoDB_BSON_UTCDateTime(strtotime($row['year_and_date_submitted_ched']) * 1000) : null,
                'amount' => (float)($row['amount'] ?? 0),
                'first_semester' => $row['first_semester'],
                'second_semester' => $row['second_semester'],
                'status' => $row['status'],
                'payment_scholarship_type' => $row['payment_scholarship_type'],
                'payment_amount' => (float)($row['payment_amount'] ?? 0),
                'payment_year_and_date' => $row['payment_year_and_date'] ? new MongoDB_BSON_UTCDateTime(strtotime($row['payment_year_and_date']) * 1000) : null,
                'payment_or_number' => $row['payment_or_number'],
                'payment_amount_per_or' => (float)($row['payment_amount_per_or'] ?? 0),
                'refund_first_sem' => (float)($row['refund_first_sem'] ?? 0),
                'refund_second_sem' => (float)($row['refund_second_sem'] ?? 0),
                'refund_year_and_date_released' => $row['refund_year_and_date_released'] ? new MongoDB_BSON_UTCDateTime(strtotime($row['refund_year_and_date_released']) * 1000) : null
            ];
            
            $billingCollection->insertOne($document);
            $count++;
        }
        
        echo "Migrated {$count} billing records\n";
    }
    
    /**
     * Migrate campuses table
     */
    private function migrateCampuses() {
        echo "Migrating campuses table...\n";
        
        $result = $this->mysqlConn->query("SELECT * FROM campuses");
        $campusesCollection = $this->mongodb->collection('campuses');
        
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            $document = [
                'campus_id' => (int)$row['campus_id'],
                'campus_name' => $row['campus_name'],
                'created_at' => new MongoDB_BSON_UTCDateTime(strtotime($row['created_at']) * 1000)
            ];
            
            $campusesCollection->insertOne($document);
            $count++;
        }
        
        echo "Migrated {$count} campus records\n";
    }
    
    /**
     * Migrate ched_masterlist table
     */
    private function migrateChedMasterlist() {
        echo "Migrating ched_masterlist table...\n";
        
        $result = $this->mysqlConn->query("SELECT * FROM ched_masterlist LIMIT 100"); // Limit for testing
        $chedCollection = $this->mongodb->collection('ched_masterlist');
        
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            $document = $this->convertRowToDocument($row);
            $chedCollection->insertOne($document);
            $count++;
        }
        
        echo "Migrated {$count} CHED masterlist records\n";
    }
    
    /**
     * Migrate registrar_master_list table
     */
    private function migrateRegistrarMasterList() {
        echo "Migrating registrar_master_list table...\n";
        
        $result = $this->mysqlConn->query("SELECT * FROM registrar_master_list LIMIT 100"); // Limit for testing
        $registrarCollection = $this->mongodb->collection('registrar_master_list');
        
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            $document = $this->convertRowToDocument($row);
            $registrarCollection->insertOne($document);
            $count++;
        }
        
        echo "Migrated {$count} registrar masterlist records\n";
    }
    
    /**
     * Migrate document_uploads table
     */
    private function migrateDocumentUploads() {
        echo "Migrating document_uploads table...\n";
        
        $result = $this->mysqlConn->query("SELECT * FROM document_uploads");
        $documentsCollection = $this->mongodb->collection('document_uploads');
        
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            $document = [
                'id' => (int)$row['id'],
                'user_id' => (int)$row['user_id'],
                'filename' => $row['filename'],
                'file_path' => $row['file_path'],
                'file_type' => $row['file_type'],
                'file_size' => (int)$row['file_size'],
                'uploaded_at' => new MongoDB_BSON_UTCDateTime(strtotime($row['uploaded_at']) * 1000)
            ];
            
            $documentsCollection->insertOne($document);
            $count++;
        }
        
        echo "Migrated {$count} document upload records\n";
    }
    
    /**
     * Migrate file_submissions table
     */
    private function migrateFileSubmissions() {
        echo "Migrating file_submissions table...\n";
        
        $result = $this->mysqlConn->query("SELECT * FROM file_submissions");
        $submissionsCollection = $this->mongodb->collection('file_submissions');
        
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            $document = $this->convertRowToDocument($row);
            $submissionsCollection->insertOne($document);
            $count++;
        }
        
        echo "Migrated {$count} file submission records\n";
    }
    
    /**
     * Migrate verification_attempts table
     */
    private function migrateVerificationAttempts() {
        echo "Migrating verification_attempts table...\n";
        
        $result = $this->mysqlConn->query("SELECT * FROM verification_attempts");
        $verificationCollection = $this->mongodb->collection('verification_attempts');
        
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            $document = [
                'id' => (int)$row['id'],
                'email' => $row['email'],
                'code' => $row['code'],
                'attempts' => (int)$row['attempts'],
                'last_attempt' => new MongoDB_BSON_UTCDateTime(strtotime($row['last_attempt']) * 1000),
                'created_at' => new MongoDB_BSON_UTCDateTime(strtotime($row['created_at']) * 1000)
            ];
            
            $verificationCollection->insertOne($document);
            $count++;
        }
        
        echo "Migrated {$count} verification attempt records\n";
    }
    
    /**
     * Convert MySQL row to MongoDB document
     */
    private function convertRowToDocument($row) {
        $document = [];
        
        foreach ($row as $key => $value) {
            if (is_numeric($value) && !is_float($value)) {
                $document[$key] = (int)$value;
            } elseif (is_numeric($value)) {
                $document[$key] = (float)$value;
            } elseif (strtotime($value) !== false && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                $document[$key] = new MongoDB_BSON_UTCDateTime(strtotime($value) * 1000);
            } else {
                $document[$key] = $value;
            }
        }
        
        return $document;
    }
}

// Run migration if called directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    $migration = new MySQLToMongoDBMigration($mysqlConn);
    $migration->migrate();
}
?>

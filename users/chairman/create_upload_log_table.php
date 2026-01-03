<?php
include 'config/conn.php';

// Create ched_upload_log table
$sql = "CREATE TABLE IF NOT EXISTS ched_upload_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_group VARCHAR(255) NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    semester VARCHAR(20) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    record_count INT DEFAULT 0,
    success_count INT DEFAULT 0,
    error_count INT DEFAULT 0,
    uploaded_by VARCHAR(255) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'ched_upload_log' created successfully!<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create ched_masterlist table if it doesn't exist
$sql2 = "CREATE TABLE IF NOT EXISTS ched_masterlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    course VARCHAR(100),
    year_level VARCHAR(20),
    campus VARCHAR(50),
    award_no VARCHAR(100),
    app_no VARCHAR(100),
    academic_year VARCHAR(20) NOT NULL,
    semester VARCHAR(20) NOT NULL,
    file_group VARCHAR(255) NOT NULL,
    uploaded_by VARCHAR(255) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_student_id (student_id),
    INDEX idx_academic_year (academic_year),
    INDEX idx_semester (semester),
    INDEX idx_campus (campus)
)";

if ($conn->query($sql2) === TRUE) {
    echo "Table 'ched_masterlist' created successfully!<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

$conn->close();
echo "<br><a href='upload_ched_tdp.php'>Go to Upload Page</a>";
?>













<?php
include 'config/conn.php';

echo "<h2>Setting up CHED TDP Upload Database Tables</h2>";

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
    echo "<p style='color: green;'>✅ Table 'ched_upload_log' created successfully!</p>";
} else {
    echo "<p style='color: red;'>❌ Error creating table: " . $conn->error . "</p>";
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
    echo "<p style='color: green;'>✅ Table 'ched_masterlist' created successfully!</p>";
} else {
    echo "<p style='color: red;'>❌ Error creating table: " . $conn->error . "</p>";
}

// Create uploads directory
$upload_dir = '../../uploads/ched_tdp/';
if (!is_dir($upload_dir)) {
    if (mkdir($upload_dir, 0755, true)) {
        echo "<p style='color: green;'>✅ Upload directory created: $upload_dir</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create upload directory: $upload_dir</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ️ Upload directory already exists: $upload_dir</p>";
}

$conn->close();

echo "<br><div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>🎉 Setup Complete!</h3>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>✅ Database tables created</li>";
echo "<li>✅ Upload directory created</li>";
echo "<li>✅ Ready for CHED TDP masterlist uploads</li>";
echo "</ul>";
echo "<p><a href='upload_ched_tdp.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Go to Upload Page</a></p>";
echo "</div>";
?>













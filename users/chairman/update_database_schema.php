<?php
include 'config/conn.php';

echo "<h2>Updating CHED TDP Database Schema</h2>";

// Update ched_masterlist table to include all fields from the Excel format
$sql = "ALTER TABLE ched_masterlist 
ADD COLUMN IF NOT EXISTS seq VARCHAR(20),
ADD COLUMN IF NOT EXISTS ext_name VARCHAR(50),
ADD COLUMN IF NOT EXISTS sex VARCHAR(10),
ADD COLUMN IF NOT EXISTS birthdate DATE,
ADD COLUMN IF NOT EXISTS total_units VARCHAR(20),
ADD COLUMN IF NOT EXISTS municipality VARCHAR(100),
ADD COLUMN IF NOT EXISTS province VARCHAR(100),
ADD COLUMN IF NOT EXISTS pwd_classification VARCHAR(100),
ADD COLUMN IF NOT EXISTS grant_amount VARCHAR(50),
ADD COLUMN IF NOT EXISTS batch_no VARCHAR(50),
ADD COLUMN IF NOT EXISTS validation_status VARCHAR(100),
ADD COLUMN IF NOT EXISTS remarks TEXT";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✅ Database schema updated successfully!</p>";
} else {
    echo "<p style='color: red;'>❌ Error updating schema: " . $conn->error . "</p>";
}

$conn->close();

echo "<br><div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>🎉 Database Updated!</h3>";
echo "<p><strong>New fields added:</strong></p>";
echo "<ul>";
echo "<li>SEQ (Sequence Number)</li>";
echo "<li>EXT NAME (Extension Name)</li>";
echo "<li>SEX (Gender)</li>";
echo "<li>BIRTHDATE</li>";
echo "<li>TOTAL UNITS</li>";
echo "<li>MUNICIPALITY</li>";
echo "<li>PROVINCE</li>";
echo "<li>PWD CLASSIFICATION</li>";
echo "<li>GRANT AMOUNT</li>";
echo "<li>BATCH NO</li>";
echo "<li>VALIDATION STATUS</li>";
echo "<li>REMARKS</li>";
echo "</ul>";
echo "<p><a href='upload_ched_tdp.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Try Upload Again</a></p>";
echo "</div>";
?>

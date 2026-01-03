<?php
include 'config/session.php';

echo "<h2>Debug Excel File Structure</h2>";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        try {
            require_once '../vendor/autoload.php';
            
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($file['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            
            echo "<h3>Excel File Analysis:</h3>";
            echo "<p><strong>Total rows:</strong> " . count($data) . "</p>";
            
            if (count($data) > 0) {
                echo "<h4>Header Row (Row 1):</h4>";
                echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
                echo "<tr>";
                foreach ($data[0] as $index => $header) {
                    echo "<th>Column " . ($index + 1) . "</th>";
                }
                echo "</tr>";
                echo "<tr>";
                foreach ($data[0] as $header) {
                    echo "<td>" . htmlspecialchars($header) . "</td>";
                }
                echo "</tr>";
                echo "</table>";
                
                echo "<h4>First Data Row (Row 2):</h4>";
                if (count($data) > 1) {
                    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
                    echo "<tr>";
                    foreach ($data[1] as $index => $value) {
                        echo "<th>Column " . ($index + 1) . "</th>";
                    }
                    echo "</tr>";
                    echo "<tr>";
                    foreach ($data[1] as $value) {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                    echo "</tr>";
                    echo "</table>";
                }
                
                echo "<h4>Column Mapping:</h4>";
                echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
                echo "<tr><th>Column #</th><th>Header</th><th>Sample Data</th></tr>";
                for ($i = 0; $i < min(20, count($data[0])); $i++) {
                    echo "<tr>";
                    echo "<td>" . ($i + 1) . "</td>";
                    echo "<td>" . htmlspecialchars($data[0][$i] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($data[1][$i] ?? '') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error reading Excel file: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>File upload error: " . $file['error'] . "</p>";
    }
} else {
    echo "<form method='post' enctype='multipart/form-data'>";
    echo "<h3>Upload Excel File for Analysis:</h3>";
    echo "<input type='file' name='excel_file' accept='.xlsx,.xls' required><br><br>";
    echo "<button type='submit'>Analyze File Structure</button>";
    echo "</form>";
}

echo "<br><div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>🔧 Next Steps:</h3>";
echo "<ol>";
echo "<li>Upload your Excel file above to see the exact structure</li>";
echo "<li>Copy the column mapping and update the upload handler</li>";
echo "<li><a href='upload_ched_tdp.php'>Try Upload Again</a></li>";
echo "</ol>";
echo "</div>";
?>













<?php
include 'config/session.php';

echo "<h3>🔍 DEBUG: Masterlist COR Detection</h3>";

try {
    $registrarCollection = $mongodb->collection('registrar_master_list');
    
    // Get first 5 students to test COR detection
    $students = $registrarCollection->find([], ['limit' => 5, 'sort' => ['last_name' => 1]]);
    
    echo "<h4>Testing COR Detection for First 5 Students:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Student Name</th><th>COR Detection</th><th>COR File Found</th><th>Test Link</th></tr>";
    
    foreach ($students as $student) {
        $lastName = strtoupper(trim($student['last_name'] ?? ''));
        $firstName = strtoupper(trim($student['first_name'] ?? ''));
        $studentName = $lastName . ', ' . $firstName;
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($studentName) . "</td>";
        
        // Check COR files in multiple directories
        $corDirs = [
            'uploads/COR/',
            'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
            'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
            'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
            'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/'
        ];
        
        $hasCOR = false;
        $corFile = '';
        $foundFiles = [];
        
        foreach ($corDirs as $corDir) {
            if (is_dir($corDir)) {
                $corFiles = scandir($corDir);
                foreach ($corFiles as $file) {
                    if ($file != '.' && $file != '..' && is_file($corDir . $file)) {
                        $fileName = pathinfo($file, PATHINFO_FILENAME);
                        $fileNameUpper = strtoupper($fileName);
                        
                        // Multiple matching strategies
                        $matchFound = false;
                        
                        // Strategy 1: Exact last name match
                        if (strpos($fileNameUpper, $lastName) !== false) {
                            $matchFound = true;
                        }
                        
                        // Strategy 2: Last name + first name match
                        if (!$matchFound && strpos($fileNameUpper, $lastName) !== false && strpos($fileNameUpper, $firstName) !== false) {
                            $matchFound = true;
                        }
                        
                        // Strategy 3: Full name match (LASTNAME, FIRSTNAME)
                        if (!$matchFound) {
                            $fullNamePattern = $lastName . ', ' . $firstName;
                            if (strpos($fileNameUpper, $fullNamePattern) !== false) {
                                $matchFound = true;
                            }
                        }
                        
                        // Strategy 4: Check if filename starts with last name
                        if (!$matchFound && strpos($fileNameUpper, $lastName) === 0) {
                            $matchFound = true;
                        }
                        
                        if ($matchFound) {
                            $hasCOR = true;
                            $corFile = $corDir . $file;
                            $foundFiles[] = $file;
                            break 2;
                        }
                    }
                }
            }
        }
        
        if ($hasCOR) {
            echo "<td style='color: green;'>✅ COR Found</td>";
            echo "<td>" . htmlspecialchars(basename($corFile)) . "</td>";
            $viewUrl = 'view_document.php?file=' . urlencode($corFile) . '&type=COR';
            echo "<td><a href='$viewUrl' target='_blank' style='color: blue;'>Test View</a></td>";
        } else {
            echo "<td style='color: red;'>❌ No COR</td>";
            echo "<td>-</td>";
            echo "<td>-</td>";
        }
        
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Also show what COR files are available
    echo "<h4>Available COR Files (First 10):</h4>";
    $corDir = 'uploads/documents/ISULAN/2024-2025/1st Semester/COR/';
    if (is_dir($corDir)) {
        $corFiles = scandir($corDir);
        $pdfFiles = array_filter($corFiles, function($file) {
            return $file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'pdf';
        });
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Available COR Files</th><th>Size</th></tr>";
        
        $count = 0;
        foreach ($pdfFiles as $file) {
            if ($count >= 10) break;
            
            $filePath = $corDir . $file;
            $fileSize = filesize($filePath);
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($file) . "</td>";
            echo "<td>" . number_format($fileSize) . " bytes</td>";
            echo "</tr>";
            
            $count++;
        }
        
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='masterlist.php' class='btn btn-primary'>← Back to Masterlist</a></p>";
?>













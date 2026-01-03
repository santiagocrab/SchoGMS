<?php
// Diagnostic script to check COR/COG documents for TDP masterlist students
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load MongoDB connection
require_once 'conn_mongodb.php';

// Get user info directly - avoid including session.php which has MySQL dependencies
$user_id = $_SESSION['user_id'] ?? null;
$sheet_name = null;

if ($user_id) {
    // Try to get user info from MongoDB first
    try {
        $usersCollection = $mongodb->collection('users');
        $userDoc = $usersCollection->findOne(['user_id' => (int)$user_id]);
        if ($userDoc) {
            $sheet_name = $userDoc['campus'] ?? null;
        }
    } catch (Exception $e) {
        // MongoDB failed, try MySQL directly
    }
    
    // If MongoDB didn't work, try MySQL directly (without session.php)
    if (!$sheet_name) {
        try {
            // Connect to MySQL directly
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "schogms";
            
            $mysql_conn = new mysqli($servername, $username, $password, $dbname);
            
            if (!$mysql_conn->connect_error) {
                $sql = "SELECT campus FROM users WHERE user_id = ?";
                $stmt = $mysql_conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result && $result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $sheet_name = $row['campus'] ?? null;
                    }
                    $stmt->close();
                }
                $mysql_conn->close();
            }
        } catch (Exception $e) {
            // MySQL also failed
        }
    }
}

// Fallback to default if still not set
if (!$sheet_name) {
    $sheet_name = 'ISULAN'; // Default campus
}

// Also get MySQL connection for querying masterlist
try {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "schogms";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        $conn = null;
    }
} catch (Exception $e) {
    $conn = null;
}

echo "<!DOCTYPE html><html><head><title>TDP Masterlist COR/COG Check</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    .error { color: red; }
    .success { color: green; }
    .warning { color: orange; }
    h2 { color: #333; }
    h3 { color: #666; margin-top: 30px; }
</style></head><body>";

echo "<h2>🔍 TDP Masterlist COR/COG Diagnostic</h2>";

// Get coordinator campus
$sheet_name = $sheet_name ?? 'ISULAN';
echo "<p><strong>Coordinator Campus:</strong> " . htmlspecialchars($sheet_name) . "</p>";

// Get students from TDP masterlist (CHED masterlist)
// MySQL connection should already be set above
if (!isset($conn) || !$conn) {
    echo "<p class='error'>❌ Could not connect to MySQL database</p>";
    exit;
}

$query = "SELECT id, lastname, firstname, middlename FROM ched_masterlist WHERE sheet_name = '" . $conn->real_escape_string($sheet_name) . "' LIMIT 20";
$result = $conn->query($query);

if (!$result) {
    echo "<p class='error'>❌ Error querying masterlist: " . ($conn->error ?? 'Unknown error') . "</p>";
    exit;
}

echo "<h3>Sample Students from TDP Masterlist (First 20)</h3>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Lastname</th><th>Firstname</th><th>Middlename</th><th>Name Pattern</th><th>COR Found?</th><th>COG Found?</th><th>COR Details</th></tr>";

$documentCollection = $mongodb->collection('document_uploads');
$studentsWithCOR = 0;
$studentsWithCOG = 0;
$studentsChecked = 0;

while ($row = $result->fetch_assoc()) {
    $studentsChecked++;
    $lastName = trim($row['lastname'] ?? '');
    $firstName = trim($row['firstname'] ?? '');
    $middleName = trim($row['middlename'] ?? '');
    
    if (empty($lastName) || empty($firstName)) {
        continue;
    }
    
    // Create name patterns
    $namePattern1 = $lastName . ', ' . $firstName; // "Lastname, Firstname"
    $namePattern2 = $lastName . ', ' . $firstName . ' ' . $middleName; // "Lastname, Firstname Middlename"
    $pattern1 = preg_quote($namePattern1, '/');
    $pattern2 = preg_quote($namePattern2, '/');
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id'] ?? 'N/A') . "</td>";
    echo "<td>" . htmlspecialchars($lastName) . "</td>";
    echo "<td>" . htmlspecialchars($firstName) . "</td>";
    echo "<td>" . htmlspecialchars($middleName) . "</td>";
    echo "<td><small>" . htmlspecialchars($namePattern1) . "</small></td>";
    
    // Search for COR
    $corDoc = null;
    $corDetails = [];
    
    // Try pattern 1 with campus
    $corQuery1 = [
        'original_name' => ['$regex' => '^' . $pattern1 . '(\.pdf)?$', '$options' => 'i'],
        'category' => 'COR'
    ];
    if (!empty($sheet_name)) {
        $corQuery1['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
    }
    $corDocs1 = $documentCollection->find($corQuery1, ['limit' => 1]);
    foreach ($corDocs1 as $doc) { $corDoc = $doc; break; }
    
    // Try pattern 1 without campus
    if (!$corDoc) {
        $corQuery2 = [
            'original_name' => ['$regex' => '^' . $pattern1 . '(\.pdf)?$', '$options' => 'i'],
            'category' => 'COR'
        ];
        $corDocs2 = $documentCollection->find($corQuery2, ['limit' => 1]);
        foreach ($corDocs2 as $doc) { $corDoc = $doc; break; }
    }
    
    // Try pattern 2
    if (!$corDoc && !empty($pattern2)) {
        $corQuery3 = [
            'original_name' => ['$regex' => '^' . $pattern2 . '(\.pdf)?$', '$options' => 'i'],
            'category' => 'COR'
        ];
        if (!empty($sheet_name)) {
            $corQuery3['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
        }
        $corDocs3 = $documentCollection->find($corQuery3, ['limit' => 1]);
        foreach ($corDocs3 as $doc) { $corDoc = $doc; break; }
    }
    
    // Try flexible match (contains)
    if (!$corDoc) {
        $corQuery4 = [
            'original_name' => ['$regex' => $pattern1, '$options' => 'i'],
            'category' => 'COR'
        ];
        if (!empty($sheet_name)) {
            $corQuery4['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
        }
        $corDocs4 = $documentCollection->find($corQuery4, ['limit' => 1]);
        foreach ($corDocs4 as $doc) { $corDoc = $doc; break; }
    }
    
    // Try file_name
    if (!$corDoc) {
        $corQuery5 = [
            'file_name' => ['$regex' => $pattern1, '$options' => 'i'],
            'category' => 'COR'
        ];
        if (!empty($sheet_name)) {
            $corQuery5['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
        }
        $corDocs5 = $documentCollection->find($corQuery5, ['limit' => 1]);
        foreach ($corDocs5 as $doc) { $corDoc = $doc; break; }
    }
    
    if ($corDoc) {
        $studentsWithCOR++;
        $corDetails[] = "Name: " . htmlspecialchars($corDoc['original_name'] ?? 'N/A');
        $corDetails[] = "Campus: " . htmlspecialchars($corDoc['campus'] ?? 'N/A');
        $corDetails[] = "File: " . htmlspecialchars($corDoc['file_name'] ?? 'N/A');
        echo "<td class='success'>✅ YES</td>";
    } else {
        echo "<td class='error'>❌ NO</td>";
    }
    
    // Search for COG
    $cogDoc = null;
    
    $cogQuery1 = [
        'original_name' => ['$regex' => '^' . $pattern1 . '(\.pdf)?$', '$options' => 'i'],
        'category' => 'COG'
    ];
    if (!empty($sheet_name)) {
        $cogQuery1['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
    }
    $cogDocs1 = $documentCollection->find($cogQuery1, ['limit' => 1]);
    foreach ($cogDocs1 as $doc) { $cogDoc = $doc; break; }
    
    if (!$cogDoc) {
        $cogQuery2 = [
            'original_name' => ['$regex' => '^' . $pattern1 . '(\.pdf)?$', '$options' => 'i'],
            'category' => 'COG'
        ];
        $cogDocs2 = $documentCollection->find($cogQuery2, ['limit' => 1]);
        foreach ($cogDocs2 as $doc) { $cogDoc = $doc; break; }
    }
    
    if ($cogDoc) {
        $studentsWithCOG++;
        echo "<td class='success'>✅ YES</td>";
    } else {
        echo "<td class='error'>❌ NO</td>";
    }
    
    // Show COR details
    if (!empty($corDetails)) {
        echo "<td><small>" . implode("<br>", $corDetails) . "</small></td>";
    } else {
        echo "<td>-</td>";
    }
    
    echo "</tr>";
}

echo "</table>";
echo "<p><strong>Summary:</strong> Checked $studentsChecked students. Found COR for $studentsWithCOR, COG for $studentsWithCOG</p>";

// Check all COR documents in database for this campus
echo "<h3>All COR Documents in Database for Campus: " . htmlspecialchars($sheet_name) . "</h3>";
$allCorDocs = $documentCollection->find(['category' => 'COR', 'campus' => ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i']], ['limit' => 20]);
echo "<table border='1'>";
echo "<tr><th>Original Name</th><th>File Name</th><th>Campus</th><th>Academic Year</th><th>Semester</th></tr>";

$corCount = 0;
foreach ($allCorDocs as $doc) {
    $corCount++;
    echo "<tr>";
    echo "<td>" . htmlspecialchars($doc['original_name'] ?? 'N/A') . "</td>";
    echo "<td>" . htmlspecialchars($doc['file_name'] ?? 'N/A') . "</td>";
    echo "<td>" . htmlspecialchars($doc['campus'] ?? 'N/A') . "</td>";
    echo "<td>" . htmlspecialchars($doc['academic_year'] ?? 'N/A') . "</td>";
    echo "<td>" . htmlspecialchars($doc['semester'] ?? 'N/A') . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "<p><strong>Total COR documents found:</strong> $corCount</p>";

// Check all COR documents without campus filter
echo "<h3>All COR Documents in Database (Any Campus - First 20)</h3>";
$allCorDocsAny = $documentCollection->find(['category' => 'COR'], ['limit' => 20]);
echo "<table border='1'>";
echo "<tr><th>Original Name</th><th>File Name</th><th>Campus</th></tr>";

$corCountAny = 0;
foreach ($allCorDocsAny as $doc) {
    $corCountAny++;
    echo "<tr>";
    echo "<td>" . htmlspecialchars($doc['original_name'] ?? 'N/A') . "</td>";
    echo "<td>" . htmlspecialchars($doc['file_name'] ?? 'N/A') . "</td>";
    echo "<td>" . htmlspecialchars($doc['campus'] ?? 'N/A') . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "<p><strong>Total COR documents (any campus):</strong> $corCountAny</p>";

echo "</body></html>";
?>


<?php
// Test script to debug COR viewing issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>COR Viewing Debug Test</title>";
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

echo "<h2>🔍 COR Viewing Debug Test</h2>";

// Step 1: Check MongoDB connection
echo "<h3>1. MongoDB Connection Test</h3>";
try {
    require_once 'conn_mongodb.php';
    echo "<p class='success'>✅ MongoDB connection file loaded</p>";
    
    if (isset($mongodb)) {
        echo "<p class='success'>✅ MongoDB object created</p>";
        $testConnection = $mongodb->testConnection();
        if ($testConnection) {
            echo "<p class='success'>✅ MongoDB connection test passed</p>";
        } else {
            echo "<p class='warning'>⚠️ MongoDB connection test failed (but continuing...)</p>";
        }
    } else {
        echo "<p class='error'>❌ MongoDB object not created</p>";
        die("Cannot continue without MongoDB connection");
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error loading MongoDB: " . htmlspecialchars($e->getMessage()) . "</p>";
    die();
}

// Step 2: Check session and user
echo "<h3>2. Session & User Info</h3>";
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<p class='warning'>⚠️ No user session found. Please log in as a coordinator first.</p>";
    echo "<p><strong>To test without login, we'll check all documents...</strong></p>";
    $sheet_name = null;
    $user_id = null;
} else {
    echo "<p class='success'>✅ Session found</p>";
    echo "<p><strong>User ID:</strong> " . htmlspecialchars($_SESSION['user_id'] ?? 'NOT SET') . "</p>";
    
    // Try to get user info from MySQL first, then MongoDB as fallback
    $user_id = $_SESSION['user_id'];
    $sheet_name = null;
    $userFound = false;
    
    // Try MySQL first
    try {
        @include 'users/coordinator/config/conn.php';
        if (isset($conn)) {
            $sql = "SELECT * FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $sheet_name = $row['campus'] ?? null;
                    echo "<p class='success'>✅ User found in MySQL database</p>";
                    echo "<p><strong>Campus (sheet_name):</strong> " . htmlspecialchars($sheet_name ?? 'NOT SET') . "</p>";
                    echo "<p><strong>Full Name:</strong> " . htmlspecialchars($row['name'] ?? 'N/A') . "</p>";
                    echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email'] ?? 'N/A') . "</p>";
                    echo "<p><strong>Role:</strong> " . htmlspecialchars($row['role'] ?? 'N/A') . "</p>";
                    $userFound = true;
                }
            }
        }
    } catch (Exception $e) {
        echo "<p class='warning'>⚠️ MySQL error (trying MongoDB): " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // If MySQL failed, try MongoDB
    if (!$userFound && isset($mongodb)) {
        try {
            $usersCollection = $mongodb->collection('users');
            $userDoc = $usersCollection->findOne(['user_id' => (int)$user_id]);
            
            if ($userDoc) {
                $sheet_name = $userDoc['campus'] ?? null;
                echo "<p class='success'>✅ User found in MongoDB</p>";
                echo "<p><strong>Campus (sheet_name):</strong> " . htmlspecialchars($sheet_name ?? 'NOT SET') . "</p>";
                echo "<p><strong>Full Name:</strong> " . htmlspecialchars($userDoc['name'] ?? 'N/A') . "</p>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($userDoc['email'] ?? 'N/A') . "</p>";
                echo "<p><strong>Role:</strong> " . htmlspecialchars($userDoc['role'] ?? 'N/A') . "</p>";
                $userFound = true;
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ MongoDB error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    if (!$userFound) {
        echo "<p class='error'>❌ User not found in either database</p>";
    }
}

// Step 3: Get sample students from CHED masterlist
echo "<h3>3. Sample Students from CHED Masterlist</h3>";
try {
    // Try MySQL first
    $result = null;
    if (isset($conn) && $conn) {
        $query = "SELECT lastname, firstname, middlename FROM ched_masterlist";
        if (!empty($sheet_name)) {
            $query .= " WHERE sheet_name = '" . $conn->real_escape_string($sheet_name) . "'";
        }
        $query .= " LIMIT 10";
        
        $result = @$conn->query($query);
    }
    
    // If MySQL failed, try MongoDB
    if (!$result && isset($mongodb)) {
        try {
            $chedCollection = $mongodb->collection('ched_masterlist');
            $filter = [];
            if (!empty($sheet_name)) {
                $filter['sheet_name'] = $sheet_name;
            }
            $chedDocs = $chedCollection->find($filter, ['limit' => 10]);
            
            // Convert MongoDB results to MySQL-like format
            $students = [];
            foreach ($chedDocs as $doc) {
                $students[] = [
                    'lastname' => $doc['lastname'] ?? '',
                    'firstname' => $doc['firstname'] ?? '',
                    'middlename' => $doc['middlename'] ?? ''
                ];
            }
            
            // Create a mock result object with data array
            $result = (object)[
                'num_rows' => count($students),
                'data' => $students
            ];
        } catch (Exception $e) {
            echo "<p class='error'>❌ MongoDB query error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    if ($result) {
        // Handle both MySQL result and MongoDB array
        $numRows = 0;
        $studentsArray = [];
        
        if (is_object($result) && method_exists($result, 'num_rows')) {
            // MySQL result
            $numRows = $result->num_rows;
        } else if (isset($result->num_rows)) {
            // MongoDB mock result
            $numRows = $result->num_rows;
            $studentsArray = $result->data ?? [];
        }
        
        if ($numRows > 0 || !empty($studentsArray)) {
            $displayCount = $numRows > 0 ? $numRows : count($studentsArray);
            echo "<p class='success'>✅ Found " . $displayCount . " students</p>";
            echo "<table border='1'>";
            echo "<tr><th>Lastname</th><th>Firstname</th><th>Middlename</th><th>COR Found?</th><th>COG Found?</th><th>Details</th></tr>";
            
            $documentCollection = $mongodb->collection('document_uploads');
            $studentsChecked = 0;
            $corFoundCount = 0;
            $cogFoundCount = 0;
            
            // Process students - either from MySQL fetch_assoc or MongoDB array
            $studentIterator = !empty($studentsArray) ? $studentsArray : null;
            $useArray = !empty($studentsArray);
            $arrayIndex = 0;
            
            while (true) {
                if ($useArray) {
                    // MongoDB array
                    if ($arrayIndex >= count($studentsArray)) break;
                    $student = $studentsArray[$arrayIndex++];
                } else {
                    // MySQL result
                    $student = $result->fetch_assoc();
                    if (!$student) break;
                }
            $studentsChecked++;
            $lastName = trim($student['lastname'] ?? '');
            $firstName = trim($student['firstname'] ?? '');
            $middleName = trim($student['middlename'] ?? '');
            
            if (empty($lastName) || empty($firstName)) {
                continue;
            }
            
            // Create name patterns
            $namePattern1 = $lastName . ', ' . $firstName;
            $namePattern2 = $lastName . ', ' . $firstName . ' ' . $middleName;
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($lastName) . "</td>";
            echo "<td>" . htmlspecialchars($firstName) . "</td>";
            echo "<td>" . htmlspecialchars($middleName) . "</td>";
            
            // Test COR search - using same logic as coordinator pages
            $corFound = false;
            $corDetails = [];
            $corDoc = null;
            
            // Escape special regex characters
            $pattern1 = preg_quote($namePattern1, '/');
            $pattern2 = preg_quote($namePattern2, '/');
            
            // Try pattern 1 with campus (same as coordinator validate.php)
            $corQuery1 = [
                'original_name' => ['$regex' => '^' . $pattern1, '$options' => 'i'],
                'category' => 'COR'
            ];
            if (!empty($sheet_name)) {
                $corQuery1['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
            }
            $corDocs1 = $documentCollection->find($corQuery1, ['limit' => 1]);
            foreach ($corDocs1 as $doc) { $corDoc = $doc; break; }
            
            // Fallback 1: pattern 1 without campus
            if (!$corDoc) {
                $corQuery2 = [
                    'original_name' => ['$regex' => '^' . $pattern1, '$options' => 'i'],
                    'category' => 'COR'
                ];
                $corDocs2 = $documentCollection->find($corQuery2, ['limit' => 1]);
                foreach ($corDocs2 as $doc) { $corDoc = $doc; break; }
            }
            
            // Fallback 2: pattern 2 with campus
            if (!$corDoc && !empty($pattern2)) {
                $corQuery3 = [
                    'original_name' => ['$regex' => '^' . $pattern2, '$options' => 'i'],
                    'category' => 'COR'
                ];
                if (!empty($sheet_name)) {
                    $corQuery3['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
                }
                $corDocs3 = $documentCollection->find($corQuery3, ['limit' => 1]);
                foreach ($corDocs3 as $doc) { $corDoc = $doc; break; }
            }
            
            // Fallback 3: file_name search (last resort)
            if (!$corDoc) {
                $corQuery4 = [
                    'file_name' => ['$regex' => $pattern1, '$options' => 'i'],
                    'category' => 'COR'
                ];
                if (!empty($sheet_name)) {
                    $corQuery4['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
                }
                $corDocs4 = $documentCollection->find($corQuery4, ['limit' => 1]);
                foreach ($corDocs4 as $doc) { $corDoc = $doc; break; }
            }
            
            if ($corDoc) {
                $corFound = true;
                $corFoundCount++;
                $corDetails[] = [
                    'original_name' => $corDoc['original_name'] ?? 'N/A',
                    'campus' => $corDoc['campus'] ?? 'N/A',
                    'file_path' => $corDoc['file_path'] ?? 'N/A'
                ];
            }
            
            if ($corFound) {
                $detailText = "";
                foreach ($corDetails as $detail) {
                    $detailText .= "Name: " . htmlspecialchars($detail['original_name']) . "<br>";
                    $detailText .= "Campus: " . htmlspecialchars($detail['campus']) . "<br>";
                    if (isset($detail['note'])) {
                        $detailText .= "<span class='warning'>" . htmlspecialchars($detail['note']) . "</span><br>";
                    }
                }
                echo "<td class='success'>✅ YES</td>";
                echo "<td colspan='2'><small>" . $detailText . "</small></td>";
                } else {
                    echo "<td class='error'>❌ NO</td>";
                    
                    // Test COG - using same logic as COR
                    $cogFound = false;
                    $cogDoc = null;
                    
                    $cogQuery1 = [
                        'original_name' => ['$regex' => '^' . $pattern1, '$options' => 'i'],
                        'category' => 'COG'
                    ];
                    if (!empty($sheet_name)) {
                        $cogQuery1['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
                    }
                    $cogDocs1 = $documentCollection->find($cogQuery1, ['limit' => 1]);
                    foreach ($cogDocs1 as $doc) { $cogDoc = $doc; break; }
                    
                    if (!$cogDoc) {
                        $cogQuery2 = [
                            'original_name' => ['$regex' => '^' . $pattern1, '$options' => 'i'],
                            'category' => 'COG'
                        ];
                        $cogDocs2 = $documentCollection->find($cogQuery2, ['limit' => 1]);
                        foreach ($cogDocs2 as $doc) { $cogDoc = $doc; break; }
                    }
                    
                    if (!$cogDoc && !empty($pattern2)) {
                        $cogQuery3 = [
                            'original_name' => ['$regex' => '^' . $pattern2, '$options' => 'i'],
                            'category' => 'COG'
                        ];
                        if (!empty($sheet_name)) {
                            $cogQuery3['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
                        }
                        $cogDocs3 = $documentCollection->find($cogQuery3, ['limit' => 1]);
                        foreach ($cogDocs3 as $doc) { $cogDoc = $doc; break; }
                    }
                    
                    if ($cogDoc) {
                        $cogFound = true;
                        $cogFoundCount++;
                    }
                    
                    if ($cogFound) {
                        echo "<td class='success'>✅ YES</td>";
                    } else {
                        echo "<td class='error'>❌ NO</td>";
                    }
                    echo "<td>-</td>";
                }
            
            echo "</tr>";
        }
            echo "</table>";
            echo "<p><strong>Summary:</strong> Checked $studentsChecked students. Found COR for $corFoundCount, COG for $cogFoundCount</p>";
        } else {
            echo "<p class='error'>❌ No students found in CHED masterlist</p>";
        }
    } else {
        echo "<p class='error'>❌ Could not query students (both MySQL and MongoDB failed)</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error querying students: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Step 4: Check all COR documents in database
echo "<h3>4. All COR Documents in Database (First 20)</h3>";
try {
    $documentCollection = $mongodb->collection('document_uploads');
    $allCorDocs = $documentCollection->find(['category' => 'COR'], ['limit' => 20]);
    
    $corCount = 0;
    echo "<table border='1'>";
    echo "<tr><th>#</th><th>Campus</th><th>Original Name</th><th>File Name</th><th>File Path</th><th>Upload Date</th></tr>";
    
    foreach ($allCorDocs as $doc) {
        $corCount++;
        echo "<tr>";
        echo "<td>$corCount</td>";
        echo "<td>" . htmlspecialchars($doc['campus'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($doc['original_name'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars(substr($doc['file_name'] ?? 'NULL', 0, 40)) . "...</td>";
        echo "<td>" . htmlspecialchars(substr($doc['file_path'] ?? 'NULL', 0, 60)) . "...</td>";
        echo "<td>" . htmlspecialchars($doc['upload_date'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if ($corCount == 0) {
        echo "<p class='error'>❌ No COR documents found in database!</p>";
    } else {
        echo "<p class='success'>✅ Found $corCount COR documents</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error querying COR documents: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Step 5: Check all COG documents
echo "<h3>5. All COG Documents in Database (First 20)</h3>";
try {
    $documentCollection = $mongodb->collection('document_uploads');
    $allCogDocs = $documentCollection->find(['category' => 'COG'], ['limit' => 20]);
    
    $cogCount = 0;
    echo "<table border='1'>";
    echo "<tr><th>#</th><th>Campus</th><th>Original Name</th><th>File Name</th><th>File Path</th></tr>";
    
    foreach ($allCogDocs as $doc) {
        $cogCount++;
        echo "<tr>";
        echo "<td>$cogCount</td>";
        echo "<td>" . htmlspecialchars($doc['campus'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($doc['original_name'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars(substr($doc['file_name'] ?? 'NULL', 0, 40)) . "...</td>";
        echo "<td>" . htmlspecialchars(substr($doc['file_path'] ?? 'NULL', 0, 60)) . "...</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if ($cogCount == 0) {
        echo "<p class='error'>❌ No COG documents found in database!</p>";
    } else {
        echo "<p class='success'>✅ Found $cogCount COG documents</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error querying COG documents: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Step 6: Campus matching test
echo "<h3>6. Campus Matching Test</h3>";
if (!empty($sheet_name)) {
    echo "<p><strong>Your Campus:</strong> " . htmlspecialchars($sheet_name) . "</p>";
    
    try {
        $documentCollection = $mongodb->collection('document_uploads');
        $campusDocs = $documentCollection->find(['campus' => ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i']], ['limit' => 10]);
        
        $matchCount = 0;
        echo "<p>Documents with matching campus (case-insensitive):</p>";
        echo "<table border='1'>";
        echo "<tr><th>Category</th><th>Original Name</th><th>Campus (from DB)</th></tr>";
        
        foreach ($campusDocs as $doc) {
            $matchCount++;
            echo "<tr>";
            echo "<td>" . htmlspecialchars($doc['category'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($doc['original_name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($doc['campus'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        if ($matchCount == 0) {
            echo "<p class='warning'>⚠️ No documents found with your campus name. This might be the issue!</p>";
            echo "<p>Let's check what campus values actually exist in the database:</p>";
            
            // Get all unique campus values
            $allDocs = $documentCollection->find([], ['limit' => 100]);
            $uniqueCampuses = [];
            foreach ($allDocs as $doc) {
                $campus = $doc['campus'] ?? null;
                if ($campus && !in_array($campus, $uniqueCampuses)) {
                    $uniqueCampuses[] = $campus;
                }
            }
            
            echo "<p><strong>Unique campus values found in database:</strong></p>";
            echo "<ul>";
            foreach ($uniqueCampuses as $campus) {
                $match = (strcasecmp($campus, $sheet_name) === 0) ? " ✅ MATCH" : "";
                echo "<li>" . htmlspecialchars($campus) . $match . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='success'>✅ Found $matchCount documents with matching campus</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error checking campus: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p class='warning'>⚠️ No campus set (not logged in or user has no campus)</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a> | <a href='test_cor_viewing.php'>🔄 Refresh Test</a></p>";
echo "</body></html>";
?>

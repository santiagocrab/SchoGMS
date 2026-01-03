<!DOCTYPE html>
<html>
<head>
    <title>Fix Coordinator Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .info {
            color: #17a2b8;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Fix Coordinator Account Status</h1>
        <p>This script will update all coordinator accounts to 'active' status.</p>
        <hr>
        
        <?php
        require_once 'conn_mongodb.php';
        
        echo "<h2>Processing...</h2>\n";
        echo "<pre>\n";
        
        // Find all coordinator accounts
        $coordinators = $users->find(['role' => 'coordinator']);
        
        $updated = 0;
        $alreadyActive = 0;
        $failed = 0;
        $results = [];
        
        foreach ($coordinators as $coordinator) {
            $userId = $coordinator['user_id'];
            $username = $coordinator['name'];
            $currentStatus = $coordinator['status'] ?? 'unknown';
            
            if ($currentStatus !== 'active') {
                // Update status to active
                try {
                    $result = $users->updateOne(
                        ['user_id' => $userId],
                        ['$set' => ['status' => 'active']]
                    );
                    
                    // Handle array return type (SimpleFastMongoDB returns array)
                    $modifiedCount = isset($result['modifiedCount']) ? $result['modifiedCount'] : 0;
                    
                    if ($modifiedCount > 0) {
                        echo "<span class='success'>✓ Updated: $username (ID: $userId) - Changed from '$currentStatus' to 'active'</span>\n";
                        $updated++;
                        $results[] = "✓ $username: $currentStatus → active";
                    } else {
                        echo "<span class='error'>✗ Failed to update: $username (ID: $userId)</span>\n";
                        $failed++;
                        $results[] = "✗ $username: Update failed";
                    }
                } catch (Exception $e) {
                    echo "<span class='error'>✗ Error updating $username: " . $e->getMessage() . "</span>\n";
                    $failed++;
                    $results[] = "✗ $username: " . $e->getMessage();
                }
            } else {
                echo "<span class='info'>- Already active: $username (ID: $userId)</span>\n";
                $alreadyActive++;
                $results[] = "- $username: Already active";
            }
        }
        
        echo "</pre>\n";
        echo "<hr>\n";
        echo "<h2>Summary</h2>\n";
        echo "<ul>\n";
        echo "<li><strong>Updated to active:</strong> <span class='success'>$updated</span></li>\n";
        echo "<li><strong>Already active:</strong> <span class='info'>$alreadyActive</span></li>\n";
        if ($failed > 0) {
            echo "<li><strong>Failed:</strong> <span class='error'>$failed</span></li>\n";
        }
        echo "<li><strong>Total coordinators:</strong> " . ($updated + $alreadyActive + $failed) . "</li>\n";
        echo "</ul>\n";
        
        if ($updated > 0) {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin-top: 20px;'>\n";
            echo "<strong class='success'>✓ Success!</strong> All coordinator accounts have been updated to 'active' status.<br>\n";
            echo "You can now log in with any coordinator account.\n";
            echo "</div>\n";
        } else if ($alreadyActive > 0) {
            echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin-top: 20px;'>\n";
            echo "<strong class='info'>ℹ Info:</strong> All coordinator accounts are already active.\n";
            echo "</div>\n";
        }
        
        echo "<hr>\n";
        echo "<p><a href='index.php'>← Back to Login</a></p>\n";
        ?>
    </div>
</body>
</html>


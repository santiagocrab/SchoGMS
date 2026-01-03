<?php
/**
 * Test MongoDB Login Functionality
 */

require_once 'conn_mongodb.php';

echo "<h2>MongoDB Login Test</h2>";

// Test user authentication
$testEmail = 'registrarisulan@mail';
$testPassword = 'schogms123';

echo "<h3>Testing User Authentication</h3>";
echo "Email: {$testEmail}<br>";
echo "Password: {$testPassword}<br><br>";

$user = $dbHelper->authenticateUser($testEmail, $testPassword);

if ($user) {
    echo "<div style='color: green;'>✅ Login successful!</div>";
    echo "<h4>User Details:</h4>";
    echo "<pre>" . print_r($user, true) . "</pre>";
} else {
    echo "<div style='color: red;'>❌ Login failed!</div>";
}

// Test admin authentication
echo "<h3>Testing Admin Authentication</h3>";
$adminUser = $dbHelper->authenticateAdmin('admin', 'admin123');

if ($adminUser) {
    echo "<div style='color: green;'>✅ Admin login successful!</div>";
    echo "<h4>Admin Details:</h4>";
    echo "<pre>" . print_r($adminUser, true) . "</pre>";
} else {
    echo "<div style='color: red;'>❌ Admin login failed!</div>";
}

// Test getting users by role
echo "<h3>Testing Get Users by Role</h3>";
$registrarUsers = $dbHelper->getUsersByRole('registrar');

echo "<h4>Registrar Users:</h4>";
foreach ($registrarUsers as $user) {
    echo "- {$user['name']} ({$user['email']})<br>";
}

// Test counting records
echo "<h3>Testing Record Counts</h3>";
$userCount = $dbHelper->countRecords('users');
$adminCount = $dbHelper->countRecords('admin');
$campusCount = $dbHelper->countRecords('campuses');

echo "Users: {$userCount}<br>";
echo "Admins: {$adminCount}<br>";
echo "Campuses: {$campusCount}<br>";

// Test search functionality
echo "<h3>Testing Search Functionality</h3>";
$searchResults = $dbHelper->searchRecords('users', 'registrar', ['name', 'email', 'role']);

echo "<h4>Search Results for 'registrar':</h4>";
foreach ($searchResults as $result) {
    echo "- {$result['name']} ({$result['email']}) - {$result['role']}<br>";
}

echo "<h3>MongoDB Migration Test Complete!</h3>";
echo "<p>If you can see this page and the tests above show successful results, the MongoDB migration is working correctly.</p>";
?>

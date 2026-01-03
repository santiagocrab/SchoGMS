<?php
// Include the database connection
include 'conn.php';

// Query to count total users
$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $conn->query($sql_users);

// Query to count users by role and status
$sql_users_role_status = "SELECT 
                    SUM(CASE WHEN role = 'coordinator' THEN 1 ELSE 0 END) AS total_coordinators,
                    SUM(CASE WHEN role = 'chairman' THEN 1 ELSE 0 END) AS total_chairman,
                    SUM(CASE WHEN role = 'registrar' THEN 1 ELSE 0 END) AS total_registrars,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS total_active_users,
                    SUM(CASE WHEN status = 'restricted' THEN 1 ELSE 0 END) AS total_restricted_users
                  FROM users";

$result_users_role_status = $conn->query($sql_users_role_status);

// Check if both queries were successful
if ($result_users && $result_users_role_status) {
    // Fetch the results
    $row_users = $result_users->fetch_assoc();
    $row_users_role_status = $result_users_role_status->fetch_assoc();

    // Store counts
    $total_users = $row_users['total_users'];
    $total_coordinators = $row_users_role_status['total_coordinators'];
    $total_chairman = $row_users_role_status['total_chairman'];
    $total_registrars = $row_users_role_status['total_registrars'];
    $total_active_users = $row_users_role_status['total_active_users'];
    $total_restricted_users = $row_users_role_status['total_restricted_users'];

    // Display output
    // echo "Total Users: " . $total_users . "<br>";
    // echo "Total Coordinators: " . $total_coordinators . "<br>";
    // echo "Total chairman: " . $total_chairman . "<br>";
    // echo "Total Registrars: " . $total_registrars . "<br>";
    // echo "Total Active Users: " . $total_active_users . "<br>";
    // echo "Total restricted Users: " . $total_restricted_users . "<br>";
} else {
    // Display error messages if queries fail
    echo "Error counting users: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
<?php
// Include the database connection
include 'conn.php';

// Query to count active and restricted users per month
$sql = "SELECT 
            DATE_FORMAT(created_at, '%b') AS month,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_users,
            SUM(CASE WHEN status = 'restrictive' THEN 1 ELSE 0 END) AS restricted_users
        FROM users
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY month
        ORDER BY MIN(created_at)";

$result = $conn->query($sql);

$labels = [];
$activeUsers = [];
$restrictedUsers = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['month']; 
        $activeUsers[] = $row['active_users'];
        $restrictedUsers[] = $row['restricted_users'];
    }
} else {
    echo "Error: " . $conn->error;
}

// Close connection
$conn->close();
?>

<?php
// Include database connection
include 'config/conn.php';

// Set response header to JSON
header('Content-Type: application/json');

$response = ["success" => false, "message" => "Invalid request."];

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $campusName = trim($_POST['campusName']); // Get the campus name

    // Validate input
    if (!empty($campusName)) {
        // Check the total number of campuses
        $countQuery = "SELECT COUNT(*) FROM campuses";
        $countResult = $conn->query($countQuery);
        $row = $countResult->fetch_array();
        $totalCampuses = $row[0];

        if ($totalCampuses >= 7) {
            $response = ["success" => false, "message" => "Campus limit reached! Maximum 7 campuses allowed."];
        } else {
            // Check if campus already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM campuses WHERE campus_name = ?");
            $stmt->bind_param("s", $campusName);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                $response = ["success" => false, "message" => "Campus already exists."];
            } else {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO campuses (campus_name) VALUES (?)");
                $stmt->bind_param("s", $campusName);

                if ($stmt->execute()) {
                    $response = ["success" => true, "message" => "Campus added successfully!"];
                } else {
                    $response = ["success" => false, "message" => "Error adding campus: " . $conn->error];
                }

                $stmt->close();
            }
        }
    } else {
        $response = ["success" => false, "message" => "Campus name cannot be empty."];
    }
}

// Close database connection
$conn->close();

// Output JSON response
echo json_encode($response);
?>

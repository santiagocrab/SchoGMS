<?php
echo "<h2>Basic PHP Test</h2>";
echo "<p>Request Method: " . $_SERVER['REQUEST_METHOD'] . "</p>";
echo "<p>POST Data: " . print_r($_POST, true) . "</p>";
echo "<p>GET Data: " . print_r($_GET, true) . "</p>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Request Received!</h3>";
    echo "<p>POST array is empty: " . (empty($_POST) ? 'YES' : 'NO') . "</p>";
} else {
    echo "<h3>Test Form</h3>";
    echo '<form method="post">';
    echo '<input type="text" name="test_field" value="test_value">';
    echo '<input type="submit" value="Submit">';
    echo '</form>';
}
?>


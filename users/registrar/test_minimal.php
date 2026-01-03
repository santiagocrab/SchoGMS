<?php
echo "<h2>Minimal Form Test</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    echo "<h3>FILES Data:</h3>";
    echo "<pre>" . print_r($_FILES, true) . "</pre>";
} else {
    echo '<form method="post" enctype="multipart/form-data">';
    echo '<p>Academic Year: <select name="academic_year" required>';
    echo '<option value="2024-2025" selected>2024-2025</option>';
    echo '<option value="2023-2024">2023-2024</option>';
    echo '</select></p>';
    echo '<p>Semester: <select name="semester" required>';
    echo '<option value="1st Semester" selected>1st Semester</option>';
    echo '<option value="2nd Semester">2nd Semester</option>';
    echo '</select></p>';
    echo '<p>File: <input type="file" name="fileUpload[]" multiple></p>';
    echo '<p><input type="submit" value="Submit"></p>';
    echo '</form>';
}
?>


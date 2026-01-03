<?php
echo "<h2>PHP Upload Limits Check</h2>";

echo "<h3>Current PHP Settings:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Setting</th><th>Value</th><th>Description</th></tr>";

$settings = [
    'upload_max_filesize' => 'Maximum size of uploaded files',
    'post_max_size' => 'Maximum size of POST data',
    'max_file_uploads' => 'Maximum number of files that can be uploaded',
    'max_input_vars' => 'Maximum number of input variables',
    'max_execution_time' => 'Maximum execution time (seconds)',
    'memory_limit' => 'Memory limit for scripts'
];

foreach ($settings as $setting => $description) {
    $value = ini_get($setting);
    echo "<tr>";
    echo "<td><strong>$setting</strong></td>";
    echo "<td>$value</td>";
    echo "<td>$description</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>Recommendations for Large Uploads:</h3>";
echo "<ul>";
echo "<li><strong>max_file_uploads</strong>: Currently " . ini_get('max_file_uploads') . " - This limits how many files can be uploaded at once</li>";
echo "<li><strong>post_max_size</strong>: Currently " . ini_get('post_max_size') . " - This limits total POST data size</li>";
echo "<li><strong>upload_max_filesize</strong>: Currently " . ini_get('upload_max_filesize') . " - This limits individual file size</li>";
echo "</ul>";

echo "<h3>Solution for 3000+ Files:</h3>";
echo "<p>For uploading 3000+ files, we need to:</p>";
echo "<ol>";
echo "<li>Increase max_file_uploads to handle more files</li>";
echo "<li>Implement batch processing (upload in chunks)</li>";
echo "<li>Use AJAX for progress tracking</li>";
echo "<li>Handle timeouts and memory limits</li>";
echo "</ol>";

echo "<p><a href='cor-cog.php'>← Back to COR Upload</a></p>";
?>

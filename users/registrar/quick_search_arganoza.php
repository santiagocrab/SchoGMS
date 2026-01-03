<?php
include 'config/session.php';

echo "<h3>🔍 QUICK SEARCH: ARGAÑOZA, ANGELINE SACAY</h3>";

// Quick search in all collections
$searchName = "ARGAÑOZA";
$searchVariations = ["ARGAÑOZA", "ARGA?OZA", "ANGELINE", "SACAY"];

echo "<h4>📋 Quick Results:</h4>";

// Check database documents
try {
    $documentCollection = $mongodb->collection('document_uploads');
    $allDocs = $documentCollection->find([]);
    
    $foundDocs = [];
    foreach ($allDocs as $doc) {
        $originalName = $doc['original_name'] ?? '';
        $searchableText = strtolower($originalName . ' ' . str_replace('?', 'Ñ', $originalName));
        
        foreach ($searchVariations as $variation) {
            if (stripos($searchableText, strtolower($variation)) !== false) {
                $foundDocs[] = $originalName;
                break;
            }
        }
    }
    
    if (!empty($foundDocs)) {
        echo "<div class='alert alert-success'>";
        echo "<h5>✅ Found Documents:</h5>";
        echo "<ul>";
        foreach ($foundDocs as $doc) {
            echo "<li>" . htmlspecialchars($doc) . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-warning'>❌ No documents found in database</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Check masterlist
try {
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $allStudents = $registrarCollection->find([]);
    
    $foundStudents = [];
    foreach ($allStudents as $student) {
        $lastName = $student['last_name'] ?? '';
        $firstName = $student['first_name'] ?? '';
        $middleName = $student['middle_name'] ?? '';
        
        $fullName = $lastName . ', ' . $firstName . ' ' . $middleName;
        $searchableText = strtolower($fullName . ' ' . str_replace('?', 'Ñ', $fullName));
        
        foreach ($searchVariations as $variation) {
            if (stripos($searchableText, strtolower($variation)) !== false) {
                $foundStudents[] = [
                    'id' => $student['id_number'] ?? '',
                    'name' => $fullName,
                    'course' => $student['course'] ?? ''
                ];
                break;
            }
        }
    }
    
    if (!empty($foundStudents)) {
        echo "<div class='alert alert-info'>";
        echo "<h5>✅ Found Students:</h5>";
        echo "<ul>";
        foreach ($foundStudents as $student) {
            echo "<li><strong>" . htmlspecialchars($student['name']) . "</strong> (ID: " . htmlspecialchars($student['id']) . ", Course: " . htmlspecialchars($student['course']) . ")</li>";
        }
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-warning'>❌ No students found in masterlist</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Masterlist Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Check filesystem
$directories = ['uploads/COR/', 'uploads/COG/'];
$foundFiles = [];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && is_file($dir . $file)) {
                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $searchableText = strtolower($fileName . ' ' . str_replace('?', 'Ñ', $fileName));
                
                foreach ($searchVariations as $variation) {
                    if (stripos($searchableText, strtolower($variation)) !== false) {
                        $foundFiles[] = $dir . $file;
                        break;
                    }
                }
            }
        }
    }
}

if (!empty($foundFiles)) {
    echo "<div class='alert alert-success'>";
    echo "<h5>✅ Found Files:</h5>";
    echo "<ul>";
    foreach ($foundFiles as $file) {
        echo "<li><a href='view_document.php?file=" . urlencode($file) . "&type=COR' target='_blank'>" . htmlspecialchars($file) . "</a></li>";
    }
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div class='alert alert-warning'>❌ No files found in filesystem</div>";
}

?>

<div class="row mt-4">
    <div class="col-md-4">
        <a href="find_arganoza_cor.php" class="btn btn-primary btn-block">🔍 Detailed Search</a>
    </div>
    <div class="col-md-4">
        <a href="cor-cog.php" class="btn btn-info btn-block">📤 COR Interface</a>
    </div>
    <div class="col-md-4">
        <a href="masterlist.php" class="btn btn-success btn-block">📋 Masterlist</a>
    </div>
</div>













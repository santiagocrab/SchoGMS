<?php
include 'config/session.php';

// Check if user is chairman
if ($role !== 'chairman') {
    header("Location: index.php?error=access_denied");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $academic_year = trim($_POST['academic_year']);
    $semester = trim($_POST['semester']);
    $file_group = trim($_POST['file_group']);
    
    // Validate required fields
    if (empty($academic_year) || empty($semester) || empty($file_group)) {
        header("Location: upload_ched_tdp.php?error=missing_fields");
        exit();
    }
    
    // Check if file was uploaded
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        header("Location: upload_ched_tdp.php?error=file_upload_failed");
        exit();
    }
    
    $file = $_FILES['excel_file'];
    
    // Validate file type
    $allowed_types = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
        'application/vnd.ms-excel' // .xls
    ];
    
    if (!in_array($file['type'], $allowed_types)) {
        header("Location: upload_ched_tdp.php?error=invalid_file_type");
        exit();
    }
    
    // Validate file size (10MB limit)
    if ($file['size'] > 10 * 1024 * 1024) {
        header("Location: upload_ched_tdp.php?error=file_too_large");
        exit();
    }
    
    // Create upload directory if it doesn't exist
    $upload_dir = '../../uploads/ched_tdp/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            header("Location: upload_ched_tdp.php?error=file_move_failed&details=" . urlencode("Failed to create upload directory"));
            exit();
        }
    }
    
    // Check if directory is writable
    if (!is_writable($upload_dir)) {
        // Try to fix permissions
        chmod($upload_dir, 0777);
        if (!is_writable($upload_dir)) {
            header("Location: upload_ched_tdp.php?error=file_move_failed&details=" . urlencode("Upload directory is not writable. Please contact administrator."));
            exit();
        }
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_filename = $file_group . '_' . date('Y-m-d_H-i-s') . '.' . $file_extension;
    $upload_path = $upload_dir . $unique_filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        try {
            // Include PhpSpreadsheet
            require_once '../vendor/autoload.php';
            
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($upload_path);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            
            // Remove header row
            array_shift($data);
            
            $record_count = 0;
            $success_count = 0;
            $error_count = 0;
            $errors = [];
            
            // Process each row
            foreach ($data as $row) {
                if (empty(array_filter($row))) continue; // Skip empty rows
                
                $record_count++;
                
                // Debug: Log the first few rows to see the structure
                if ($record_count <= 3) {
                    error_log("Row $record_count data: " . print_r($row, true));
                }
                
                // Extract data based on your actual Excel structure
                // SEQ, APPNO, AWARD NO, LASTNAME, FIRSTNAME, EXTNAME, MIDDLE NAME, SEX, BIRTHDATE, COURSE/PROGRAM ENROLLED, YEAR LEVEL, etc.
                $seq = $row[0] ?? '';
                $app_no = $row[1] ?? '';
                $award_no = $row[2] ?? '';
                $last_name = $row[3] ?? '';
                $first_name = $row[4] ?? '';
                $ext_name = $row[5] ?? '';
                $middle_name = $row[6] ?? '';
                $sex = $row[7] ?? '';
                $birthdate = $row[8] ?? '';
                $course = $row[9] ?? '';
                $year_level = $row[10] ?? '';
                $total_units = $row[11] ?? '';
                $municipality = $row[12] ?? '';
                $province = $row[13] ?? '';
                $pwd_classification = $row[14] ?? '';
                $grant = $row[15] ?? '';
                $batch_no = $row[16] ?? '';
                $validation_status = $row[17] ?? '';
                $remarks = $row[18] ?? '';
                
                // Use APP NO as student_id if available, otherwise use SEQ
                $student_id = !empty($app_no) ? $app_no : $seq;
                
                // Validate required fields
                if (empty($last_name) || empty($first_name)) {
                    $error_count++;
                    $errors[] = "Row $record_count: Missing required fields (Last Name or First Name)";
                    continue;
                }
                
                // Insert into MongoDB collection
                $document = [
                    'seq' => $seq,
                    'app_no' => $app_no,
                    'award_no' => $award_no,
                    'lastname' => $last_name,
                    'firstname' => $first_name,
                    'ext_name' => $ext_name,
                    'middle_name' => $middle_name,
                    'sex' => $sex,
                    'birthdate' => $birthdate,
                    'course' => $course,
                    'year_level' => $year_level,
                    'total_units' => $total_units,
                    'municipality' => $municipality,
                    'province' => $province,
                    'pwd_classification' => $pwd_classification,
                    'grant_amount' => $grant,
                    'batch_no' => $batch_no,
                    'validation_status' => $validation_status,
                    'remarks' => $remarks,
                    'academic_year' => $academic_year,
                    'semester' => $semester,
                    'file_group' => $file_group,
                    'filename' => $unique_filename,
                    'sheet_name' => 'Sheet1', // Default sheet name
                    'uploaded_by' => $fullname,
                    'upload_date' => new MongoDB\BSON\UTCDateTime()
                ];
                
                try {
                    $result = $ched_masterlist->insertOne($document);
                    if ($result->getInsertedId()) {
                        $success_count++;
                    } else {
                        $error_count++;
                        $errors[] = "Row $record_count: Failed to insert document";
                    }
                } catch (Exception $e) {
                    $error_count++;
                    $errors[] = "Row $record_count: Database error - " . $e->getMessage();
                    error_log("Failed to insert row $record_count: " . print_r($row, true));
                }
            }
            
            // Log upload information to MongoDB
            $log_document = [
                'file_group' => $file_group,
                'academic_year' => $academic_year,
                'semester' => $semester,
                'file_path' => $upload_path,
                'record_count' => $record_count,
                'success_count' => $success_count,
                'error_count' => $error_count,
                'uploaded_by' => $fullname,
                'upload_date' => new MongoDB\BSON\UTCDateTime()
            ];
            
            try {
                $ched_upload_log = $mongodb->collection('ched_upload_log');
                $log_result = $ched_upload_log->insertOne($log_document);
            } catch (Exception $e) {
                error_log("Failed to log upload: " . $e->getMessage());
            }
            
            // Redirect with success message
            $message = "Upload completed! Records processed: $record_count, Success: $success_count, Errors: $error_count";
            if (!empty($errors)) {
                $message .= " Errors: " . implode("; ", array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= "... and " . (count($errors) - 5) . " more errors";
                }
            }
            
            header("Location: upload_ched_tdp.php?success=" . urlencode($message));
            exit();
            
        } catch (Exception $e) {
            // Delete uploaded file on error
            if (file_exists($upload_path)) {
                unlink($upload_path);
            }
            
            header("Location: upload_ched_tdp.php?error=processing_failed&details=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: upload_ched_tdp.php?error=file_move_failed");
        exit();
    }
} else {
    header("Location: upload_ched_tdp.php");
    exit();
}
?>

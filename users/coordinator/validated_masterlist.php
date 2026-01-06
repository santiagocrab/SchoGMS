<?php
require '../config/conn.php';
require '../vendor/autoload.php';  // Ensure PhpSpreadsheet is installed via Composer

// Get filters from GET request
$sheet_name = isset($_GET['sheet_name']) ? $_GET['sheet_name'] : '';

// Query to retrieve data from both ched_masterlist and registrar_master_list
// $query = "SELECT 
//             cm.id, cm.filename, cm.file_group, cm.seq, cm.app_no, cm.award_no, cm.lastname, cm.firstname, cm.extname, 
//             cm.middlename, cm.sex, cm.birthdate, cm.course_program_enrolled, cm.year_level, cm.total_units_enrolled, 
//             cm.status_of_enrollment, cm.remarks, cm.upload_time,
//             rm.id_number, rm.zip_code, rm.email_address, rm.mobile_number
//           FROM ched_masterlist cm
//           LEFT JOIN registrar_master_list rm 
//           ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
//           AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
//           AND cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci
//           WHERE cm.file_group = '" . $conn->real_escape_string($file_group) . "'";
$query = "SELECT 
    cm.id, cm.filename, cm.sheet_name, cm.seq, cm.app_no, cm.award_no, cm.lastname, cm.firstname, cm.extname, 
    cm.middlename, cm.sex, cm.birthdate, cm.course_program_enrolled, cm.year_level, cm.total_units_enrolled, 
    cm.status_of_enrollment, cm.remarks, cm.upload_time,
    rm.id_number, rm.enrolled, rm.zip_code, rm.email_address, rm.mobile_number
FROM ched_masterlist cm
LEFT JOIN registrar_master_list rm
    ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
    AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
    AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
         OR cm.middlename IS NULL 
         OR rm.middle_name IS NULL 
         OR cm.middlename = '' 
         OR rm.middle_name = '')  
WHERE cm.sheet_name = '" . $conn->real_escape_string($sheet_name) . "'
ORDER BY cm.sheet_name ASC, cm.id ASC;

";


// Execute the query
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}


// If export button is clicked
if (isset($_POST['export'])) {
    // Load existing Excel file
    $inputFileName = 'anex/Annex 7 TDP New Form.xlsx';
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);

    // Select Sheet 3 (Index starts from 0, so Sheet 3 is index 2) for data with id_number
    $sheet3 = $spreadsheet->getSheet(2); // getSheet() instead of getActiveSheet()

    // Start inserting data from Row 33 in Sheet 3
    $row_num3 = 33;
    $control_number3 = 1;

    while ($row = $result->fetch_assoc()) {
        // Ensure only records with an id_number are inserted
        if (!empty($row['id_number'])) {
            // Apply styles to Sheet 3
            for ($col = 'A'; $col <= 'Q'; $col++) {
                $cell = $sheet3->getCell($col . $row_num3);
                $cell->getStyle()->getAlignment()->setWrapText(true);
            }

            // Insert new row in Sheet 3
            $sheet3->insertNewRowBefore($row_num3, 1);

            // Populate data, ensuring numbers remain as text
            $sheet3->setCellValue('A' . $row_num3, str_pad($control_number3++, 5, '0', STR_PAD_LEFT));
            $sheet3->setCellValue('B' . $row_num3, $row['id_number']);
            $sheet3->setCellValue('C' . $row_num3, $row['award_no']);
            $sheet3->setCellValue('D' . $row_num3, $row['lastname']);
            $sheet3->setCellValue('E' . $row_num3, $row['firstname']);
            $sheet3->setCellValue('F' . $row_num3, $row['middlename']);
            $sheet3->setCellValue('G' . $row_num3, $row['sex']);
            $sheet3->setCellValue('H' . $row_num3, $row['birthdate']);
            $sheet3->setCellValue('I' . $row_num3, $row['course_program_enrolled']);
            $sheet3->setCellValue('J' . $row_num3, $row['year_level']);
            // Column K removed (Total Units Enrolled) - now empty
            $sheet3->setCellValue('K' . $row_num3, '');
            $sheet3->setCellValue('L' . $row_num3, $row['zip_code']);
            // Column M: Student Email Address (from Registrar)
            $sheet3->setCellValue('M' . $row_num3, $row['email_address'] ?? '');
            // Column N: Student Semester
            $sheet3->setCellValue('N' . $row_num3, $row['registrar_semester'] ?? $row['student_semester'] ?? '');

            // Additional columns
            $sheet3->setCellValue('O' . $row_num3, '');
            $sheet3->setCellValue('P' . $row_num3, '');
            $sheet3->setCellValue('Q' . $row_num3, '');

            // Move to next row
            $row_num3++;
        }
    }

    // Remove template empty rows
    $sheet3->removeRow(32, 1);

    // Write the file to the browser as an Excel file
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Annex_7_TDP_New_Form.xlsx"');
    header('Cache-Control: max-age=0');

    // Save the file to php://output
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');

    exit;
}


?>



<?php
// Error logging setup
ini_set('log_errors', 1);  // Enable error logging
error_reporting(E_ALL);
ini_set('error_log', 'remark_error.txt');  // Path to the error log file

require '../config/conn.php';
require '../vendor/autoload.php';  // Ensure PhpSpreadsheet is installed via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// SMTP Configuration
$smtp_host = 'smtp.hostinger.com';
$smtp_username = 'server-email@cloudhost.host';
$smtp_password = 'Schogms_2025';
$smtp_port = 465;
$smtp_secure = PHPMailer::ENCRYPTION_SMTPS;

// Get filters from GET request
$sheet_name = isset($_GET['sheet_name']) ? $_GET['sheet_name'] : '';

$query = "SELECT 
    cm.id, cm.filename, cm.sheet_name, cm.seq, cm.app_no, cm.award_no, cm.lastname, cm.firstname, cm.extname, 
    cm.middlename, cm.sex, cm.birthdate, cm.course_program_enrolled, cm.year_level, cm.total_units_enrolled, 
    cm.status_of_enrollment, cm.remarks, cm.upload_time,
    rm.id_number, rm.enrolled, rm.zip_code, rm.email_address, rm.mobile_number
FROM ched_masterlist cm
LEFT JOIN registrar_master_list rm
    ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
    AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
    AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
         OR cm.middlename IS NULL 
         OR rm.middle_name IS NULL 
         OR cm.middlename = '' 
         OR rm.middle_name = '')  
WHERE cm.sheet_name = '" . $conn->real_escape_string($sheet_name) . "'
ORDER BY cm.sheet_name ASC, cm.id ASC;

";

// Execute the query
$result = $conn->query($query);
if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Database query failed: ' . $conn->error
    ]);
    exit;
}

// Determine Enrollment Status   
$control_number3 = 1;
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Load existing Excel file
    $inputFileName = 'anex/Annex 7 TDP New Form.xlsx';
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
        $sheet3 = $spreadsheet->getSheet(2); // getSheet() instead of getActiveSheet()
    try {
    // Start inserting data from Row 33 in Sheet 3
    $row_num3 = 33;
 

        while ($row = $result->fetch_assoc()) {
        // Ensure only records with an id_number are inserted
            if (!empty($row['id_number'])) {
                // Apply styles to Sheet 3
                for ($col = 'A'; $col <= 'Q'; $col++) {
                    $cell = $sheet3->getCell($col . $row_num3);
                    $cell->getStyle()->getAlignment()->setWrapText(true);
                }
    
                // Insert new row in Sheet 3
                $sheet3->insertNewRowBefore($row_num3, 1);
    
                // Populate data, ensuring numbers remain as text
                $sheet3->setCellValue('A' . $row_num3, str_pad($control_number3++, 5, '0', STR_PAD_LEFT));
                $sheet3->setCellValue('B' . $row_num3, $row['id_number']);
                $sheet3->setCellValue('C' . $row_num3, $row['award_no']);
                $sheet3->setCellValue('D' . $row_num3, $row['lastname']);
                $sheet3->setCellValue('E' . $row_num3, $row['firstname']);
                $sheet3->setCellValue('F' . $row_num3, $row['middlename']);
                $sheet3->setCellValue('G' . $row_num3, $row['sex']);
                $sheet3->setCellValue('H' . $row_num3, $row['birthdate']);
            $sheet3->setCellValue('I' . $row_num3, $row['course_program_enrolled']);
            $sheet3->setCellValue('J' . $row_num3, $row['year_level']);
            // Column K removed (Total Units Enrolled) - now empty
            $sheet3->setCellValue('K' . $row_num3, '');
            $sheet3->setCellValue('L' . $row_num3, $row['zip_code']);
            // Column M: Student Email Address (from Registrar)
            $sheet3->setCellValue('M' . $row_num3, $row['email_address'] ?? '');
            // Column N: Student Semester
            $sheet3->setCellValue('N' . $row_num3, $row['registrar_semester'] ?? $row['student_semester'] ?? '');
    
                // Additional columns
                $sheet3->setCellValue('O' . $row_num3, '');
                $sheet3->setCellValue('P' . $row_num3, '');
                $sheet3->setCellValue('Q' . $row_num3, '');
    
                // Move to next row
                $row_num3++;
            }
        }

         $sheet3->removeRow(32, 1);

        // Save file
        $outputFile = 'materlist_remarks/' . $sheet_name . '_exported.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputFile);

        // Email sending
        $sql = "SELECT email FROM users WHERE role = 'chairman' LIMIT 1";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        $chairmanEmail = $user['email'];

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port = $smtp_port;
// fremegio_230000000175@uic.edu.ph
        $mail->setFrom($smtp_username, 'SchoGMS Export Notification');
        $mail->addAddress($chairmanEmail);
        $mail->addReplyTo($smtp_username, 'SchoGMS Support');
        $mail->isHTML(true);
        $mail->Subject = 'Your Export Request Has Been Processed';
        $mail->Body = "
            <html>
            <head>
                        <style>
                            body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                            .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
                            .header { text-align: center; padding: 10px; background: #5f76e8; color: white; border-radius: 5px; }
                            .message { padding: 15px; font-size: 16px; line-height: 1.6; color: #333333; text-align: center; }
                            .footer { text-align: center; font-size: 14px; color: #777777; margin-top: 20px; }
                        </style>
                    </head>
            <body>
            <div style='font-family: Arial, sans-serif;'>
                <h2 style='background:#5f76e8;color:white;padding:10px;'>SchoGMS Export Request</h2>
                <p>Dear Chairman,</p>
                <p>The export request for the sheet <strong>{$sheet_name}</strong> has been successfully processed.</p>
                <p>Please find the exported file attached.</p>
                <p>Best regards,<br>SchoGMS Team</p>
            </div>
            </body>
            </html>";
        $mail->addAttachment($outputFile);
        $mail->send();
        
        echo json_encode(['success' => true, 'message' => 'Export and email sent successfully.']);
        exit;

    } catch (Exception $e) {
       echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtered Data</title>
    <link href="../../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="../../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="../../assets/extra-libs/jvector/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 10px;
        }

        table {
            width: 90%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        #export-btn {
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        #export-btn:hover {
            background-color: #45a049;
        }
        #export-btn2 {
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        #export-btn2:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <?php include 'loading-screen.php'; ?>

    <h2>Filtered Data Results</h2>

    <!-- Export to Excel Button -->
   <!-- Include SweetAlert -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Export Form -->
<form method="POST">
    <button type="submit" name="export" id="export-btn">Export to Excel</button>
</form>

<script>
    document.getElementById("export-btn").addEventListener("click", function (event) {
        // event.preventDefault(); // Prevents immediate form submission

        // Submit the form first
        event.target.closest("form").submit();

        // Show SweetAlert message after submitting the form
        Swal.fire({
            title: "Export Started",
            text: "Your file is being prepared. The download starting please wait shortly.",
            icon: "info",
            allowOutsideClick: false,
            confirmButtonText: "OK"
        });
    });
</script>

<form method="POST" id="export-form">
  <button id="export-btn2" name="export_data">Send Masterlist as Email</button>
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Listen for form submission
  document.getElementById('export-form').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent the default form submission

    Swal.fire({
      title: 'Processing...',
      text: 'Please wait while we export and send the email...',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    // Use FormData to gather all form data automatically
    var formData = new FormData(this);

    // Use fetch to send the form data
    fetch('', {
      method: 'POST',
      body: formData
    })
    .then(async response => {
      const text = await response.text();
      try {
        const data = JSON.parse(text); // Parse JSON from server response
        console.log('Parsed JSON:', data);
        if (data.success === true) {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: data.message
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'Unknown error.'
          });
        }
      } catch (e) {
        console.error('Response is not valid JSON:', text);
        Swal.fire({
          icon: 'error',
          title: 'Server Error',
          text: 'The server did not return valid JSON.'
        });
      }
    })
    .catch(error => {
      console.error('Fetch error:', error);
      Swal.fire({
        icon: 'error',
        title: 'Network Error',
        text: 'Something went wrong while connecting to the server.'
      });
    });
  });
</script>


    <table id="zero_config" class="table table-striped table-bordered no-wrap">
        <thead>
            <tr>
                <th>5-digit Control Number</th>
                <th>Student Number</th>
                <th>TDP Award Number</th>
                <th>Last Name</th>
                <th>Given Name</th>
                <th>Middle Initial</th>
                <th>Sex at Birth (M/F)</th>
                <th>Birthdate (mm/dd/yyyy)</th>
                <th>Degree Program</th>
                <th>Year Level</th>
                <th>Total Academic Units Enrolled (credit and non-credit courses)</th>
                <th>ZIP Code</th>
                <th>E-mail Address</th>
                <th>Phone Number</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo str_pad($control_number3++, 5, '0', STR_PAD_LEFT); ?></td> <!-- 5-digit Control Number -->
                    <td><?php echo htmlspecialchars($row['id_number']); ?></td> <!-- Student Number -->
                    <td><?php echo htmlspecialchars($row['award_no']); ?></td> <!-- TDP Award Number -->
                    <td><?php echo htmlspecialchars($row['lastname']); ?></td> <!-- Last Name -->
                    <td><?php echo htmlspecialchars($row['firstname']); ?></td> <!-- Given Name -->
                    <td><?php echo htmlspecialchars($row['middlename']); ?></td> <!-- Middle Initial -->
                    <td><?php echo htmlspecialchars($row['sex']); ?></td> <!-- Sex at Birth (M/F) -->
                    <td><?php echo htmlspecialchars($row['birthdate']); ?></td> <!-- Birthdate -->
                    <td><?php echo htmlspecialchars($row['course_program_enrolled']); ?></td> <!-- Degree Program -->
                    <td><?php echo htmlspecialchars($row['year_level']); ?></td> <!-- Year Level -->
                    <td><?php echo htmlspecialchars($row['enrolled']); ?></td> <!-- Total Units Enrolled -->
                    <td><?php echo htmlspecialchars($row['zip_code']); ?></td> <!-- ZIP Code -->
                    <td><?php echo htmlspecialchars($row['email_address']); ?></td> <!-- E-mail Address -->
                    <td><?php echo htmlspecialchars($row['mobile_number']); ?></td> <!-- Phone Number -->
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script src="../../assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../../dist/js/pages/datatable/datatable-basic.init.js"></script>
    
</body>

</html>
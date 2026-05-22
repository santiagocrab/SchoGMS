<?php
require '../config/conn.php';
require '../vendor/autoload.php';  // Ensure PhpSpreadsheet is installed via Composer

// Get filters from GET request
require_once __DIR__ . '/inc/validation_export.php';
$program = strtolower((string) ($_GET['program'] ?? 'tes'));
$sheet_name = trim((string) ($_GET['sheet_name'] ?? ''));
$exportRows = schogms_validation_export_rows($conn, $program, $sheet_name, $_GET);

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
                                        cm.*, 
                                        rm.id_number, rm.enrolled, rm.zip_code, rm.email_address, rm.mobile_number, rm.date_of_birth as birthdate,

                                        -- Get categories (COR & COG) uploaded for each student
                                        GROUP_CONCAT(DISTINCT du.category ORDER BY du.category SEPARATOR ', ') AS uploaded_categories,
                                        GROUP_CONCAT(DISTINCT du.file_name ORDER BY du.file_name SEPARATOR ', ') AS uploaded_files,

                                        -- Determine enrollment status: Enrolled if both COR & COG exist
                                        CASE 
                                            WHEN SUM(CASE WHEN du.category = 'COR' THEN 1 ELSE 0 END) > 0 
                                            THEN 'Enrolled'
                                            ELSE 'Not Enrolled'
                                        END AS enrollment_status

                                    FROM ched_masterlist_tes cm

                                    -- LEFT JOIN with registrar_master_list to get enrolled status and other details
                                    LEFT JOIN registrar_master_list rm
                                        ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
                                        AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
                                        AND cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci

                                    -- LEFT JOIN with document_uploads based on student name AND campus restriction
                                    LEFT JOIN document_uploads du 
                                        ON du.file_name LIKE CONCAT(cm.lastname, ', ', cm.firstname, ' ', cm.middlename, '%')
                                        AND du.campus = '" . $conn->real_escape_string($sheet_name) . "'

                                    WHERE cm.campus = '" . $conn->real_escape_string($sheet_name) . "'

                                    GROUP BY cm.id, cm.lastname, cm.firstname, cm.middlename, rm.id_number, rm.enrolled, rm.zip_code, rm.email_address, rm.mobile_number

                                    ORDER BY cm.campus ASC, cm.id ASC;

";

// Execute the query
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}
// Determine Enrollment Status


$control_number = 1; // Starting control number
if (isset($_POST['export_data'])) {
    // Load existing Excel file
    $inputFileName = 'anex/SKSU_TES_LISTAHAN.xlsx';  // Path to the existing file
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
    $sheet = $spreadsheet->getActiveSheet();

    // Start updating from row 5
    $row_num = 3;

    foreach ($exportRows as $row) {
        $check = $row['_check'] ?? schogms_validation_row_check($row, [], $program);

        // Apply styles to L, M, N, O columns
        for ($col = 'A'; $col <= 'P'; $col++) {

            $cell = $sheet->getCell($col . $row_num);
            $style = $cell->getStyle();
            $style->getAlignment()->setWrapText(true);  // Enable text wrapping
            $style->getFont()->setSize(11);  // Set font size
            $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);  // Center horizontally
            $style->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);  // Center vertically
            $style->getFont()->setBold(false);  // Remove bold style
            $style->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT); // Ensure text format
        }

        // Determine Enrollment Status
        $enrollment_status = $check['enrollment'];

        // Determine COR & COG Submission Status
        $hasCOR = $check['has_cor'];
        $hasCOG = $check['has_cog'];

        if ($hasCOR && $hasCOG) {
            $cor_cog_status = "COR & COG Submitted";
        } elseif ($hasCOR) {
            $cor_cog_status = "Only COR Submitted";
        } elseif ($hasCOG) {
            $cor_cog_status = "Only COG Submitted";
        } else {
            $cor_cog_status = "Not Submitted";
        }
        $sheet->insertNewRowBefore($row_num, 2);
        // Now update values into Excel Sheet (L, M, N, O)
        $sheet->setCellValueExplicit('A' . $row_num, $row['seq'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B' . $row_num, $row['app_no'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('C' . $row_num, $row['lastname'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('D' . $row_num, $row['firstname'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('E' . $row_num, $row['extname'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('F' . $row_num, $row['middlename'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('G' . $row_num, $row['sex'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('H' . $row_num, $row['course_program_enrolled'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('I' . $row_num, $row['year_level'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('J' . $row_num, $row['street'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('K' . $row_num, $row['town_city'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);  // Column M - COR & COG Submission Status
        $sheet->setCellValueExplicit('L' . $row_num, $row['contact'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);  // Column M - COR & COG Submission Status
        $sheet->setCellValueExplicit('M' . $row_num, $row['batch_no'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);  // Column M - COR & COG Submission Status
        $sheet->setCellValueExplicit('N' . $row_num, $cor_cog_status, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);  // Column M - COR & COG Submission Status
        $sheet->setCellValueExplicit('O' . $row_num, $enrollment_status, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);  // Column N - Enrollment Status
        $sheet->setCellValueExplicit('P' . $row_num, '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING); // Empty column O
        // Move to the next row without inserting new ones
        $row_num++;
    }

    $sheet->removeRow(2, 1);
    // Write the file to the browser as an Excel file
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="SKSU_TES_LISTAHAN_' . $sheet_name . '.xlsx"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1'); // Ensure cache control for modern browsers

    // Save the file to php://output
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');

    // JavaScript to close the window and refresh the parent page
    echo "<script>
            window.close();  // Close this window
            window.opener.location.reload();  // Reload the parent window
          </script>";
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

require_once __DIR__ . '/../../config/mail.php';

// Get filters from GET request
$sheet_name = isset($_GET['sheet_name']) ? $_GET['sheet_name'] : '';

$query = "SELECT 
                                        cm.*, 
                                        rm.id_number, rm.enrolled, rm.zip_code, rm.email_address, rm.mobile_number,

                                        -- Get categories (COR & COG) uploaded for each student
                                        GROUP_CONCAT(DISTINCT du.category ORDER BY du.category SEPARATOR ', ') AS uploaded_categories,
                                        GROUP_CONCAT(DISTINCT du.file_name ORDER BY du.file_name SEPARATOR ', ') AS uploaded_files,

                                        -- Determine enrollment status: Enrolled if both COR & COG exist
                                        CASE 
                                            WHEN SUM(CASE WHEN du.category = 'COR' THEN 1 ELSE 0 END) > 0 
                                            AND SUM(CASE WHEN du.category = 'COG' THEN 1 ELSE 0 END) > 0 
                                            THEN 'Enrolled'
                                            ELSE 'Not Enrolled'
                                        END AS enrollment_status

                                    FROM ched_masterlist cm

                                    -- LEFT JOIN with registrar_master_list to get enrolled status and other details
                                    LEFT JOIN registrar_master_list rm
                                        ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
                                        AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
                                        AND cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci

                                    -- LEFT JOIN with document_uploads based on student name AND campus restriction
                                    LEFT JOIN document_uploads du 
                                        ON du.file_name LIKE CONCAT(cm.lastname, ', ', cm.firstname, ' ', cm.middlename, '%')
                                        AND du.campus = '" . $conn->real_escape_string($sheet_name) . "'

                                    WHERE cm.sheet_name = '" . $conn->real_escape_string($sheet_name) . "'

                                    GROUP BY cm.id, cm.lastname, cm.firstname, cm.middlename, rm.id_number, rm.enrolled, rm.zip_code, rm.email_address, rm.mobile_number

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
$control_number = 1; // Starting control number
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Load existing Excel file
    $inputFileName = 'anex/SKSU_TES_LISTAHAN.xlsx';  // Path to the existing file
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
    $sheet = $spreadsheet->getActiveSheet();
    try {
        $row_num = 3;

        while ($row = $result->fetch_assoc()) {
            for ($col = 'A'; $col <= 'P'; $col++) {
                $cell = $sheet->getCell($col . $row_num);
                $style = $cell->getStyle();
                $style->getAlignment()->setWrapText(true);
                $style->getFont()->setSize(11);
                $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $style->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $style->getFont()->setBold(false);
                $style->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            }

            $enrollment_status = ($row['enrollment_status'] == 'Enrolled') ? 'Enrolled' : 'Not Enrolled';
            $hasCOR = strpos($row['uploaded_categories'], 'COR') !== false;
            $hasCOG = strpos($row['uploaded_categories'], 'COG') !== false;

            $cor_cog_status = $hasCOR && $hasCOG ? "COR & COG Submitted" : ($hasCOR ? "Only COR Submitted" : ($hasCOG ? "Only COG Submitted" : "Not Submitted"));

            $sheet->insertNewRowBefore($row_num, 2);
            $sheet->setCellValueExplicit('A' . $row_num, $row['seq'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('B' . $row_num, $row['app_no'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row_num, $row['lastname'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $row_num, $row['firstname'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E' . $row_num, $row['extname'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('F' . $row_num, $row['middlename'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('G' . $row_num, $row['sex'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('H' . $row_num, $row['course_program_enrolled'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('I' . $row_num, $row['year_level'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('J' . $row_num, $row['street'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('K' . $row_num, $row['town_city'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('L' . $row_num, $row['contact'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('M' . $row_num, $row['batch_no'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('N' . $row_num, $cor_cog_status, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('O' . $row_num, $enrollment_status, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('P' . $row_num, '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            $row_num++;
        }

        $sheet->removeRow(2, 1);

        // Save file
        $outputFile = 'remarks/' . $sheet_name . '_exported.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputFile);

        // Email sending
        $sql = "SELECT email FROM users WHERE role = 'chairman' LIMIT 1";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        $chairmanEmail = $user['email'];

        $html = schogms_email_export_processed([
            'recipient_label' => 'Chairman',
            'sheet_name' => $sheet_name,
            'detail' => 'The TES masterlist remarks export has been processed successfully.',
        ]);
        schogms_send_mail(
            $chairmanEmail,
            'Export Ready — SchoGMS TES Remarks',
            $html,
            'Chairman',
            'SchoGMS Export',
            [$outputFile]
        );
        
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
<?php schogms_loading_screen_once(); ?>

    <?php include 'loading-screen.php'; ?>

    <h2>Data Results For TES Remarks</h2>
    <!-- Export to Excel Button -->
    <!-- Include SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Export Form -->
    <form method="POST">
        <button type="submit" name="export" id="export-btn">Export Remarks</button>
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
  <button id="export-btn2" name="export_data">Send Remarks as Email</button>
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
                <th>SEQ</th>
                <th>APP NO</th>
                <th>LASTNAME</th>
                <th>FIRSTNAME</th>
                <th>EXTNAME</th>
                <th>MIDDLENAME</th>
                <th>SEX</th>
                <th>BIRTHDATE</th>
                <th>COURSE</th>
                <th>YEAR LEVEL</th>
                <th>UNITS ENROLLED</th>
                <th>STATUS</th>
                <th>REMARKS</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['seq']); ?></td>
                    <td><?php echo htmlspecialchars($row['app_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                    <td><?php echo htmlspecialchars($row['extname']); ?></td>
                    <td><?php echo htmlspecialchars($row['middlename']);?></td>
                    <td><?php echo htmlspecialchars($row['sex']); ?></td>
                    <td><?php echo htmlspecialchars($row['birthdate']); ?></td>
                    <td><?php echo htmlspecialchars($row['course_program_enrolled']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['year_level']); ?></td>
                    <td><?php echo htmlspecialchars($row['enrolled']) ?? ''; ?>
                    </td>

                    <!-- COR & COG Status -->
                    <td>
                        <?php
                        $hasCOR = strpos($row['uploaded_categories'], 'COR') !== false;
                        $hasCOG = strpos($row['uploaded_categories'], 'COG') !== false;

                        if ($hasCOR && $hasCOG): ?>
                            <span class="badge badge-success">COR & COG Submitted</span>
                        <?php elseif ($hasCOR): ?>
                            <span class="badge badge-warning">Only COR Submitted</span>
                        <?php elseif ($hasCOG): ?>
                            <span class="badge badge-warning">Only COG Submitted</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Not Submitted</span>
                        <?php endif; ?>
                    </td>


                    <!-- Enrollment Status -->
                    <td>
                        <?php if ($row['enrollment_status'] == 'Enrolled'): ?>
                            <span class="badge badge-success">Enrolled</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Not Enrolled</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script src="../../assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../../dist/js/pages/datatable/datatable-basic.init.js"></script>

</body>

</html>
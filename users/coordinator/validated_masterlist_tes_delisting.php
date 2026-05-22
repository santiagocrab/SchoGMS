<?php
ini_set('max_execution_time', 600);
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
    cm.id, cm.filename, cm.campus, cm.seq, cm.app_no, cm.lastname, cm.firstname, cm.ext, 
    cm.middlename, cm.sex, cm.course_program_enrolled, cm.year_level, cm.street, cm.town_city,
    cm.contact, cm.batch_no, 
    rm.id_number, rm.enrolled, rm.zip_code, rm.email_address, rm.mobile_number, rm.date_of_birth
FROM ched_masterlist_tes cm
LEFT JOIN registrar_master_list rm
    ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
    AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
    AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
         OR cm.middlename IS NULL 
         OR rm.middle_name IS NULL 
         OR cm.middlename = '' 
         OR rm.middle_name = '')  
WHERE cm.campus = '" . $conn->real_escape_string($sheet_name) . "'
ORDER BY cm.campus ASC, cm.id ASC;

";


// Execute the query
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}

if (isset($_POST['export'])) {
    // Load existing Excel file
    $inputFileName = 'anex/0-Annex-5-TES-New-Form.xlsx';
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);

    // Select Sheet 3 (Index starts from 0, so Sheet 3 is index 2) for data with id_number
    $sheet5 = $spreadsheet->getSheet(2);

    // Start inserting data from Row 33 in Sheet 3
    $row_num5 = 40; // For Sheet 3
    $control_number5 = 1;

    // Process only entries without id_number
    foreach ($exportRows as $row) {
        if (empty($row['id_number'])) {
            // Insert new row in Sheet 3
            $sheet5->insertNewRowBefore($row_num5);

            // **Apply styling**
            for ($col = 'A'; $col <= 'L'; $col++) {
                $cell = $sheet5->getCell($col . $row_num5);
                $cell->getStyle()->getAlignment()->setWrapText(true);
            }
            // **Populate Data in Sheet 5**
            $sheet5->setCellValue('B' . $row_num5, str_pad($control_number5++, 5, '0', STR_PAD_LEFT));
            $sheet5->setCellValue('C' . $row_num5, 'No ID');
            $sheet5->setCellValue('D' . $row_num5, $row['app_no']);
            $sheet5->setCellValue('E' . $row_num5, $row['lastname']);
            $sheet5->setCellValue('F' . $row_num5, $row['firstname']);
            $sheet5->setCellValue('G' . $row_num5, $row['middlename']);
            $sheet5->setCellValue('H' . $row_num5, $row['sex']);
            $sheet5->setCellValue('I' . $row_num5, $row['date_of_birth']);
            $sheet5->setCellValue('J' . $row_num5, $row['course_program_enrolled']);
            $sheet5->setCellValue('K' . $row_num5, $row['year_level']);
            $sheet5->setCellValue('L' . $row_num5, '');
            $sheet5->setCellValue('M' . $row_num5, '');

            // Move to next row in Sheet 5
            $row_num5++;
        }
    }

    // **Remove template empty rows dynamically**
    if ($row_num5 > 40) {
        $sheet5->removeRow(39, 1);
    }

    // **Write the file to the browser as an Excel file**
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="0-Annex-5-TES-New-Form-TES.xlsx"');
    header('Cache-Control: max-age=0');

    // Save the file to php://output
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');

    exit;
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
    </style>
</head>

<body>

    <h2>Data TES Delisting Results</h2>

    <!-- Export to Excel Button -->
    <!-- Include SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Export Form -->
    <form method="POST">
        <button type="submit" name="export" id="export-btn">Export Form</button>
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

    <table id="zero_config" class="table table-striped table-bordered no-wrap">
        <thead>
            <tr>
                <th>5-digit Control Number</th>
                <th>Student Number</th>
                <th>TES App Number</th>
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
    <?php if (empty($row['id_number'])): // Check if id_number is NULL or empty ?>
        <tr>
                    <td><?php echo str_pad($control_number++, 5, '0', STR_PAD_LEFT); ?></td> <!-- 5-digit Control Number -->
                    <td><?php echo htmlspecialchars($row['id_number']); ?></td> <!-- Student Number -->
                    <td><?php echo htmlspecialchars($row['app_no']); ?></td> <!-- TDP Award Number -->
                    <td><?php echo htmlspecialchars($row['lastname']); ?></td> <!-- Last Name -->
                    <td><?php echo htmlspecialchars($row['firstname']); ?></td> <!-- Given Name -->
                    <td><?php echo htmlspecialchars($row['middlename']); ?></td> <!-- Middle Initial -->
                    <td><?php echo htmlspecialchars($row['sex']); ?></td> <!-- Sex at Birth (M/F) -->
                    <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td> <!-- Birthdate -->
                    <td><?php echo htmlspecialchars($row['course_program_enrolled']); ?></td> <!-- Degree Program -->
                    <td><?php echo htmlspecialchars($row['year_level']); ?></td> <!-- Year Level -->
                    <td><?php echo htmlspecialchars($row['enrolled']); ?></td> <!-- Total Units Enrolled -->
                    <td><?php echo htmlspecialchars($row['zip_code']); ?></td> <!-- ZIP Code -->
                    <td><?php echo htmlspecialchars($row['email_address']); ?></td> <!-- E-mail Address -->
                    <td><?php echo htmlspecialchars($row['mobile_number']); ?></td> <!-- Phone Number -->
                </tr>
    <?php endif; ?>
<?php endwhile; ?>

        </tbody>
    </table>

    <script src="../../assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../../dist/js/pages/datatable/datatable-basic.init.js"></script>

</body>

</html>
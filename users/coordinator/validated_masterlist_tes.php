<?php
ini_set('max_execution_time', 600);
require '../config/conn.php';
require '../vendor/autoload.php';  // Ensure PhpSpreadsheet is installed via Composer

// Get filters from GET request
$sheet_name = isset($_GET['sheet_name']) ? $_GET['sheet_name'] : '';

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
    $sheet3 = $spreadsheet->getSheet(1);

    // Start inserting data from Row 33 in Sheet 3
    $row_num3 = 36; // For Sheet 3
    $control_number3 = 1;

    // Process only entries with id_number
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['id_number'])) {
            // Insert new row in Sheet 3
            $sheet3->insertNewRowBefore($row_num3, 1);

            // Apply styles
            for ($col = 'A'; $col <= 'Q'; $col++) {
                $cell = $sheet3->getCell($col . $row_num3);
                $cell->getStyle()->getAlignment()->setWrapText(true);
            }

            // Populate data in Sheet 3
            $sheet3->setCellValue('A' . $row_num3, str_pad($control_number3++, 5, '0', STR_PAD_LEFT));
            $sheet3->setCellValue('B' . $row_num3, $row['id_number']);
            $sheet3->setCellValue('C' . $row_num3, $row['app_no']);
            $sheet3->setCellValue('D' . $row_num3, $row['lastname']);
            $sheet3->setCellValue('E' . $row_num3, $row['firstname']);
            $sheet3->setCellValue('F' . $row_num3, $row['middlename']);
            $sheet3->setCellValue('G' . $row_num3, $row['sex']);
            $sheet3->setCellValue('H' . $row_num3, $row['date_of_birth']);
            $sheet3->setCellValue('I' . $row_num3, $row['course_program_enrolled']);
            $sheet3->setCellValue('J' . $row_num3, $row['year_level']);
            $sheet3->setCellValue('K' . $row_num3, $row['enrolled']);
            $sheet3->setCellValue('L' . $row_num3, $row['zip_code']);
            $sheet3->setCellValue('M' . $row_num3, $row['email_address']);
            $sheet3->setCellValue('N' . $row_num3, $row['contact']);

            // Move to next row in Sheet 3
            $row_num3++;
        }
    }

    // **Remove template empty rows dynamically**
    if ($row_num3 > 36) {
        $sheet3->removeRow(35, 1);
    }

    // **Write the file to the browser as an Excel file**
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="0-Annex-5-TES-New-Form.xlsx"');
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
    <?php include 'loading-screen.php'; ?>

    <h2>Data TES Results</h2>

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
            <?php endwhile; ?>
        </tbody>
    </table>

    <script src="../../assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../../dist/js/pages/datatable/datatable-basic.init.js"></script>

</body>

</html>
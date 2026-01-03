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

$control_number = 1; // Starting control number
// If export button is clicked
// If export button is clicked
if (isset($_POST['export'])) {
    // Load existing Excel file
    $inputFileName = 'anex/Annex 7 TDP New Form.xlsx';
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);

    // Select Sheet 3 (Index starts from 0, so Sheet 3 is index 2) for data with id_number
    $sheet3 = $spreadsheet->setActiveSheetIndex(2);

    // Select Sheet 5 (Index starts from 0, so Sheet 5 is index 4) for data without id_number
    $sheet5 = $spreadsheet->setActiveSheetIndex(4);

    // Start inserting data from Row 33 in Sheet 3 and Row 23 in Sheet 5
    $row_num3 = 33; // For Sheet 3
    $row_num5 = 23; // For Sheet 5

    // Control numbering per sheet
    $control_number3 = 1;
    $control_number5 = 1;

    while ($row = $result->fetch_assoc()) {
        if (empty($row['id_number'])) {
            // Apply styles to Sheet 5
            for ($col = 'A'; $col <= 'L'; $col++) {
                $cell = $sheet5->getCell($col . $row_num5);
                $cell->getStyle()->getAlignment()->setWrapText(true);  // Enable text wrapping
                $cell->getStyle()->getFont()->setSize(5);  // Set font size to 5 (adjust this as needed)
                $cell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);  // Center horizontally
                $cell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);  // Center vertically
                $cell->getStyle()->getFont()->setBold(false);  // Remove bold style
            }


            // Insert new row in Sheet 5
            $sheet5->insertNewRowBefore($row_num5, 1);

            // Populate data, ensuring numbers remain as text
            $sheet5->setCellValue('A' . $row_num5, str_pad($control_number5++, 5, '0', STR_PAD_LEFT));
            $sheet5->setCellValue('B' . $row_num5, 'No ID');
            $sheet5->setCellValue('C' . $row_num5, $row['award_no']);
            $sheet5->setCellValue('D' . $row_num5, $row['lastname']);
            $sheet5->setCellValue('E' . $row_num5, $row['firstname']);
            $sheet5->setCellValue('F' . $row_num5, $row['middlename']);
            $sheet5->setCellValue('G' . $row_num5, $row['sex']);
            $sheet5->setCellValue('H' . $row_num5, $row['birthdate']);
            $sheet5->setCellValue('I' . $row_num5, $row['course_program_enrolled']);
            $sheet5->setCellValue('J' . $row_num5, $row['year_level']);
            $sheet5->setCellValue('K' . $row_num5, '');
            $sheet5->setCellValue('L' . $row_num5, '');

            // Move to the next row
            $row_num5++;
        } else {
            // Apply styles to Sheet 3
            for ($col = 'A'; $col <= 'Q'; $col++) {
                $cell = $sheet3->getCell($col . $row_num3);
                $cell->getStyle()->getAlignment()->setWrapText(true);  // Enable text wrapping
                $cell->getStyle()->getFont()->setSize(5);  // Set font size to 5 (adjust this as needed)
                $cell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);  // Center horizontally
                $cell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);  // Center vertically
                $cell->getStyle()->getFont()->setBold(false);  // Remove bold style
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
            $sheet3->setCellValue('K' . $row_num3, $row['enrolled']);
            $sheet3->setCellValue('L' . $row_num3, $row['zip_code']);
            $sheet3->setCellValue('M' . $row_num3, $row['email_address']);
            $sheet3->setCellValue('N' . $row_num3, $row['mobile_number']);

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
    $sheet5->removeRow(22, 1);

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
                    <td><?php echo str_pad($control_number++, 5, '0', STR_PAD_LEFT); ?></td> <!-- 5-digit Control Number -->
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
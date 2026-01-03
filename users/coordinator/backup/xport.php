<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export'])) {
    ob_start();

    // Load existing Excel file
    $inputFileName = 'anex/Annex 7 TDP New Form.xlsx';  // Path to the existing file
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
    $sheet = $spreadsheet->getActiveSheet();

    // Populate the data starting from Row 33 (Columns A to Q)
    $row_num = 33;  // Starting from row 33
    while ($row = $result->fetch_assoc()) {
        // Insert a new row at the current row position
        $sheet->insertNewRowBefore($row_num, 1);

        // Copy the entire content and style from row 32 to the newly inserted row
        for ($col = 'A'; $col <= 'Q'; $col++) {
            $cell = $sheet->getCell($col . $row_num);
            $cell->getStyle()->getAlignment()->setWrapText(true);  // Enable text wrapping
            $cell->getStyle()->getFont()->setSize(5);  // Set font size to 5 (adjust this as needed)
            $cell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);  // Center horizontally
            $cell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);  // Center vertically
            $cell->getStyle()->getFont()->setBold(false);  // Remove bold style
        }

        // Now set the actual values for each row from the database to replace the copied cells
        $sheet->setCellValue('A' . $row_num, str_pad($control_number++, 5, '0', STR_PAD_LEFT));  // 5-digit Control Number
        $sheet->setCellValue('B' . $row_num, $row['id_number']);  // Student Number
        $sheet->setCellValue('C' . $row_num, $row['award_no']);  // TDP Award Number
        $sheet->setCellValue('D' . $row_num, $row['lastname']);  // Last Name
        $sheet->setCellValue('E' . $row_num, $row['firstname']);  // Given Name
        $sheet->setCellValue('F' . $row_num, $row['middlename']);  // Middle Initial
        $sheet->setCellValue('G' . $row_num, $row['sex']);  // Sex at Birth (M/F)
        $sheet->setCellValue('H' . $row_num, $row['birthdate']);  // Birthdate
        $sheet->setCellValue('I' . $row_num, $row['course_program_enrolled']);  // Degree Program
        $sheet->setCellValue('J' . $row_num, $row['year_level']);  // Year Level
        $sheet->setCellValue('K' . $row_num, $row['enrolled']);  // Total Units Enrolled
        $sheet->setCellValue('L' . $row_num, $row['zip_code']);  // ZIP Code
        $sheet->setCellValue('M' . $row_num, $row['email_address']);  // E-mail Address
        $sheet->setCellValue('N' . $row_num, $row['mobile_number']);  // Phone Number

        // You can add more columns if necessary
        $sheet->setCellValue('O' . $row_num, ''); // Example for additional column
        $sheet->setCellValue('P' . $row_num, ''); // Example for additional column
        $sheet->setCellValue('Q' . $row_num, ''); // Example for additional column

        // Move to the next row
        $row_num++;
    }

    // After inserting all data, remove the originally empty row (row 32)
    $sheet->removeRow(32, 1);

    // Write the file to the browser as an Excel file
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Annex 7 TDP New Form.xlsx"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1'); // Ensure cache control for modern browsers

    // Save the file to php://output
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');

    // JavaScript to close the window and redirect to the opener window
    echo "<script>
            window.close();  // Close this window
            window.opener.location.reload();  // Reload the parent window
          </script>";
    ob_end_flush();
    exit;
}


?>
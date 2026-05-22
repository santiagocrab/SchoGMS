<!DOCTYPE html>
<html>
<head>
    <title>Direct Form Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php schogms_loading_screen_once(); ?>

    <div class="container mt-5">
        <h2>Direct Form Test - No Modal</h2>
        <form action="submit_document.php" method="post" enctype="multipart/form-data">
            <!-- Hidden campus backup -->
            <input type="hidden" name="campus_backup" value="Isulan">
            <!-- Hidden category backup -->
            <input type="hidden" name="category_backup" value="COR">
            
            <!-- Campus -->
            <div class="mb-3">
                <label for="session_campus">Campus</label>
                <input type="text" class="form-control" id="session_campus" name="session_campus" value="Isulan" readonly style="background-color: #f8f9fa;">
            </div>

            <!-- Category -->
            <div class="mb-3">
                <label for="fileCategory">Category</label>
                <input type="text" class="form-control" id="fileCategory" name="category" value="COR" readonly style="background-color: #f8f9fa;">
            </div>

            <!-- Academic Year -->
            <div class="mb-3">
                <label for="academicYear">Academic Year</label>
                <select class="form-control" id="academicYear" name="academic_year" required>
                    <option value="2026-2027">2026-2027</option>
                    <option value="2025-2026">2025-2026</option>
                    <option value="2024-2025" selected>2024-2025</option>
                    <option value="2023-2024">2023-2024</option>
                    <option value="2022-2023">2022-2023</option>
                </select>
            </div>

            <!-- Semester -->
            <div class="mb-3">
                <label for="semester">Semester</label>
                <select class="form-control" id="semester" name="semester" required>
                    <option value="1st Semester" selected>1st Semester</option>
                    <option value="2nd Semester">2nd Semester</option>
                    <option value="Summer">Summer</option>
                </select>
            </div>

            <!-- File Upload -->
            <div class="mb-3">
                <label for="fileUpload">Upload Documents</label>
                <input type="file" class="form-control" id="fileUpload" name="fileUpload[]" multiple accept=".pdf,.jpg,.jpeg,.png" required>
            </div>

            <button type="submit" class="btn btn-primary">Upload Documents</button>
        </form>
    </div>
</body>
</html>


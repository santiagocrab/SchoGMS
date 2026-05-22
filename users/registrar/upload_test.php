<!DOCTYPE html>
<html>
<head>
    <title>COR Upload Test - Simple</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php schogms_loading_screen_once(); ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>COR Upload Test - Simple Version</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">This is a simplified version to test COR uploads without complex JavaScript.</p>
                        
                        <form action="submit_document_simple.php" method="post" enctype="multipart/form-data">
                            <!-- Campus -->
                            <div class="mb-3">
                                <label for="session_campus" class="form-label">Campus</label>
                                <input type="text" class="form-control" id="session_campus" name="session_campus" value="ISULAN" readonly style="background-color: #f8f9fa;">
                            </div>

                            <!-- Category -->
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" class="form-control" id="category" name="category" value="COR" readonly style="background-color: #f8f9fa;">
                            </div>

                            <!-- Academic Year -->
                            <div class="mb-3">
                                <label for="academic_year" class="form-label">Academic Year *</label>
                                <select class="form-control" id="academic_year" name="academic_year" required>
                                    <option value="">Select Academic Year</option>
                                    <option value="2026-2027">2026-2027</option>
                                    <option value="2025-2026">2025-2026</option>
                                    <option value="2024-2025" selected>2024-2025</option>
                                    <option value="2023-2024">2023-2024</option>
                                    <option value="2022-2023">2022-2023</option>
                                </select>
                            </div>

                            <!-- Semester -->
                            <div class="mb-3">
                                <label for="semester" class="form-label">Semester *</label>
                                <select class="form-control" id="semester" name="semester" required>
                                    <option value="">Select Semester</option>
                                    <option value="1st Semester" selected>1st Semester</option>
                                    <option value="2nd Semester">2nd Semester</option>
                                    <option value="Summer">Summer</option>
                                </select>
                            </div>

                            <!-- File Upload -->
                            <div class="mb-3">
                                <label for="fileUpload" class="form-label">Upload COR Documents *</label>
                                <input type="file" class="form-control" id="fileUpload" name="fileUpload[]" multiple accept=".pdf,.jpg,.jpeg,.png" required>
                                <div class="form-text">Select multiple PDF, JPG, or PNG files</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Upload COR Documents</button>
                            </div>
                        </form>
                        
                        <hr>
                        <div class="text-center">
                            <a href="cor-cog.php" class="btn btn-secondary">Back to COR/COG Page</a>
                            <a href="view_documents.php" class="btn btn-info">View Uploaded Documents</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php include 'config/session.php'; ?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>COR Upload - Standalone | SchoGMS</title>
    <link href="../../dist/css/style.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php schogms_loading_screen_once(); ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">COR Document Upload - Standalone</h4>
                        <p class="mb-0">Direct upload without modal complications</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <form action="submit_document_simple.php" method="post" enctype="multipart/form-data">
                                    <!-- Campus -->
                                    <div class="mb-3">
                                        <label for="session_campus" class="form-label">Campus</label>
                                        <input type="text" class="form-control" id="session_campus" name="session_campus" value="<?= htmlspecialchars($sheet_name ?: 'ISULAN'); ?>" readonly style="background-color: #f8f9fa;">
                                        <div class="form-text">Your assigned campus: <strong><?= htmlspecialchars($sheet_name ?: 'ISULAN'); ?></strong></div>
                                    </div>

                                    <!-- Category -->
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <input type="text" class="form-control" id="category" name="category" value="COR" readonly style="background-color: #f8f9fa;">
                                        <div class="form-text">Document category: <strong>COR (Certificate of Registration)</strong></div>
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
                                        <div class="form-text">Select multiple PDF, JPG, or PNG files. Maximum 10MB per file.</div>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fa fa-upload"></i> Upload COR Documents
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6>Upload Instructions</h6>
                                    </div>
                                    <div class="card-body">
                                        <ol class="small">
                                            <li>Select the correct Academic Year</li>
                                            <li>Choose the appropriate Semester</li>
                                            <li>Select multiple COR PDF files</li>
                                            <li>Click Upload Documents</li>
                                        </ol>
                                        
                                        <div class="alert alert-info small">
                                            <strong>Note:</strong> Files will be organized by Campus/Academic Year/Semester/Category
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        <div class="text-center">
                            <a href="cor-cog.php" class="btn btn-secondary">Back to COR/COG Page</a>
                            <a href="view_documents.php" class="btn btn-info">View Uploaded Documents</a>
                            <a href="masterlist.php" class="btn btn-success">Registrar Masterlist</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

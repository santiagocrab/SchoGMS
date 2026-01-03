<!DOCTYPE html>
<html>
<head>
    <title>Debug Form Submission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Debug Form Submission</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">This form will show exactly what data is being submitted.</p>
                        
                        <form action="submit_document_simple.php" method="post" enctype="multipart/form-data">
                            <!-- Campus -->
                            <div class="mb-3">
                                <label for="session_campus" class="form-label">Campus</label>
                                <input type="text" class="form-control" id="session_campus" name="session_campus" value="ISULAN">
                            </div>

                            <!-- Category -->
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" class="form-control" id="category" name="category" value="COR">
                            </div>

                            <!-- Academic Year -->
                            <div class="mb-3">
                                <label for="academic_year" class="form-label">Academic Year</label>
                                <select class="form-control" id="academic_year" name="academic_year">
                                    <option value="2024-2025" selected>2024-2025</option>
                                    <option value="2023-2024">2023-2024</option>
                                </select>
                            </div>

                            <!-- Semester -->
                            <div class="mb-3">
                                <label for="semester" class="form-label">Semester</label>
                                <select class="form-control" id="semester" name="semester">
                                    <option value="1st Semester" selected>1st Semester</option>
                                    <option value="2nd Semester">2nd Semester</option>
                                </select>
                            </div>

                            <!-- File Upload -->
                            <div class="mb-3">
                                <label for="fileUpload" class="form-label">Upload Files</label>
                                <input type="file" class="form-control" id="fileUpload" name="fileUpload[]" multiple>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit Form</button>
                        </form>
                        
                        <hr>
                        <div class="alert alert-info">
                            <h6>Form Debug Info:</h6>
                            <p><strong>Method:</strong> POST</p>
                            <p><strong>Action:</strong> submit_document_simple.php</p>
                            <p><strong>Enctype:</strong> multipart/form-data</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php 
include 'config/session.php'; 

// Set default values if not in session
$sheet_name = $_SESSION['campus'] ?? 'ISULAN';
$default_academic_year = '2024-2025';
$default_semester = '1st Semester';
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UPLOAD ALL 3,000+ COR FILES AT ONCE</title>
    <link href="../../dist/css/style.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .progress-container {
            display: none;
            margin-top: 20px;
        }
        .file-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            margin-top: 10px;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .upload-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">🚀 UPLOAD ALL 3,000+ COR FILES AT ONCE</h4>
                        <p class="mb-0">Upload ALL your COR documents in ONE single upload!</p>
                    </div>
                    <div class="card-body">
                        <div class="upload-info">
                            <h6>✅ UPLOAD ALL COR FILES - ONE TIME UPLOAD</h6>
                            <ul class="mb-0">
                                <li><strong>UNLIMITED FILES:</strong> Upload ALL 3,000+ COR documents at once</li>
                                <li><strong>NO BATCH LIMITS:</strong> No need to split into batches</li>
                                <li><strong>ONE CLICK UPLOAD:</strong> Select all files and upload everything</li>
                                <li><strong>AUTOMATIC PROCESSING:</strong> System handles everything automatically</li>
                                <li><strong>PROGRESS TRACKING:</strong> See real-time upload progress</li>
                                <li><strong>DUPLICATE PREVENTION:</strong> Automatically skips duplicate names for same A.Y & semester</li>
                                <li><strong>ALPHABETICAL SORTING:</strong> Documents automatically sorted by last name</li>
                            </ul>
                        </div>

                        <form id="uploadAllForm" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="session_campus" class="form-label">Campus</label>
                                        <input type="text" class="form-control" id="session_campus" name="session_campus" value="<?= htmlspecialchars($sheet_name); ?>" readonly style="background-color: #f8f9fa;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <input type="text" class="form-control" id="category" name="category" value="COR" readonly style="background-color: #f8f9fa;">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="academic_year" class="form-label">Academic Year</label>
                                        <select class="form-control" id="academic_year" name="academic_year" required>
                                            <option value="2026-2027">2026-2027</option>
                                            <option value="2025-2026">2025-2026</option>
                                            <option value="2024-2025" selected>2024-2025</option>
                                            <option value="2023-2024">2023-2024</option>
                                            <option value="2022-2023">2022-2023</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="semester" class="form-label">Semester</label>
                                        <select class="form-control" id="semester" name="semester" required>
                                            <option value="1st Semester" selected>1st Semester</option>
                                            <option value="2nd Semester">2nd Semester</option>
                                            <option value="Summer">Summer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="fileUpload" class="form-label">Select ALL Your COR Documents (3,000+ files)</label>
                                <input type="file" class="form-control" id="fileUpload" name="fileUpload[]" multiple accept=".pdf,.jpg,.jpeg,.png,.rar,.zip" required>
                                <div class="form-text">
                                    <strong>Select ALL your 3,000+ COR files at once!</strong> 
                                    Hold Ctrl (or Cmd on Mac) and click to select multiple files, or use Ctrl+A to select all files in a folder.
                                    <br><strong>Supported formats:</strong> PDF, RAR, ZIP, JPG, JPEG, PNG
                                </div>
                            </div>

                            <div id="fileList" class="file-list" style="display: none;">
                                <h6>Selected Files:</h6>
                                <div id="fileListContent"></div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg btn-block" id="uploadBtn">
                                <i class="fa fa-upload"></i> UPLOAD ALL COR FILES (3,000+ files)
                            </button>
                        </form>

                        <!-- Progress Container -->
                        <div id="progressContainer" class="progress-container">
                            <h6>Upload Progress:</h6>
                            <div class="progress mb-2" style="height: 30px;">
                                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div id="progressText" class="text-center h5">Preparing upload...</div>
                            <div id="uploadResults"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('uploadAllForm');
            const fileInput = document.getElementById('fileUpload');
            const fileList = document.getElementById('fileList');
            const fileListContent = document.getElementById('fileListContent');
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const uploadResults = document.getElementById('uploadResults');
            const uploadBtn = document.getElementById('uploadBtn');

            // Show selected files
            fileInput.addEventListener('change', function() {
                const files = this.files;
                if (files.length > 0) {
                    fileList.style.display = 'block';
                    fileListContent.innerHTML = '';
                    
                    // Show file count
                    fileListContent.innerHTML = '<div class="alert alert-success"><strong>Selected ' + files.length + ' files for upload!</strong></div>';
                    
                    // Show first 10 files
                    for (let i = 0; i < Math.min(files.length, 10); i++) {
                        const file = files[i];
                        const fileItem = document.createElement('div');
                        fileItem.className = 'small text-muted';
                        fileItem.textContent = (i + 1) + '. ' + file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                        fileListContent.appendChild(fileItem);
                    }
                    
                    if (files.length > 10) {
                        const moreItem = document.createElement('div');
                        moreItem.className = 'small text-muted';
                        moreItem.textContent = '... and ' + (files.length - 10) + ' more files';
                        fileListContent.appendChild(moreItem);
                    }
                } else {
                    fileList.style.display = 'none';
                }
            });

            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                handleUpload();
            });
            
            function handleUpload() {
                const files = fileInput.files;
                if (files.length === 0) {
                    alert('Please select files to upload.');
                    return;
                }

                // Validate form fields
                const academicYear = document.getElementById('academic_year').value;
                const semester = document.getElementById('semester').value;
                
                if (!academicYear || academicYear.trim() === '') {
                    alert('Please select an Academic Year.');
                    return;
                }
                
                if (!semester || semester.trim() === '') {
                    alert('Please select a Semester.');
                    return;
                }

                // Confirm large upload
                if (files.length > 1000) {
                    if (!confirm('You are about to upload ' + files.length + ' files. This may take a long time. Continue?')) {
                        return;
                    }
                }

                // Show progress
                progressContainer.style.display = 'block';
                uploadBtn.disabled = true;
                uploadBtn.textContent = 'Uploading ALL files...';

                // Prepare form data
                const formData = new FormData();
                formData.append('session_campus', document.getElementById('session_campus').value);
                formData.append('category', document.getElementById('category').value);
                formData.append('academic_year', document.getElementById('academic_year').value);
                formData.append('semester', document.getElementById('semester').value);

                // Add ALL files
                for (let i = 0; i < files.length; i++) {
                    formData.append('fileUpload[]', files[i]);
                }

                // Upload with progress
                const xhr = new XMLHttpRequest();
                
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        progressBar.style.width = percentComplete + '%';
                        progressText.textContent = 'Uploading ALL files... ' + Math.round(percentComplete) + '%';
                    }
                });

                xhr.addEventListener('load', function() {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                progressText.textContent = 'ALL files uploaded successfully!';
                                uploadResults.innerHTML = '<div class="alert alert-success"><h5>🎉 SUCCESS! ALL COR FILES UPLOADED!</h5>' + response.message + '</div>';
                                
                                // Show stats
                                if (response.stats) {
                                    uploadResults.innerHTML += '<div class="alert alert-info"><h6>Upload Statistics:</h6>' +
                                        '<strong>Total Files:</strong> ' + response.stats.total + '<br>' +
                                        '<strong>Successfully Uploaded:</strong> ' + response.stats.success + '<br>' +
                                        '<strong>Failed:</strong> ' + response.stats.failed +
                                        '</div>';
                                }
                                
                                setTimeout(() => {
                                    alert('🎉 ALL ' + files.length + ' COR files uploaded successfully!');
                                    progressContainer.style.display = 'none';
                                }, 2000);
                            } else {
                                progressText.textContent = 'Upload failed!';
                                uploadResults.innerHTML = '<div class="alert alert-danger"><h5>❌ Upload Failed</h5>' + response.message + '</div>';
                            }
                        } catch (e) {
                            progressText.textContent = 'Upload failed!';
                            uploadResults.innerHTML = '<div class="alert alert-danger"><h5>❌ Error Processing Response</h5>Please try again.</div>';
                        }
                    } else {
                        progressText.textContent = 'Upload failed!';
                        uploadResults.innerHTML = '<div class="alert alert-danger"><h5>❌ Upload Failed</h5>Server error: ' + xhr.status + '</div>';
                    }
                    
                    uploadBtn.disabled = false;
                    uploadBtn.textContent = 'UPLOAD ALL COR FILES (3,000+ files)';
                });

                xhr.addEventListener('error', function() {
                    progressText.textContent = 'Upload failed!';
                    uploadResults.innerHTML = '<div class="alert alert-danger"><h5>❌ Network Error</h5>Please check your connection and try again.</div>';
                    uploadBtn.disabled = false;
                    uploadBtn.textContent = 'UPLOAD ALL COR FILES (3,000+ files)';
                });

                xhr.open('POST', 'submit_document_mongodb_batch.php');
                xhr.send(formData);
            }
        });
    </script>
</body>
</html>

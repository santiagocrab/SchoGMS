<!DOCTYPE html>
<html>
<head>
    <title>Upload Test Form</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 10px 0; }
        label { display: block; margin-bottom: 5px; }
        input, select { padding: 8px; width: 300px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .result { margin-top: 20px; padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; }
    </style>
</head>
<body>
    <h2>🧪 Upload Test Form</h2>
    
    <form id="uploadForm" enctype="multipart/form-data">
        <div class="form-group">
            <label for="session_campus">Campus:</label>
            <input type="text" id="session_campus" name="session_campus" value="ISULAN" readonly>
        </div>
        
        <div class="form-group">
            <label for="file_group">File Group:</label>
            <input type="text" id="file_group" name="file_group" value="College of Engineering" required>
        </div>
        
        <div class="form-group">
            <label for="academic_year">Academic Year:</label>
            <select id="academic_year" name="academic_year" required>
                <option value="2022-2023" selected>2022-2023</option>
                <option value="2023-2024">2023-2024</option>
                <option value="2024-2025">2024-2025</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="semester">Semester:</label>
            <select id="semester" name="semester" required>
                <option value="1st Semester" selected>1st Semester</option>
                <option value="2nd Semester">2nd Semester</option>
                <option value="Mid Year">Mid Year</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="excelFile">Upload File:</label>
            <input type="file" id="excelFile" name="excelFile" accept=".csv,.xlsx,.xls" required>
        </div>
        
        <button type="submit">Test Upload</button>
    </form>
    
    <div id="result" class="result" style="display: none;"></div>
    
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('session_campus', document.getElementById('session_campus').value);
            formData.append('file_group', document.getElementById('file_group').value);
            formData.append('academic_year', document.getElementById('academic_year').value);
            formData.append('semester', document.getElementById('semester').value);
            formData.append('excelFile', document.getElementById('excelFile').files[0]);
            
            const resultDiv = document.getElementById('result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = 'Uploading...';
            
            fetch('users/registrar/submit_master_list.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.innerHTML = '<h3>Result:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                resultDiv.innerHTML = '<h3>Error:</h3><pre>' + error.message + '</pre>';
            });
        });
    </script>
    
    <br><a href="users/registrar/masterlist.php">← Back to Masterlist</a>
</body>
</html>

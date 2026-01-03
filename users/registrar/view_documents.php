<?php include 'config/session.php'; ?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Uploaded Documents | SchoGMS</title>
    <link href="../../dist/css/style.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Uploaded COR & COG Documents</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Campus</th>
                                        <th>Category</th>
                                        <th>Academic Year</th>
                                        <th>Semester</th>
                                        <th>File Name</th>
                                        <th>Upload Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $dataFile = 'uploads/uploaded_documents.json';
                                    if (file_exists($dataFile)) {
                                        $documents = json_decode(file_get_contents($dataFile), true) ?? [];
                                        foreach ($documents as $doc) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($doc['campus']) . "</td>";
                                            echo "<td>" . htmlspecialchars($doc['category']) . "</td>";
                                            echo "<td>" . htmlspecialchars($doc['academic_year']) . "</td>";
                                            echo "<td>" . htmlspecialchars($doc['semester']) . "</td>";
                                            echo "<td>" . htmlspecialchars($doc['original_name']) . "</td>";
                                            echo "<td>" . htmlspecialchars($doc['uploaded_at']) . "</td>";
                                            echo "<td><a href='" . htmlspecialchars($doc['file_path']) . "' target='_blank' class='btn btn-sm btn-primary'>View</a></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>No documents uploaded yet</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="cor-cog.php" class="btn btn-primary">Back to Upload</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


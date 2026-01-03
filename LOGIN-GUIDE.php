<!DOCTYPE html>
<html>
<head>
    <title>Login Guide - SchoGMS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #333; }
        h2 { color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #007bff;
            color: white;
        }
        .btn {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover { background: #218838; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-primary { background: #007bff; }
        .btn-primary:hover { background: #0056b3; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info-box {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 SchoGMS Login Guide</h1>
        <hr>
        
        <div class="info-box">
            <h3>⚠️ IMPORTANT: Before Logging In</h3>
            <p><strong>You MUST run the password reset script first!</strong></p>
            <p><a href="reset-mongodb-passwords.php" class="btn btn-danger">🔧 Reset All Passwords to password123</a></p>
        </div>
        
        <h2>📋 Available Dashboards</h2>
        <table>
            <tr>
                <th>Role</th>
                <th>Login URL</th>
                <th>Username Examples</th>
                <th>Password</th>
                <th>Dashboard URL</th>
            </tr>
            <tr>
                <td><strong>Admin</strong></td>
                <td><a href="admin-12-02/" target="_blank">http://localhost/SchoGMS/admin-12-02/</a></td>
                <td>admin</td>
                <td>admin123</td>
                <td>http://localhost/SchoGMS/admin-12-02/dashboard.php</td>
            </tr>
            <tr>
                <td><strong>Coordinator</strong></td>
                <td><a href="index.php" target="_blank">http://localhost/SchoGMS/index.php</a></td>
                <td>access, Coordinator, Coordinator Isulan, coordinator Tacurong, coordinator access, Coordinator Palimbang</td>
                <td>password123</td>
                <td>http://localhost/SchoGMS/users/coordinator/</td>
            </tr>
            <tr>
                <td><strong>Registrar</strong></td>
                <td><a href="index.php" target="_blank">http://localhost/SchoGMS/index.php</a></td>
                <td>registrar, registrar isulan, registrar access</td>
                <td>password123</td>
                <td>http://localhost/SchoGMS/users/registrar/</td>
            </tr>
            <tr>
                <td><strong>Chairman</strong></td>
                <td><a href="index.php" target="_blank">http://localhost/SchoGMS/index.php</a></td>
                <td>chairman</td>
                <td>password123</td>
                <td>http://localhost/SchoGMS/users/chairman/</td>
            </tr>
            <tr>
                <td><strong>Director</strong></td>
                <td><a href="index.php" target="_blank">http://localhost/SchoGMS/index.php</a></td>
                <td>Campus Director Isulan</td>
                <td>password123</td>
                <td>http://localhost/SchoGMS/users/director/</td>
            </tr>
            <tr>
                <td><strong>Dean</strong></td>
                <td><a href="index.php" target="_blank">http://localhost/SchoGMS/index.php</a></td>
                <td>(varies by campus)</td>
                <td>password123</td>
                <td>http://localhost/SchoGMS/users/dean/</td>
            </tr>
            <tr>
                <td><strong>Program Head</strong></td>
                <td><a href="index.php" target="_blank">http://localhost/SchoGMS/index.php</a></td>
                <td>(varies by program)</td>
                <td>password123</td>
                <td>http://localhost/SchoGMS/users/program-head/</td>
            </tr>
        </table>
        
        <h2>🚀 Quick Start Steps</h2>
        <ol>
            <li><strong>Step 1:</strong> <a href="reset-mongodb-passwords.php" class="btn btn-danger">Reset All Passwords</a></li>
            <li><strong>Step 2:</strong> Choose your role from the table above</li>
            <li><strong>Step 3:</strong> Click the Login URL for your role</li>
            <li><strong>Step 4:</strong> Enter username and password (password123 for all users)</li>
            <li><strong>Step 5:</strong> You'll be redirected to your dashboard automatically</li>
        </ol>
        
        <h2>🔍 Troubleshooting</h2>
        <ul>
            <li><strong>Getting "incorrect username or password"?</strong>
                <ul>
                    <li>Run the <a href="reset-mongodb-passwords.php">password reset script</a> first</li>
                    <li>Make sure you're using the correct username (case-sensitive)</li>
                    <li>Check the <a href="debug_login_mongodb.php">debug page</a> to see all users</li>
                </ul>
            </li>
            <li><strong>Getting "account restricted" error?</strong>
                <ul>
                    <li>The password reset script also activates all accounts</li>
                    <li>Coordinators can bypass this restriction</li>
                </ul>
            </li>
            <li><strong>Not redirected to dashboard?</strong>
                <ul>
                    <li>Check if the dashboard folder exists: <code>users/[your-role]/</code></li>
                    <li>Check browser console for errors</li>
                </ul>
            </li>
        </ul>
        
        <h2>🛠️ Tools</h2>
        <p>
            <a href="reset-mongodb-passwords.php" class="btn btn-danger">Reset All Passwords</a>
            <a href="debug_login_mongodb.php" class="btn btn-primary">Debug Login Issues</a>
            <a href="index.php" class="btn">Main Login Page</a>
            <a href="admin-12-02/" class="btn">Admin Login</a>
        </p>
        
        <hr>
        <p style="text-align: center; color: #666;">
            <small>Scholarship and Grants Management System (SchoGMS)</small>
        </p>
    </div>
</body>
</html>


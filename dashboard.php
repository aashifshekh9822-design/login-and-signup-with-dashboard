<?php
// 1. session start 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// safe user authentication check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: profile.php");
    exit;
}

// 2. लॉगआउट हैंडलर
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    // session 
    $_SESSION = array();
    session_destroy();
    
    // login page redirect on logout
    header("Location: profile.php"); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            background-color: #f9f9f9;
        }
        /* logout button  */
        .top-bar {
            position: fixed;
            top: 0;
            right: 0;
            width: calc(100% - 200px);
            height: 60px;
            background-color: #000;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-right: 30px;
            z-index: 10;
        }
        
        .logout-btn {
            background: linear-gradient(135deg, #2575fc, #6a11cb);
            color: #ff4d4d;
            padding: 8px 18px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: 0.3s;
        }
        .logout-btn:hover {
            opacity: 0.9;
        }
        /* sidebar styles */
        .sidebar {
            width: 200px;
            height: 100vh;
            background-color: #000;
            color: #fff;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 11;
        }
        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        .sidebar ul {
            list-style: none;
        }
        .sidebar ul li {
            margin-bottom: 25px;
        }
        .sidebar ul li a {
            color: #bbb;
            text-decoration: none;
            font-size: 14px;
            display: block;
        }
        .sidebar ul li a:hover, .sidebar ul li a.active {
            color: #fff;
            font-weight: bold;
        }
        /* मुख्य कंटेंट एरिया */
        .main-content {
            margin-left: 200px;
            margin-top: 60px;
            width: calc(100% - 200px);
            padding: 40px;
        }
        .main-content h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #000;
        }
        /* टेबल स्टाइल */
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .user-table th {
            background-color: #85cbf2;
            color: #000;
            font-weight: bold;
            padding: 12px;
            text-align: center;
            border: 1px solid #b0d4ea;
        }
        .user-table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #e0e0e0;
            font-size: 14px;
            color: #333;
        }
        .user-table tr:nth-child(even) {
            background-color: #fcfcfc;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>ADMIN</h2>
        <ul>
            <li><a href="#">Dashboard</a></li>
            <li><a href="#" class="active">Users</a></li>
        </ul>
    </div>

    <div class="top-bar">
        <a href="dashboard.php?action=logout" class="logout-btn">Logout</a>
    </div>

    <div class="main-content">
        <h1>All Users</h1>

        <?php
        try {
            $dsn = 'mysql:host=localhost;dbname=register;charset=utf8mb4';
            $db = new PDO($dsn, 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $db->query("SELECT id, firstname, email, role FROM section");
            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<table class='user-table'>";
            echo "<tr><th>User ID</th><th>User Name</th><th>Email</th><th>Role</th></tr>";
            
            foreach ($sections as $section) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($section['id'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($section['firstname'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($section['email'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($section['role'] ?? 'user') . "</td>"; // if role is null, default to 'user'
                echo "</tr>";
            }
            
            echo "</table>";

        } catch(\PDOException $e) {
            echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

</body>
</html>
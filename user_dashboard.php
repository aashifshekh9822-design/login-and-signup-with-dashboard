<?php
// 1. Start the session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable full error reporting to diagnose potential runtime issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: profile.php');
    exit();
}

// 3. Security Check: Prevent admins from accessing the regular user dashboard
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding-top: 30px;
        }
        header {
            background: #35424a;
            color: #ffffff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #e8491d 3px solid;
        }
        header a {
            color: #ffffff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 16px;
        }
        .logout-btn {
            float: right;
            background: #e8491d;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .logout-btn:hover {
            background: #cf3c13;
        }
        .content {
            background: #ffffff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'User'); ?></h1>
        <a href="profile.php?action=logout" class="logout-btn">Logout</a>
    </div>
</header>

<div class="container">
    <div class="content">
        <h2>User Dashboard</h2>
        <p>Here you can view your information and activities.</p>
    </div>
</div>

</body>
</html>
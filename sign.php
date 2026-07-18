<?php
// 1. Enable error reporting to diagnose silent failures
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = '';
$messageClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname1 = trim($_POST['f_name'] ?? '');
    $email1 = trim($_POST['e_mail'] ?? '');
    $password1 = $_POST['pass_word'] ?? '';
    $confirm1 = $_POST['con_firm'] ?? '';

    if ($password1 !== $confirm1) {
        $message = 'Passwords do not match.';
        $messageClass = 'error';
    } elseif (empty($fname1) || empty($email1) || empty($password1)) {
        $message = 'All fields are required.';
        $messageClass = 'error';
    } elseif (!filter_var($email1, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address (e.g., example@gmail.com).';
        $messageClass = 'error';
    } else {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "register";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if email already exists in the system to prevent duplicates
        $checkEmail = $conn->prepare("SELECT id FROM section WHERE email = ?");
        if ($checkEmail) {
            $checkEmail->bind_param("s", $email1);
            $checkEmail->execute();
            $checkEmail->store_result();
            if ($checkEmail->num_rows > 0) {
                $message = 'This email address is already registered.';
                $messageClass = 'error';
                $checkEmail->close();
                $conn->close();
            } else {
                $checkEmail->close();

                // Hash the password for maximum security
                $hashedPassword = password_hash($password1, PASSWORD_DEFAULT);
                
                // CRITICAL FIX: Explicitly pass the default role 'user' during creation
                $defaultRole = 'user';
                $stmt = $conn->prepare("INSERT INTO section (firstname, email, password, role) VALUES (?, ?, ?, ?)");

                if ($stmt) {
                    $stmt->bind_param("ssss", $fname1, $email1, $hashedPassword, $defaultRole);

                    if ($stmt->execute()) {
                        // CRITICAL FIX: Safely disconnect variables before execution transfer
                        $stmt->close();
                        $conn->close();
                        
                        header('Location: profile.php?registered=1');
                        exit;
                    } else {
                        $message = 'Error during registration: ' . htmlspecialchars($stmt->error);
                        $messageClass = 'error';
                        $stmt->close();
                    }
                } else {
                    $message = 'Prepare failed: ' . htmlspecialchars($conn->error);
                    $messageClass = 'error';
                }
                $conn->close();
            }
        } else {
            $message = 'Database tracking check failed.';
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="sign.css">
</head>
<body>

    <div class="signup-card">
        <h2>Create Account</h2>
        <p class="subtitle">Join our Page</p>
        
        <?php if ($message): ?>
            <p style="padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; font-weight: bold;
              background-color: <?= $messageClass === 'success' ? '#d4edda' : '#f8d7da' ?>; 
              color: <?= $messageClass === 'success' ? '#155724' : '#721c24' ?>;">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>
        
        <form method="POST" action="sign.php">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" placeholder="MD AASHIF" required name="f_name">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" placeholder="example@gmail.com" required name="e_mail">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" placeholder="Create password" required name="pass_word">
            </div>
            
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" placeholder="Confirm password" required name="con_firm">
            </div>
            
            <button type="submit" class="register-btn">Register Now</button>
        </form>
        
        <a href="profile.php" class="login-link">Back To Login</a>
    </div>

</body>
</html>
<?php
// 1. Start the session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable full error reporting to stop silent white screens
error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = '';
$messageClass = '';

// 2. Handle Logout Action
if (isset($_GET['logout']) || (isset($_GET['action']) && $_GET['action'] == 'logout')) {
    session_unset();
    session_destroy();
    header('Location: profile.php');
    exit;
}

// 3. Auto-redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
        header('Location: dashboard.php');
    } else {
        header('Location: user_dashboard.php');
    }
    exit;
}

if (isset($_GET['registered'])) {
    $message = 'Registration successful. Please log in.';
    $messageClass = 'success';
}

// 4. Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $message = 'Email and password are required.';
        $messageClass = 'error';
    } else {
        $servername = "localhost";
        $username = "root";
        $password_db = "";
        $dbname = "register";

        // Establish Database Connection
        $conn = new mysqli($servername, $username, $password_db, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare Statement
        $stmt = $conn->prepare("SELECT password, role FROM section WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($hashedPassword, $role);

            if ($stmt->fetch()) {
                // Verify password against database hash
                if (password_verify($password, $hashedPassword)) {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_role'] = $role;

                    // CRITICAL FIX: Close connections completely before redirecting
                    $stmt->close();
                    $conn->close();

                    if ($_SESSION['user_role'] == 'admin') {
                        header('Location: dashboard.php');
                    } else {
                        header('Location: user_dashboard.php');
                    }
                    exit();
                } else {
                    $message = 'Invalid email or password.';
                    $messageClass = 'error';
                }
            } else {
                $message = 'Invalid email or password.';
                $messageClass = 'error';
            }
            $stmt->close();
        } else {
            $message = 'Database query failed: ' . htmlspecialchars($conn->error);
            $messageClass = 'error';
        }
        $conn->close();
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile Login</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>

    <header>
        <div class="logo">Login Page</div>
        <nav>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="login-container">
      <header class="brand">
        <img src="my.png" alt="Logo" class="logo-icon" />
        <span class="logo-text">Aashif Shekh</span>
      </header>

      <div class="login-card">
        
        <?php if ($message): ?>
            <p style="padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; font-weight: bold; 
              background-color: <?= $messageClass === 'success' ? '#d4edda' : '#f8d7da' ?>; 
              color: <?= $messageClass === 'success' ? '#155724' : '#721c24' ?>;">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <p class="subtitle">Please enter your details</p>
        <h1>Welcome back</h1>

        <form method="POST" action="profile.php">
          <div class="input-group">
            <input type="email" name="email" placeholder="Email address" required />
          </div>
          <div class="input-group">
            <input type="password" name="password" placeholder="Password" required />
          </div>

          <div class="options-row">
            <label class="checkbox-container">
              <input type="checkbox" />
              <span class="checkmark"></span>
              Remember for 30 days
            </label>
            <a href="#" class="forgot-link">Forgot password</a>
          </div>

          <button type="submit" class="btn-primary">Log in</button>

          <a class="btn-google" href="http://account.google.com" target="_blank" rel="noopener noreferrer">
            <img src="google.png" alt="Google" />
            Sign in with Google
          </a>
          <a class="btn-google" href="http://m.facebook.com" target="_blank" rel="noopener noreferrer">
            <img src="fb.png" alt="Facebook" />
            Sign in with Facebook
          </a>
          <a class="btn-google" href="http://appleid.apple.com" target="_blank" rel="noopener noreferrer">
            <img src="Apple.png" alt="Apple" />
            Sign in with iOS
          </a>
        </form>

        <p class="signup-footer">
          Don’t have an account? <a href="sign.php">Sign up</a>
        </p>
      </div>
    </div>
  </body>
</html>
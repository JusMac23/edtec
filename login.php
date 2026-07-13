<?php

// 1. Include the Database wrapper class using a reliable absolute path
require_once __DIR__ . '/database/db.php';

// 2. Safely initialize the PDO connection engine (also handles autoloading & .env)
$pdo = Database::getInstance()->getConnection();

// 3. Start the session securely with modern cookie parameters
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict'); // Protects against cross-site session leaks
    // ini_set('session.cookie_secure', 1); // Uncomment this when you deploy to a live HTTPS server
    session_start();
}

// 4. If the user is already logged in, send them straight to the homepage
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user inputs
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        // ENHANCEMENT: Prevent Password Hashing DoS Attacks.
        // Extremely long strings can lag password_verify() and freeze server CPU cores.
        if (strlen($password) > 72) {
            $error = "Invalid email or password.";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id, first_name, last_name, password_hash, role FROM users WHERE email = :email LIMIT 1");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch();

                // ENHANCEMENT: Timing Attack Mitigation.
                // If the user doesn't exist, execute a dummy verify to equalize request times
                $dummy_hash = '$2y$10$abcdefghijklmnopqrstuvwx'; 
                $hash_to_verify = $user ? $user['password_hash'] : $dummy_hash;

                if (password_verify($password, $hash_to_verify) && $user) {
                    
                    // SECURITY: Regenerate session ID to prevent Session Fixation attacks
                    session_regenerate_id(true);

                    // Store critical user details in the session
                    $_SESSION['user_id']    = (int)$user['id']; 
                    $_SESSION['first_name'] = $user['first_name']; 
                    $_SESSION['last_name']  = $user['last_name'];  
                    $_SESSION['role']       = $user['role'];

                    // Redirect to protected homepage
                    header("Location: index.php");
                    exit;
                } else {
                    // SECURITY NOTE: Keep error messages vague to prevent user enumeration
                    $error = "Invalid email or password.";
                }
            } catch (\PDOException $e) {
                error_log("Database execution fault during login: " . $e->getMessage());
                $error = "An error occurred. Please try again later.";
            }
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./css/auth-styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="login-container">
        <h1>Admin Login</h1>
        <p class="subtitle">Please log in to access the website</p>

        <?php if (!empty($error)): ?>
            <div class="error-box" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="error-icon"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="name@example.com" required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-field-container">
                    <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="password-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                        <svg class="eye-show" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg class="eye-hide" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path><line x1="2" y1="2" x2="22" y2="22"></line></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="submit-btn">Sign In</button>

            <a href="index.php" class="back-index-btn">
                <svg style="margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Main Site
            </a>
        </form>

    </div>

    <script>
        // Synchronize dark/light layout context matching theme configurations from dashboard
        const currentTheme = localStorage.getItem('theme');
        if (currentTheme === 'dark' || (!currentTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }

        // Handle Reveal/Obfuscate transformations on password text fields
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');

        passwordToggle.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            passwordToggle.classList.toggle('revealed');
        });
    </script>
</body>
</html>
<?php
// Location: C:\xampp\htdocs\sample-blogs\auth\auth.php

// Step up one directory level to find the root vendor folder
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Include the Database wrapper class file using absolute path evaluation
require_once dirname(__DIR__) . '/database/db.php';

// Explicitly pull your PDO database engine connection instance safely
$pdo = Database::getInstance()->getConnection();

// Start the session securely with modern cookie parameters
if (session_status() === PHP_SESSION_NONE) {
    // 1. Prevent JavaScript access to session cookies (XSS mitigation)
    ini_set('session.cookie_httponly', 1);
    
    // 2. Prevent session ID from being passed via URLs
    ini_set('session.use_only_cookies', 1);
    
    // 3. ENHANCEMENT: Enforce SameSite policy to mitigate Cross-Site Request Forgery (CSRF) attacks
    ini_set('session.cookie_samesite', 'Strict');
    
    // 4. ENHANCEMENT: Uncomment this line if your live server uses HTTPS!
    // ini_set('session.cookie_secure', 1); 
    
    session_start();
}

// Global utility variables initialized with safe default configurations
$userId = $_SESSION['user_id'] ?? null;
$currentUser = null; 
$modalError = '';
$modalSuccess = '';

// Data-fetching engine
if ($userId !== null && !isset($currentUser)) {
    try {
        // ENHANCEMENT: Force the Session ID to be strictly evaluated as an integer.
        // This prevents attackers from attempting "Type Juggling" by injecting arrays or obscure string values.
        $cleanUserId = (int) $userId;

        // Prepared statements already act as an impenetrable wall against SQL injection.
        // ENHANCEMENT: Added 'LIMIT 1' for a micro-performance boost on unique ID lookups.
        $stmt = $pdo->prepare("SELECT first_name, last_name, email, role, avatar_url FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$cleanUserId]);
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Stale Session / Deleted User Protection
        if (!$currentUser) {
            // ENHANCEMENT: If the user was deleted from the DB but still has a cookie, 
            // completely destroy their active session on the server to prevent lingering unauthorized access.
            $_SESSION = [];
            session_destroy();
            
            $currentUser = null;
            $userId = null;
        }
    } catch (PDOException $e) {
        // Log the exact error internally, but keep the user interface safe
        error_log("CMS Loader Error (Auth): " . $e->getMessage());
        
        $currentUser = [
            'first_name' => 'Database Error',
            'last_name'  => 'User',
            'email'      => 'Offline Mode Active',
            'role'       => 'Error',
            'avatar_url' => null
        ];
    }
}
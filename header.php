<?php 

    // Authenticate and initialize safe database configurations
    require_once __DIR__ . '/auth/auth.php';

    // State Tracking logic setup
    $isDashboard = (basename($_SERVER['PHP_SELF']) === 'dashboard.php');
    $pathPrefix = $isDashboard ? '../' : '';
    $adminPrefix = $isDashboard ? '' : 'admin/';

    $org_email = "contact@example.com";

    try {
        // Re-engineered to safely query for admin profiles without insecure short-circuits (OR 1=1 removed)
        $stmt = $pdo->query("SELECT org_email FROM cms_contact_page ORDER BY org_id ASC LIMIT 1");
        $profile = $stmt->fetch();

        // Secondary fallback validation layer if an admin profile does not exist yet
        if (!$profile) {
            $stmt = $pdo->query("SELECT org_email FROM cms_contact_page ORDER BY org_id ASC LIMIT 1");
            $profile = $stmt->fetch();
        }

        if ($profile) {
            
            $org_email = !empty($profile['org_email']) ? $profile['org_email'] : $org_email;
        }
    } catch (\PDOException $e) {
        // Log query errors safely behind the scenes
        error_log("Contact Page DB Fetch Error: " . $e->getMessage());
    }
?>

<style>
    /* Glassmorphic Sticky Header Engine */
    header { background-color: var(--bg-primary); border-bottom: 1px solid var(--card-border); position: sticky; top: 0; z-index: 100; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); display: flex; flex-direction: column; width: 100%; }

    /* ==============================================
    TOP UTILITY NAVBAR (Theme & Account)
    ============================================== */
    .navbar-top { position: relative; height: 35px; width: 100%; max-width: 1500px; margin: 0 auto; padding: 0 2rem; display: flex; align-items: center; justify-content: space-between; }

    /* Full-bleed layout modifier for background AND borders */
    .navbar-top::before { content: ''; position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 100vw; height: 100%; background-color: var(--accent); border-top: 1px solid rgba(0, 0, 0, 0.05); border-bottom: 1px solid rgba(0, 0, 0, 0.05); pointer-events: none; z-index: -1; }

    /* Dark mode theme adjustments */
    [data-theme="dark"] .navbar-top::before { background-color: var(--accent); border-top: 1px solid rgba(255, 255, 255, 0.05); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }

    body { overflow-x: hidden; }

    /* Left Nav / Email Styling */
    .nav-left { color: #ffffff; }
    .email-link { display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit; font-size: 14px; font-weight: 500; opacity: 0.85; transition: opacity 0.2s; }
    .email-link:hover { opacity: 1; }
    .email-icon { width: 18px; height: 18px; }

    /* Right Nav Links Structure */
    .nav-links { display: flex; align-items: center; gap: 1rem; height: 100%; }
    .social-icons { display: flex; gap: 10px; margin-right: 14px; align-items: center; }

    /* Compact Reusable System Action Icons */
    .navbar-top .icon-btn { background: none; border: none; color: rgba(255, 255, 255, 0.85); cursor: pointer; padding: 0.25rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    .navbar-top .icon-btn:hover { background-color: rgba(255, 255, 255, 0.2); color: #ffffff; transform: scale(1.05); }
    .navbar-top .icon-btn svg { width: 19px; height: 19px; }
    .theme-toggle-btn { margin-right: 8px; }

    /* Logic Engine visibility styles for Dark Mode Theme Toggles */
    .sun-icon { display: none; width: 16px; height: 16px; }
    .moon-icon { display: block; width: 16px; height: 16px; }
    [data-theme="dark"] .sun-icon { display: block; }
    [data-theme="dark"] .moon-icon { display: none; }

    /* Admin specific buttons for Top Bar */
    .admin-link-btn { display: flex; align-items: center; gap: 0.35rem; background-color: rgba(255, 255, 255, 0.15); color: #ffffff; text-decoration: none; padding: 0.15rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid rgba(255, 255, 255, 0.1); }
    .admin-link-btn:hover { background-color: rgba(255, 255, 255, 0.25); border-color: rgba(255, 255, 255, 0.3); transform: translateY(-1px); }
    .admin-link-btn svg { width: 13px; height: 13px; }

    /* Account Management Component Layout Styles */
    .account-menu { position: relative; display: flex; align-items: center; height: 100%; }
    .avatar-btn { background: none; border: 2px solid transparent; cursor: pointer; border-radius: 50%; width: 26px; height: 26px; overflow: hidden; display: flex; align-items: center; justify-content: center; padding: 0; transition: border-color 0.2s ease, transform 0.2s ease; }
    .avatar-btn:focus, .avatar-btn:hover { border-color: #ffffff; transform: scale(1.05); }
    .avatar-img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-fallback { width: 100%; height: 100%; background-color: #ffffff; color: var(--accent); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.75rem; }

    /* Dropdown Window Animation Configurations */
    .account-dropdown { position: absolute; top: calc(100% + 8px); right: 0; background-color: var(--card-bg, var(--bg-primary)); border: 1px solid var(--card-border); border-radius: 12px; width: 260px; padding: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.08); display: flex; flex-direction: column; opacity: 0; visibility: hidden; transform: translateY(-10px) scale(0.95); transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); z-index: 200; transform-origin: top right; }
    .account-dropdown.active { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
    .dropdown-header { padding-bottom: 0.5rem; }
    .user-name { font-weight: 600; font-size: 0.95rem; color: var(--text-primary); line-height: 1.2; margin: 0 0 0.25rem 0; }
    .user-email { font-size: 0.8rem; color: var(--text-secondary); word-break: break-all; margin-bottom: 0.5rem; margin-top: 0.2rem; }
    .user-badge { display: inline-block; font-size: 0.7rem; font-weight: 600; background-color: var(--bg-secondary); color: var(--text-primary); padding: 0.2rem 0.6rem; border-radius: 20px; border: 1px solid var(--card-border); }
    .dropdown-divider { border: 0; border-top: 1px solid var(--card-border); margin: 0.75rem 0; }
    .dropdown-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 0.75rem; border-radius: 8px; text-decoration: none; font-size: 0.9rem; color: var(--text-primary); font-weight: 500; transition: background-color 0.2s ease, color 0.2s ease; }
    .dropdown-item svg { width: 18px; height: 18px; opacity: 0.8; }
    .dropdown-item:hover { background-color: var(--bg-secondary); }
    .logout-link { color: #ef4444; }
    .logout-link:hover { background-color: #fef2f2; color: #b91c1c; }
    [data-theme="dark"] .logout-link:hover { background-color: rgba(239, 68, 68, 0.1); color: #f87171; }

    /* ==============================================
    RESPONSIVE MEDIA QUERIES (TOP NAVBAR)
    ============================================= */
    @media screen and (max-width: 768px) {
        .navbar-top { padding: 0 1rem; }
        .nav-links { gap: 0.75rem; }
        .social-icons { margin-right: 5px; gap: 5px; }
        .email-text, .btn-text { display: none; }
        .admin-link-btn { padding: 0.25rem; border-radius: 50%; width: 28px; height: 28px; justify-content: center; gap: 0; }
        .admin-link-btn svg { width: 16px; height: 16px; }
    }

    @media screen and (max-width: 480px) {
        .navbar-top { padding: 0 0.5rem; }
        .theme-toggle-btn { margin-right: 0; }
    }

    /* ==============================================
    MAIN NAVBAR (Logo & Links)
    ============================================== */
    .navbar { height: 70px; width: 100%; max-width: 1500px; margin: 0 auto; padding: 0 2rem; display: flex; align-items: center; gap: 2.5rem; }
    .menu-toggle { display: none !important; width: 40px; height: 40px; }
    .logo { font-size: 1.35rem; font-weight: 700; color: var(--text-tertiary); text-decoration: none; letter-spacing: -0.03em; display: flex; align-items: center; gap: 0.5rem; transition: opacity 0.2s ease; margin-right: auto; }
    .logo:hover { opacity: 0.8; }
    .nav-menu { display: flex; align-items: center; gap: 0.1rem; height: 100%; }
    .nav-item { color: var(--text-secondary); text-decoration: none; font-size: 0.925rem; font-weight: 600; position: relative; padding: 0 1.25rem; display: flex; align-items: center; height: 100%; width: auto; transition: background-color 0.2s ease, color 0.2s ease; border-top: 2px solid transparent; box-sizing: border-box; }
    .nav-item:hover, .nav-item.active { color: var(--text-primary); background-color: var(--bg-secondary); border-top-color: var(--accent); }
    .nav-item::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 2px; background-color: var(--accent); transform: scaleX(0); transform-origin: right; transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
    .nav-item:hover::after, .nav-item.active::after { transform: scaleX(1); transform-origin: left; }

    /* ==============================================
    COURSES DROPDOWN ENGINE MODULE (ENHANCED)
    ============================================== */
    .nav-item.dropdown { padding: 0; cursor: pointer; }
    .nav-item.dropdown > span { display: flex; align-items: center; gap: 6px; padding: 0 1.25rem; height: 100%; width: 100%; box-sizing: border-box; }
    .nav-item.dropdown > span::after { content: ''; display: inline-block; width: 0; height: 0; border-top: 4px solid currentColor; border-right: 4px solid transparent; border-left: 4px solid transparent; transition: transform 0.2s ease; }
    .nav-item.dropdown:hover > span::after { transform: rotate(180deg); }
    
    .nav-item.dropdown ul { position: absolute; top: 100%; left: 0; background-color: var(--card-bg, var(--bg-primary)); border: 1px solid var(--card-border); border-radius: 12px; min-width: 250px; list-style: none; padding: 0.5rem; margin: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.08); opacity: 0; visibility: hidden; transform: translateY(10px); transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); z-index: 200; display: flex; flex-direction: column; gap: 2px; }
    .nav-item.dropdown:hover ul { opacity: 1; visibility: visible; transform: translateY(0); }
    .nav-item.dropdown ul li { width: 100%; }
    
    /* Enhanced Link Styling */
    .nav-item.dropdown ul li a { display: flex; align-items: center; padding: 0.65rem 1rem; color: var(--text-secondary); text-decoration: none; font-size: 0.875rem; font-weight: 500; border-radius: 8px; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
    
    /* Indent on hover for sleek effect */
    .nav-item.dropdown ul li a:hover { background-color: var(--bg-secondary); color: var(--text-primary); padding-left: 1.25rem; }

    /* Animation specifically for the "//" prefix */
    .slash { display: inline-block; color: var(--accent); margin-right: 6px; font-weight: 700; opacity: 0.6; transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), color 0.25s ease, opacity 0.25s ease; }
    
    /* Slide right and change color/opacity on hover */
    .nav-item.dropdown ul li a:hover .slash { transform: translateX(4px) scale(1.05); color: var(--text-primary); opacity: 1; }

    /* Dashboard Action Buttons */
    .register-index-btn, .back-index-btn { display: inline-flex; align-items: center; margin-left: 1rem; background-color: var(--accent); color: #ffffff; text-decoration: none; padding: 0.5rem 1.25rem; border-radius: 10px; font-size: 0.875rem; font-weight: 600; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); transition: background-color 0.2s ease, transform 0.2s ease; }
    .register-index-btn, .back-index-btn:hover { opacity: 0.9; transform: translateY(-1px); }

    /* ==============================================
    RESPONSIVE MOBILE VIEWPORT (ENHANCED)
    ============================================== */
    @media screen and (max-width: 768px) {
        .navbar-top { padding: 0 1rem; }
        .navbar { padding: 0 1rem; gap: 1rem; }
        .menu-toggle { display: flex !important; }
        .nav-menu { position: absolute; top: 100%; left: 0; width: 100%; height: auto; background-color: var(--bg-primary); border-bottom: 1px solid var(--card-border); flex-direction: column; align-items: stretch; opacity: 0; visibility: hidden; transform: translateY(-12px); transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1); z-index: 90; padding: 0.75rem; box-shadow: 0 12px 25px -5px rgba(0,0,0,0.08); gap: 0.35rem; box-sizing: border-box; }
        .nav-menu.active { opacity: 1; visibility: visible; transform: translateY(0); }
        .nav-item { height: 48px; padding: 0 1.25rem; width: 100%; box-sizing: border-box; border-top: none; border-radius: 8px; border-left: 3px solid transparent; font-size: 0.95rem; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .nav-item:hover, .nav-item.active { color: var(--accent); background-color: var(--bg-secondary); border-left-color: var(--accent); padding-left: calc(1.25rem + 4px); font-weight: 700; }
        .nav-item::after { display: none; }

        /* Mobile specific overrides for Courses Dropdown Layout */
        .nav-item.dropdown { height: auto !important; padding: 0 !important; flex-direction: column; align-items: stretch; }
        .nav-item.dropdown > span { height: 48px; padding: 0 1.25rem; justify-content: space-between; }
        .nav-item.dropdown:hover > span, .nav-item.dropdown.active > span { padding-left: calc(1.25rem + 4px); }
        .nav-item.dropdown ul { position: static; opacity: 1; visibility: visible; transform: none; box-shadow: none; border: none; width: 100%; padding: 0.25rem 0 0.5rem 1.5rem; background-color: transparent; display: none; gap: 0.25rem; }
        .nav-item.dropdown:hover ul, .nav-item.dropdown ul.active { display: flex; }
        .nav-item.dropdown ul li a { padding: 0.5rem 1.25rem; }
        
        /* Keep mobile indent clean */
        .nav-item.dropdown ul li a:hover { padding-left: 1.5rem; }
    }

    @media screen and (max-width: 480px) {
        .admin-link-btn span { display: none; }
        .admin-link-btn { padding: 0.35rem; border-radius: 50%; }
    }
</style> 

<header>
    <div class="navbar-top">
        <div class="nav-left">
            <a href="mailto:contact@yourdomain.com" class="email-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="email-icon">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <span class="email-text"><?= htmlspecialchars($org_email ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
        </div>

        <div class="nav-links">
            <div class="social-icons">
                <a href="https://facebook.com/" class="icon-btn" target="_blank" aria-label="Facebook" title="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                    </svg>
                </a>
                
                <a href="https://youtube.com/" class="icon-btn" target="_blank" aria-label="YouTube" title="YouTube">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path>
                        <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
                    </svg>
                </a>

                <a href="https://instagram.com/" class="icon-btn" target="_blank" aria-label="Instagram" title="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>
                </a>

                <a href="https://twitter.com/" class="icon-btn" target="_blank" aria-label="Twitter" title="Twitter">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                    </svg>
                </a>
            </div>

            <button class="icon-btn theme-toggle-btn" id="themeToggle" aria-label="Toggle Theme">
                <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path></svg>
                <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path></svg>
            </button>
            
            <?php if (isset($userId) && $userId && isset($currentUser)): ?>
                <a href="<?= $adminPrefix ?>dashboard.php" class="admin-link-btn" title="Manage CMS">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                    <span class="btn-text">CMS Panel</span>
                </a>

                <div class="account-menu">
                    <button class="avatar-btn" id="accountToggle" aria-label="Toggle Account Menu">
                        <?php if (!empty($currentUser['avatar_url'])): ?>
                            <img src="<?= htmlspecialchars($currentUser['avatar_url']) ?>" alt="Avatar" class="avatar-img">
                        <?php else: ?>
                            <div class="avatar-fallback">
                                <?php 
                                    $fInit = substr(trim($currentUser['first_name'] ?? 'A'), 0, 1);
                                    $lInit = substr(trim($currentUser['last_name'] ?? 'D'), 0, 1);
                                    echo htmlspecialchars(strtoupper($fInit . $lInit));
                                ?>
                            </div>
                        <?php endif; ?>
                    </button>
                    
                    <div class="account-dropdown" id="accountDropdown">
                        <div class="dropdown-header">
                            <p class="user-name">
                                <?= htmlspecialchars(trim(($currentUser['first_name'] ?? 'Admin') . ' ' . ($currentUser['last_name'] ?? 'User'))) ?>
                            </p>
                            <p class="user-email"><?= htmlspecialchars($currentUser['email'] ?? '') ?></p>
                            <span class="user-badge"><?= ucfirst(htmlspecialchars($currentUser['role'] ?? 'Administrator')) ?></span>
                        </div>
                        <hr class="dropdown-divider">
                        <a href="<?= $pathPrefix ?>logout.php" class="dropdown-item logout-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= $pathPrefix ?>login.php" class="admin-link-btn" title="Admin Login">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    <span class="btn-text">Admin Login</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <nav class="navbar">
        <button class="icon-btn menu-toggle" id="menuToggle" aria-label="Toggle Navigation Menu">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 22px; height: 22px;">
                <line x1="4" y1="12" x2="20" y2="12"></line>
                <line x1="4" y1="6" x2="20" y2="6"></line>
                <line x1="4" y1="18" x2="20" y2="18"></line>
            </svg>
        </button>

        <a href="<?= $pathPrefix ?>index.php" class="logo">EDTEC</a>
        
        <div class="nav-menu" id="navMenu">
            <?php if ($isDashboard): ?>
                <a href="../index.php" class="back-index-btn">
                    <svg style="margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to Main Site
                </a>
            <?php else: ?>
                <a href="index.php" class="nav-item">Home</a>
                <div class="nav-item dropdown<?php if (basename($_SERVER['PHP_SELF']) === 'course.php') echo ' active'; ?>">
                    <span>Courses</span>
                    <ul<?php if (basename($_SERVER['PHP_SELF']) === 'course.php') echo ' class="active"'; ?>>
                        <li><a href="course.php#2-year-bundled-courses"><span class="slash">//</span> 2-year Bundled Courses</a></li>
                        <li><a href="course.php#nominal-duration-courses"><span class="slash">//</span> Nominal Duration Courses</a></li>
                        <li><a href="course.php#digital-special-training-courses"><span class="slash">//</span> Special Training Courses </a></li>
                    </ul>
                </div>

                <div class="nav-item dropdown<?php if (basename($_SERVER['PHP_SELF']) === 'about.php') echo ' active'; ?>">
                    <span>About Us</span>
                    <ul<?php if (basename($_SERVER['PHP_SELF']) === 'about.php') echo ' class="active"'; ?>>
                        <li><a href="about.php#about"><span class="slash">//</span> Facts and History</a></li>
                        <li><a href="about.php#mission-vision"><span class="slash">//</span> Mission and Vission</a></li>
                        <li><a href="about.php#goals"><span class="slash">//</span> Goals</a></li>
                        <li><a href="about.php#tagline"><span class="slash">//</span> Tagline</a></li>
                        <li><a href="officers.php"><span class="slash">//</span> Officers</a></li>
                    </ul>
                </div>

                <a href="contact.php" class="nav-item">Contact Us</a>
                <a href="policy.php" class="nav-item">Policy Guidelines</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<script>
    // --- Mobile Menu Navigation Toggle Logic ---
    const menuToggle = document.getElementById('menuToggle');
    const navMenu = document.getElementById('navMenu');
    const accountDropdown = document.getElementById('accountDropdown');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            navMenu.classList.toggle('active');
            if (accountDropdown) accountDropdown.classList.remove('active');
        });
    }

    // --- Auto-Active Link Engine ---
    const currentPath = window.location.pathname.split('/').pop() || 'index.php';
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });

    // --- Theme Engine Logic ---
    const themeToggle = document.getElementById('themeToggle');
    const currentTheme = localStorage.getItem('theme');

    if (currentTheme) {
        document.documentElement.setAttribute('data-theme', currentTheme);
    } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.setAttribute('data-theme', 'dark');
    }

    themeToggle.addEventListener('click', () => {
        let theme = document.documentElement.getAttribute('data-theme');
        if (theme === 'dark') {
            document.documentElement.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        }
    });

    // --- Account Dropdown Navigation Logic (Only triggers if logged in) ---
    const accountToggle = document.getElementById('accountToggle');

    if (accountToggle && accountDropdown) {
        accountToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            accountDropdown.classList.toggle('active');
            if (navMenu) navMenu.classList.remove('active');
        });
    }

    // Global document interaction cleaner
    document.addEventListener('click', (e) => {
        if (accountDropdown && accountToggle && !accountDropdown.contains(e.target) && !accountToggle.contains(e.target)) {
            accountDropdown.classList.remove('active');
        }
        if (navMenu && menuToggle && !navMenu.contains(e.target) && !menuToggle.contains(e.target)) {
            navMenu.classList.remove('active');
        }
    });
</script>
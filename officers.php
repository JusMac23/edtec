<?php

// Authenticate and initialize safe database configurations
require_once __DIR__ . '/auth/auth.php';

// Safe programmatic fallback values
$officersData = [
    'officer_firstname' => 'John',
    'officer_middleinitial' => 'D.',
    'officer_lastname' => 'Doe',
    'officer_position' => 'Position Title',
    'officer_photo' => '', // Set to empty to trigger the fallback span instead of a broken image
];

$fallbackData = $officersData ?? [];

// Initialize distinct arrays to hold data for each independent section
$botOfficers  = [];
$ofadOfficers = [];
$titOfficers  = [];

// Smart PDO Connection Detector (Handles common variable names: $db, $conn, $pdo)
$pdo_conn = null;
if (isset($db) && $db instanceof PDO) {
    $pdo_conn = $db;
} elseif (isset($conn) && $conn instanceof PDO) {
    $pdo_conn = $conn;
} elseif (isset($pdo) && $pdo instanceof PDO) {
    $pdo_conn = $pdo;
}

// Fetch data for all categories if the database connection is available
if ($pdo_conn) {
    try {
        // 1. Fetch all Board of Trustees
        $stmt = $pdo_conn->prepare("SELECT * FROM cms_officers_bot_page ORDER BY officer_id ASC");
        $stmt->execute();
        $botOfficers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Fetch all Officers for Administration
        $stmt = $pdo_conn->prepare("SELECT * FROM cms_officers_ofad_page ORDER BY officer_id ASC");
        $stmt->execute();
        $ofadOfficers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Fetch all Training and Instructions Team members
        $stmt = $pdo_conn->prepare("SELECT * FROM cms_officers_tit_page ORDER BY officer_id ASC");
        $stmt->execute();
        $titOfficers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Log the error silently if queries fail
        error_log("Database Fetch Error: " . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officers</title>
    <link rel="stylesheet" href="./css/index-styles.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Modern CSS Variables Design System */
        :root {
            --bg-primary: #f8fafc;
            --bg-surface: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --accent: #1e40af;
            --accent-light: #eff6ff;
            --card-border: rgba(226, 232, 240, 0.8);
            --radius-lg: 20px;
            --radius-md: 12px;
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 16px -6px rgba(15, 23, 42, 0.08);
            --transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        [data-theme="dark"], body.dark {
            --bg-primary: #0f172a;
            --bg-surface: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #3b82f6;
            --accent-light: rgba(59, 130, 246, 0.1);
            --card-border: rgba(51, 65, 85, 0.5);
            --shadow-md: 0 20px 30px -10px rgba(0, 0, 0, 0.3);
        }

        /* Base Resets & Global Smoothness */
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-primary); color: var(--text-main); margin: 0; padding: 0; transition: background-color 0.3s ease, color 0.3s ease; }
        @keyframes containerFadeIn { 0% { opacity: 0; transform: translateY(30px); } 100% { opacity: 1; transform: translateY(0); } }

        /* Layout Architecture */
        .officers-container { width: 100%; max-width: 1300px; margin: 0 auto; padding: 5rem 2rem; box-sizing: border-box; display: flex; flex-direction: column; gap: 5rem; animation: containerFadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* Category Sub-containers */
        .bot-container, .ofad-container, .tit-container { display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 2.5rem; width: 100%; }
        .officers-container h1 { font-size: 2.25rem; font-weight: 800; color: var(--accent); margin: 0; letter-spacing: -0.03em; text-align: center; position: relative; padding-bottom: 0.5rem; }
        .officers-container h1::before { content: ''; position: absolute; left: 50%; bottom: 0; top: auto; transform: translateX(-50%); height: 4px; width: 60px; background: var(--accent); border-radius: 4px; }

        /* Responsive Grid Framework */
        .officers-section { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 320px)); justify-content: center; gap: 2.5rem; width: 100%; }

        /* Modernized Profile Cards */
        .officer-card { background: var(--bg-surface); border: 1px solid var(--card-border); border-radius: var(--radius-lg); padding: 2.5rem 2rem; box-shadow: var(--shadow-sm); transition: var(--transition); display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; position: relative; overflow: hidden; width: 100%; box-sizing: border-box; }
        .officer-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-md); border-color: var(--accent); }

        /* Dynamic Visual Accent Ring around Avatar */
        .officers-photo-placeholder { width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), #6366f1); padding: 4px; box-shadow: 0 8px 20px -6px rgba(0,0,0,0.15); margin-bottom: 1.75rem; transition: var(--transition); display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .officer-card:hover .officers-photo-placeholder { transform: scale(1.05) rotate(2deg); }
        .officers-photo-placeholder img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; background: var(--bg-surface); }
        .officers-photo-placeholder span { font-size: 8.0rem; color: #ffffff; font-weight: 500; padding: 1rem; text-align: center; line-height: 1.3; }

        /* Profile Typography */
        .officer-card h2 { font-size: 1.35rem; font-weight: 700; margin: 0 0 0.5rem 0; color: var(--text-main); letter-spacing: -0.01em; line-height: 1.3; }
        .officer-card .position-tag { font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--accent); background: var(--accent-light); padding: 0.4rem 1.2rem; border-radius: 30px; display: inline-block; }

        /* Smooth Responsiveness Adjustments */
        @media screen and (max-width: 1024px) { 
            .officers-container { padding: 4rem 1.5rem; gap: 4rem; } 
            .officers-container h1 { font-size: 1.85rem; } 
        }
        @media screen and (max-width: 600px) { 
            .officers-container { padding: 3rem 1rem; gap: 3rem; } 
            .officers-section { grid-template-columns: 1fr; gap: 1.5rem; } 
            .officer-card { padding: 2rem 1.5rem; } 
        }
    </style>
</head>
<body>

    <?php include("header.php"); ?>

    <div id="officers" class="officers-container">

        <div class="bot-container">
            <h1>Board of Trustees</h1>
            <section class="officers-section">

                <?php if (!empty($botOfficers)): ?>
                    <?php foreach ($botOfficers as $dbData): 
                        $filteredDbData = array_filter($dbData, function($value) {
                            return $value !== null && $value !== '';
                        });
                        $currentOfficer = array_merge($fallbackData, $filteredDbData);
                        
                        // Check for both 'officer_photo' and 'officers_photo' to prevent key mismatch bugs
                        $photoUrl = $currentOfficer['officer_photo'] ?? $currentOfficer['officers_photo'] ?? '';
                    ?>
                        <div class="officer-card">
                            <div class="officers-photo-placeholder">
                                <?php if (!empty($photoUrl)): ?>
                                    <img src="<?= htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Officer Photo">
                                <?php else: ?>
                                    <span class="material-symbols-outlined">person</span>
                                <?php endif; ?>
                            </div>
                            <h2><?= htmlspecialchars(trim(($currentOfficer['officer_firstname'] ?? '') . ' ' . ($currentOfficer['officer_middleinitial'] ?? '') . ' ' . ($currentOfficer['officer_lastname'] ?? '')), ENT_QUOTES, 'UTF-8') ?></h2>
                            <span class="position-tag"><?= htmlspecialchars($currentOfficer['officer_position'] ?? 'Trustee', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-muted);">No Board of Trustees profiles active at this time.</p>
                <?php endif; ?>

            </section>
        </div>

        <div class="ofad-container">
            <h1>Officers for Administration</h1>
            <section class="officers-section">

                <?php if (!empty($ofadOfficers)): ?>
                    <?php foreach ($ofadOfficers as $dbData): 
                        $filteredDbData = array_filter($dbData, function($value) {
                            return $value !== null && $value !== '';
                        });
                        $currentOfficer = array_merge($fallbackData, $filteredDbData);
                        $photoUrl = $currentOfficer['officer_photo'] ?? $currentOfficer['officers_photo'] ?? '';
                    ?>
                        <div class="officer-card">
                            <div class="officers-photo-placeholder">
                                <?php if (!empty($photoUrl)): ?>
                                    <img src="<?= htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Officer Photo">
                                <?php else: ?>
                                    <span class="material-symbols-outlined">person</span>
                                <?php endif; ?>
                            </div>
                            <h2><?= htmlspecialchars(trim(($currentOfficer['officer_firstname'] ?? '') . ' ' . ($currentOfficer['officer_middleinitial'] ?? '') . ' ' . ($currentOfficer['officer_lastname'] ?? '')), ENT_QUOTES, 'UTF-8') ?></h2>
                            <span class="position-tag"><?= htmlspecialchars($currentOfficer['officer_position'] ?? 'Administrator', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-muted);">No Administration profiles active at this time.</p>
                <?php endif; ?>

            </section>
        </div>

        <div class="tit-container">
            <h1>Training and Instructions Team</h1>
            <section class="officers-section">

                <?php if (!empty($titOfficers)): ?>
                    <?php foreach ($titOfficers as $dbData): 
                        $filteredDbData = array_filter($dbData, function($value) {
                            return $value !== null && $value !== '';
                        });
                        $currentOfficer = array_merge($fallbackData, $filteredDbData);
                        $photoUrl = $currentOfficer['officer_photo'] ?? $currentOfficer['officers_photo'] ?? '';
                    ?>
                        <div class="officer-card">
                            <div class="officers-photo-placeholder">
                                <?php if (!empty($photoUrl)): ?>
                                    <img src="<?= htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Officer Photo">
                                <?php else: ?>
                                    <span class="material-symbols-outlined">person</span>
                                <?php endif; ?>
                            </div>
                            <h2><?= htmlspecialchars(trim(($currentOfficer['officer_firstname'] ?? '') . ' ' . ($currentOfficer['officer_middleinitial'] ?? '') . ' ' . ($currentOfficer['officer_lastname'] ?? '')), ENT_QUOTES, 'UTF-8') ?></h2>
                            <span class="position-tag"><?= htmlspecialchars($currentOfficer['officer_position'] ?? 'Instructor', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-muted);">No Training Team profiles active at this time.</p>
                <?php endif; ?>

            </section>
        </div>
        
    </div>

    <?php include("footer.php"); ?>

</body>
</html>
<?php

// Authenticate and initialize safe database configurations
require_once __DIR__ . '/auth/auth.php';

// Safe programmatic fallback values (including new image track variables)
$aboutData = [
    'hero_title'    => 'About our organization.',
    'hero_desc1'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    'hero_desc2'     => 'Additional description for the hero banner.',
    'hero_desc3'     => 'Yet another description for the hero banner.',
    'hero_desc4'     => 'One more description for the hero banner.',
    'hero_desc5'     => 'Final description for the hero banner.',

    'mission_title' => 'Our Mission',
    'mission_p'    => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
    'mission_img'   => '',
    'vision_title'  => 'Our Vision',
    'vision_p'      => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
    'vision_img'    => '',

    'goals_title'   => 'Our Goals',
    'goals_p1'       => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    'goals_p2'       => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    'goals_p3'       => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
    'goals_p4'       => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
    'goals_p5'       => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
    'goals_p6'       => 'Additional goal description 6.',
    'goals_p7'       => 'Additional goal description 7.',
    'goals_p8'       => 'Additional goal description 8.',

    'tagline_title' => 'Our Tagline',
    'tagline_p1'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    'tagline_p2'     => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    'tagline_p3'     => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
    'tagline_p4'     => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
    'tagline_p5'     => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
];

// Fetch content from the database table "cms_about_page"
try {
    // Check if a PDO instance is available (fallback to $conn if that's your variable name)
    $db = $pdo ?? $conn ?? null;

    if ($db instanceof PDO) {
        $stmt = $db->prepare("SELECT * FROM cms_about_page LIMIT 1");
        $stmt->execute();
        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dbData) {
            // Clean out null or empty values from DB so fallback hardcoded arrays remain intact
            $filteredDbData = array_filter($dbData, function($value) {
                return $value !== null && $value !== '';
            });
            $aboutData = array_merge($aboutData, $filteredDbData);
        }
    }
} catch (PDOException $e) {
    // Fail gracefully: log the error and let fallback text display
    error_log("Database error fetching about page content: " . $e->getMessage());
}

// Global sanitization check for image file-paths to prevent Directory Traversal manipulations
foreach (['mission_img', 'vision_img'] as $imgKey) {
    if (!empty($aboutData[$imgKey])) {
        // Enforce basic cleanup checks to eliminate potential path injections (e.g., ../../../etc/passwd)
        $aboutData[$imgKey] = str_replace(['../', '..\\'], '', $aboutData[$imgKey]);
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="./css/index-styles.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Animations & Keyframes */
        @keyframes containerFadeIn { 0% { opacity: 0; transform: translateY(40px); } 100% { opacity: 1; transform: translateY(0); } }
        @keyframes slideFromLeft { 0% { transform: translateX(-80px); opacity: 0; } 100% { transform: translateX(0); opacity: 1; } }
        @keyframes slideFromRight { 0% { transform: translateX(80px); opacity: 0; } 100% { transform: translateX(0); opacity: 1; } }

        /* Global & Hero Banner Container */
        .about-container { width: 100%; background: linear-gradient(180deg, var(--bg-secondary, #f1f5f9) 0%, var(--bg-primary, #ffffff) 100%); border-bottom: 1px solid var(--card-border); box-sizing: border-box; text-align: center; padding: 6rem 2rem; animation: containerFadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
        .about-section { max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 0.75rem; }
        .about-section h1 { font-size: 3.25rem; font-weight: 800; letter-spacing: -0.04em; margin: 0 0 1.5rem 0; color: var(--accent); line-height: 1.15; transition: color 0.3s ease; }
        .about-section p { font-size: 1.15rem; text-align: justify; line-height: 1.6; color: var(--text-secondary, var(--accent)); margin: 0; transition: color 0.3s ease; }

        /* Mission & Vision Container (White Theme) */
        .mission-vision-container { width: 100%; background: var(--bg-primary, #ffffff); border-bottom: 1px solid var(--card-border); box-sizing: border-box; padding: 6rem 2rem; animation: containerFadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.15s forwards; opacity: 0; }
        .mission-vision-grid { width: 100%; max-width: 1400px; margin: 0 auto; display: flex; flex-direction: column; gap: 6rem; }
        .story-section { display: grid; grid-template-columns: 1fr 1fr; gap: 4.5rem; align-items: center; }
        .story-content h2 { font-size: 2.5rem; font-weight: 800; margin-top: 0; margin-bottom: 1.25rem; letter-spacing: -0.03em; color: var(--accent); line-height: 1.2; transition: color 0.3s ease; }
        .story-content p { font-size: 1.1rem; line-height: 1.8; margin: 0; color: var(--text-secondary); transition: color 0.3s ease; }
        .story-image-placeholder { background: linear-gradient(135deg, var(--accent-ring), var(--bg-tertiary)); border: 1px solid var(--card-border); border-radius: 24px; height: 420px; display: flex; align-items: center; justify-content: center; color: var(--accent); font-weight: 600; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1); transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease; overflow: hidden; }
        .story-image-placeholder img { width: 100%; height: 100%; object-fit: cover; }
        .story-image-placeholder:hover { transform: translateY(-8px) !important; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.15); }
        .story-section:nth-of-type(1) .story-content { animation: slideFromLeft 0.9s cubic-bezier(0.25, 1, 0.5, 1) 0.3s forwards; opacity: 0; }
        .story-section:nth-of-type(1) .story-image-placeholder { animation: slideFromRight 0.9s cubic-bezier(0.25, 1, 0.5, 1) 0.3s forwards; opacity: 0; }
        .story-section:nth-of-type(2) .story-image-placeholder { animation: slideFromLeft 0.9s cubic-bezier(0.25, 1, 0.5, 1) 0.4s forwards; opacity: 0; }
        .story-section:nth-of-type(2) .story-content { animation: slideFromRight 0.9s cubic-bezier(0.25, 1, 0.5, 1) 0.4s forwards; opacity: 0; }

        /* Goals Container (Gray Theme) */
        .goals-container { width: 100%; background: linear-gradient(180deg, var(--bg-secondary, #f8fafc) 0%, var(--bg-tertiary, #e2e8f0) 100%); border-bottom: 1px solid var(--card-border); box-sizing: border-box; padding: 6rem 2rem; animation: containerFadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.3s forwards; opacity: 0; }
        .goals-section { width: 100%; max-width: 1400px; margin: 0 auto; background: var(--bg-primary, #ffffff); border: 1px solid var(--card-border); border-radius: 24px; padding: 4rem; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.08); }
        .goals-content h2 { font-size: 2.5rem; font-weight: 800; color: var(--accent); text-align: center; margin: 0 0 2rem 0; }
        .goals-content p { font-size: 1.1rem; line-height: 1.8; margin: 0 0 1rem 0; text-align: left; color: var(--text-secondary); }
        .goals-content p:last-child { margin-bottom: 0; }
        .goals-section:hover { transform: translateY(-6px) !important; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.12); }

        /* Tagline Container (Gradient Blue Theme) */
        .tagline-container { width: 100%; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); box-sizing: border-box; padding: 8rem 2rem; animation: containerFadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.45s forwards; opacity: 0; }
        .tagline-section { width: 100%; max-width: 1200px; margin: 0 auto; text-align: center; color: #ffffff; }
        .tagline-content h2 { font-size: 2.75rem; font-weight: 800; color: #ffffff; margin: 0 0 1.5rem 0; text-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .tagline-content p { font-size: 1.2rem; line-height: 1.7; margin: 0 0 0.75rem 0; color: rgba(255, 255, 255, 0.95); }
        .tagline-content p:last-child { margin-bottom: 0; }
        .tagline-section:hover .tagline-content h2 { text-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .tagline-section:hover .tagline-content p { text-shadow: 0 2px 10px rgba(0,0,0,0.2); }

        /* DARK MODE OVERRIDES */
        body.dark .about-section h1, body.dark .story-content h2, body.dark .goals-content h2, [data-theme="dark"] .about-section h1, [data-theme="dark"] .story-content h2, [data-theme="dark"] .goals-content h2 { color: #ffffff !important; }
        body.dark .hero p, body.dark .story-content p, body.dark .goals-content p, [data-theme="dark"] .hero p, [data-theme="dark"] .story-content p, [data-theme="dark"] .goals-content p { color: rgba(255, 255, 255, 0.85) !important; }
        body.dark .goals-section { background: rgba(255, 255, 255, 0.03); border-color: rgba(255, 255, 255, 0.1); }

        /* RESPONSIVE QUERIES */
        @media screen and (max-width: 1024px) { 
            .hero-container, .mission-vision-container, .goals-container { padding: 4rem 1.5rem; } 
            .tagline-container { padding: 5rem 1.5rem; } .hero h1 { font-size: 2.75rem; } 
            .mission-vision-grid { gap: 4rem; } 
            .story-section { grid-template-columns: 1fr; gap: 2.5rem; } 
            .story-section:nth-of-type(odd) .story-content, .story-section:nth-of-type(even) .story-content { order: 1; text-align: center; } 
            .story-section:nth-of-type(odd) .story-image-placeholder, .story-section:nth-of-type(even) .story-image-placeholder { order: 2; height: 350px; } 
            .goals-section { padding: 3rem 2rem; } 
        }

        @media screen and (max-width: 768px) { 
            .about-section h1 { font-size: 2.5rem; } .about-section p { font-size: 1rem; }
            .tagline-container { padding: 4rem 1.25rem; } 
            .mission-vision-grid { gap: 3rem; } .story-image-placeholder { height: 260px; border-radius: 16px; } 
            .story-content h2, .goals-content h2, .tagline-content h2 { font-size: 1.85rem; } 
            .goals-section { padding: 2.5rem 1.25rem; border-radius: 16px; } 
        }
    </style>
</head>
<body>

    <?php include("header.php"); ?>

    <div id="about" class="about-container">
        <div class="about-section">
            <h1><?= htmlspecialchars($aboutData['hero_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
            <?php if(!empty($aboutData['hero_desc1'])): ?><p><?= htmlspecialchars($aboutData['hero_desc1'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($aboutData['hero_desc2'])): ?><p><?= htmlspecialchars($aboutData['hero_desc2'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($aboutData['hero_desc3'])): ?><p><?= htmlspecialchars($aboutData['hero_desc3'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($aboutData['hero_desc4'])): ?><p><?= htmlspecialchars($aboutData['hero_desc4'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($aboutData['hero_desc5'])): ?><p><?= htmlspecialchars($aboutData['hero_desc5'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>
    </div>

    <div id="mission-vision" class="mission-vision-container">
        <div class="mission-vision-grid">
            <section class="story-section">
                <div class="story-content">
                    <h2><?= htmlspecialchars($aboutData['mission_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
                    <p><?= htmlspecialchars($aboutData['mission_p'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div class="story-image-placeholder">
                    <?php if (!empty($aboutData['mission_img']) && file_exists($aboutData['mission_img'])): ?>
                        <img src="<?= htmlspecialchars($aboutData['mission_img'], ENT_QUOTES, 'UTF-8') ?>" alt="Our Mission">
                    <?php else: ?>
                        <span>No Mission Image Assigned</span>
                    <?php endif; ?>
                </div>
            </section>

            <section class="story-section">
                <div class="story-image-placeholder">
                    <?php if (!empty($aboutData['vision_img']) && file_exists($aboutData['vision_img'])): ?>
                        <img src="<?= htmlspecialchars($aboutData['vision_img'], ENT_QUOTES, 'UTF-8') ?>" alt="Our Vision">
                    <?php else: ?>
                        <span>No Vision Image Assigned</span>
                    <?php endif; ?>
                </div>
                <div class="story-content">
                    <h2><?= htmlspecialchars($aboutData['vision_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
                    <p><?= htmlspecialchars($aboutData['vision_p'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </section>
        </div>
    </div>

    <div id="goals" class="goals-container">
        <section class="goals-section">
            <div class="goals-content">
                <h2><?= htmlspecialchars($aboutData['goals_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
                <?php if(!empty($aboutData['goals_p1'])): ?><p><?= htmlspecialchars($aboutData['goals_p1'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                <?php if(!empty($aboutData['goals_p2'])): ?><p><?= htmlspecialchars($aboutData['goals_p2'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                <?php if(!empty($aboutData['goals_p3'])): ?><p><?= htmlspecialchars($aboutData['goals_p3'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                <?php if(!empty($aboutData['goals_p4'])): ?><p><?= htmlspecialchars($aboutData['goals_p4'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                <?php if(!empty($aboutData['goals_p5'])): ?><p><?= htmlspecialchars($aboutData['goals_p5'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                <?php if(!empty($aboutData['goals_p6'])): ?><p><?= htmlspecialchars($aboutData['goals_p6'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                <?php if(!empty($aboutData['goals_p7'])): ?><p><?= htmlspecialchars($aboutData['goals_p7'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                <?php if(!empty($aboutData['goals_p8'])): ?><p><?= htmlspecialchars($aboutData['goals_p8'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
        </section>
    </div>

    <div id="tagline" class="tagline-container">
        <section class="tagline-section">
            <div class="tagline-content">
                <h2><?= htmlspecialchars($aboutData['tagline_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
                <?php if(!empty($aboutData['tagline_p1'])): ?><p><?= htmlspecialchars($aboutData['tagline_p1'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                <?php if(!empty($aboutData['tagline_p2'])): ?><p><?= htmlspecialchars($aboutData['tagline_p2'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                <?php if(!empty($aboutData['tagline_p3'])): ?><p><?= htmlspecialchars($aboutData['tagline_p3'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                <?php if(!empty($aboutData['tagline_p4'])): ?><p><?= htmlspecialchars($aboutData['tagline_p4'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                <?php if(!empty($aboutData['tagline_p5'])): ?><p><?= htmlspecialchars($aboutData['tagline_p5'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
        </section>
    </div>

    <?php include("footer.php"); ?>

</body>
</html>
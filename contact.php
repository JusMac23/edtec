<?php

// Authenticate and initialize safe database configurations
require_once __DIR__ . '/auth/auth.php';

// Safe programmatic fallback values
$contactData = [
    'org_address'              => 'Cubao, Quezon City, Metro Manila, Philippines',
    'org_email'                => 'sample@gmail.com',
    'org_contact_number_globe' => 'No Contact Number.',
    'org_contact_number_smart' => 'No Contact Number.',
];

// Fetch content from the database table "cms_contact_page"
try {
    // Check if a PDO instance is available (fallback to $conn if that's your variable name)
    $db = $pdo ?? $conn ?? null;

    if ($db instanceof PDO) {
        $stmt = $db->prepare("SELECT * FROM cms_contact_page LIMIT 1");
        $stmt->execute();
        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dbData) {
            // Clean out null or empty values from DB so fallback hardcoded arrays remain intact
            $filteredDbData = array_filter($dbData, function($value) {
                return $value !== null && $value !== '';
            });
            $contactData = array_merge($contactData, $filteredDbData);
        }
    }
} catch (PDOException $e) {
    // Fail gracefully: log the error and let fallback text display
    error_log("Database error fetching contact page content: " . $e->getMessage());
}

extract($contactData);

?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Map Container Styling */
        .map-container { width: 100%; height: 500px; border-bottom: 1px solid var(--card-border); background-color: var(--bg-secondary); overflow: hidden; }
        .map-container iframe { width: 100%; height: 100%; border: 0; }

        /* Responsive Main Layout Container Block */
        main { width: 100%; max-width: 1500px; margin: 0 auto; padding: 3rem 1.5rem; box-sizing: border-box; }

        /* Contact Split Architecture */
        .contact-grid { display: grid; grid-template-columns: 1.1fr 1fr; gap: 4rem; align-items: start; }
        .contact-form-card { background-color: var(--bg-primary); border: 1px solid var(--card-border); padding: 3rem; border-radius: 24px; box-shadow: var(--card-shadow); transition: background-color 0.3s ease, border-color 0.3s ease; }
        .contact-info-panel h2 { font-size: 2.5rem; font-weight: 800; margin-top: 0; margin-bottom: 1.25rem; letter-spacing: -0.03em; color: var(--accent); transition: color 0.3s ease; }
        .info-card { display: flex; align-items: flex-start; gap: 1.25rem; margin-bottom: 2rem; padding: 0.5rem; transition: transform 0.2s ease; }
        .info-card-icon { background: var(--bg-primary); border: 1px solid var(--card-border); color: var(--accent); width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: var(--card-shadow); transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease; }

        /* Accessible, User-Friendly Interactive Form Element Design Styling Rules */
        .form-group { margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem; }
        .form-group label { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); transition: color 0.3s ease; }
        .form-group input, .form-group textarea { font-family: 'Poppins', sans-serif; width: 100%; padding: 0.5rem 1.25rem; box-sizing: border-box; border: 1px solid var(--card-border); background-color: var(--bg-primary); border-radius: 12px; font-size: 0.95rem; color: var(--text-primary); transition: all 0.2s ease; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--accent); background-color: var(--bg-primary); box-shadow: 0 0 0 4px var(--accent-ring); }
        .form-group textarea { height: 150px; resize: vertical; }

        /* Reusable System Action Icons Rules (Base) */
        .icon-btn { background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 0.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }

        /* Enhanced Accent Button (Overrides base icon-btn) */
        .icon-btn.action-accent-btn { background-color: var(--accent); color: #ffffff; width: 100%; height: 45px; padding: 0 1.5rem; border-radius: 14px; margin-bottom: 0.5rem; display: inline-flex; gap: 0.5rem; font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1rem; box-shadow: 0 4px 14px var(--accent-ring); }
        .icon-btn.action-accent-btn:hover { background-color: var(--accent-hover); color: #ffffff; transform: translateY(-2px); box-shadow: 0 6px 20px var(--accent-ring); }
        .icon-btn.action-accent-btn:active { transform: translateY(0); box-shadow: 0 2px 6px var(--accent-ring); }
        .text-btn { display: inline-block; letter-spacing: -0.01em; white-space: nowrap; }

        /* ==========================================================================
        DARK MODE OVERRIDES (Forces crisp white typography context)
        ========================================================================== */
        body.dark .contact-info-panel h2, body.dark .form-group label, body.dark .form-group input, body.dark .form-group textarea, [data-theme="dark"] .contact-info-panel h2, [data-theme="dark"] .form-group label, [data-theme="dark"] .form-group input, [data-theme="dark"] .form-group textarea { color: #ffffff !important; }
        body.dark .info-card p, body.dark .info-card span, [data-theme="dark"] .info-card p, [data-theme="dark"] .info-card span { color: rgba(255, 255, 255, 0.85) !important; }
        body.dark .form-group input::placeholder, body.dark .form-group textarea::placeholder, [data-theme="dark"] .form-group input::placeholder, [data-theme="dark"] .form-group textarea::placeholder { color: rgba(255, 255, 255, 0.45); }

        /* Media Queries for Maximum Fluid Responsive Interoperability */
        @media screen and (max-width: 1024px) {
            .contact-grid { grid-template-columns: 1fr; gap: 3.5rem; }
            .contact-info-panel h2 { font-size: 2.25rem; }
        }

        @media screen and (max-width: 768px) {
            .map-container { height: 320px; }
            main { padding: 2.5rem 1.25rem; }
            .contact-form-card { padding: 2rem 1.5rem; border-radius: 20px; }
        }
    </style>
</head>
<body>

    <?php include("header.php"); ?>

    <div class="map-container">
        <?php if ($org_address !== "No address listed"): ?>
            <iframe 
                loading="lazy" 
                allowfullscreen 
                referrerpolicy="no-referrer-when-downgrade" 
                src="https://maps.google.com/maps?q=<?= urlencode($org_address) ?>&t=&z=15&ie=UTF8&iwloc=&output=embed">
            </iframe>
        <?php else: ?>
            <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-secondary);">
                <p>No map location configuration available.</p>
            </div>
        <?php endif; ?>
    </div>

    <main>
        <div class="contact-grid">

            <div class="contact-info-panel">
                <h2>Contact Details</h2>
                <p style="color: var(--text-secondary); margin-bottom: 3rem; line-height: 1.6; font-size: 1.05rem;">
                    Give us a call or drop by anytime. We endeavor to answer all inquiries within 24 hours on business days. We will be happy to answer your questions.
                </p>
                
                <div class="info-card">
                    <div class="info-card-icon">
                        <svg style="width:22px;height:22px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 0.25rem 0; font-weight: 700; font-size: 1rem;">Address</h4>
                        <p style="margin: 0; color: var(--text-secondary); font-size: 0.95rem; line-height: 1.4;"><?= htmlspecialchars($org_address ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <svg style="width:22px;height:22px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 0.25rem 0; font-weight: 700; font-size: 1rem;">Email</h4>
                        <p style="margin: 0; color: var(--text-secondary); font-size: 0.95rem;"><?= htmlspecialchars($org_email ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <svg style="width:22px;height:22px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 0.25rem 0; font-weight: 700; font-size: 1rem;">Contact Number</h4>
                        <p style="margin: 0; color: var(--text-secondary); font-size: 0.95rem;">Globe: <?= htmlspecialchars($org_contact_number_globe ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        <p style="margin: 0; color: var(--text-secondary); font-size: 0.95rem;">Smart: <?= htmlspecialchars($org_contact_number_smart ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-card">
                <form action="#" method="POST" style="margin: 0;">
                    
                    <div class="form-group">
                        <label for="contact-name">Your Name</label>
                        <input type="text" id="contact-name" name="name" placeholder="Juan A. Dela Cruz" required>
                    </div>

                    <div class="form-group">
                        <label for="contact-email">Your Email</label>
                        <input type="email" id="contact-email" name="email" placeholder="sample@gmail.com" required>
                    </div>

                    <div class="form-group">
                        <label for="contact-message">Message</label>
                        <textarea id="contact-message" name="message" placeholder="Write your message here..." required></textarea>
                    </div>

                    <button type="submit" class="icon-btn action-accent-btn" style="justify-content: center; margin-top: 1rem;">
                        <span class="text-btn">Send Message</span>
                    </button>
                </form>
            </div>

        </div>
    </main>

    <?php include("footer.php"); ?>

</body>
</html>
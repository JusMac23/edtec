<?php

// Authenticate and initialize safe database configurations
require_once __DIR__ . '/auth/auth.php';

// Safe programmatic fallback values
$policyData = [
    'policy_title' => 'Administrative Rules',
    'policy_desc1' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    'policy_desc2' => 'Additional description for the policy section.',
    'policy_desc3' => 'Yet another description for the policy section.',
    'policy_desc4' => 'One more description for the policy section.',
];

// Fetch content from the database table "cms_about_page"
try {
    // Check if a PDO instance is available (fallback to $conn if that's your variable name)
    $db = $pdo ?? $conn ?? null;

    if ($db instanceof PDO) {
        $stmt = $db->prepare("SELECT * FROM cms_policy_page LIMIT 1");
        $stmt->execute();
        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dbData) {
            // Clean out null or empty values from DB so fallback hardcoded arrays remain intact
            $filteredDbData = array_filter($dbData, function($value) {
                return $value !== null && $value !== '';
            });
            $policyData = array_merge($policyData, $filteredDbData);
        }
    }
} catch (PDOException $e) {
    // Fail gracefully: log the error and let fallback text display
    error_log("Database error fetching policy page content: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Policy and Guidelines</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Animations & Keyframes */
        @keyframes containerFadeIn { 0% { opacity: 0; transform: translateY(40px); } 100% { opacity: 1; transform: translateY(0); } }
        @keyframes slideFromLeft { 0% { transform: translateX(-80px); opacity: 0; } 100% { transform: translateX(0); opacity: 1; } }
        @keyframes slideFromRight { 0% { transform: translateX(80px); opacity: 0; } 100% { transform: translateX(0); opacity: 1; } }

        /* Global & Hero Banner Container */
        .policy-container { width: 100%; background: linear-gradient(180deg, var(--bg-secondary, #f1f5f9) 0%, var(--bg-primary, #ffffff) 100%); border-bottom: 1px solid var(--card-border); box-sizing: border-box; text-align: center; padding: 6rem 2rem; animation: containerFadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
        .policy-section { max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 0.75rem; }
        .policy-section h1 { font-size: 3.25rem; font-weight: 800; letter-spacing: -0.04em; margin: 0 0 1.5rem 0; color: var(--accent); line-height: 1.15; transition: color 0.3s ease; }
        .policy-section h2 { font-size: 1.5rem; font-weight: 600; letter-spacing: -0.02em; margin: 0 0 1rem 0; color: var(--text-secondary, var(--accent)); line-height: 1.4; transition: color 0.3s ease; }
        .policy-section p { font-size: 1.15rem; text-align: justify; line-height: 1.6; color: var(--text-secondary, var(--accent)); margin: 0; transition: color 0.3s ease; }

        /* DARK MODE OVERRIDES */
        body.dark .policy-section h1, body.dark .policy-section p, [data-theme="dark"] .policy-section h1, [data-theme="dark"] .policy-section p { color: #ffffff !important; }

        /* RESPONSIVE QUERIES */
        @media screen and (max-width: 1024px) { 
            .policy-container{ padding: 4rem 1.5rem; } 
        }

        @media screen and (max-width: 768px) { 
            .policy-section h1 { font-size: 2.5rem; } .policy-section p { font-size: 1rem; }
        }
    </style>
</head>
<body>

    <?php include("header.php"); ?>

    <div class="policy-container">
        <div class="policy-section">
            <h1><?= htmlspecialchars($policyData['policy_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
            <h2>POLICY and GUIDELINES:</h2>
            <?php if(!empty($policyData['policy_desc1'])): ?><p><?= htmlspecialchars($policyData['policy_desc1'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($policyData['policy_desc2'])): ?><p><?= htmlspecialchars($policyData['policy_desc2'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($policyData['policy_desc3'])): ?><p><?= htmlspecialchars($policyData['policy_desc3'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($policyData['policy_desc4'])): ?><p><?= htmlspecialchars($policyData['policy_desc4'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>
    </div>

    <?php include("footer.php"); ?>

</body>
</html>
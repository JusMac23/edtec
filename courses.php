<?php

// Authenticate and initialize safe database configurations
require_once __DIR__ . '/auth/auth.php';

// Safe programmatic fallback values (used if the database is empty)
$course2yrbundledData = [
    'course_title' => 'Course Title',
    'course_desc1' => 'Course Description 1',
    'course_desc2' => 'Course Description 2',
    'course_desc3' => 'Course Description 3',
    'course_desc4' => 'Course Description 4', 
    'course_desc5' => 'Course Description 5', 
];

$coursenominaldurationData = [
    'course_title' => 'Course Title',
    'course_desc1' => 'Course Description 1',
    'course_desc2' => 'Course Description 2',
    'course_desc3' => 'Course Description 3',
    'course_desc4' => 'Course Description 4', 
    'course_desc5' => 'Course Description 5', 
    'course_desc6' => 'Course Description 6', 
    'course_desc7' => 'Course Description 7', 
];

$coursespecialtrainingData = [
    'course_title' => 'Course Title',
    'course_desc1' => 'Course Description 1',
    'course_desc2' => 'Course Description 2',
    'course_desc3' => 'Course Description 3',
    'course_desc4' => 'Course Description 4', 
    'course_desc5' => 'Course Description 5', 
    'course_desc6' => 'Course Description 6', 
    'course_desc7' => 'Course Description 7', 
    'course_desc8' => 'Course Description 8', 
];

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
        // 1. Fetch 2-Year Bundled (Get the first record and overwrite fallback data)
        $stmt = $pdo_conn->prepare("SELECT * FROM cms_course_2yr_bundled ORDER BY course_id ASC LIMIT 1");
        $stmt->execute();
        $fetchedBundled = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetchedBundled) {
            $course2yrbundledData = $fetchedBundled;
        }

        // 2. Fetch Nominal Duration (Get the first record and overwrite fallback data)
        $stmt = $pdo_conn->prepare("SELECT * FROM cms_course_nominal_duration ORDER BY course_id ASC LIMIT 1");
        $stmt->execute();
        $fetchedNominal = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetchedNominal) {
            $coursenominaldurationData = $fetchedNominal;
        }

        // 3. Fetch Special Training (Get the first record and overwrite fallback data)
        $stmt = $pdo_conn->prepare("SELECT * FROM cms_course_special_training ORDER BY course_id ASC LIMIT 1");
        $stmt->execute();
        $fetchedSpecial = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetchedSpecial) {
            $coursespecialtrainingData = $fetchedSpecial;
        }
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
    <title>Courses</title>
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
        .course-container { width: 100%; background: linear-gradient(180deg, var(--bg-secondary, #f1f5f9) 0%, var(--bg-primary, #ffffff) 100%); border-bottom: 1px solid var(--card-border); box-sizing: border-box; text-align: center; padding: 6rem 2rem; animation: containerFadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
        .course-section { max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 0.75rem; }
        .course-section h1 { font-size: 3.25rem; font-weight: 800; letter-spacing: -0.04em; margin: 0 0 1.5rem 0; color: var(--accent); line-height: 1.15; transition: color 0.3s ease; }
        .course-section h2 { font-size: 1.5rem; font-weight: 600; letter-spacing: -0.02em; margin: 0 0 1rem 0; color: var(--text-secondary, var(--accent)); line-height: 1.4; transition: color 0.3s ease; }
        .course-section p { font-size: 1.15rem; text-align: justify; line-height: 1.6; color: var(--text-secondary, var(--accent)); margin: 0; transition: color 0.3s ease; }

        /* DARK MODE OVERRIDES */
        body.dark .course-section h1, body.dark .course-section p, [data-theme="dark"] .course-section h1, [data-theme="dark"] .course-section p { color: #ffffff !important; }

        /* RESPONSIVE QUERIES */
        @media screen and (max-width: 1024px) { 
            .course-container{ padding: 4rem 1.5rem; } 
        }

        @media screen and (max-width: 768px) { 
            .course-section h1 { font-size: 2.5rem; } .course-section p { font-size: 1rem; }
        }
    </style>
</head>
<body>

    <?php include("header.php"); ?>

    <div id="2-year-bundled" class="course-container">
        <div class="course-section">
            <h1><?= htmlspecialchars($course2yrbundledData['course_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
            <h2>COURSE DESCRIPTION:</h2>
            <?php if(!empty($course2yrbundledData['course_desc1'])): ?><p><?= htmlspecialchars($course2yrbundledData['course_desc1'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($course2yrbundledData['course_desc2'])): ?><p><?= htmlspecialchars($course2yrbundledData['course_desc2'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($course2yrbundledData['course_desc3'])): ?><p><?= htmlspecialchars($course2yrbundledData['course_desc3'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($course2yrbundledData['course_desc4'])): ?><p><?= htmlspecialchars($course2yrbundledData['course_desc4'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($course2yrbundledData['course_desc5'])): ?><p><?= htmlspecialchars($course2yrbundledData['course_desc5'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>
    </div>

    <div id="nominal-duration" class="course-container">
        <div class="course-section">
            <h1><?= htmlspecialchars($coursenominaldurationData['course_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
            <h2>COURSE DESCRIPTION:</h2>
            <?php if(!empty($coursenominaldurationData['course_desc1'])): ?><p><?= htmlspecialchars($coursenominaldurationData['course_desc1'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($coursenominaldurationData['course_desc2'])): ?><p><?= htmlspecialchars($coursenominaldurationData['course_desc2'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($coursenominaldurationData['course_desc3'])): ?><p><?= htmlspecialchars($coursenominaldurationData['course_desc3'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($coursenominaldurationData['course_desc4'])): ?><p><?= htmlspecialchars($coursenominaldurationData['course_desc4'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($coursenominaldurationData['course_desc5'])): ?><p><?= htmlspecialchars($coursenominaldurationData['course_desc5'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($coursenominaldurationData['course_desc6'])): ?><p><?= htmlspecialchars($coursenominaldurationData['course_desc6'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($coursenominaldurationData['course_desc7'])): ?><p><?= htmlspecialchars($coursenominaldurationData['course_desc7'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>
    </div>

    <div id="special-training" class="course-container">
        <div class="course-section">
            <h1><?= htmlspecialchars($coursespecialtrainingData['course_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
            <h2>COURSE DESCRIPTION:</h2>
            <?php if(!empty($coursespecialtrainingData['course_desc1'])): ?><p><?= htmlspecialchars($coursespecialtrainingData['course_desc1'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($coursespecialtrainingData['course_desc2'])): ?><p><?= htmlspecialchars($coursespecialtrainingData['course_desc2'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($coursespecialtrainingData['course_desc3'])): ?><p><?= htmlspecialchars($coursespecialtrainingData['course_desc3'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($coursespecialtrainingData['course_desc4'])): ?><p><?= htmlspecialchars($coursespecialtrainingData['course_desc4'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($coursespecialtrainingData['course_desc5'])): ?><p><?= htmlspecialchars($coursespecialtrainingData['course_desc5'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($coursespecialtrainingData['course_desc6'])): ?><p><?= htmlspecialchars($coursespecialtrainingData['course_desc6'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($coursespecialtrainingData['course_desc7'])): ?><p><?= htmlspecialchars($coursespecialtrainingData['course_desc7'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if(!empty($coursespecialtrainingData['course_desc8'])): ?><p><?= htmlspecialchars($coursespecialtrainingData['course_desc8'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>
    </div>

    <?php include("footer.php"); ?>

</body>
</html>
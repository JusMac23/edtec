<?php

// This fixes the nested relative path bugs inside auth.php and db.php automatically!
chdir(dirname(__DIR__));

// 1. Authenticate and initialize database connections
require_once './auth/auth.php';

// Strict Security Fence: Block guests AND non-admins
if (!isset($userId) || !$userId || ($currentUser['role'] ?? '') !== 'admin') {
    
    http_response_code(403); // Forbidden
    
    // Render a clean structural warning screen immediately
    die('
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Access Denied</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    </head>
    <body style="font-family: \'Poppins\', system-ui, -apple-system, sans-serif; background-color: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0;">
        <div style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.05); max-width: 400px; width: 100%; text-align: center; border: 1px solid #e2e8f0;">
            
            <div style="margin-bottom: 20px;">
                <span class="material-symbols-outlined" style="font-size: 64px; color: #ef4444; background: #fef2f2; padding: 16px; border-radius: 50%; display: inline-block;">
                    gpp_maybe
                </span>
            </div>

            <h1 style="color: #0f172a; font-size: 24px; font-weight: 600; margin: 0 0 10px 0;">Access Denied</h1>
            <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin: 0 0 28px 0;">
                This is a restricted page. You have no authority to access this page.
            </p>
            
            <a href="../index.php" style="display: block; padding: 12px 24px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 15px; transition: background 0.2s ease; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);">
                Return to Safety
            </a>
        </div>
    </body>
    </html>
    ');
}

// Ensure database connection instance is mapped
$db = $pdo ?? $conn ?? null;


// ==========================================
// CENTRAL CONTROLLER ENGINE BASE STATE
// ==========================================
$messageSuccess = '';
$messageError = '';
$error = '';
$success = '';


// ==========================================
// ARTICLES PAGE CONTROLLER
// ==========================================

$modalSuccess = '';
$modalError = '';
$posts = [];
$isEditMode = false;
$editPostData = [];

if ($db instanceof PDO) {
    
    // Function to generate URL-safe slugs
    $generateSlug = function($string) {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    };

    // 1. Handle Article POST Actions (Create, Edit, Delete)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];

        // Helper to process uploaded files vs URL inputs
        $processArticleImage = function() {
            if (!empty($_FILES['featured_image_file']['name']) && $_FILES['featured_image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = './uploads/articles/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                $ext = pathinfo($_FILES['featured_image_file']['name'], PATHINFO_EXTENSION);
                $filename = $uploadDir . 'article_' . time() . '_' . uniqid() . '.' . $ext;
                
                if (move_uploaded_file($_FILES['featured_image_file']['tmp_name'], $filename)) {
                    return $filename;
                }
            }
            // Fallback to URL input if no valid file uploaded
            return $_POST['featured_image'] ?? ''; 
        };

        if ($action === 'create_post') {
            try {
                $title   = $_POST['title'] ?? '';
                $excerpt = $_POST['excerpt'] ?? '';
                $content = $_POST['content'] ?? '';
                $slug    = $generateSlug($title);
                $image   = $processArticleImage();
                $author  = $userId; // Derived from auth.php
                
                $stmt = $db->prepare("INSERT INTO cms_articles_page (title, slug, excerpt, content, featured_image, author_id, published_at, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'Published')");
                $stmt->execute([$title, $slug, $excerpt, $content, $image, $author]);
                $modalSuccess = "Article created successfully!";
                
            } catch (PDOException $e) {
                $modalError = "Failed to create article: " . $e->getMessage();
            }
        } 
        elseif ($action === 'edit_post') {
            try {
                $postId  = (int)($_POST['post_id'] ?? 0);
                $title   = $_POST['title'] ?? '';
                $excerpt = $_POST['excerpt'] ?? '';
                $content = $_POST['content'] ?? '';
                $slug    = $generateSlug($title);
                
                $image = $processArticleImage();
                
                // Retain old image if no new input provided
                if (empty($image)) {
                    $stmt = $db->prepare("UPDATE cms_articles_page SET title=?, slug=?, excerpt=?, content=? WHERE id=?");
                    $stmt->execute([$title, $slug, $excerpt, $content, $postId]);
                } else {
                    $stmt = $db->prepare("UPDATE cms_articles_page SET title=?, slug=?, excerpt=?, content=?, featured_image=? WHERE id=?");
                    $stmt->execute([$title, $slug, $excerpt, $content, $image, $postId]);
                    $modalSuccess = "Article updated successfully!";
                }
            } catch (PDOException $e) {
                $modalError = "Failed to update article: " . $e->getMessage();
            }
        }
        elseif ($action === 'delete_post') {
            try {
                $postId = (int)($_POST['post_id'] ?? 0);
                $stmt = $db->prepare("DELETE FROM cms_articles_page WHERE id=?");
                $stmt->execute([$postId]);
                $modalSuccess = "Article deleted successfully!";
            } catch (PDOException $e) {
                $modalError = "Failed to delete article: " . $e->getMessage();
            }
        }
    }

    // 2. Fetch data for Edit Mode Modal
    if (isset($_GET['edit_id'])) {
        $isEditMode = true;
        try {
            $stmt = $db->prepare("SELECT * FROM cms_articles_page WHERE id = ?");
            $stmt->execute([(int)$_GET['edit_id']]);
            $editPostData = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$editPostData) {
                $isEditMode = false;
                $modalError = "The requested article could not be found.";
            }
        } catch (PDOException $e) {
            $modalError = "Database error: " . $e->getMessage();
        }
    }

    // 3. Fetch all Articles for the Data Table (Joining users table for author names)
    try {
        $stmt = $db->query("
            SELECT p.*, u.first_name, u.last_name 
            FROM cms_articles_page p 
            LEFT JOIN users u ON p.author_id = u.id 
            ORDER BY p.id DESC
        ");
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback in case table names differ slightly or user join fails
        try {
            $stmt = $db->query("SELECT * FROM cms_articles_page ORDER BY id DESC");
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            $posts = [];
        }
    }
}


// ==========================================
// ABOUT PAGE CONTROLLER
// ==========================================

// Safe programmatic fallback values
$aboutData = [
    'hero_title'        => 'About our organization.',
    'hero_desc1'        => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    'hero_desc2'        => '', 'hero_desc3' => '', 'hero_desc4' => '', 'hero_desc5' => '',
    'mission_title'     => 'Our Mission', 'mission_p' => '', 'mission_img' => '',
    'vision_title'      => 'Our Vision', 'vision_p' => '', 'vision_img' => '',
    'goals_title'       => 'Our Goals',
    'goals_p1'          => '','goals_p2' => '', 'goals_p3' => '', 'goals_p4' => '',
    'goals_p5'          => '', 'goals_p6' => '', 'goals_p7' => '', 'goals_p8' => '',
    'tagline_title'     => 'Our Tagline',
    'tagline_p1'        => '', 'tagline_p2' => '', 'tagline_p3' => '', 'tagline_p4' => '', 'tagline_p5' => ''
];

if ($db instanceof PDO) {
    // 1. HANDLE SYNCHRONOUS DATABASE UPDATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_about_cms') {
        try {
            // Get current images from DB to retain them if no new files are uploaded
            $imgStmt = $db->query("SELECT mission_img, vision_img FROM cms_about_page LIMIT 1");
            $currentImages = $imgStmt->fetch(PDO::FETCH_ASSOC);
            
            $mission_img = $currentImages['mission_img'] ?? '';
            $vision_img = $currentImages['vision_img'] ?? '';
            
            // Handle File Uploads safely
            $aboutUploadDir = './uploads/about/';
            if (!is_dir($aboutUploadDir)) {
                mkdir($aboutUploadDir, 0755, true);
            }

            if (!empty($_FILES['mission_img_file']['name']) && $_FILES['mission_img_file']['error'] === UPLOAD_ERR_OK) {
                $mission_ext = pathinfo($_FILES['mission_img_file']['name'], PATHINFO_EXTENSION);
                $mission_img = $aboutUploadDir . 'mission_' . time() . '.' . $mission_ext;
                move_uploaded_file($_FILES['mission_img_file']['tmp_name'], $mission_img);
            }

            if (!empty($_FILES['vision_img_file']['name']) && $_FILES['vision_img_file']['error'] === UPLOAD_ERR_OK) {
                $vision_ext = pathinfo($_FILES['vision_img_file']['name'], PATHINFO_EXTENSION);
                $vision_img = $aboutUploadDir . 'vision_' . time() . '.' . $vision_ext;
                move_uploaded_file($_FILES['vision_img_file']['tmp_name'], $vision_img);
            }

            // Prepare dataset payload
            $payload = [
                ':hero_title'    => $_POST['hero_title'] ?? '',
                ':hero_desc1'    => $_POST['hero_desc1'] ?? '',
                ':hero_desc2'    => $_POST['hero_desc2'] ?? '',
                ':hero_desc3'    => $_POST['hero_desc3'] ?? '',
                ':hero_desc4'    => $_POST['hero_desc4'] ?? '',
                ':hero_desc5'    => $_POST['hero_desc5'] ?? '',
                ':mission_title' => $_POST['mission_title'] ?? '',
                ':mission_p'     => $_POST['mission_p'] ?? '',
                ':mission_img'   => $mission_img,
                ':vision_title'  => $_POST['vision_title'] ?? '',
                ':vision_p'      => $_POST['vision_p'] ?? '',
                ':vision_img'    => $vision_img,
                ':goals_title'   => $_POST['goals_title'] ?? '',
                ':goals_p1'      => $_POST['goals_p1'] ?? '',
                ':goals_p2'      => $_POST['goals_p2'] ?? '',
                ':goals_p3'      => $_POST['goals_p3'] ?? '',
                ':goals_p4'      => $_POST['goals_p4'] ?? '',
                ':goals_p5'      => $_POST['goals_p5'] ?? '',
                ':goals_p6'      => $_POST['goals_p6'] ?? '',
                ':goals_p7'      => $_POST['goals_p7'] ?? '',
                ':goals_p8'      => $_POST['goals_p8'] ?? '',
                ':tagline_title' => $_POST['tagline_title'] ?? '',
                ':tagline_p1'    => $_POST['tagline_p1'] ?? '',
                ':tagline_p2'    => $_POST['tagline_p2'] ?? '',
                ':tagline_p3'    => $_POST['tagline_p3'] ?? '',
                ':tagline_p4'    => $_POST['tagline_p4'] ?? '',
                ':tagline_p5'    => $_POST['tagline_p5'] ?? ''
            ];

            // Verify if record exists to decide between INSERT or UPDATE
            $count = $db->query("SELECT COUNT(*) FROM cms_about_page")->fetchColumn();

            if ($count == 0) {
                $sql = "INSERT INTO cms_about_page (
                            hero_title, hero_desc1, hero_desc2, hero_desc3, hero_desc4, hero_desc5,
                            mission_title, mission_p, mission_img, vision_title, vision_p, vision_img,
                            goals_title, goals_p1, goals_p2, goals_p3, goals_p4, goals_p5, goals_p6, goals_p7, goals_p8,
                            tagline_title, tagline_p1, tagline_p2, tagline_p3, tagline_p4, tagline_p5
                        ) VALUES (
                            :hero_title, :hero_desc1, :hero_desc2, :hero_desc3, :hero_desc4, :hero_desc5,
                            :mission_title, :mission_p, :mission_img, :vision_title, :vision_p, :vision_img,
                            :goals_title, :goals_p1, :goals_p2, :goals_p3, :goals_p4, :goals_p5, :goals_p6, :goals_p7, :goals_p8,
                            :tagline_title, :tagline_p1, :tagline_p2, :tagline_p3, :tagline_p4, :tagline_p5
                        )";
            } else {
                $sql = "UPDATE cms_about_page SET 
                            hero_title = :hero_title, hero_desc1 = :hero_desc1, hero_desc2 = :hero_desc2, hero_desc3 = :hero_desc3, hero_desc4 = :hero_desc4, hero_desc5 = :hero_desc5,
                            mission_title = :mission_title, mission_p = :mission_p, mission_img = :mission_img, vision_title = :vision_title, vision_p = :vision_p, vision_img = :vision_img,
                            goals_title = :goals_title, goals_p1 = :goals_p1, goals_p2 = :goals_p2, goals_p3 = :goals_p3, goals_p4 = :goals_p4, goals_p5 = :goals_p5, goals_p6 = :goals_p6, goals_p7 = :goals_p7, goals_p8 = :goals_p8,
                            tagline_title = :tagline_title, tagline_p1 = :tagline_p1, tagline_p2 = :tagline_p2, tagline_p3 = :tagline_p3, tagline_p4 = :tagline_p4, tagline_p5 = :tagline_p5
                        LIMIT 1";
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($payload);
            $messageSuccess = "About Us content updated and synchronized successfully!";
        } catch (PDOException $e) {
            $messageError = "Database Error: " . htmlspecialchars($e->getMessage()) . " Please check your input and try again.";
        }
    }

    // 2. FETCH DATA FROM DATABASE TO VIEW WITHIN FORM
    try {
        $stmt = $db->prepare("SELECT * FROM cms_about_page LIMIT 1");
        $stmt->execute();
        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dbData) {
            $aboutData = array_merge($aboutData, array_filter($dbData, function($val) {
                return $val !== null;
            }));
        }
    } catch (PDOException $e) {
        $messageError = "Failed to fetch layout content: " . $e->getMessage();
    }
}

// ==========================================
// OFFICERS PAGE CONTROLLER (CRUD OPERATIONS)
// ==========================================

$allOfficers = [];

if ($db instanceof PDO) {
    
    // ---------------------------------------------------------
    // 1. DELETE ACTION HANDLER
    // ---------------------------------------------------------
    if (isset($_GET['action']) && $_GET['action'] === 'delete_officer' && isset($_GET['officer_id']) && isset($_GET['category'])) {
        try {
            $officerId = (int)$_GET['officer_id'];
            $category = $_GET['category'];
            
            $tables = [
                'bot'  => 'cms_officers_bot_page',
                'ofad' => 'cms_officers_ofad_page',
                'tit'  => 'cms_officers_tit_page'
            ];
            
            if (array_key_exists($category, $tables)) {
                $targetTable = $tables[$category];
                
                // Optional: Fetch old photo to delete file from disk storage
                $imgStmt = $db->prepare("SELECT officer_photo FROM {$targetTable} WHERE officer_id = :id");
                $imgStmt->execute([':id' => $officerId]);
                $oldPhoto = $imgStmt->fetchColumn();
                if (!empty($oldPhoto) && file_exists($oldPhoto)) {
                    unlink($oldPhoto);
                }
                
                // Execute Deletion
                $stmt = $db->prepare("DELETE FROM {$targetTable} WHERE officer_id = :id");
                $stmt->execute([':id' => $officerId]);
                
                $messageSuccess = "Officer profile successfully deleted";
            }
        } catch (PDOException $e) {
            $messageError = "Deletion Fault: " . htmlspecialchars($e->getMessage());
        }
    }

    // ---------------------------------------------------------
    // 2. CREATE AND UPDATE ACTION HANDLER
    // ---------------------------------------------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_officers_cms') {
        try {
            $category = $_POST['officer_category'] ?? 'bot';
            $formMode = $_POST['form_mode'] ?? 'create'; // Detect create vs edit
            $officerId = isset($_POST['officer_id']) ? (int)$_POST['officer_id'] : 0;
            
            $tables = [
                'bot'  => 'cms_officers_bot_page',
                'ofad' => 'cms_officers_ofad_page',
                'tit'  => 'cms_officers_tit_page'
            ];
            
            $targetTable = $tables[$category] ?? 'cms_officers_bot_page';
            $officersUploadDir = './uploads/officers/';
            
            if (!is_dir($officersUploadDir)) {
                mkdir($officersUploadDir, 0755, true);
            }

            // Photo Handler
            $officer_photo = $_POST['existing_photo'] ?? ''; // fallback to old photo if editing
            if (!empty($_FILES['officer_photo']['name']) && $_FILES['officer_photo']['error'] === UPLOAD_ERR_OK) {
                // Delete old image if it exists during update
                if (!empty($_POST['existing_photo']) && file_exists($_POST['existing_photo'])) {
                    unlink($_POST['existing_photo']);
                }
                $ext = pathinfo($_FILES['officer_photo']['name'], PATHINFO_EXTENSION);
                $officer_photo = $officersUploadDir . $category . '_officer_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['officer_photo']['tmp_name'], $officer_photo);
            }

            $payload = [
                ':officer_firstname'  => $_POST['officer_firstname'] ?? '',
                ':officer_middleinitial' => $_POST['officer_middleinitial'] ?? '',
                ':officer_lastname'   => $_POST['officer_lastname'] ?? '',
                ':officer_position'   => $_POST['officer_position'] ?? '',
                ':officer_photo'      => $officer_photo
            ];

            if ($formMode === 'edit' && $officerId > 0) {
                // Update Route
                $payload[':id'] = $officerId;
                $sql = "UPDATE {$targetTable} SET 
                            officer_firstname = :officer_firstname, 
                            officer_middleinitial = :officer_middleinitial, 
                            officer_lastname = :officer_lastname, 
                            officer_position = :officer_position, 
                            officer_photo = :officer_photo 
                        WHERE officer_id = :id";
                $stmt = $db->prepare($sql);
                $stmt->execute($payload);
                $messageSuccess = "Officer profile updated successfully!";
            } else {
                // Create Route
                $sql = "INSERT INTO {$targetTable} (
                            officer_firstname, officer_middleinitial, officer_lastname, officer_position, officer_photo
                        ) VALUES (
                            :officer_firstname, :officer_middleinitial, :officer_lastname, :officer_position, :officer_photo
                        )";
                $stmt = $db->prepare($sql);
                $stmt->execute($payload);
                $messageSuccess = "New Officer successfully added!";
            }
        } catch (PDOException $e) {
            $messageError = "Database Operation Failure: " . htmlspecialchars($e->getMessage());
        }
    }

    // ---------------------------------------------------------
    // 3. RETRIEVE ALL PROFILES FOR RE-RENDERING
    // ---------------------------------------------------------
    try {
        $allOfficers = [];

        $stmtBot = $db->query("SELECT * FROM cms_officers_bot_page ORDER BY officer_id ASC");
        while ($row = $stmtBot->fetch(PDO::FETCH_ASSOC)) {
            $row['category_slug'] = 'bot';
            $row['category_label'] = 'Board of Trustees';
            $allOfficers[] = $row;
        }

        $stmtOfad = $db->query("SELECT * FROM cms_officers_ofad_page ORDER BY officer_id ASC");
        while ($row = $stmtOfad->fetch(PDO::FETCH_ASSOC)) {
            $row['category_slug'] = 'ofad';
            $row['category_label'] = 'Administration';
            $allOfficers[] = $row;
        }

        $stmtTit = $db->query("SELECT * FROM cms_officers_tit_page ORDER BY officer_id ASC");
        while ($row = $stmtTit->fetch(PDO::FETCH_ASSOC)) {
            $row['category_slug'] = 'tit';
            $row['category_label'] = 'Training & Instructions';
            $allOfficers[] = $row;
        }
    } catch (PDOException $e) {
        $messageError = "System Synchronization Error: " . $e->getMessage();
    }
}

// ==========================================
// POLICY GUIDELINES PAGE CONTROLLER
// ==========================================

// Safe programmatic fallback values instantiated inside dashboard workspace
$policyData = [
    'policy_title' => 'Administrative Rules',
    'policy_desc1' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    'policy_desc2' => 'Additional description for the policy section.',
    'policy_desc3' => 'Yet another description for the policy section.',
    'policy_desc4' => 'One more description for the policy section.',
];

if ($db instanceof PDO) {
    // 1. HANDLE SYNCHRONOUS DATABASE UPDATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_policy_cms') {
        try {
            // Prepare dataset payload
            $payload = [
                ':policy_title' => $_POST['policy_title'] ?? '',
                ':policy_desc1' => $_POST['policy_desc1'] ?? '',
                ':policy_desc2' => $_POST['policy_desc2'] ?? '',
                ':policy_desc3' => $_POST['policy_desc3'] ?? '',
                ':policy_desc4' => $_POST['policy_desc4'] ?? '',
            ];

            // Verify if record exists to decide between INSERT or UPDATE
            $count = $db->query("SELECT COUNT(*) FROM cms_policy_page")->fetchColumn();

            if ($count == 0) {
                $sql = "INSERT INTO cms_policy_page (
                            policy_title, policy_desc1, policy_desc2, policy_desc3, policy_desc4
                        ) VALUES (
                            :policy_title, :policy_desc1, :policy_desc2, :policy_desc3, :policy_desc4
                        )";
            } else {
                $sql = "UPDATE cms_policy_page SET 
                            policy_title = :policy_title, policy_desc1 = :policy_desc1, policy_desc2 = :policy_desc2, policy_desc3 = :policy_desc3, policy_desc4 = :policy_desc4
                        LIMIT 1";
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($payload);
            $messageSuccess = "Policy content updated and synchronized successfully!";
        } catch (PDOException $e) {
            $messageError = "Database Error: " . htmlspecialchars($e->getMessage()) . " Please check your input and try again.";
        }
    }

    // 2. FETCH DATA FROM DATABASE TO VIEW WITHIN FORM
    try {
        $stmt = $db->prepare("SELECT * FROM cms_policy_page LIMIT 1");
        $stmt->execute();
        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dbData) {
            $policyData = array_merge($policyData, array_filter($dbData, function($value) {
                return $value !== null;
            }));
        }
    } catch (PDOException $e) {
        $messageError = "Failed to fetch layout content: " . $e->getMessage();
    }
}

// ==========================================
// CONTACT US PAGE CONTROLLER
// ==========================================

// Safe programmatic fallback values instantiated inside dashboard workspace
$contactData = [
    'org_address' => 'Cubao, Quezon City, Metro Manila, Philippines',
    'org_email' => 'sample@gmail.com',
    'org_contact_number_globe' => 'No Contact Number.',
    'org_contact_number_smart' => 'No Contact Number.',
];

if ($db instanceof PDO) {
    // 1. HANDLE SYNCHRONOUS DATABASE UPDATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_contact_cms') {
        try {
            // Prepare dataset payload
            $payload = [
                ':org_address' => $_POST['org_address'] ?? '',
                ':org_email' => $_POST['org_email'] ?? '',
                ':org_contact_number_globe' => $_POST['org_contact_number_globe'] ?? '',
                ':org_contact_number_smart' => $_POST['org_contact_number_smart'] ?? '',
            ];

            // Verify if record exists to decide between INSERT or UPDATE
            $count = $db->query("SELECT COUNT(*) FROM cms_contact_page")->fetchColumn();

            if ($count == 0) {
                $sql = "INSERT INTO cms_contact_page (
                            org_address, org_email, org_contact_number_globe, org_contact_number_smart
                        ) VALUES (
                            :org_address, :org_email, :org_contact_number_globe, :org_contact_number_smart
                        )";
            } else {
                $sql = "UPDATE cms_contact_page SET 
                            org_address = :org_address, org_email = :org_email, org_contact_number_globe = :org_contact_number_globe, org_contact_number_smart = :org_contact_number_smart
                        LIMIT 1";
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($payload);
            $messageSuccess = "Contact information updated and synchronized successfully!";
        } catch (PDOException $e) {
            $messageError = "Database Error: " . htmlspecialchars($e->getMessage()) . " Please check your input and try again.";
        }
    }

    // 2. FETCH DATA FROM DATABASE TO VIEW WITHIN FORM
    try {
        $stmt = $db->prepare("SELECT * FROM cms_contact_page LIMIT 1");
        $stmt->execute();
        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dbData) {
            $contactData = array_merge($contactData, array_filter($dbData, function($value) {
                return $value !== null;
            }));
        }
    } catch (PDOException $e) {
        $messageError = "Failed to fetch contact information: " . $e->getMessage();
    }
}


// ==========================================
// REGISTERED SYSTEM USERS ENGINE
// ==========================================
$users_list = [];
$adminExists = false;

if ($db instanceof PDO) {
    
    // Check if an admin exists to dictate fallback framework UI logic constraints
    try {
        $adminCount = $db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        $adminExists = ($adminCount > 0);
    } catch (PDOException $e) {
        $adminExists = false;
    }

    // 1. HANDLE SYSTEM USER REGISTRATION VIA FORM
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register_user_cms') {
        $firstName     = trim($_POST['first_name'] ?? '');
        $lastName      = trim($_POST['last_name'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $contactNumber = trim($_POST['contact_number'] ?? '');
        $role          = $_POST['role'] ?? 'subscriber';
        $address       = trim($_POST['address'] ?? '');
        $password      = $_POST['password'] ?? '';
        $confirmPass   = $_POST['confirm_password'] ?? '';

        // Validation constraints
        if ($password !== $confirmPass) {
            $error = "Verification mismatch: Passwords do not match.";
        } elseif (strlen($password) < 8) {
            $error = "Security validation error: Password must be at least 8 characters.";
        } else {
            try {
                // Assert uniqueness of Email Address
                $checkEmail = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
                $checkEmail->execute([$email]);
                
                if ($checkEmail->fetchColumn() > 0) {
                    $error = "Conflict: This email address is already assigned to a profile index.";
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $insertUser = $db->prepare("INSERT INTO users (first_name, last_name, email, contact_number, role, address, password, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                    $insertUser->execute([$firstName, $lastName, $email, $contactNumber, $role, $address, $hashedPassword]);
                    
                    $success = "Operational success: System user account compiled and synced.";
                    $modalSuccess = "System user account created successfully!";
                    
                    // Refresh admin constraints status
                    $adminCount = $db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
                    $adminExists = ($adminCount > 0);
                }
            } catch (PDOException $e) {
                $error = "Structural Database failure: " . $e->getMessage();
            }
        }
    }

    // 2. RETRIEVE ALL ACCOUNTS TO FILL VIEW INDEXES
    try {
        $usersStmt = $db->query("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY id ASC");
        $users_list = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $users_list = [];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Administrative Workspace - EDTEC</title>
    <link rel="stylesheet" href="../css/index-styles.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<style>
    body { font-family: 'Poppins', system-ui, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; }
    .cms-wrapper { max-width: 1500px; margin: 40px auto; padding: 0 20px; box-sizing: border-box; }
    .cms-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; border-bottom: 1px solid #e2e8f0; padding-bottom: 24px; flex-wrap: wrap; gap: 20px; }
    h1 { font-size: 1.85rem; color: #0f172a; font-weight: 700; margin: 0; }

    /* Dashboard Layout Architecture (Horizontal Navbar + Content) */
    .dashboard-layout { display: flex; flex-direction: column; gap: 30px; }
    
    /* Horizontal Navbar Styling */
    .navbar-menu { width: 100%; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; padding: 12px 16px; box-sizing: border-box; z-index: 10; position: sticky; top: 20px; }
    .navbar-menu ul { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; gap: 10px; }
    .navbar-menu li { padding: 10px 20px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 10px; color: #475569; font-weight: 500; font-size: 0.95rem; transition: all 0.2s ease; white-space: nowrap; }
    .navbar-menu li:hover { background-color: #f1f5f9; color: #1e293b; }
    .navbar-menu li.active { background-color: #e0f2fe; color: #0369a1; font-weight: 600; }
    .navbar-menu li .material-symbols-outlined { font-size: 20px; }

    .dashboard-content { width: 100%; min-width: 0; }
    .dashboard-section { display: none; animation: fadeIn 0.3s ease; }
    .dashboard-section.active { display: block; }

    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 15px; }
    h2 { font-size: 1.35rem; color: #1e293b; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px; }

    /* Layout Card & Table Architecture */
    .cms-table-card { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 40px; }
    .table-responsive-wrapper { overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch; }
    .cms-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; min-width: 700px; }
    .cms-table th { background: #f8fafc; padding: 14px 20px; font-weight: 500; color: #64748b; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
    .cms-table td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
    .cms-table tr:last-child td { border-bottom: none; }

    /* Action Elements & State Badges */
    .status-pill { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; background: #dcfce7; color: #15803d; }
    .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; text-transform: capitalize; }
    .badge-admin { background-color: #e0f2fe; color: #0369a1; }
    .badge-user { background-color: #f1f5f9; color: #475569; }

    .action-container { display: flex; gap: 8px; align-items: center; }
    .action-trash-btn { background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; padding: 8px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease; }
    .action-trash-btn:hover { background: #991b1b; color: #ffffff; border-color: #991b1b; }
    .action-edit-btn { background: #f5f3ff; color: #5b21b6; border: 1px solid #ddd6fe; padding: 8px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease; text-decoration: none; }
    .action-edit-btn:hover { background: #5b21b6; color: #ffffff; border-color: #5b21b6; }

    .icon-btn.action-accent-btn { background-color: #2563eb; color: #ffffff; border: none; padding: 10px 18px; border-radius: 10px; font-family: 'Poppins', system-ui, sans-serif; font-size: 0.9rem; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); transition: background 0.2s ease; }
    .icon-btn.action-accent-btn:hover { background-color: #1d4ed8; }
    .icon-btn.action-accent-btn svg { width: 18px; height: 18px; }

    .welcome-badge { color: #64748b; margin-top: 6px; font-size: 0.875rem; }
    .cms-card-heading { padding: 16px 20px; font-size: 1rem; font-weight: 600; border-bottom: 1px solid #e2e8f0; background: #fafafa; color: #334155; }

    /* Unified System Forms UI Engine */
    .about-cms-form, .policy-cms-form, .contact-cms-form, .footer-cms-form, .officers-cms-form { padding: 24px; display: flex; flex-direction: column; gap: 20px; }
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group label { font-size: 0.85rem; font-weight: 500; color: #475569; }
    .form-group input[type="text"], .form-group input[type="url"], .form-group textarea, .form-group input[type="email"], .form-group input[type="tel"] { font-family: inherit; font-size: 0.9rem; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; color: #1e293b; background-color: #ffffff; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02); transition: border-color 0.15s ease, box-shadow 0.15s ease; width: 100%; box-sizing: border-box; }
    .form-group input:focus, .form-group textarea:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }

    .btn { font-family: inherit; font-size: 0.9rem; font-weight: 500; padding: 10px 20px; border-radius: 6px; cursor: pointer; border: 1px solid transparent; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease; }
    .btn-primary { background-color: #4f46e5; color: white; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.15); }
    .btn-primary:hover { background-color: #4338ca; }
    .btn-secondary { background-color: #f1f5f9; color: #475569; border-color: #cbd5e1; }
    .btn-secondary:hover { background-color: #e2e8f0; color: #334155; }

    .inline-preview-box { display: flex; align-items: center; gap: 15px; margin-top: 8px; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px dashed #cbd5e1; }
    .inline-preview-box img { width: 80px; height: 55px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; }
    .inline-preview-box span { font-size: 0.8rem; color: #64748b; }

    /* Modal System Layout definitions */
    .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.4); display: flex; align-items: center; justify-content: center; z-index: 1000; opacity: 0; pointer-events: none; transition: opacity 0.25s ease; padding: 20px; }
    .modal-overlay.active { opacity: 1; pointer-events: auto; }
    .modal-card { background: white; border-radius: 12px; width: 100%; max-width: 600px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); max-height: 90vh; display: flex; flex-direction: column; animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes slideUp { from { transform: translateY(15px); } to { transform: translateY(0); } }
    .modal-header { padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .modal-header h2 { margin: 0; font-size: 1.25rem; }
    .modal-close-btn { background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; transition: color 0.15s ease; }
    .modal-close-btn:hover { color: #475569; }
    .modal-form { padding: 24px; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; margin: 0; }
    .modal-actions-footer { border-top: 1px solid #e2e8f0; padding: 16px 24px; display: flex; justify-content: flex-end; gap: 12px; background: #f8fafc; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; }

    /* Upload Tab Systems */
    .upload-tabs { display: flex; border-bottom: 1px solid #e2e8f0; gap: 4px; margin-bottom: 10px; }
    .upload-tabs .tab-btn { background: none; border: none; padding: 8px 14px; font-family: inherit; font-size: 0.85rem; font-weight: 500; color: #64748b; cursor: pointer; border-bottom: 2px solid transparent; transition: all 0.15s ease; }
    .upload-tabs .tab-btn.active { color: #2563eb; border-bottom-color: #2563eb; }
    .upload-slot { display: none; }
    .upload-slot.active { display: block; }

    .dropzone-area { border: 2px dashed #cbd5e1; padding: 20px; border-radius: 8px; text-align: center; background: #f8fafc; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 6px; }
    .dropzone-area:hover { background: #f1f5f9; border-color: #94a3b8; }
    .dropzone-icon { width: 28px; height: 28px; color: #64748b; }
    .dropzone-text { font-size: 0.85rem; font-weight: 500; color: #334155; }
    .dropzone-hint { font-size: 0.75rem; color: #94a3b8; }
    .dropzone-area input[type="file"] { display: none; }
    .file-name-indicator { font-size: 0.8rem; color: #16a34a; margin-top: 6px; font-weight: 500; }

    .flash-alert { padding: 14px 20px; border-radius: 8px; margin: 20px auto 0 auto; max-width: 1160px; display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem; font-weight: 500; border: 1px solid #bbf7d0; background-color: #f0fdf4; color: #166534; animation: fadeIn 0.3s ease; }
    .flash-close { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: inherit; line-height: 1; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 768px) { 
        .navbar-menu { position: static; }
        .navbar-menu ul { flex-direction: column; }
        .navbar-menu li { width: 100%; box-sizing: border-box; }
        .form-grid-2 { grid-template-columns: 1fr; gap: 16px; } 
        .cms-header-row { flex-direction: column; align-items: flex-start; }
        .icon-btn.action-accent-btn { width: 100%; justify-content: center; }
    }
</style>

<body>

    <?php include dirname(__DIR__) . '/header.php'; ?>

    <div class="cms-wrapper">
        <div class="cms-header-row">
            <div>
                <h1>Control Panel Management</h1>
                <div class="welcome-badge">
                    Logged in operational context: <strong><?= htmlspecialchars(trim(($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? ''))) ?: 'Administrator Entity' ?></strong> 
                    (<?= htmlspecialchars($currentUser['email'] ?? 'System Context') ?>)
                </div>
            </div>
        </div>

        <?php if (!empty($modalSuccess)): ?>
        <div class="flash-alert" id="flashModalSuccess" style="background-color: #f0fdf4; color: #166534; border-color: #bbf7d0;">
            <span><?= htmlspecialchars($modalSuccess) ?></span>
            <button class="flash-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php endif; ?>

        <?php if (!empty($modalError)): ?>
        <div class="flash-alert" id="flashModalError" style="background-color: #fef2f2; color: #991b1b; border-color: #fecaca;">
            <span><?= htmlspecialchars($modalError) ?></span>
            <button class="flash-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php endif; ?>

        <?php if (!empty($messageSuccess)): ?>
        <div class="flash-alert" id="flashMessageSuccess" style="background-color: #f0fdf4; color: #166534; border-color: #bbf7d0;">
            <span><?= htmlspecialchars($messageSuccess) ?></span>
            <button class="flash-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php endif; ?>

        <?php if (!empty($messageError)): ?>
        <div class="flash-alert" id="flashMessageError" style="background-color: #fef2f2; color: #991b1b; border-color: #fecaca;">
            <span><?= htmlspecialchars($messageError) ?></span>
            <button class="flash-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php endif; ?>

        <div class="dashboard-layout">
            <nav class="navbar-menu">
                <ul>
                    <li data-target="manage-articles">
                        <span class="material-symbols-outlined">article</span>Articles
                    </li>
                    <li data-target="manage-about">
                        <span class="material-symbols-outlined">info</span>About Us
                    </li>
                    <li data-target="manage-officers">
                        <span class="material-symbols-outlined">people</span>Officers
                    </li>
                    <li data-target="manage-contact">
                        <span class="material-symbols-outlined">contact_page</span>Contact Us
                    </li>
                    <li data-target="manage-policy">
                        <span class="material-symbols-outlined">policy</span>Policy Guidelines
                    </li>
                    <li data-target="manage-footer">
                        <span class="material-symbols-outlined">page_footer</span>Footer
                    </li>
                    <li data-target="system-users">
                        <span class="material-symbols-outlined">group</span> System User
                    </li>
                </ul>
            </nav>

            <main class="dashboard-content">
                
                <section id="manage-articles" class="dashboard-section">
                    <?php include dirname(__DIR__) . '/admin/cms-articles.php'; ?>
                </section>

                <section id="manage-about" class="dashboard-section">
                    <?php include dirname(__DIR__) . '/admin/cms-about.php'; ?>
                </section>

                <section id="manage-officers" class="dashboard-section">
                    <?php include dirname(__DIR__) . '/admin/cms-officers.php'; ?>
                </section>

                <section id="manage-contact" class="dashboard-section">
                    <?php include dirname(__DIR__) . '/admin/cms-contact.php'; ?>
                </section>

                <section id="manage-policy" class="dashboard-section">
                    <?php include dirname(__DIR__) . '/admin/cms-policy.php'; ?>
                </section>

                <section id="system-users" class="dashboard-section">
                    <?php include dirname(__DIR__) . '/admin/cms-users.php'; ?>
                </section>

                <section id="manage-footer" class="dashboard-section">
                    <?php include dirname(__DIR__) . '/admin/cms-footer.php'; ?>
                </section>

            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // Navbar Navigation Logic Elements
            const navItems = document.querySelectorAll('.navbar-menu li');
            const sections = document.querySelectorAll('.dashboard-section');

            // Switcher helper function
            const switchTab = (targetId) => {
                navItems.forEach(nav => {
                    if (nav.getAttribute('data-target') === targetId) {
                        nav.classList.add('active');
                    } else {
                        nav.classList.remove('active');
                    }
                });

                sections.forEach(sec => {
                    if (sec.id === targetId) {
                        sec.classList.add('active');
                    } else {
                        sec.classList.remove('active');
                    }
                });
            };

            // Read URL parameters on load to preserve navigation state across updates
            const urlParams = new URLSearchParams(window.location.search);
            let activeTab = 'manage-articles'; // Default View

            if (urlParams.has('about_updated')) {
                activeTab = 'manage-about';
            } else if (urlParams.has('footer_updated')) {
                activeTab = 'manage-footer';
            } else if (urlParams.has('user_updated') || urlParams.has('user_added')) {
                activeTab = 'system-users';
            }

            // Execute initial tab view assignment
            switchTab(activeTab);

            // Handle client-side clicks on menu navigation tabs
            navItems.forEach(item => {
                item.addEventListener('click', () => {
                    const targetId = item.getAttribute('data-target');
                    switchTab(targetId);
                });
            });

            // Modal Controls Initialization
            const modalOverlay = document.getElementById('postModalOverlay');
            const openBtn = document.getElementById('modalOpenBtn');
            const closeBtn = document.getElementById('modalCloseBtn');
            const cancelBtn = document.getElementById('modalCancelBtn');

            const closeModal = () => {
                if (modalOverlay) {
                    modalOverlay.classList.remove('active');
                }
                if (window.location.search.includes('edit_id=')) {
                    window.location.href = 'dashboard.php';
                }
            };

            if (modalOverlay) {
                if (openBtn) openBtn.addEventListener('click', () => modalOverlay.classList.add('active'));
                if (closeBtn) closeBtn.addEventListener('click', closeModal);
                if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

                modalOverlay.addEventListener('click', (e) => {
                    if (e.target === modalOverlay) closeModal();
                });
            }

            // Flash Messaging Runtime Closes automatically after 4.5 seconds
            const flash = document.getElementById('flashBanner');
            if (flash) {
                setTimeout(() => { 
                    flash.style.transition = 'opacity 0.4s ease';
                    flash.style.opacity = '0'; 
                    setTimeout(() => flash.remove(), 400); 
                }, 4500);
            }

            // Tab Component Routing logic inside overlay modals
            const tabButtons = document.querySelectorAll('.upload-tabs .tab-btn');
            const uploadSlots = document.querySelectorAll('.upload-slot');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');

                    const targetId = button.getAttribute('data-target');
                    uploadSlots.forEach(slot => {
                        slot.classList.remove('active');
                        if (slot.id === targetId) {
                            slot.classList.add('active');
                        }
                    });

                    const postImageFile = document.getElementById('postImageFile');
                    const selectedName = document.getElementById('file-selected-name');
                    const postImageURL = document.getElementById('postImageURL');

                    if (targetId === 'url-mode') {
                        if (postImageFile) postImageFile.value = "";
                        if (selectedName) selectedName.textContent = "";
                    } else {
                        if (postImageURL) postImageURL.value = "";
                    }
                });
            });

            // Tracking and reflecting chosen file uploads cleanly
            const fileInput = document.getElementById('postImageFile');
            const fileNameIndicator = document.getElementById('file-selected-name');

            if (fileInput && fileNameIndicator) {
                fileInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        fileNameIndicator.textContent = `✓ Selected asset file: ${e.target.files[0].name}`;
                    } else {
                        fileNameIndicator.textContent = "";
                    }
                });
            }
        });
    </script>
</body>
</html>
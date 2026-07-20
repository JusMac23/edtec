<?php

// Authenticate and initialize safe database configurations
require_once __DIR__ . '/auth/auth.php';

// System Context Interface Data Fetching queries
$posts = []; 

try {
    // Fetch logged-in profile context
    $currentUser = null;
    if (isset($userId) && $userId) {
        // Secure binding utilizing strict types
        $userStmt = $pdo->prepare("SELECT first_name, last_name, email, role, avatar_url FROM users WHERE id = :id LIMIT 1");
        $userStmt->execute(['id' => (int)$userId]);
        $currentUser = $userStmt->fetch();
    }

    // Explicitly selecting fields for the public visitor stream payload
    $sql = "SELECT p.title, p.slug, p.excerpt, p.content, p.featured_image, p.published_at, u.first_name, u.last_name 
            FROM cms_articles_page p
            JOIN users u ON p.author_id = u.id
            WHERE p.status = 'published'
            ORDER BY p.published_at DESC
            LIMIT 6";
            
    // Safe because the SQL context is static and does not contain user inputs
    $stmt = $pdo->query($sql);
    if ($stmt) {
        $posts = $stmt->fetchAll() ?: [];
    }
} catch (\PDOException $e) {
    error_log("Fetch Posts Loop Failure: " . $e->getMessage());
    $posts = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link class="styles" rel="stylesheet" href="./css/index.css">
    <link class="styles" rel="stylesheet" href="./css/modal-styles.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <?php include("header.php"); ?>

    <?php if (isset($_GET['posted']) && $_GET['posted'] === 'true'): ?>
        <div class="flash-alert flash-success" id="flashBanner">
            <span>🎉 Blog article created and published live successfully!</span>
            <button class="flash-close" onclick="document.getElementById('flashBanner').remove()">×</button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; margin: 20px auto; max-width: 600px; border-radius: 4px; font-family: sans-serif; text-align: center;">
            <?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <section class="hero-container">
        <div class="hero">
            <h1>ExpertDev and TechEd Channels (EDTEC) Academy Inc.</h1>
            <h2>SEC 2025040199006-61</h2>
        
            <div class="carousel-wrapper">
                <?php if (!empty($posts) && is_array($posts) && count(array_slice($posts, 0, 3)) > 1): ?>
                    <button class="carousel-arrow prev-btn" id="prevBtn" aria-label="Previous Slide">
                        <span class="material-symbols-outlined">arrow_back_ios_new</span>
                    </button>
                    <button class="carousel-arrow next-btn" id="nextBtn" aria-label="Next Slide">
                        <span class="material-symbols-outlined">arrow_forward_ios</span>
                    </button>
                <?php endif; ?>

                <div class="carousel-track" id="carouselTrack">
                    <?php
                    $carouselPosts = (!empty($posts) && is_array($posts)) ? array_slice($posts, 0, 3) : [];
                    if (!empty($carouselPosts)):
                        foreach ($carouselPosts as $index => $post):
                            $imgPath = $post['featured_image'] ?? '';
                            if (!empty($imgPath) && strpos($imgPath, './') === 0) {
                                $imgPath = substr($imgPath, 2);
                            }
                            
                            $finalSrc = !empty($imgPath) && file_exists($imgPath) 
                                ? htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8') 
                                : 'https://picsum.photos/seed/' . urlencode($post['slug'] ?? 'default') . '/1200/500';
                    ?>
                            <div class="carousel-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                                <a href="post.php?slug=<?= urlencode($post['slug'] ?? '') ?>" class="carousel-image-link" aria-label="Read <?= htmlspecialchars($post['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <img src="<?= $finalSrc ?>" alt="<?= htmlspecialchars($post['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="pure-carousel-img">
                                </a>
                            </div>
                    <?php 
                        endforeach;
                    else:
                    ?>
                        <div class="no-posts">
                            <h3>No featured images to display.</h3>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (count($carouselPosts) > 1): ?>
                    <div class="carousel-indicators" id="carouselIndicators">
                        <?php foreach ($carouselPosts as $index => $post): ?>
                            <button class="indicator-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?= (int)$index ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>    
        </div>
    </section>

    <main>
        <div class="grid">
            <div class="latest-updates">
                <span>// OUR LATEST SERVICES</span>
                <h2>Read Our Latest Services</h2>
            </div>
        </div>
        <div class="grid">
            <?php if (!empty($posts) && is_array($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <?php 
                        // Resolve Image
                        $postImg = !empty($post['featured_image']) ? htmlspecialchars($post['featured_image'], ENT_QUOTES, 'UTF-8') : 'https://picsum.photos/seed/'.urlencode($post['slug'] ?? 'default').'/600/400';
                        
                        // Resolve Meta Data String
                        $metaData = '';
                        if (isset($_SESSION['user_id'])) {
                            $authorName = htmlspecialchars(trim(($post['first_name'] ?? '') . ' ' . ($post['last_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?: 'Unknown Author';
                            $pubDate = !empty($post['published_at']) ? date('M d, Y', strtotime($post['published_at'])) : date('M d, Y');
                            $metaData = "By <strong>{$authorName}</strong> &bull; {$pubDate}";
                        } else {
                            $pubDate = !empty($post['published_at']) ? date('M d, Y', strtotime($post['published_at'])) : date('M d, Y');
                            $metaData = "Published on {$pubDate}";
                        }
                    ?>
                    <article class="card">
                        <img class="card-img" src="<?= $postImg ?>" alt="<?= htmlspecialchars($post['title'] ?? 'Blog Post', ENT_QUOTES, 'UTF-8') ?>">
                        
                        <div class="card-content">
                            <div class="card-meta">
                                <?= $metaData ?>
                            </div>
                            
                            <h2 class="card-title">
                                <a href="post.php?slug=<?= urlencode($post['slug'] ?? '') ?>">
                                    <?= htmlspecialchars($post['title'] ?? 'Untitled Article', ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </h2>
                            
                            <p class="card-excerpt">
                                <?= htmlspecialchars($post['excerpt'] ?? substr(strip_tags($post['content'] ?? ''), 0, 120) . '...', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            
                            <a href="javascript:void(0);" 
                               class="read-more open-modal-btn"
                               data-title="<?= htmlspecialchars($post['title'] ?? 'Untitled Article', ENT_QUOTES, 'UTF-8') ?>"
                               data-image="<?= $postImg ?>"
                               data-meta="<?= htmlspecialchars($metaData, ENT_QUOTES, 'UTF-8') ?>"
                               data-content="<?= htmlspecialchars($post['content'] ?? 'No content available for this post.', ENT_QUOTES, 'UTF-8') ?>">
                                Read Article →
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-posts">
                    <h3>No posts published yet.</h3>
                </div>
            <?php endif; ?>
        </div>
    </main> 

    <?php include("footer.php"); ?>

    <div id="articleModal" class="modal-overlay" aria-modal="true" role="dialog">
        <div class="modal-content">
            <button class="modal-close" id="modalCloseBtn" aria-label="Close modal">&times;</button>
            <div class="modal-img-wrapper">
                <img id="modalImg" src="" alt="Article Image">
            </div>
            <div class="modal-body">
                <h2 class="modal-title" id="modalTitle"></h2>
                <div class="modal-meta" id="modalMeta"></div>
                <div class="modal-text" id="modalText"></div>
            </div>
        </div>
    </div>

    <script>
        // --- Carousel System Logic Implementation ---
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('carouselTrack');
            const indicatorsContainer = document.getElementById('carouselIndicators');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            if (!track) return;

            const slides = Array.from(track.children);
            const indicators = indicatorsContainer ? Array.from(indicatorsContainer.children) : [];
            
            let currentIndex = 0;
            const slideIntervalTime = 5000;
            let slideInterval;

            const updateCarousel = (targetIndex) => {
                slides[currentIndex].classList.remove('active');
                if (indicators.length) indicators[currentIndex].classList.remove('active');

                slides[targetIndex].classList.add('active');
                if (indicators.length) indicators[targetIndex].classList.add('active');
                
                track.style.transform = `translateX(-${targetIndex * 100}%)`;
                currentIndex = targetIndex;
            };

            const startAutoSlide = () => {
                if (slides.length <= 1) return;
                slideInterval = setInterval(() => {
                    const nextIndex = (currentIndex + 1) % slides.length;
                    updateCarousel(nextIndex);
                }, slideIntervalTime);
            };

            const resetAutoSlide = () => {
                clearInterval(slideInterval);
                startAutoSlide();
            };

            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    const nextIndex = (currentIndex + 1) % slides.length;
                    updateCarousel(nextIndex);
                    resetAutoSlide();
                });
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    const prevIndex = (currentIndex - 1 + slides.length) % slides.length;
                    updateCarousel(prevIndex);
                    resetAutoSlide();
                });
            }

            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => {
                    if (index === currentIndex) return;
                    updateCarousel(index);
                    resetAutoSlide();
                });
            });

            startAutoSlide();
        });

        // --- Flash Alert Logic ---
        const flash = document.getElementById('flashBanner');
        if(flash){
            setTimeout(() => { flash.style.opacity = '0'; setTimeout(() => flash.remove(), 400); }, 4500);
        }

        // --- Modal System Logic Implementation ---
        const modal = document.getElementById('articleModal');
        const closeBtn = document.getElementById('modalCloseBtn');
        const openBtns = document.querySelectorAll('.open-modal-btn');
        
        const mTitle = document.getElementById('modalTitle');
        const mImg = document.getElementById('modalImg');
        const mMeta = document.getElementById('modalMeta');
        const mText = document.getElementById('modalText');

        // Open Modal Event
        openBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Populate Modal Data
                mTitle.textContent = btn.dataset.title;
                mImg.src = btn.dataset.image;
                mImg.alt = btn.dataset.title;
                
                // Handle optional meta visibility
                if (btn.dataset.meta) {
                    mMeta.innerHTML = btn.dataset.meta;
                    mMeta.style.display = 'block';
                } else {
                    mMeta.style.display = 'none';
                }

                // Inject full HTML content
                mText.innerHTML = btn.dataset.content; 

                // Trigger animations
                modal.classList.add('active');
                document.body.classList.add('modal-open');
            });
        });

        // Close Logic Variables
        const closeModal = () => {
            modal.classList.remove('active');
            document.body.classList.remove('modal-open');
        };

        // Close on 'X' Button Click
        closeBtn.addEventListener('click', closeModal);

        // Close on Outside Overlay Click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Close on ESC Key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('active')) closeModal();
        });
    </script>
</body>
</html>
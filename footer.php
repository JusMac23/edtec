<?php
// Establish an absolute path so this works regardless of which directory includes the footer
$footerJsonPath = __DIR__ . '/uploads/footer_content.json';

// Define the default fallback data structure, now including social links
$footerData = [
    'blog_name'   => 'DevBlog',
    'footer_text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    'socials'     => [
        'facebook'  => '#',
        'youtube'   => '#',
        'instagram' => '#',
        'twitter'   => '#'
    ]
];

// Load and parse the CMS data if it exists
if (file_exists($footerJsonPath)) {
    $existingFooter = json_decode(file_get_contents($footerJsonPath), true);
    if (is_array($existingFooter)) {
        // Use array_replace_recursive to deeply merge nested arrays (like 'socials')
        $footerData = array_replace_recursive($footerData, $existingFooter);
    }
}
?>

<style>
    /* Footer Layout Base */
    footer { background-color: var(--accent); padding: 3rem 2rem; text-align: center; color: #ffffff; font-size: 0.95rem; font-weight: 500; width: 100%; box-sizing: border-box; margin-top: auto; box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; align-items: center; gap: 2rem; }
    
    /* Footer Info Block */
    .footer-info { max-width: 600px; }
    /* Copyright & Blog Name */
    .footer-info p { margin: 0 0 0.5rem 0; color: #ffffff; font-weight: 800; font-size: 1.1rem; letter-spacing: 0.02em; }
    /* Footer Fallback Text Description */
    .footer-info span { display: block; font-size: 0.9rem; color: rgba(255, 255, 255, 0.85); font-weight: 500; line-height: 1.5; }
    
    /* Social Media Section */
    .footer-socials { display: flex; flex-direction: column; align-items: center; gap: 1.2rem; }
    /* Follow Us Section Title */
    .footer-socials-title { color: #ffffff; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 800; }
    /* Social Icons Horizontal Flex Container */
    .social-icons-wrapper { display: flex; gap: 1.5rem; }
    
    /* Social Action Buttons */
    .social-icon { color: #ffffff; background-color: rgba(255, 255, 255, 0.15); border: 2px solid transparent; border-radius: 50%; padding: 0.65rem; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
    /* Global Tactical Icon Hover Scaling Effect */
    .social-icon:hover { transform: translateY(-5px) scale(1.15); background-color: #ffffff; border-color: #ffffff; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); }
    
    /* Brand Color Transitions */
    .social-icon.facebook:hover { color: #1877F2; }
    .social-icon.youtube:hover { color: #FF0000; }
    .social-icon.instagram:hover { color: #E4405F; }
    .social-icon.twitter:hover { color: #000000; } 
    
    /* SVG & Media Queries */
    .social-icon svg { width: 22px; height: 22px; fill: currentColor; }
    /* Mobile Device Responsiveness Breakpoint */
    @media screen and (max-width: 480px) { footer { padding: 2.5rem 1rem; gap: 1.5rem; } .social-icons-wrapper { gap: 1rem; } }
</style>

<footer>
    <div class="footer-socials">
        <div class="footer-socials-title">Follow Us</div>
        <div class="social-icons-wrapper">
            <a href="<?= htmlspecialchars($footerData['socials']['facebook'] ?? '#') ?>" class="social-icon facebook" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                <svg viewBox="0 0 24 24">
                    <path d="M18.77,7.46H14.5v-1.9c0-.9.6-1.1,1-1.1h3V.5h-4.33C10.24.5,9.5,3.44,9.5,5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4Z"/>
                </svg>
            </a>
            
            <a href="<?= htmlspecialchars($footerData['socials']['youtube'] ?? '#') ?>" class="social-icon youtube" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                <svg viewBox="0 0 24 24">
                    <path d="M21.58,6.18A2.71,2.71,0,0,0,19.67,4.3C18,3.85,12,3.85,12,3.85s-6,0-7.67.45A2.71,2.71,0,0,0,2.42,6.18C2,7.82,2,12,2,12s0,4.18.42,5.82a2.71,2.71,0,0,0,1.91,1.88C6,20.15,12,20.15,12,20.15s6,0,7.67-.45a2.71,2.71,0,0,0,1.91-1.88C22,16.18,22,12,22,12s0-4.18-.42-5.82ZM9.6,15.5V8.5l6.5,3.5-6.5,3.5Z"/>
                </svg>
            </a>
            
            <a href="<?= htmlspecialchars($footerData['socials']['instagram'] ?? '#') ?>" class="social-icon instagram" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                <svg viewBox="0 0 24 24">
                    <path d="M12,5.74c-2.14,0-3.86,1.72-3.86,3.86S9.86,13.46,12,13.46s3.86-1.72,3.86-3.86S14.14,5.74,12,5.74Zm0,6.33a2.47,2.47,0,1,1,2.47-2.47A2.47,2.47,0,0,1,12,12.07Z"/>
                    <path d="M16.92,5.43a1.05,1.05,0,1,1-1.05-1.05,1.05,1.05,0,0,1,1.05,1.05Z"/>
                    <path d="M11.96,2h.08c2.68,0,3,0,4.06.05a5.55,5.55,0,0,1,1.86.35,3.34,3.34,0,0,1,1.91,1.91,5.55,5.55,0,0,1,.35,1.86c.05,1.06.05,1.38.05,4.06s0,3-.05,4.06a5.55,5.55,0,0,1-.35,1.86,3.34,3.34,0,0,1-1.91,1.91,5.55,5.55,0,0,1-1.86.35c-1.06.05-1.38.05-4.06.05h-.08c-2.68,0-3,0-4.06-.05a5.55,5.55,0,0,1-1.86-.35,3.34,3.34,0,0,1-1.91-1.91,5.55,5.55,0,0,1-.35-1.86c-.05-1.06-.05-1.38-.05-4.06s0-3,.05-4.06a5.55,5.55,0,0,1,.35-1.86A3.34,3.34,0,0,1,6.04,2.4,5.55,5.55,0,0,1,7.9,2.05C8.96,2,9.28,2,11.96,2ZM12,4c-2.64,0-2.97,0-4,.05a3.52,3.52,0,0,0-1.18.22,1.34,1.34,0,0,0-.77.77A3.52,3.52,0,0,0,5.83,6.22C5.78,7.25,5.77,7.58,5.77,12s0,4.75.05,5.78a3.52,3.52,0,0,0,.22,1.18,1.34,1.34,0,0,0,.77.77,3.52,3.52,0,0,0,1.18.22c1.03.05,1.36.05,4,.05s2.97,0,4-.05a3.52,3.52,0,0,0,1.18-.22,1.34,1.34,0,0,0,.77-.77,3.52,3.52,0,0,0,.22-1.18c.05-1.03.05-1.36.05-4s0-2.97-.05-4a3.52,3.52,0,0,0-.22-1.18,1.34,1.34,0,0,0-.77-.77A3.52,3.52,0,0,0,18,4.05C16.97,4,16.64,4,14,4h-2Z"/>
                </svg>
            </a>
            
            <a href="<?= htmlspecialchars($footerData['socials']['twitter'] ?? '#') ?>" class="social-icon twitter" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                <svg viewBox="0 0 24 24">
                    <path d="M23,3a10.9,10.9,0,0,1-3.14,1.53,4.48,4.48,0,0,0-7.86,3v1A10.66,10.66,0,0,1,3,4s-4,9,5,13a11.64,11.64,0,0,1-7,2c9,5,20,0,20-11.5a4.5,4.5,0,0,0-.08-.83A7.72,7.72,0,0,0,23,3Z"/>
                </svg>
            </a>
        </div>
    </div>

    <div class="footer-info">
        <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($footerData['blog_name'] ?? 'DevBlog') ?></p>
        <span><?= htmlspecialchars($footerData['footer_text'] ?? 'Built for absolute performance and reliability.') ?></span>
    </div>
</footer>
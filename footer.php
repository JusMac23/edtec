<?php
// Establish an absolute path so this works regardless of which directory includes the footer
$footerJsonPath = __DIR__ . '/uploads/footer_content.json';

// Define the default fallback data structure
$footerData = [
    'blog_name'   => 'DevBlog',
    'footer_text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
];

// Load and parse the CMS data if it exists
if (file_exists($footerJsonPath)) {
    $existingFooter = json_decode(file_get_contents($footerJsonPath), true);
    if (is_array($existingFooter)) {
        $footerData = array_merge($footerData, $existingFooter);
    }
}
?>

<style>
    footer { background-color: var(--bg-secondary); border-top: 1px solid var(--card-border); padding: 2rem 2rem; text-align: center; color: var(--text-secondary); font-size: 0.95rem; font-weight: 500; width: 100%; box-sizing: border-box; margin-top: auto; }
</style>

<footer>
    <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($footerData['blog_name'] ?? 'DevBlog') ?></p>
    <span><?= htmlspecialchars($footerData['footer_text'] ?? 'Built for absolute performance and reliability.') ?></span>
</footer>
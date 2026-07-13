<section id="manage-footer" class="dashboard-section">

    <div class="section-header">
        <h2>Manage Footer Content</h2>
    </div>
    
    <div class="cms-table-card">
        <div class="cms-card-heading">Global Footer Component Layout Configurations</div>
        <form action="dashboard.php" method="POST" class="footer-cms-form">
            <input type="hidden" name="action" value="update_footer_cms">
            
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="blog_name">Website Branding / Blog Identity Name Text</label>
                    <input type="text" id="blog_name" name="blog_name" value="<?= htmlspecialchars($footerData['blog_name'] ?? '') ?>" required placeholder="e.g., DevBlog">
                </div>
                <div class="form-group">
                    <label for="footer_text">Footer Context Narrative Meta Info</label>
                    <input type="text" id="footer_text" name="footer_text" value="<?= htmlspecialchars($footerData['footer_text'] ?? '') ?>" required placeholder="e.g., Built for absolute performance and reliability.">
                </div>
            </div>

            <div style="text-align: right; margin-top: 10px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px; background-color: #4f46e5; border-color: #4f46e5;">Update Footer Configuration</button>
            </div>
        </form>
    </div>
</section>
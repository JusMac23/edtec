<section id="manage-about" class="dashboard-section">

    <div class="section-header">
        <h2>Manage About Us Content</h2>
    </div>
    
    <div class="cms-table-card">
        <div class="cms-card-heading">Static Structural Component Controls</div>
        <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="about-cms-form">
            <input type="hidden" name="action" value="update_about_cms">
            
            <div class="form-grid">
                <h1>Facts and History Section</h1>
                <div class="form-group">
                    <label for="hero_title">About Us Title</label>
                    <input type="text" id="hero_title" name="hero_title" value="<?= htmlspecialchars($aboutData['hero_title'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="hero_desc1">About Us Paragraph 1</label>
                    <textarea id="hero_desc1" name="hero_desc1" required><?= htmlspecialchars($aboutData['hero_desc1'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="hero_desc2">About Us Paragraph 2</label>
                    <textarea id="hero_desc2" name="hero_desc2" required><?= htmlspecialchars($aboutData['hero_desc2'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="hero_desc3">About Us Paragraph 3</label>
                    <textarea id="hero_desc3" name="hero_desc3" required><?= htmlspecialchars($aboutData['hero_desc3'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="hero_desc4">About Us Paragraph 4</label>
                    <textarea id="hero_desc4" name="hero_desc4" required><?= htmlspecialchars($aboutData['hero_desc4'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="hero_desc5">About Us Paragraph 5</label>
                    <textarea id="hero_desc5" name="hero_desc5" required><?= htmlspecialchars($aboutData['hero_desc5'] ?? '') ?></textarea>
                </div>
            </div>

            <h1>Mision Vision Section</h1>
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="mission_title">Mission Section Heading</label>
                    <input type="text" id="mission_title" name="mission_title" value="<?= htmlspecialchars($aboutData['mission_title'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="vision_title">Vision Section Heading</label>
                    <input type="text" id="vision_title" name="vision_title" value="<?= htmlspecialchars($aboutData['vision_title'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-grid-2">
                <div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="mission_p">Mission Content</label>
                        <textarea id="mission_p" name="mission_p" rows="3" required><?= htmlspecialchars($aboutData['mission_p'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="mission_img_file">Mission Component Image File</label>
                        <input type="file" id="mission_img_file" name="mission_img_file" accept="image/*">
                        <?php if (!empty($aboutData['mission_img']) && file_exists($aboutData['mission_img'])): ?>
                            <div class="inline-preview-box">
                                <img src="<?= htmlspecialchars($aboutData['mission_img']) ?>" alt="Mission Asset">
                                <span>Active image stored: <strong><?= htmlspecialchars(basename($aboutData['mission_img'])) ?></strong></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="vision_p">Vision Content</label>
                        <textarea id="vision_p" name="vision_p" rows="3" required><?= htmlspecialchars($aboutData['vision_p'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="vision_img_file">Vision Component Image File</label>
                        <input type="file" id="vision_img_file" name="vision_img_file" accept="image/*">
                        <?php if (!empty($aboutData['vision_img']) && file_exists($aboutData['vision_img'])): ?>
                            <div class="inline-preview-box">
                                <img src="<?= htmlspecialchars($aboutData['vision_img']) ?>" alt="Vision Asset">
                                <span>Active image stored: <strong><?= htmlspecialchars(basename($aboutData['vision_img'])) ?></strong></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="form-grid">
                <h1>Goals Section</h1>
                <div class="form-group">
                    <label for="goals_title">Goals Title</label>
                    <input type="text" id="goals_title" name="goals_title" value="<?= htmlspecialchars($aboutData['goals_title'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="goals_p1">Goal 1</label>
                    <textarea id="goals_p1" name="goals_p1" required><?= htmlspecialchars($aboutData['goals_p1'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="goals_p2">Goal 2</label>
                    <textarea id="goals_p2" name="goals_p2" required><?= htmlspecialchars($aboutData['goals_p2'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="goals_p3">Goal 3</label>
                    <textarea id="goals_p3" name="goals_p3" required><?= htmlspecialchars($aboutData['goals_p3'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="goals_p4">Goal 4</label>
                    <textarea id="goals_p4" name="goals_p4" required><?= htmlspecialchars($aboutData['goals_p4'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="goals_p5">Goal 5</label>
                    <textarea id="goals_p5" name="goals_p5" required><?= htmlspecialchars($aboutData['goals_p5'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="goals_p6">Goal 6</label>
                    <textarea id="goals_p6" name="goals_p6" required><?= htmlspecialchars($aboutData['goals_p6'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="goals_p7">Goal 7</label>
                    <textarea id="goals_p7" name="goals_p7" required><?= htmlspecialchars($aboutData['goals_p7'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="goals_p8">Goal 8</label>
                    <textarea id="goals_p8" name="goals_p8" required><?= htmlspecialchars($aboutData['goals_p8'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-grid">
                <h1>Tagline Section</h1>
                <div class="form-group">
                    <label for="tagline_title">Tagline Title</label>
                    <input type="text" id="tagline_title" name="tagline_title" value="<?= htmlspecialchars($aboutData['tagline_title'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="tagline_p1">Tagline 1</label>
                    <textarea id="tagline_p1" name="tagline_p1" required><?= htmlspecialchars($aboutData['tagline_p1'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="tagline_p2">Tagline 2</label>
                    <textarea id="tagline_p2" name="tagline_p2" required><?= htmlspecialchars($aboutData['tagline_p2'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="tagline_p3">Tagline 3</label>
                    <textarea id="tagline_p3" name="tagline_p3" required><?= htmlspecialchars($aboutData['tagline_p3'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="tagline_p4">Tagline 4</label>
                    <textarea id="tagline_p4" name="tagline_p4" required><?= htmlspecialchars($aboutData['tagline_p4'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="tagline_p5">Tagline 5</label>
                    <textarea id="tagline_p5" name="tagline_p5" required><?= htmlspecialchars($aboutData['tagline_p5'] ?? '') ?></textarea>
                </div>
            </div>

            <div style="text-align: right; margin-top: 10px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Update About Fields</button>
            </div>
        </form>
    </div>

</section>
<section id="manage-courses" class="dashboard-section">

    <div class="section-header">
        <h2>Manage Courses Content</h2>
    </div>
    
    <div class="cms-table-card">
        <div class="cms-card-heading">Static Structural Component Controls</div>
        
        <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="courses-cms-form">
            <input type="hidden" name="action" value="update_courses_cms">
            
            <div class="form-grid" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0;">
                <h3 style="margin-bottom: 15px; color: #0f172a;">2-Year Bundled Courses Section</h3>
                
                <div class="form-group">
                    <label>Course Title</label>
                    <input type="text" name="bundled[course_title]" value="<?= htmlspecialchars($course2yrbundledData['course_title'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Course Description 1</label>
                    <textarea name="bundled[course_desc1]" required><?= htmlspecialchars($course2yrbundledData['course_desc1'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 2</label>
                    <textarea name="bundled[course_desc2]" required><?= htmlspecialchars($course2yrbundledData['course_desc2'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 3</label>
                    <textarea name="bundled[course_desc3]" required><?= htmlspecialchars($course2yrbundledData['course_desc3'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 4</label>
                    <textarea name="bundled[course_desc4]" required><?= htmlspecialchars($course2yrbundledData['course_desc4'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 5</label>
                    <textarea name="bundled[course_desc5]" required><?= htmlspecialchars($course2yrbundledData['course_desc5'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-grid" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0;">
                <h3 style="margin-bottom: 15px; color: #0f172a;">Nominal Duration Courses Section</h3>
                
                <div class="form-group">
                    <label>Course Title</label>
                    <input type="text" name="nominal[course_title]" value="<?= htmlspecialchars($coursenominaldurationData['course_title'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Course Description 1</label>
                    <textarea name="nominal[course_desc1]" required><?= htmlspecialchars($coursenominaldurationData['course_desc1'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 2</label>
                    <textarea name="nominal[course_desc2]" required><?= htmlspecialchars($coursenominaldurationData['course_desc2'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 3</label>
                    <textarea name="nominal[course_desc3]" required><?= htmlspecialchars($coursenominaldurationData['course_desc3'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 4</label>
                    <textarea name="nominal[course_desc4]" required><?= htmlspecialchars($coursenominaldurationData['course_desc4'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 5</label>
                    <textarea name="nominal[course_desc5]" required><?= htmlspecialchars($coursenominaldurationData['course_desc5'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 6</label>
                    <textarea name="nominal[course_desc6]" required><?= htmlspecialchars($coursenominaldurationData['course_desc6'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 7</label>
                    <textarea name="nominal[course_desc7]" required><?= htmlspecialchars($coursenominaldurationData['course_desc7'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-grid" style="margin-bottom: 20px;">
                <h3 style="margin-bottom: 15px; color: #0f172a;">Special Training Courses Section</h3>
                
                <div class="form-group">
                    <label>Course Title</label>
                    <input type="text" name="special[course_title]" value="<?= htmlspecialchars($coursespecialtrainingData['course_title'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Course Description 1</label>
                    <textarea name="special[course_desc1]" required><?= htmlspecialchars($coursespecialtrainingData['course_desc1'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 2</label>
                    <textarea name="special[course_desc2]" required><?= htmlspecialchars($coursespecialtrainingData['course_desc2'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 3</label>
                    <textarea name="special[course_desc3]" required><?= htmlspecialchars($coursespecialtrainingData['course_desc3'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 4</label>
                    <textarea name="special[course_desc4]" required><?= htmlspecialchars($coursespecialtrainingData['course_desc4'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 5</label>
                    <textarea name="special[course_desc5]" required><?= htmlspecialchars($coursespecialtrainingData['course_desc5'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 6</label>
                    <textarea name="special[course_desc6]" required><?= htmlspecialchars($coursespecialtrainingData['course_desc6'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 7</label>
                    <textarea name="special[course_desc7]" required><?= htmlspecialchars($coursespecialtrainingData['course_desc7'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Description 8</label>
                    <textarea name="special[course_desc8]" required><?= htmlspecialchars($coursespecialtrainingData['course_desc8'] ?? '') ?></textarea>
                </div>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 28px; background-color: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    Update All Course Content
                </button>
            </div>
        </form>
    </div>

</section>
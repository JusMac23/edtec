<section id="manage-policy" class="dashboard-section">

    <div class="section-header">
        <h2>Manage Policy and Guidelines Content</h2>
    </div>
    
    <div class="cms-table-card">
        <div class="cms-card-heading">Static Structural Component Controls</div>
        <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="policy-cms-form">
            <input type="hidden" name="action" value="update_policy_cms">
            
            <div class="form-grid">
                <h1>Administrative Rules</h1>
                <div class="form-group">
                    <label for="policy_title">Policy Title</label>
                    <input type="text" id="policy_title" name="policy_title" value="<?= htmlspecialchars($policyData['policy_title'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="policy_desc1">Policy Description 1</label>
                    <textarea id="policy_desc1" name="policy_desc1" required><?= htmlspecialchars($policyData['policy_desc1'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="policy_desc2">Policy Description 2</label>
                    <textarea id="policy_desc2" name="policy_desc2" required><?= htmlspecialchars($policyData['policy_desc2'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="policy_desc3">Policy Description 3</label>
                    <textarea id="policy_desc3" name="policy_desc3" required><?= htmlspecialchars($policyData['policy_desc3'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="policy_desc4">Policy Description 4</label>
                    <textarea id="policy_desc4" name="policy_desc4" required><?= htmlspecialchars($policyData['policy_desc4'] ?? '') ?></textarea>
                </div>
            </div>

            <div style="text-align: right; margin-top: 10px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Update Policy Fields</button>
            </div>
        </form>
    </div>

</section>
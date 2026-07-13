<section id="manage-contact" class="dashboard-section">

    <div class="section-header">
        <h2>Manage Contact Information</h2>
    </div>
    
    <div class="cms-table-card">
        <div class="cms-card-heading">Static Structural Component Controls</div>
        <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="contact-cms-form">
            <input type="hidden" name="action" value="update_contact_cms">
            
            <div class="form-grid-1">
                <h1>Contact Information Section</h1>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" required value="<?= htmlspecialchars($contactData['org_address'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($contactData['org_email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="contact_number_globe">Contact Number Globe</label>
                    <input type="text" id="contact_number_globe" name="contact_number_globe" required value="<?= htmlspecialchars($contactData['org_contact_number_globe'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="contact_number_smart">Contact Number Smart</label>
                    <input type="text" id="contact_number_smart" name="contact_number_smart" required value="<?= htmlspecialchars($contactData['org_contact_number_smart'] ?? '') ?>">
                </div>
            </div>

            <div style="text-align: right; margin-top: 10px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Update Contact Fields</button>
            </div>
        </form>
    </div>

</section>
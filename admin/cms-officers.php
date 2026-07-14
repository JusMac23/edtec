<section id="manage-officers" class="dashboard-section">

    <div class="section-header">
        <h2>Registered Officers Directory</h2>
        <button class="icon-btn action-accent-btn" id="modalOpenOfficerBtn" aria-label="Add Officer">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="action-icon">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
            <span class="text-btn">Add Officer</span>
        </button>
    </div>

    <div class="cms-table-card">
        <div class="cms-card-heading">Active Structural Profile Directories</div>
        <div class="table-responsive-wrapper">
            <table class="cms-table">
                <thead>
                    <tr>
                        <th>Officer ID</th>
                        <th>Photo Reference</th>
                        <th>Full Name</th>
                        <th>Position Title</th>
                        <th>Category Placement</th>
                        <th style="text-align: center; width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($allOfficers) || !is_array($allOfficers)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 45px 30px;">
                                No profile indexes populated matching framework standard variables.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($allOfficers as $officerProfileItem): ?>
                            <tr>
                                <td style="font-family: monospace; color: #64748b; font-weight: 600;">
                                    #<?= (int)($officerProfileItem['officer_id'] ?? 0) ?>
                                </td>
                                <td>
                                    <?php if(!empty($officerProfileItem['officer_photo']) && file_exists($officerProfileItem['officer_photo'])): ?>
                                        <?= htmlspecialchars(basename($officerProfileItem['officer_photo'])) ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 600; color: #0f172a;">
                                    <?= htmlspecialchars(($officerProfileItem['officer_firstname'] ?? '') . ' ' . ($officerProfileItem['officer_middleinitial'] ?? '') . ' ' . ($officerProfileItem['officer_lastname'] ?? '')) ?>
                                </td>
                                <td style="color: #475569; font-weight: 500;">
                                    <?= htmlspecialchars($officerProfileItem['officer_position'] ?? 'Unassigned') ?>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: #f8fafc; color: #334155; padding: 6px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; border: 1px solid #e2e8f0; display: inline-block;">
                                        <?= htmlspecialchars($officerProfileItem['category_label'] ?? 'General') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-wrapper-cell">
                                        <button type="button" class="action-ui-btn edit-trigger-btn" 
                                                title="Edit Profile"
                                                data-id="<?= (int)$officerProfileItem['officer_id'] ?>"
                                                data-fname="<?= htmlspecialchars($officerProfileItem['officer_firstname'] ?? '') ?>"
                                                data-mname="<?= htmlspecialchars($officerProfileItem['officer_middleinitial'] ?? '') ?>"
                                                data-lname="<?= htmlspecialchars($officerProfileItem['officer_lastname'] ?? '') ?>"
                                                data-position="<?= htmlspecialchars($officerProfileItem['officer_position'] ?? '') ?>"
                                                data-category="<?= htmlspecialchars($officerProfileItem['category_slug'] ?? '') ?>"
                                                data-photo="<?= htmlspecialchars($officerProfileItem['officer_photo'] ?? '') ?>">
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                        <a href="dashboard.php?action=delete_officer&officer_id=<?= urlencode($officerProfileItem['officer_id'] ?? '') ?>&category=<?= urlencode($officerProfileItem['category_slug'] ?? '') ?>" 
                                           class="action-ui-btn delete-trigger-btn" 
                                           title="Delete Profile" 
                                           onclick="return confirm('Are you sure you want to permanently delete this officer profile?');">
                                            <span class="material-symbols-outlined">delete</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="user-modal-overlay" id="officerModalOverlay">
        <div class="user-modal-card">
            <div class="user-modal-header">
                <h3 id="modalDynamicTitle">Add New Organizational Officer</h3>
                <button type="button" class="user-modal-close-btn" id="modalCloseOfficerBtn" title="Close Modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="user-modal-form" id="officerFormEngine">
                <input type="hidden" name="action" value="update_officers_cms">
                <input type="hidden" name="form_mode" id="formModeControl" value="create">
                <input type="hidden" name="officer_id" id="formOfficerIdControl" value="">
                <input type="hidden" name="existing_photo" id="formExistingPhotoControl" value="">

                <div class="form-row-2col">
                    <div class="form-group">
                        <label for="officer_firstname">First Name</label>
                        <input type="text" id="officer_firstname" name="officer_firstname" required placeholder="John">
                    </div>

                    <div class="form-group">
                        <label for="officer_lastname">Last Name</label>
                        <input type="text" id="officer_lastname" name="officer_lastname" required placeholder="Doe">
                    </div>
                </div>

                <div class="form-group">
                    <label for="officer_middleinitial">Middle Initial</label>
                    <input type="text" id="officer_middleinitial" name="officer_middleinitial" placeholder="e.g. A.">
                </div>

                <div class="form-row-2col">
                    <div class="form-group">
                        <label for="officer_position">Position / Rank Title</label>
                        <input type="text" id="officer_position" name="officer_position" required placeholder="e.g. Managing Director">
                    </div>

                    <div class="form-group">
                        <label for="officer_category">Assigned Department Board</label>
                        <select id="officer_category" name="officer_category" required>
                            <option value="bot">Board of Trustees</option>
                            <option value="ofad">Officers for Administration</option>
                            <option value="tit">Training and Instructions Team</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="officer_photo">Profile Photo Portrait</label>
                    <input type="file" id="officer_photo" name="officer_photo" accept="image/*" style="padding: 7px 14px;">
                    <small id="photoChangeNotice" style="color: #64748b; font-size: 0.75rem; margin-top: 5px; display: none;">Leave blank to preserve existing profile file portrait.</small>
                </div>

                <div class="user-modal-footer">
                    <button type="button" class="modal-cancel-btn" id="modalCancelOfficerBtn">Cancel</button>
                    <button type="submit" class="modal-submit-btn" id="modalSubmitBtnText">Create Profile</button>
                </div>
            </form>
        </div>
    </div>
</section>

<style>
    /* Framework Design Engine Integration UI Styling */
    .user-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 9999; opacity: 0; pointer-events: none; transition: opacity 0.25s ease; }
    .user-modal-overlay.active { opacity: 1; pointer-events: auto; }
    
    .user-modal-card { background-color: #ffffff; width: 100%; max-width: 580px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid #e2e8f0; overflow: hidden; transform: scale(0.95); transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
    .user-modal-overlay.active .user-modal-card { transform: scale(1); }
    
    .user-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; border-bottom: 1px solid #f1f5f9; background-color: #f8fafc; }
    .user-modal-header h3 { margin: 0; font-size: 1.15rem; color: #0f172a; font-weight: 600; }
    
    .user-modal-close-btn { background: none; border: none; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 4px; border-radius: 6px; transition: background-color 0.2s, color 0.2s; }
    .user-modal-close-btn:hover { background-color: #edf2f7; color: #334155; }
    
    .user-modal-form { padding: 24px; max-height: calc(100vh - 120px); overflow-y: auto; }
    .form-row-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .user-modal-form .form-group { margin-bottom: 16px; display: flex; flex-direction: column; }
    .user-modal-form label { font-size: 0.85rem; font-weight: 500; color: #344054; margin-bottom: 6px; }
    
    .user-modal-form input[type="text"], .user-modal-form input[type="file"], .user-modal-form select { padding: 10px 14px; border: 1px solid #d0d5dd; border-radius: 8px; font-size: 0.95rem; color: #101828; background-color: #ffffff; transition: border-color 0.15s, box-shadow 0.15s; width: 100%; box-sizing: border-box; }
    .user-modal-form input:focus, .user-modal-form select:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    
    /* Dedicated Actions Interface System Layout */
    .actions-wrapper-cell { display: flex; items-items: center; justify-content: center; gap: 8px; }
    
    .action-ui-btn { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease; text-decoration: none; color: #64748b; }
    .action-ui-btn .material-symbols-outlined { font-size: 1.2rem; }
    
    .action-ui-btn.edit-trigger-btn:hover { border-color: #3b82f6; color: #2563eb; background-color: #eff6ff; }
    .action-ui-btn.delete-trigger-btn:hover { border-color: #fca5a5; color: #dc2626; background-color: #fef2f2; }
    
    .user-modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #f1f5f9; }
    .modal-cancel-btn { font-family: system-ui, -apple-system, sans-serif; padding: 10px 18px; border: 1px solid #d0d5dd; background-color: #ffffff; color: #344054; border-radius: 8px; font-size: 0.95rem; font-weight: 500; cursor: pointer; transition: background-color 0.15s; }
    .modal-cancel-btn:hover { background-color: #f9fafb; }
    
    .modal-submit-btn { font-family: system-ui, -apple-system, sans-serif; padding: 10px 18px; border: 1px solid #4f46e5; background-color: #4f46e5; color: #ffffff; border-radius: 8px; font-size: 0.95rem; font-weight: 500; cursor: pointer; transition: background-color 0.15s, border-color 0.15s; }
    .modal-submit-btn:hover { background-color: #4338ca; border-color: #4338ca; }
    
    @media (max-width: 600px) { .form-row-2col { grid-template-columns: 1fr; gap: 0; } .user-modal-card { width: 92%; } }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const overlay = document.getElementById('officerModalOverlay');
        const openBtn = document.getElementById('modalOpenOfficerBtn');
        const closeBtn = document.getElementById('modalCloseOfficerBtn');
        const cancelBtn = document.getElementById('modalCancelOfficerBtn');
        
        // Dom Form Control Form elements
        const formEngine = document.getElementById('officerFormEngine');
        const modalTitle = document.getElementById('modalDynamicTitle');
        const modalSubmitBtn = document.getElementById('modalSubmitBtnText');
        const modeInput = document.getElementById('formModeControl');
        const idInput = document.getElementById('formOfficerIdControl');
        const existingPhotoInput = document.getElementById('formExistingPhotoControl');
        const photoNotice = document.getElementById('photoChangeNotice');
        const categorySelect = document.getElementById('officer_category');

        // Setup clear form state for adding new profiles
        const resetToCreateMode = () => {
            formEngine.reset();
            modalTitle.innerText = "Add New Organizational Officer";
            modalSubmitBtn.innerText = "Create Profile";
            modeInput.value = "create";
            idInput.value = "";
            existingPhotoInput.value = "";
            categorySelect.disabled = false; // Category editable when creating new records
            photoNotice.style.display = "none";
        };

        if (openBtn) openBtn.addEventListener('click', () => {
            resetToCreateMode();
            overlay.classList.add('active');
        });

        if (closeBtn) closeBtn.addEventListener('click', () => overlay.classList.remove('active'));
        if (cancelBtn) cancelBtn.addEventListener('click', () => overlay.classList.remove('active'));
        
        // Dynamic event registration intercepting table line context data
        document.querySelectorAll('.edit-trigger-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                modalTitle.innerText = "Modify Officer Configuration Profile";
                modalSubmitBtn.innerText = "Save Adjustments";
                modeInput.value = "edit";
                
                // Populate core structural keys
                idInput.value = btn.getAttribute('data-id');
                document.getElementById('officer_firstname').value = btn.getAttribute('data-fname');
                document.getElementById('officer_middleinitial').value = btn.getAttribute('data-mname');
                document.getElementById('officer_lastname').value = btn.getAttribute('data-lname');
                document.getElementById('officer_position').value = btn.getAttribute('data-position');
                existingPhotoInput.value = btn.getAttribute('data-photo');
                
                // Match select element options values
                categorySelect.value = btn.getAttribute('data-category');
                categorySelect.disabled = true; // Lock category from changes to preserve database routing integrity

                photoNotice.style.display = "block";
                overlay.classList.add('active');
            });
        });

        // intercept submit to re-enable select control so values get processed by PHP pipeline
        formEngine.addEventListener('submit', () => { categorySelect.disabled = false; });

        overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.classList.remove('active'); });
    });
</script>
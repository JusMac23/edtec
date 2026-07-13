<section id="system-users" class="dashboard-section">

    <div class="section-header">
        <h2>Registered System Users Directory</h2>
        <button class="icon-btn action-accent-btn" id="modalOpenUserBtn" aria-label="Add User">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="action-icon">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
            <span class="text-btn">Add User</span>
        </button>
    </div>

    <div class="cms-table-card">
        <div class="cms-card-heading">Security Accounts Syncing Logs</div>
        <div class="table-responsive-wrapper">
            <table class="cms-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Role</th>
                        <th>Timestamp Logged</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users_list) || !is_array($users_list)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94a3b8; padding: 30px;">
                                No profile indexes populated matching framework standard variables.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users_list as $userProfileItem): ?>
                            <tr>
                                <td style="font-family: monospace; color: #64748b; font-weight: 600;">#<?= (int)$userProfileItem['id'] ?></td>
                                <td style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars(($userProfileItem['first_name'] ?? '') . ' ' . ($userProfileItem['last_name'] ?? '')) ?></td>
                                <td style="color: #475569;"><?= htmlspecialchars($userProfileItem['email'] ?? '') ?></td>
                                <td>
                                    <span class="badge badge-<?= (($userProfileItem['role'] ?? '') === 'admin') ? 'admin' : 'user' ?>">
                                        <?= htmlspecialchars($userProfileItem['role'] ?? 'user') ?>
                                    </span>
                                </td>
                                <td style="color: #64748b; font-size: 0.85rem;"><?= htmlspecialchars($userProfileItem['created_at'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="user-modal-overlay" id="userModalOverlay">
        <div class="user-modal-card">
            <div class="user-modal-header">
                <h3>Add New System User</h3>
                <button type="button" class="user-modal-close-btn" id="modalCloseUserBtn" title="Close Modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form action="dashboard.php" method="POST" class="user-modal-form">
                <input type="hidden" name="action" value="register_user_cms">

                <?php if (!empty($error)): ?>
                    <div class="modal-alert-box error-alert">
                        ⚠️ <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="modal-alert-box success-alert">
                        ✓ <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <div class="form-row-2col">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required 
                               value="<?= isset($_POST['first_name']) && empty($success) ? htmlspecialchars($_POST['first_name']) : '' ?>" 
                               placeholder="John" autocomplete="given-name">
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required 
                               value="<?= isset($_POST['last_name']) && empty($success) ? htmlspecialchars($_POST['last_name']) : '' ?>" 
                               placeholder="Doe" autocomplete="family-name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= isset($_POST['email']) && empty($success) ? htmlspecialchars($_POST['email']) : '' ?>" 
                           placeholder="you@example.com" autocomplete="email">
                </div>

                <div class="form-row-2col">
                    <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="tel" id="contact_number" name="contact_number" required 
                               value="<?= isset($_POST['contact_number']) && empty($success) ? htmlspecialchars($_POST['contact_number']) : '' ?>" 
                               placeholder="e.g., 09123456789" autocomplete="tel">
                    </div>

                    <div class="form-group">
                        <label for="role">User Authority Level / Role</label>
                        <?php if (isset($adminExists) && $adminExists): ?>
                            <input type="text" value="Subscriber" disabled class="disabled-input-look">
                            <input type="hidden" name="role" value="subscriber">
                        <?php else: ?>
                            <select id="role" name="role" required>
                                <option value="subscriber" <?= (isset($_POST['role']) && $_POST['role'] === 'subscriber') ? 'selected' : '' ?>>Subscriber</option>
                                <option value="admin" <?= (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : '' ?>>Administrator</option>
                            </select>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Home Address</label>
                    <input type="text" id="address" name="address" required 
                           value="<?= isset($_POST['address']) && empty($success) ? htmlspecialchars($_POST['address']) : '' ?>" 
                           placeholder="Street, City, Province" autocomplete="street-address">
                </div>

                <div class="form-row-2col">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required 
                               placeholder="Minimum 8 characters" autocomplete="new-password">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               placeholder="Repeat password" autocomplete="new-password">
                    </div>
                </div>

                <div class="user-modal-footer">
                    <button type="button" class="modal-cancel-btn" id="modalCancelUserBtn">Cancel</button>
                    <button type="submit" class="modal-submit-btn">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</section>

<style>
    /* User Modal Overlay Styling */
    .user-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 9999; opacity: 0; pointer-events: none; transition: opacity 0.25s ease; }
    .user-modal-overlay.active { opacity: 1; pointer-events: auto; }

    /* User Modal Card */
    .user-modal-card { background-color: #ffffff; width: 100%; max-width: 580px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid #e2e8f0; overflow: hidden; transform: scale(0.95); transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
    .user-modal-overlay.active .user-modal-card { transform: scale(1); }

    /* Modal Header */
    .user-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; border-bottom: 1px solid #f1f5f9; background-color: #f8fafc; }
    .user-modal-header h3 { margin: 0; font-size: 1.15rem; color: #0f172a; font-weight: 600; }
    .user-modal-close-btn { background: none; border: none; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 4px; border-radius: 6px; transition: background-color 0.2s, color 0.2s; }
    .user-modal-close-btn:hover { background-color: #edf2f7; color: #334155; }

    /* Modal Form Core Components */
    .user-modal-form { padding: 24px; max-height: calc(100vh - 120px); overflow-y: auto; }
    .form-row-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .user-modal-form .form-group { margin-bottom: 16px; display: flex; flex-direction: column; }
    .user-modal-form label { font-size: 0.85rem; font-weight: 500; color: #344054; margin-bottom: 6px; }
    .user-modal-form input[type="text"], .user-modal-form input[type="email"], .user-modal-form input[type="tel"], .user-modal-form input[type="password"], .user-modal-form select { padding: 10px 14px; border: 1px solid #d0d5dd; border-radius: 8px; font-size: 0.95rem; color: #101828; background-color: #ffffff; transition: border-color 0.15s, box-shadow 0.15s; width: 100%; box-sizing: border-box; }
    .user-modal-form input:focus, .user-modal-form select:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    .disabled-input-look { background-color: #f4f4f4 !important; color: #71717a !important; cursor: not-allowed; }

    /* Status Alert System */
    .modal-alert-box { padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; margin-bottom: 20px; font-weight: 500; }
    .error-alert { background-color: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; }
    .success-alert { background-color: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }

    /* Modal Footer & Actions */
    .user-modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #f1f5f9; }
    .modal-cancel-btn { font-family: 'Poppins', system-ui, -apple-system, sans-serif; padding: 10px 18px; border: 1px solid #d0d5dd; background-color: #ffffff; color: #344054; border-radius: 8px; font-size: 0.95rem; font-weight: 500; cursor: pointer; transition: background-color 0.15s; }
    .modal-cancel-btn:hover { background-color: #f9fafb; }
    .modal-submit-btn { font-family: 'Poppins', system-ui, -apple-system, sans-serif; padding: 10px 18px; border: 1px solid #4f46e5; background-color: #4f46e5; color: #ffffff; border-radius: 8px; font-size: 0.95rem; font-weight: 500; cursor: pointer; transition: background-color 0.15s, border-color 0.15s; }
    .modal-submit-btn:hover { background-color: #4338ca; border-color: #4338ca; }

    /* Responsive Viewport Optimizations */
    @media (max-width: 600px) { .form-row-2col { grid-template-columns: 1fr; gap: 0; } .user-modal-card { width: 92%; } }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const userOverlay = document.getElementById('userModalOverlay');
        const openUserBtn = document.getElementById('modalOpenUserBtn');
        const closeUserBtn = document.getElementById('modalCloseUserBtn');
        const cancelUserBtn = document.getElementById('modalCancelUserBtn');

        const openUserModal = () => {
            if (userOverlay) userOverlay.classList.add('active');
        };

        const closeUserModal = () => {
            if (userOverlay) userOverlay.classList.remove('active');
        };

        if (userOverlay) {
            if (openUserBtn) openUserBtn.addEventListener('click', openUserModal);
            if (closeUserBtn) closeUserBtn.addEventListener('click', closeUserModal);
            if (cancelUserBtn) cancelUserBtn.addEventListener('click', closeUserModal);

            // Close modal when background backdrop is clicked
            userOverlay.addEventListener('click', (e) => {
                if (e.target === userOverlay) closeUserModal();
            });
        }

        // Auto-reopen modal if validation errors occur during user creation
        <?php if (!empty($error) && isset($_POST['action']) && $_POST['action'] === 'register_user_cms'): ?>
            openUserModal();
        <?php endif; ?>
    });
</script>
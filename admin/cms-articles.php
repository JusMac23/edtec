<section id="manage-articles" class="dashboard-section active">

    <div class="section-header">
        <h2>Manage Articles Content</h2>
        <button class="icon-btn action-accent-btn" id="modalOpenBtn" aria-label="Create New Post">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="action-icon">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            <span class="text-btn">Create New Article</span>
        </button>
    </div>
    
    <div class="cms-table-card">
        <div class="table-responsive-wrapper">
            <?php if (!empty($posts) && is_array($posts)): ?>
                <table class="cms-table">
                    <thead>
                        <tr>
                            <th>Article Identifier / Title</th>
                            <th>Author Identity</th>
                            <th>Publication Date & Time</th>
                            <th>Status State</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td>
                                    <strong style="color: #0f172a; font-weight: 600;"><?= htmlspecialchars($post['title'] ?? 'Untitled Link') ?></strong>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">Slug: <?= htmlspecialchars($post['slug'] ?? '') ?></div>
                                </td>
                                <td><?= htmlspecialchars(trim(($post['first_name'] ?? '') . ' ' . ($post['last_name'] ?? ''))) ?: 'Unknown System Author' ?></td>
                                <td style="color: #475569;"><?= !empty($post['published_at']) ? date('M d, Y | h:i A', strtotime($post['published_at'])) : 'Pending Timestamp' ?></td>
                                <td><span class="status-pill"><?= htmlspecialchars($post['status'] ?? 'Draft') ?></span></td>
                                <td>
                                    <div class="action-container" style="justify-content: center;">
                                        <a href="dashboard.php?edit_id=<?= (int)$post['id'] ?>" class="action-ui-btn edit-trigger-btn" title="Edit">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                        </a>
                                        <form action="dashboard.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently erase this article? This action cannot be undone.');">
                                            <input type="hidden" name="action" value="delete_post">
                                            <input type="hidden" name="post_id" value="<?= (int)($post['id'] ?? 0) ?>">
                                            <button type="submit" class="action-ui-btn delete-trigger-btn" title="Delete">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; color: #64748b;">
                    <span class="material-symbols-outlined" style="font-size: 48px; color: #cbd5e1; margin-bottom: 8px;">auto_stories</span>
                    <h3 style="margin: 0; font-size: 1rem; color: #475569;">No entries discovered inside core databases context.</h3>
                    <p style="font-size: 0.85rem; margin: 4px 0 0 0; color: #94a3b8;">Click the button above to begin inserting data structures layout content lines.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="article-modal-overlay <?= (!empty($modalError) || $isEditMode) ? 'active' : '' ?>" id="postModalOverlay">
        <div class="article-modal-card">
            <div class="article-modal-header">
                <h3><?= $isEditMode ? 'Modify Technical Article' : 'Create New Article' ?></h3>
                <button type="button" class="article-modal-close-btn" id="modalCloseBtn" aria-label="Close Modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <form action="dashboard.php" method="POST" class="article-modal-form" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?= $isEditMode ? 'edit_post' : 'create_post' ?>">
                <?php if ($isEditMode): ?>
                    <input type="hidden" name="post_id" value="<?= (int)$editPostData['id'] ?>">
                <?php endif; ?>

                <?php if (!empty($modalError)): ?>
                    <div class="modal-alert-box error-alert">
                        ⚠️ <?= htmlspecialchars($modalError) ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="postTitle">Article Title *</label>
                    <input type="text" id="postTitle" name="title" required placeholder="e.g., UI Structural Design Trends" value="<?= htmlspecialchars($isEditMode ? ($editPostData['title'] ?? '') : ($_POST['title'] ?? '')) ?>">
                </div>

                <div class="form-group">
                    <label>Featured Asset Image Target <?= $isEditMode ? '(Optional — Leave blank to retain existing asset)' : '*' ?></label>
                    
                    <div class="upload-tabs">
                        <button type="button" class="tab-btn active" data-target="url-mode">Paste Direct Link</button>
                        <button type="button" class="tab-btn" data-target="file-mode">Upload Native File</button>
                    </div>

                    <div class="upload-slot method-url active" id="url-mode">
                        <input type="text" id="postImageURL" name="featured_image" <?= !$isEditMode ? 'required' : '' ?> placeholder="https://example.com/assets/image.jpg" value="<?= htmlspecialchars($isEditMode ? ($editPostData['featured_image'] ?? '') : ($_POST['featured_image'] ?? '')) ?>">
                    </div>

                    <div class="upload-slot method-file" id="file-mode">
                        <label for="postImageFile" class="dropzone-area">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="dropzone-icon">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span class="dropzone-text">Click to browse storage system assets</span>
                            <span class="dropzone-hint">Supports production ready PNG, JPG, JPEG, or WebP layouts</span>
                            <input type="file" id="postImageFile" name="featured_image_file" accept="image/*">
                        </label>
                        <div id="file-selected-name" class="file-name-indicator"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="postExcerpt">Brief Summary Narrative</label>
                    <input type="text" id="postExcerpt" name="excerpt" placeholder="Provide a short baseline meta overview description of this item..." value="<?= htmlspecialchars($isEditMode ? ($editPostData['excerpt'] ?? '') : ($_POST['excerpt'] ?? '')) ?>">
                </div>

                <div class="form-group">
                    <label for="postContent">Article Body Markdown Content *</label>
                    <textarea id="postContent" name="content" rows="6" required placeholder="Write your technical configuration markdown text blocks or system logs here..."><?= htmlspecialchars($isEditMode ? ($editPostData['content'] ?? '') : ($_POST['content'] ?? '')) ?></textarea>
                </div>

                <div class="article-modal-footer">
                    <button type="button" class="modal-cancel-btn" id="modalCancelBtn">Cancel</button>
                    <button type="submit" class="modal-submit-btn"><?= $isEditMode ? 'Save Changes' : 'Publish Article' ?></button>
                </div>
            </form>
        </div>
    </div>

</section>

<style>
    /* Article Modal Overlay Styling */
    .article-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 9999; opacity: 0; pointer-events: none; transition: opacity 0.25s ease; }
    .article-modal-overlay.active { opacity: 1; pointer-events: auto; }

    /* Article Modal Card */
    .article-modal-card { background-color: #ffffff; width: 100%; max-width: 640px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid #e2e8f0; overflow: hidden; transform: scale(0.95); transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
    .article-modal-overlay.active .article-modal-card { transform: scale(1); }

    /* Modal Header */
    .article-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; border-bottom: 1px solid #f1f5f9; background-color: #f8fafc; }
    .article-modal-header h3 { margin: 0; font-size: 1.15rem; color: #0f172a; font-weight: 600; }
    .article-modal-close-btn { background: none; border: none; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 4px; border-radius: 6px; transition: background-color 0.2s, color 0.2s; }
    .article-modal-close-btn:hover { background-color: #edf2f7; color: #334155; }

    /* Modal Form Core Components */
    .article-modal-form { padding: 24px; max-height: calc(100vh - 120px); overflow-y: auto; }
    .article-modal-form .form-group { margin-bottom: 18px; display: flex; flex-direction: column; }
    .article-modal-form label { font-size: 0.85rem; font-weight: 500; color: #344054; margin-bottom: 6px; }
    .article-modal-form input[type="text"], .article-modal-form input[type="url"], .article-modal-form textarea, .article-modal-form select { padding: 10px 14px; border: 1px solid #d0d5dd; border-radius: 8px; font-size: 0.95rem; color: #101828; background-color: #ffffff; transition: border-color 0.15s, box-shadow 0.15s; width: 100%; box-sizing: border-box; }
    .article-modal-form input:focus, .article-modal-form textarea:focus, .article-modal-form select:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }

    /* Sub-component Navigation Upload Tabs */
    .upload-tabs { display: flex; gap: 4px; background-color: #f1f5f9; padding: 4px; border-radius: 8px; margin-bottom: 10px; width: fit-content; }
    .upload-tabs .tab-btn { background: none; border: none; padding: 6px 14px; font-size: 0.85rem; font-weight: 500; color: #475569; border-radius: 6px; cursor: pointer; transition: background-color 0.15s, color 0.15s; }
    .upload-tabs .tab-btn.active { background-color: #ffffff; color: #0f172a; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
    .upload-slot { display: none; }
    .upload-slot.active { display: block; }

    /* Drag & Drop Dropzone Engine */
    .dropzone-area { border: 2px dashed #cbd5e1; border-radius: 8px; padding: 24px; text-align: center; cursor: pointer; background-color: #f8fafc; transition: background-color 0.15s, border-color 0.15s; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .dropzone-area:hover { background-color: #f1f5f9; border-color: #94a3b8; }
    .dropzone-icon { width: 32px; height: 32px; color: #64748b; margin-bottom: 8px; }
    .dropzone-text { font-size: 0.9rem; color: #334155; font-weight: 500; }
    .dropzone-hint { font-size: 0.75rem; color: #64748b; margin-top: 2px; }
    .dropzone-area input[type="file"] { display: none; }
    .file-name-indicator { font-size: 0.85rem; color: #166534; font-weight: 500; margin-top: 6px; }

    /* Status Alert System */
    .modal-alert-box { padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; margin: 0 0 20px 0; font-weight: 500; }
    .error-alert { background-color: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; }

    /* Modal Footer & Actions */
    .article-modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #f1f5f9; }
    .modal-cancel-btn { font-family: 'Poppins', system-ui, -apple-system, sans-serif; padding: 10px 18px; border: 1px solid #d0d5dd; background-color: #ffffff; color: #344054; border-radius: 8px; font-size: 0.95rem; font-weight: 500; cursor: pointer; transition: background-color 0.15s; }
    .modal-cancel-btn:hover { background-color: #f9fafb; }
    .modal-submit-btn { font-family: 'Poppins', system-ui, -apple-system, sans-serif; padding: 10px 18px; border: 1px solid #4f46e5; background-color: #4f46e5; color: #ffffff; border-radius: 8px; font-size: 0.95rem; font-weight: 500; cursor: pointer; transition: background-color 0.15s, border-color 0.15s; }
    .modal-submit-btn:hover { background-color: #4338ca; border-color: #4338ca; }

    /* Responsive Viewport Optimizations */
    @media (max-width: 680px) { .article-modal-card { width: 92%; } }
</style>
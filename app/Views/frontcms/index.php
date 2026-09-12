<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row fade-in">
        <div class="col-md-12 mb-4">
             <h3><i class="fa fa-desktop me-2"></i> Front CMS Settings</h3>
        </div>
        
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="mb-0 text-primary">Website Configuration</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="is_active_website" value="yes" <?php echo ($data['settings']->is_active_website == 'yes') ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold" for="flexSwitchCheckChecked">Enable Public Website</label>
                            <div class="form-text">If disabled, the public landing page will show "Under Maintenance".</div>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="onlineAdmissionSwitch" name="enable_online_admission" value="yes" <?php echo (($data['settings']->enable_online_admission ?? 'yes') == 'yes') ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold" for="onlineAdmissionSwitch">Enable Online Admission</label>
                            <div class="form-text">When disabled, the public online admission page is hidden and inaccessible.</div>
                        </div>

                        <input type="hidden" name="has_cms_feature_toggles" value="1">

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="alumniSwitch" name="enable_alumni" value="yes" <?php echo (($data['settings']->enable_alumni ?? 'yes') == 'yes') ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold" for="alumniSwitch">Enable Alumni Network &amp; Directory</label>
                            <div class="form-text">Controls public /home/alumni page and top menu link. Manage profiles at <a href="<?php echo URLROOT; ?>/frontcms/alumni" class="fw-semibold text-primary">Alumni CMS &rarr;</a></div>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="requirementsSwitch" name="enable_requirements" value="yes" <?php echo (($data['settings']->enable_requirements ?? 'yes') == 'yes') ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold" for="requirementsSwitch">Enable School Requirements &amp; Careers Portal</label>
                            <div class="form-text">Controls public /home/requirements page and menu link. Manage requirements at <a href="<?php echo URLROOT; ?>/frontcms/requirements" class="fw-semibold text-primary">Requirements CMS &rarr;</a></div>
                        </div>

                        <h6 class="text-secondary border-bottom pb-2 mb-3">Theme & Layout</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Color Theme</label>
                                <select name="theme_color" class="form-select">
                                    <option value="default" <?php echo ($data['settings']->theme_color == 'default') ? 'selected' : ''; ?>>Default Blue</option>
                                    <option value="red" <?php echo ($data['settings']->theme_color == 'red') ? 'selected' : ''; ?>>Red</option>
                                    <option value="green" <?php echo ($data['settings']->theme_color == 'green') ? 'selected' : ''; ?>>Green</option>
                                    <option value="dark" <?php echo ($data['settings']->theme_color == 'dark') ? 'selected' : ''; ?>>Dark Mode</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Layout Style</label>
                                <select name="layout_type" class="form-select">
                                    <option value="standard" <?php echo ($data['settings']->layout_type == 'standard') ? 'selected' : ''; ?>>Standard</option>
                                    <option value="modern" <?php echo ($data['settings']->layout_type == 'modern') ? 'selected' : ''; ?>>Modern</option>
                                    <option value="creative" <?php echo ($data['settings']->layout_type == 'creative') ? 'selected' : ''; ?>>Creative</option>
                                </select>
                            </div>
                        </div>

                        <h6 class="text-secondary border-bottom pb-2 mb-3">Branding</h6>
                        <div class="mb-3">
                            <label class="form-label">Front Logo</label>
                            <input type="file" name="logo" class="form-control">
                            <?php if(!empty($data['settings']->logo)): ?>
                                <img src="<?php echo URLROOT . '/' . $data['settings']->logo; ?>" height="50" class="mt-2 text-end">
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Footer Text</label>
                            <textarea name="footer_text" class="form-control" rows="2"><?php echo $data['settings']->footer_text; ?></textarea>
                        </div>

                        <h6 class="text-secondary border-bottom pb-2 mb-3 mt-4">Maintenance Page</h6>
                        <div class="mb-3">
                            <label class="form-label">Maintenance Title</label>
                            <input type="text" name="maintenance_title" class="form-control" value="<?php echo htmlspecialchars($data['settings']->maintenance_title ?? 'Site Maintenance in Progress', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Site Maintenance in Progress">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Maintenance Message</label>
                            <textarea name="maintenance_message" class="form-control" rows="3" placeholder="We are improving the website and will be back soon."><?php echo htmlspecialchars($data['settings']->maintenance_message ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estimated Return Time</label>
                            <input type="text" name="maintenance_eta" class="form-control" value="<?php echo htmlspecialchars($data['settings']->maintenance_eta ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Today 6:00 PM or 14 Mar 2026, 10:00 AM">
                            <div class="form-text">Shown on maintenance page as expected return time.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Maintenance Background Image</label>
                            <input type="file" name="maintenance_background" class="form-control">
                            <?php if(!empty($data['settings']->maintenance_background)): ?>
                                <div class="mt-2">
                                    <img src="<?php echo URLROOT . '/' . $data['settings']->maintenance_background; ?>" alt="Maintenance background" style="max-width: 220px; border-radius: 8px; border: 1px solid #ddd;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <h6 class="text-secondary border-bottom pb-2 mb-3 mt-4">Social Media Links</h6>
                        <div class="row g-3">
                             <div class="col-md-6">
                                <label class="form-label"><i class="fab fa-facebook text-primary"></i> Facebook URL</label>
                                <input type="text" name="facebook_url" class="form-control" value="<?php echo $data['settings']->facebook_url; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fab fa-twitter text-info"></i> Twitter URL</label>
                                <input type="text" name="twitter_url" class="form-control" value="<?php echo $data['settings']->twitter_url; ?>">
                            </div>
                             <div class="col-md-6">
                                <label class="form-label"><i class="fab fa-youtube text-danger"></i> Youtube URL</label>
                                <input type="text" name="youtube_url" class="form-control" value="<?php echo $data['settings']->youtube_url; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fab fa-instagram text-danger"></i> Instagram URL</label>
                                <input type="text" name="instagram_url" class="form-control" value="<?php echo $data['settings']->instagram_url; ?>">
                            </div>
                             <div class="col-md-6">
                                <label class="form-label"><i class="fab fa-linkedin text-primary"></i> Linkedin URL</label>
                                <input type="text" name="linkedin_url" class="form-control" value="<?php echo $data['settings']->linkedin_url; ?>">
                            </div>
                             <div class="col-md-6">
                                <label class="form-label"><i class="fab fa-google-plus text-danger"></i> Google Plus URL</label>
                                <input type="text" name="google_plus_url" class="form-control" value="<?php echo $data['settings']->google_plus_url; ?>">
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary shadow-sm px-4">Save Configuration</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>

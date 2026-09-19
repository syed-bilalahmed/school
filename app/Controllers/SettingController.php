<?php
class SettingController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        AuthGuard::requirePermission('manage_settings');
        // CSRF protection for all state-changing POST operations
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthGuard::verifyCSRF();
        }
    }

    public function index(){
        $settingModel = $this->model('SiteSetting');
        $userModel = $this->model('User');
        $cmsModel = $this->model('FrontCms');
        $activeTab = $_GET['tab'] ?? ($_POST['tab'] ?? 'general');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $postedTab = $_POST['tab'] ?? $activeTab;
            $activeTab = $postedTab;

            if(isset($_POST['general_setting']) || ($postedTab === 'general' && (isset($_POST['school_name']) || isset($_FILES['logo'])))){
                $data = [
                    'school_name' => trim($_POST['school_name'] ?? ''),
                    'campus_name' => trim($_POST['campus_name'] ?? ''),
                    'affiliation_board' => trim($_POST['affiliation_board'] ?? ''),
                    'affiliation_no' => trim($_POST['affiliation_no'] ?? ''),
                    'ntn_number' => trim($_POST['ntn_number'] ?? ''),
                    'school_email' => trim($_POST['school_email'] ?? ''),
                    'school_phone' => trim($_POST['school_phone'] ?? ''),
                    'school_address' => trim($_POST['school_address'] ?? ''),
                    'currency_symbol' => trim($_POST['currency_symbol'] ?? 'PKR'),
                    'session_id' => $_POST['session_id'] ?? null,
                    'bank_name' => trim($_POST['bank_name'] ?? ''),
                    'bank_account_title' => trim($_POST['bank_account_title'] ?? ''),
                    'bank_account_no' => trim($_POST['bank_account_no'] ?? ''),
                    'bank_iban' => trim($_POST['bank_iban'] ?? ''),
                    'bank_branch' => trim($_POST['bank_branch'] ?? ''),
                    'bank_instructions' => trim($_POST['bank_instructions'] ?? '')
                ];
                
                if(isset($_FILES['logo']) && !empty($_FILES['logo']['name'])){
                    if($_FILES['logo']['error'] === UPLOAD_ERR_OK){
                        require_once APPROOT . '/Core/UploadHandler.php';
                        $upload = UploadHandler::processUpload($_FILES['logo'], 'settings');
                        if($upload['success']){
                            $data['logo'] = $upload['path'];
                            $uploadedLogoPath = $upload['path'];
                        } else {
                            $uploadError = $upload['error'] ?? 'Logo upload failed.';
                        }
                    } elseif($_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
                        $uploadError = 'File upload failed (error code: ' . $_FILES['logo']['error'] . '). File may exceed server upload limit.';
                    }
                }
                
                if (!empty($uploadError)) {
                    $_SESSION['flash_error'] = $uploadError;
                } else {
                    $settingModel->updateSettings($data);
                    if (!empty($data['logo'])) {
                        $cmsModel->updateLogo($data['logo']);
                    }
                    $_SESSION['currency_symbol'] = $data['currency_symbol'];
                    $_SESSION['school_name'] = $data['school_name'];
                    $_SESSION['campus_name'] = $data['campus_name'];
                    if (!empty($data['logo'])) {
                        $_SESSION['school_logo'] = $data['logo'];
                    }
                    $_SESSION['flash_success'] = 'Institutional profile, campus branding, and bank particulars updated successfully.';
                }
                $activeTab = 'general';

            } elseif(isset($_POST['prefixes_setting']) || ($postedTab === 'prefixes' && isset($_POST['prefix_student']))){
                $data = [
                    'prefix_student' => strtoupper(trim($_POST['prefix_student'] ?? 'ADM-')),
                    'prefix_staff' => strtoupper(trim($_POST['prefix_staff'] ?? 'EMP-')),
                    'prefix_teacher' => strtoupper(trim($_POST['prefix_teacher'] ?? 'TCH-')),
                    'prefix_family' => strtoupper(trim($_POST['prefix_family'] ?? 'FAM-')),
                    'prefix_challan' => strtoupper(trim($_POST['prefix_challan'] ?? 'CHL-')),
                    'prefix_clearance' => strtoupper(trim($_POST['prefix_clearance'] ?? 'CLR-')),
                    'prefix_visitor' => strtoupper(trim($_POST['prefix_visitor'] ?? 'VIS-')),
                    'prefix_gatepass' => strtoupper(trim($_POST['prefix_gatepass'] ?? 'GP-'))
                ];

                $settingModel->updateSettings($data);
                $_SESSION['flash_success'] = 'System numbering & code prefixes saved successfully.';
                $activeTab = 'prefixes';

            } elseif(isset($_POST['features_setting']) || ($postedTab === 'features' && (isset($_POST['payroll_leave_cutting_enabled']) || isset($_POST['toggle_require_bform']) || isset($_POST['features_flag'])))){
                $data = [
                    'toggle_require_bform' => isset($_POST['toggle_require_bform']) ? '1' : '0',
                    'toggle_require_father_cnic' => isset($_POST['toggle_require_father_cnic']) ? '1' : '0',
                    'toggle_sibling_discount' => isset($_POST['toggle_sibling_discount']) ? '1' : '0',
                    'toggle_daily_attendance_sms' => isset($_POST['toggle_daily_attendance_sms']) ? '1' : '0',
                    'toggle_fee_accounting_sync' => isset($_POST['toggle_fee_accounting_sync']) ? '1' : '0',
                    'toggle_strict_clearance_slc' => isset($_POST['toggle_strict_clearance_slc']) ? '1' : '0',
                    'payroll_leave_cutting_enabled' => isset($_POST['payroll_leave_cutting_enabled']) ? '1' : '0',
                    'payroll_free_leaves_per_month' => (string)max(0, (int)($_POST['payroll_free_leaves_per_month'] ?? 2)),
                    'payroll_absent_cutting_percent' => (string)max(0, min(100, (float)($_POST['payroll_absent_cutting_percent'] ?? 100))),
                    'payroll_half_day_cutting_percent' => (string)max(0, min(100, (float)($_POST['payroll_half_day_cutting_percent'] ?? 50)))
                ];

                $settingModel->updateSettings($data);
                $_SESSION['flash_success'] = 'Student profile, payroll leave deduction policy, and system enforcement switches updated.';
                $activeTab = 'features';

            } elseif(isset($_POST['website_setting']) || ($postedTab === 'website' && (isset($_POST['footer_text']) || isset($_POST['theme_color']) || isset($_POST['hero_title']) || isset($_POST['emergency_alert_text'])))){
                $cmsData = [
                    'is_active_website' => isset($_POST['is_active_website']) ? 'yes' : 'no',
                    'enable_online_admission' => isset($_POST['enable_online_admission']) ? 'yes' : 'no',
                    'enable_fee_structure' => isset($_POST['enable_fee_structure']) ? 'yes' : 'no',
                    'enable_emergency_alert' => isset($_POST['enable_emergency_alert']) ? 'yes' : 'no',
                    'emergency_alert_text' => trim($_POST['emergency_alert_text'] ?? ''),
                    'emergency_alert_bg' => trim($_POST['emergency_alert_bg'] ?? 'danger'),
                    'emergency_alert_link' => trim($_POST['emergency_alert_link'] ?? ''),
                    'footer_text' => trim($_POST['footer_text'] ?? ''),
                    'facebook_url' => trim($_POST['facebook_url'] ?? ''),
                    'twitter_url' => trim($_POST['twitter_url'] ?? ''),
                    'instagram_url' => trim($_POST['instagram_url'] ?? ''),
                    'youtube_url' => trim($_POST['youtube_url'] ?? ''),
                    'linkedin_url' => trim($_POST['linkedin_url'] ?? ''),
                    'theme_color' => trim($_POST['theme_color'] ?? 'default'),
                    'layout_type' => trim($_POST['layout_type'] ?? 'full')
                ];
                $cmsModel->updateSettings($cmsData);

                // Update SiteSettings branding & Hero Section content
                $siteSettingsData = [
                    'school_email' => trim($_POST['school_email'] ?? ''),
                    'school_phone' => trim($_POST['school_phone'] ?? ''),
                    'school_address' => trim($_POST['school_address'] ?? ''),
                    'hero_badge' => trim($_POST['hero_badge'] ?? ''),
                    'hero_title' => trim($_POST['hero_title'] ?? ''),
                    'hero_subtitle' => trim($_POST['hero_subtitle'] ?? ''),
                    'hero_cta_text' => trim($_POST['hero_cta_text'] ?? ''),
                    'hero_cta_link' => trim($_POST['hero_cta_link'] ?? ''),
                    'hero_cta_sec_text' => trim($_POST['hero_cta_sec_text'] ?? ''),
                    'hero_cta_sec_link' => trim($_POST['hero_cta_sec_link'] ?? ''),
                    'hero_floating_badge' => trim($_POST['hero_floating_badge'] ?? ''),
                    'hero_floating_sub' => trim($_POST['hero_floating_sub'] ?? ''),
                    'hero_stat_1_val' => trim($_POST['hero_stat_1_val'] ?? ''),
                    'hero_stat_1_lbl' => trim($_POST['hero_stat_1_lbl'] ?? ''),
                    'hero_stat_2_val' => trim($_POST['hero_stat_2_val'] ?? ''),
                    'hero_stat_2_lbl' => trim($_POST['hero_stat_2_lbl'] ?? ''),
                    'hero_stat_3_val' => trim($_POST['hero_stat_3_val'] ?? ''),
                    'hero_stat_3_lbl' => trim($_POST['hero_stat_3_lbl'] ?? ''),
                    'hero_stat_4_val' => trim($_POST['hero_stat_4_val'] ?? ''),
                    'hero_stat_4_lbl' => trim($_POST['hero_stat_4_lbl'] ?? '')
                ];

                $uploadedHeroImagePath = null;
                if(isset($_FILES['hero_image']) && !empty($_FILES['hero_image']['name'])){
                    if($_FILES['hero_image']['error'] === UPLOAD_ERR_OK){
                        require_once APPROOT . '/Core/UploadHandler.php';
                        $upload = UploadHandler::processUpload($_FILES['hero_image'], 'hero');
                        if($upload['success']){
                            $siteSettingsData['hero_image'] = $upload['path'];
                            $uploadedHeroImagePath = URLROOT . '/' . $upload['path'];
                        }
                    }
                }

                $settingModel->updateSettings($siteSettingsData);

                $_SESSION['flash_success'] = 'Public website branding, Hero Section content, and CMS settings saved successfully.';
                $activeTab = 'website';

            } elseif(isset($_POST['email_setting']) || ($postedTab === 'email' && isset($_POST['smtp_host']))){
                $emailData = [
                    'smtp_host' => trim($_POST['smtp_host'] ?? 'smtp.gmail.com'),
                    'smtp_port' => (string)max(1, (int)($_POST['smtp_port'] ?? 587)),
                    'smtp_encryption' => strtolower(trim($_POST['smtp_encryption'] ?? 'tls')),
                    'smtp_username' => trim($_POST['smtp_username'] ?? ''),
                    'smtp_from_email' => trim($_POST['smtp_from_email'] ?? ''),
                    'smtp_from_name' => trim($_POST['smtp_from_name'] ?? (defined('SITENAME') ? SITENAME : 'School ERP')),
                    'smtp_timeout' => (string)max(15, (int)($_POST['smtp_timeout'] ?? 15)),
                    'smtp_enabled' => isset($_POST['smtp_enabled']) ? '1' : '0'
                ];
                if (!empty($_POST['smtp_password'])) {
                    $emailData['smtp_password'] = trim($_POST['smtp_password']);
                }

                $settingModel->updateSettings($emailData);
                $_SESSION['flash_success'] = 'Outgoing SMTP mail server settings saved successfully.';
                $activeTab = 'email';

            } elseif(isset($_POST['test_email_submit']) || ($postedTab === 'email' && isset($_POST['test_recipient_email']))){
                $testRecipient = trim($_POST['test_recipient_email'] ?? '');
                if (empty($testRecipient) || !filter_var($testRecipient, FILTER_VALIDATE_EMAIL)) {
                    $_SESSION['flash_error'] = 'Please enter a valid recipient email address for the test.';
                } else {
                    $override = [];
                    if (!empty($_POST['smtp_host'])) $override['host'] = trim($_POST['smtp_host']);
                    if (!empty($_POST['smtp_port'])) $override['port'] = (int)$_POST['smtp_port'];
                    if (!empty($_POST['smtp_username'])) $override['username'] = trim($_POST['smtp_username']);
                    if (!empty($_POST['smtp_password'])) $override['password'] = trim($_POST['smtp_password']);
                    if (!empty($_POST['smtp_encryption'])) $override['encryption'] = strtolower(trim($_POST['smtp_encryption']));
                    if (!empty($_POST['smtp_from_email'])) $override['from_email'] = trim($_POST['smtp_from_email']);
                    if (!empty($_POST['smtp_from_name'])) $override['from_name'] = trim($_POST['smtp_from_name']);

                    $res = Mailer::testConnection($testRecipient, $override);
                    if ($res['success']) {
                        $_SESSION['flash_success'] = "SMTP connection verified! Test email was successfully delivered to '{$testRecipient}'.";
                    } else {
                        $_SESSION['flash_error'] = "SMTP Test Failed: " . htmlspecialchars($res['message'] ?? 'Could not deliver test email.');
                    }
                }
                $activeTab = 'email';

            } elseif(isset($_POST['livechat_setting']) || ($postedTab === 'livechat' && isset($_POST['whatsapp_number']))){
                $chatData = [
                    'whatsapp_enabled' => isset($_POST['whatsapp_enabled']) ? '1' : '0',
                    'whatsapp_number' => trim($_POST['whatsapp_number'] ?? '+923360606905'),
                    'whatsapp_default_msg' => trim($_POST['whatsapp_default_msg'] ?? ''),
                    'whatsapp_agent_name' => trim($_POST['whatsapp_agent_name'] ?? 'Admissions & Helpdesk'),
                    'whatsapp_popup_enabled' => isset($_POST['whatsapp_popup_enabled']) ? '1' : '0',
                    'livechat_enabled' => '0'
                ];

                $settingModel->updateSettings($chatData);
                $_SESSION['flash_success'] = 'WhatsApp floating helpdesk configuration saved successfully.';
                $activeTab = 'livechat';

            } elseif(isset($_POST['update_role_permissions'])){
                $postedPermissions = $_POST['permissions'] ?? [];
                $settingModel->syncRolePermissions($postedPermissions);

                if (isset($_SESSION['user_id'])) {
                    $_SESSION['user_permissions'] = $userModel->getUserPermissions($_SESSION['user_id']);
                }

                $_SESSION['flash_success'] = 'Role permissions matrix synchronized successfully.';
                $activeTab = 'roles';

            } elseif(isset($_POST['add_custom_role'])){
                $roleName = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', $_POST['role_name'] ?? '')));
                if(!empty($roleName)){
                    if($settingModel->addRole($roleName)){
                        $_SESSION['flash_success'] = "Custom role '{$roleName}' created successfully and added to RBAC matrix.";
                    } else {
                        $_SESSION['flash_error'] = "Role '{$roleName}' already exists or could not be created.";
                    }
                }
                $activeTab = 'roles';

            } elseif(isset($_POST['quick_add_user'])){
                $uName = trim($_POST['user_name'] ?? '');
                $uEmail = trim($_POST['user_email'] ?? '');
                $uRole = trim($_POST['user_role'] ?? 'teacher');
                $uPass = !empty($_POST['user_password']) ? trim($_POST['user_password']) : '123456';
                if(!empty($uName) && !empty($uEmail)){
                    if($userModel->getUserByEmail($uEmail)){
                        $_SESSION['flash_error'] = "User account with email '{$uEmail}' already exists.";
                    } else {
                        if($userModel->register([
                            'name' => $uName,
                            'email' => $uEmail,
                            'password' => password_hash($uPass, PASSWORD_DEFAULT),
                            'role' => $uRole
                        ])){
                            Mailer::send(
                                $uEmail,
                                'Your ' . SITENAME . ' account is ready',
                                '<h2>Account created</h2><p>Hello ' . htmlspecialchars($uName, ENT_QUOTES, 'UTF-8') . ',</p><p><strong>Login:</strong> ' . htmlspecialchars($uEmail, ENT_QUOTES, 'UTF-8') . '<br><strong>Temporary password:</strong> ' . htmlspecialchars($uPass, ENT_QUOTES, 'UTF-8') . '</p><p><a href="' . URLROOT . '/auth/login">Open Login Page</a></p>',
                                "Account created\nLogin: {$uEmail}\nTemporary password: {$uPass}\nLogin URL: " . URLROOT . '/auth/login'
                            );
                            $_SESSION['flash_success'] = "New user '{$uName}' registered successfully as " . ucfirst($uRole) . " (Login: {$uEmail} / {$uPass}).";
                        } else {
                            $_SESSION['flash_error'] = "Failed to register new user.";
                        }
                    }
                }
                $activeTab = 'roles';

            } elseif(isset($_POST['add_session'])){
                $session = trim($_POST['session'] ?? '');
                if(!empty($session)){
                    $settingModel->addSession($session);
                    $_SESSION['flash_success'] = 'New academic session added.';
                }
                $activeTab = 'general';

            } elseif(isset($_POST['delete_session'])){
                $settingModel->deleteSession($_POST['delete_id']);
                $_SESSION['flash_success'] = 'Session deleted.';
                $activeTab = 'general';

            } elseif(isset($_POST['generate_api_key'])){
                $apiKeyModel = $this->model('ApiKey');
                $clientName = trim($_POST['client_name'] ?? 'Official Mobile App');
                $rateLimit = (int)($_POST['rate_limit'] ?? 120);
                $newKey = $apiKeyModel->generateKey(TenantContext::getSchoolId() ?: 1, $clientName, $rateLimit);
                if ($newKey) {
                    $_SESSION['flash_success'] = "New API Key generated successfully for '{$clientName}'. Key: " . $newKey['api_key'];
                    $_SESSION['newly_generated_key'] = $newKey['api_key'];
                } else {
                    $_SESSION['flash_error'] = 'Failed to generate API key.';
                }
                $activeTab = 'api';

            } elseif(isset($_POST['toggle_api_key'])){
                $apiKeyModel = $this->model('ApiKey');
                $keyId = (int)($_POST['key_id'] ?? 0);
                if ($keyId && $apiKeyModel->toggleStatus($keyId, TenantContext::getSchoolId() ?: 1)) {
                    $_SESSION['flash_success'] = 'API Key status toggled successfully.';
                } else {
                    $_SESSION['flash_error'] = 'Could not update API key status.';
                }
                $activeTab = 'api';

            } elseif(isset($_POST['delete_api_key'])){
                $apiKeyModel = $this->model('ApiKey');
                $keyId = (int)($_POST['key_id'] ?? 0);
                if ($keyId && $apiKeyModel->deleteKey($keyId, TenantContext::getSchoolId() ?: 1)) {
                    $_SESSION['flash_success'] = 'API Key permanently deleted and all sessions revoked.';
                } else {
                    $_SESSION['flash_error'] = 'Could not delete API key.';
                }
                $activeTab = 'api';

            } elseif(isset($_POST['license_setting']) || ($postedTab === 'license' && isset($_POST['pro_license_key']))){
                $rawKey = strtoupper(trim($_POST['pro_license_key'] ?? ''));
                $authorizedKeys = [
                    'ENVATO-SCH-2026-A1B2-C3D4' => [
                        'tier' => 'Enterprise Grade (Unlimited Campuses / 100k Students)',
                        'campuses' => 'Unlimited Campuses',
                        'type' => 'Primary Enterprise Master License'
                    ],
                    'ENVATO-SCH-2026-E5F6-G7H8' => [
                        'tier' => 'Multi-Campus Cluster Edition',
                        'campuses' => 'Unlimited Branches',
                        'type' => 'Multi-Branch Master License'
                    ],
                    'ENVATO-SCH-2026-J9K0-L1M2' => [
                        'tier' => 'Academic Institution Edition',
                        'campuses' => '5 Campuses',
                        'type' => 'Academic Master License'
                    ],
                    'ENVATO-SCH-2026-N3P4-Q5R6' => [
                        'tier' => '100k High-Concurrency Cluster Node',
                        'campuses' => 'Unlimited Nodes',
                        'type' => 'High-Load Cluster License'
                    ],
                    'ENVATO-SCH-2026-S7T8-U9V0' => [
                        'tier' => 'Standard Commercial Edition',
                        'campuses' => 'Single Campus',
                        'type' => 'Commercial License'
                    ],
                    'ENVATO-SCH-2026-W1X2-Y3Z4' => [
                        'tier' => 'Extended Agency Edition',
                        'campuses' => 'Unlimited Clients',
                        'type' => 'Agency / Reseller License'
                    ],
                    'ENVATO-SCH-2026-B8D2-9F1A' => [
                        'tier' => 'Developer Sandbox Edition',
                        'campuses' => 'Localhost / Staging',
                        'type' => 'Development License'
                    ],
                    'ENVATO-SCH-2026-7C4E-3B01' => [
                        'tier' => 'Official Campus Partner Network',
                        'campuses' => 'Franchise Network',
                        'type' => 'Campus Partner License'
                    ],
                    'ENVATO-SCH-2026-5F9D-1A8E' => [
                        'tier' => 'Unlimited Student Edition (50,000+ Enrolled)',
                        'campuses' => 'Unlimited Students',
                        'type' => 'High-Volume Student License'
                    ],
                    'ENVATO-SCH-2026-0D3B-7E2C' => [
                        'tier' => 'VIP Executive Master License (Unrestricted)',
                        'campuses' => 'Unlimited Full Access',
                        'type' => 'VIP Master License'
                    ]
                ];

                $isValid = isset($authorizedKeys[$rawKey]) || preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $rawKey);

                if ($isValid) {
                    $meta = $authorizedKeys[$rawKey] ?? [
                        'tier' => 'Verified Envato Enterprise License',
                        'campuses' => 'Multi-Campus Unlocked',
                        'type' => 'Standard Purchase Code'
                    ];

                    $settingModel->updateSettings([
                        'pro_license_key' => $rawKey,
                        'license_status' => 'active',
                        'license_tier' => $meta['tier'],
                        'license_type' => $meta['type'],
                        'license_allowed_campuses' => $meta['campuses'],
                        'license_activated_at' => date('Y-m-d H:i:s'),
                        'license_verified' => '1'
                    ]);

                    $_SESSION['flash_success'] = 'Pro License Key activated and verified successfully! Multi-Branch management and Enterprise modules unlocked.';
                } else {
                    $_SESSION['flash_error'] = 'Invalid Pro License Key. Please select or enter an authorized license key (e.g. ENVATO-SCH-2026-A1B2-C3D4).';
                }
                $activeTab = 'license';
            }
            
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                   || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                   || isset($_POST['ajax_submit']);

            if($isAjax){
                if (ob_get_length()) ob_clean();
                header('Content-Type: application/json');
                
                if (empty($_SESSION['flash_success']) && empty($_SESSION['flash_error'])) {
                    $hasError = true;
                    $msg = 'Unrecognized settings form payload. Please try again.';
                } else {
                    $hasError = !empty($_SESSION['flash_error']);
                    $msg = $hasError ? $_SESSION['flash_error'] : ($_SESSION['flash_success'] ?? 'Settings updated successfully.');
                }
                unset($_SESSION['flash_success'], $_SESSION['flash_error']);

                $response = [
                    'success' => !$hasError,
                    'message' => $msg,
                    'tab' => $activeTab,
                    'school_name' => $data['school_name'] ?? ($_SESSION['school_name'] ?? null),
                    'campus_name' => $data['campus_name'] ?? ($_SESSION['campus_name'] ?? null)
                ];

                if (!empty($uploadedLogoPath)) {
                    $response['logo'] = $uploadedLogoPath;
                    $response['logo_url'] = URLROOT . '/' . $uploadedLogoPath . '?v=' . time();
                }

                if (!empty($uploadedHeroImagePath)) {
                    $response['hero_image_path'] = $uploadedHeroImagePath . '?v=' . time();
                }

                echo json_encode($response);
                exit;
            }

            header('Location: ' . URLROOT . '/setting/index?tab=' . $activeTab);
            exit;
        }

        $settingsAry = $settingModel->getAllSettings();
        $settings = (object) $settingsAry;
        $cmsSettings = $cmsModel->getSettings();
        $sessions = $settingModel->getSessions();
        $roles = $settingModel->getRoles();
        $permissions = $settingModel->getPermissions();
        $matrix = $settingModel->getRolePermissionMatrix();

        $groupedPermissions = [];
        foreach ($permissions as $perm) {
            $cat = !empty($perm->category) ? $perm->category : 'General';
            $groupedPermissions[$cat][] = $perm;
        }

        $pageModel = $this->model('FrontPage');
        $frontPages = $pageModel ? $pageModel->getPagesWithMenuStatus() : [];
        $menuModel = $this->model('FrontMenu');
        $customLinks = $menuModel ? $menuModel->getCustomLinks() : [];

        $apiKeyModel = $this->model('ApiKey');
        $apiKeys = $apiKeyModel ? $apiKeyModel->getAllBySchool(TenantContext::getSchoolId() ?: 1) : [];

        $authorizedKeysList = [
            'ENVATO-SCH-2026-A1B2-C3D4' => [
                'name' => 'Primary Enterprise Master License',
                'tier' => 'Enterprise Grade (Unlimited Campuses / 100k Students)',
                'badge' => 'Enterprise Unlimited'
            ],
            'ENVATO-SCH-2026-E5F6-G7H8' => [
                'name' => 'Multi-Campus Cluster License',
                'tier' => 'Multi-Branch / Multi-School Campus Network',
                'badge' => 'Multi-Campus'
            ],
            'ENVATO-SCH-2026-J9K0-L1M2' => [
                'name' => 'Academic Institution Edition',
                'tier' => 'Schools, Colleges & Higher Secondary Institutions',
                'badge' => 'Academic Pro'
            ],
            'ENVATO-SCH-2026-N3P4-Q5R6' => [
                'name' => '100k High-Concurrency Cluster Node',
                'tier' => 'High-Load Cluster / Multi-Server Load Balanced',
                'badge' => '100k Cluster'
            ],
            'ENVATO-SCH-2026-S7T8-U9V0' => [
                'name' => 'Standard Commercial License',
                'tier' => 'Single School Commercial Production Deployment',
                'badge' => 'Commercial'
            ],
            'ENVATO-SCH-2026-W1X2-Y3Z4' => [
                'name' => 'Extended Agency License',
                'tier' => 'Agency / Software Reseller Deployment',
                'badge' => 'Agency Reseller'
            ],
            'ENVATO-SCH-2026-B8D2-9F1A' => [
                'name' => 'Developer Sandbox License',
                'tier' => 'Testing, Staging & Localhost Development',
                'badge' => 'Sandbox Dev'
            ],
            'ENVATO-SCH-2026-7C4E-3B01' => [
                'name' => 'Official Campus Partner License',
                'tier' => 'Franchise & Partner School Network',
                'badge' => 'Campus Partner'
            ],
            'ENVATO-SCH-2026-5F9D-1A8E' => [
                'name' => 'Unlimited Student Edition License',
                'tier' => 'Large Scale Student Body (50,000+ Enrolled)',
                'badge' => 'Unlimited Students'
            ],
            'ENVATO-SCH-2026-0D3B-7E2C' => [
                'name' => 'VIP Executive Master License',
                'tier' => 'VIP Master License (Unrestricted All Features)',
                'badge' => 'VIP Unrestricted'
            ]
        ];

        $data = [
            'settings' => $settings,
            'settings_raw' => $settingsAry,
            'cms_settings' => $cmsSettings,
            'front_pages' => $frontPages,
            'custom_links' => $customLinks,
            'sessions' => $sessions,
            'roles' => $roles,
            'permissions' => $permissions,
            'grouped_permissions' => $groupedPermissions,
            'matrix' => $matrix,
            'api_keys' => $apiKeys,
            'authorized_keys' => $authorizedKeysList,
            'active_tab' => $activeTab
        ];

        $this->view('settings/index', $data);
    }
}

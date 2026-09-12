<?php
class FrontCmsController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        AuthGuard::requirePermission('manage_settings');
        if(!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'super_admin')){
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    public function events(){
        $eventModel = $this->model('FrontEvent');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST['delete_id'])){
                $eventModel->deleteEvent($_POST['delete_id']);
                header('Location: ' . URLROOT . '/frontcms/events');
                exit;
            }
            
            // Add/Edit handling
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'start_date' => trim($_POST['start_date'] ?? date('Y-m-d H:i')),
                'venue' => trim($_POST['venue'] ?? 'Main Auditorium'),
                'image' => ''
            ];

            // Handle Image
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
                require_once APPROOT . '/Core/UploadHandler.php';
                $upload = UploadHandler::processUpload($_FILES['image'], 'events');
                if($upload['success']){
                    $data['image'] = $upload['path'];
                }
            }

            $eventModel->addEvent($data);
            $_SESSION['flash_success'] = 'Event added successfully.';
            header('Location: ' . URLROOT . '/frontcms/events');
            exit;
        }

        $events = $eventModel->getEvents();
        $this->view('frontcms/events/index', ['events' => $events]);
    }
    
    public function gallery(){
        $galleryModel = $this->model('FrontGallery');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST['delete_id'])){
                $galleryModel->deleteImage($_POST['delete_id']);
                header('Location: ' . URLROOT . '/frontcms/gallery');
                exit;
            }
            
            $data = [
                'title' => trim($_POST['title'] ?? 'Gallery Image'),
                'description' => trim($_POST['description'] ?? ''),
                'image' => ''
            ];

            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
                require_once APPROOT . '/Core/UploadHandler.php';
                $upload = UploadHandler::processUpload($_FILES['image'], 'gallery');
                if($upload['success']){
                    $data['image'] = $upload['path'];
                    $galleryModel->addImage($data);
                }
            }
            header('Location: ' . URLROOT . '/frontcms/gallery');
            exit;
        }

        $gallery = $galleryModel->getGallery();
        $this->view('frontcms/gallery/index', ['gallery' => $gallery]);
    }

    public function news(){
        $newsModel = $this->model('FrontNews');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST['delete_id'])){
                $newsModel->deleteNews($_POST['delete_id']);
                header('Location: ' . URLROOT . '/frontcms/news');
                exit;
            }
            
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'news_date' => trim($_POST['news_date'] ?? date('Y-m-d')),
                'image' => ''
            ];

            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
                require_once APPROOT . '/Core/UploadHandler.php';
                $upload = UploadHandler::processUpload($_FILES['image'], 'news');
                if($upload['success']){
                    $data['image'] = $upload['path'];
                }
            }
            
            $newsModel->addNews($data);
            header('Location: ' . URLROOT . '/frontcms/news');
            exit;
        }

        $news = $newsModel->getNews();
        $this->view('frontcms/news/index', ['news' => $news]);
    }

    public function media(){
        $mediaModel = $this->model('FrontMedia');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST['delete_id'])){
                $mediaModel->deleteFile($_POST['delete_id']);
                header('Location: ' . URLROOT . '/frontcms/media');
                exit;
            }
            
            if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
                require_once APPROOT . '/Core/UploadHandler.php';
                $upload = UploadHandler::processUpload($_FILES['file'], 'media', null, null, 20971520);
                if($upload['success']){
                    $data = [
                        'file_name' => basename($_FILES['file']['name']),
                        'file_path' => $upload['path'],
                        'file_type' => $_FILES['file']['type']
                    ];
                    $mediaModel->addFile($data);
                }
            }
            header('Location: ' . URLROOT . '/frontcms/media');
            exit;
        }

        $media = $mediaModel->getMediaFiles();
        $this->view('frontcms/media/index', ['media' => $media]);
    }

    public function pages($action = 'index', $id = 0){
        $pageModel = $this->model('FrontPage');
        $menuModel = $this->model('FrontMenu');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // 1. Delete page (also deletes linked menu)
            if(isset($_POST['delete_id']) || isset($_POST['delete_page_id'])){
                $delId = (int)($_POST['delete_page_id'] ?? $_POST['delete_id']);
                $pageModel->deletePage($delId);
                $_SESSION['flash_success'] = 'Page and its menu link deleted successfully.';
                header('Location: ' . URLROOT . '/frontcms/pages');
                exit;
            }

            // 2. Delete custom menu item
            if(isset($_POST['delete_menu_id'])){
                $menuModel->deleteMenu((int)$_POST['delete_menu_id']);
                $_SESSION['flash_success'] = 'Menu item removed successfully.';
                header('Location: ' . URLROOT . '/frontcms/pages?tab=menus');
                exit;
            }

            // 3. Quick toggle page in top navbar menu
            if(isset($_POST['quick_toggle_menu_page_id'])){
                $togglePageId = (int)$_POST['quick_toggle_menu_page_id'];
                $existingMenu = $menuModel->getMenuByPageId($togglePageId);
                if($existingMenu){
                    $menuModel->deleteMenuByPageId($togglePageId);
                    $_SESSION['flash_success'] = 'Page removed from top navigation menu.';
                } else {
                    $pageObj = $pageModel->getPageById($togglePageId);
                    if($pageObj){
                        $menuModel->addMenu([
                            'title' => $pageObj->title,
                            'link' => '',
                            'page_id' => $togglePageId,
                            'sort_order' => 10,
                            'dropdown_group' => 'none'
                        ]);
                        $_SESSION['flash_success'] = 'Page added to top navigation menu.';
                    }
                }
                header('Location: ' . URLROOT . '/frontcms/pages');
                exit;
            }

            // 4. Add standalone custom external/anchor link
            if(isset($_POST['add_custom_link'])){
                $linkGroup = trim($_POST['custom_link_dropdown_group'] ?? 'none');
                $menuModel->addMenu([
                    'title' => trim($_POST['custom_link_title'] ?? 'Menu Link'),
                    'link' => trim($_POST['custom_link_url'] ?? '#'),
                    'page_id' => 0,
                    'sort_order' => (int)($_POST['custom_link_order'] ?? 0),
                    'dropdown_group' => $linkGroup
                ]);
                $_SESSION['flash_success'] = 'Custom menu link added successfully.';
                header('Location: ' . URLROOT . '/frontcms/pages?tab=menus');
                exit;
            }

            // 5. Main unified Page & Menu Save (Create or Update)
            $pageTitle = trim($_POST['title'] ?? '');
            if(empty($pageTitle)){
                $_SESSION['flash_error'] = 'Page title is required.';
                header('Location: ' . URLROOT . '/frontcms/pages');
                exit;
            }

            $rawSlug = !empty($_POST['slug']) ? $_POST['slug'] : $pageTitle;
            $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace([' ', '_'], '-', $rawSlug)));
            if(empty($slug)) $slug = 'page-' . time();

            $data = [
                'title' => $pageTitle,
                'slug' => $slug,
                'content' => $this->cleanPageContent(trim($_POST['content'] ?? '')),
                'is_active' => isset($_POST['is_active']) ? 'yes' : 'no',
                'meta_description' => trim($_POST['meta_description'] ?? '')
            ];

            // Handle optional file/document upload
            if(isset($_FILES['file_attachment']) && $_FILES['file_attachment']['error'] == UPLOAD_ERR_OK){
                require_once APPROOT . '/Core/UploadHandler.php';
                $upload = UploadHandler::processUpload($_FILES['file_attachment'], 'pages');
                if($upload['success']){
                    $data['file_path'] = $upload['path'];
                    $data['file_name'] = basename($_FILES['file_attachment']['name']);
                }
            }

            $pageId = 0;
            $isNew = true;
            if(!empty($_POST['id'])){
                $pageId = (int)$_POST['id'];
                $data['id'] = $pageId;
                $pageModel->updatePage($data);
                $isNew = false;
            } else {
                $pageId = $pageModel->addPage($data);
            }

            // Synchronize Navbar Menu
            if($pageId){
                $addToMenu = isset($_POST['add_to_menu']) && $_POST['add_to_menu'] == 'yes';
                $existingMenu = $menuModel->getMenuByPageId($pageId);
                
                if($addToMenu){
                    $menuTitle = !empty($_POST['menu_title']) ? trim($_POST['menu_title']) : $pageTitle;
                    $menuOrder = (int)($_POST['menu_sort_order'] ?? 0);
                    $menuDropdownGroup = trim($_POST['menu_dropdown_group'] ?? 'none');
                    if($menuDropdownGroup === 'custom'){
                        $customGroup = trim($_POST['menu_custom_dropdown_group'] ?? '');
                        $menuDropdownGroup = !empty($customGroup) ? strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', str_replace(' ', '-', $customGroup))) : 'none';
                    }

                    $menuData = [
                        'title' => $menuTitle,
                        'link' => '',
                        'page_id' => $pageId,
                        'sort_order' => $menuOrder,
                        'dropdown_group' => $menuDropdownGroup
                    ];
                    if($existingMenu){
                        $menuData['id'] = $existingMenu->id;
                        $menuModel->updateMenu($menuData);
                    } else {
                        $menuModel->addMenu($menuData);
                    }
                } elseif($existingMenu && isset($_POST['has_menu_sync'])){
                    // User explicitly deselected the menu checkbox
                    $menuModel->deleteMenuByPageId($pageId);
                }
            }

            $_SESSION['flash_success'] = $isNew ? 'Page created & published successfully.' : 'Page & Menu updated successfully.';
            header('Location: ' . URLROOT . '/frontcms/pages');
            exit;
        }
        
        if($action == 'edit' && $id > 0){
            $page = $pageModel->getPageById($id);
            if(!$page){
                $_SESSION['flash_error'] = 'Page not found.';
                header('Location: ' . URLROOT . '/frontcms/pages');
                exit;
            }
            $this->view('frontcms/pages/edit', [
                'page' => $page,
                'menus' => $menuModel->getMenus()
            ]);
        } else {
            $pages = $pageModel->getPagesWithMenuStatus();
            $menus = $menuModel->getMenus();
            $customLinks = $menuModel->getCustomLinks();
            $this->view('frontcms/pages/index', [
                'pages' => $pages,
                'menus' => $menus,
                'customLinks' => $customLinks,
                'activeTab' => $_GET['tab'] ?? 'pages'
            ]);
        }
    }

    private function cleanPageContent($content){
        $content = preg_replace('/<html[^>]*>/i', '', $content);
        $content = preg_replace('/<\/html>/i', '', $content);
        $content = preg_replace('/<head>.*?<\/head>/si', '', $content);
        $content = preg_replace('/<body[^>]*>/i', '', $content);
        $content = preg_replace('/<\/body>/i', '', $content);
        $content = preg_replace('/<!DOCTYPE[^>]*>/i', '', $content);
        return $content;
    }

    public function menus(){
        // Redirect to unified manager with Menus tab active
        header('Location: ' . URLROOT . '/frontcms/pages?tab=menus');
        exit;
    }

    public function banners(){
        $bannerModel = $this->model('FrontBanner');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST['delete_id'])){
                $bannerModel->deleteBanner($_POST['delete_id']);
                header('Location: ' . URLROOT . '/frontcms/banners');
                exit;
            }

            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'link' => trim($_POST['link'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'sort_order' => (int)($_POST['sort_order'] ?? 0),
                'image' => ''
            ];

            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
                require_once APPROOT . '/Core/UploadHandler.php';
                $upload = UploadHandler::processUpload($_FILES['image'], 'banners');
                if($upload['success']){
                    $data['image'] = $upload['path'];
                    $bannerModel->addBanner($data);
                }
            }
            header('Location: ' . URLROOT . '/frontcms/banners');
            exit;
        }

        $banners = $bannerModel->getBanners();
        $this->view('frontcms/banners/index', ['banners' => $banners]);
    }

    public function index(){
        $cmsModel = $this->model('FrontCms');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'is_active_website' => isset($_POST['is_active_website']) ? 'yes' : 'no',
                'enable_online_admission' => isset($_POST['enable_online_admission']) ? 'yes' : 'no',
                'footer_text' => trim($_POST['footer_text'] ?? ''),
                'maintenance_title' => trim($_POST['maintenance_title'] ?? ''),
                'maintenance_message' => trim($_POST['maintenance_message'] ?? ''),
                'maintenance_eta' => trim($_POST['maintenance_eta'] ?? ''),
                'facebook_url' => trim($_POST['facebook_url'] ?? ''),
                'twitter_url' => trim($_POST['twitter_url'] ?? ''),
                'instagram_url' => trim($_POST['instagram_url'] ?? ''),
                'youtube_url' => trim($_POST['youtube_url'] ?? ''),
                'linkedin_url' => trim($_POST['linkedin_url'] ?? ''),
                'google_plus_url' => trim($_POST['google_plus_url'] ?? ''),
                'theme_color' => trim($_POST['theme_color'] ?? 'default'),
                'layout_type' => trim($_POST['layout_type'] ?? 'full')
            ];
            
            $cmsModel->updateSettings($data);

            if(isset($_POST['has_cms_feature_toggles'])){
                $cmsModel->updateFeatureToggle('enable_alumni', isset($_POST['enable_alumni']) ? 'yes' : 'no');
                $cmsModel->updateFeatureToggle('enable_requirements', isset($_POST['enable_requirements']) ? 'yes' : 'no');
            }
            
            // Handle Logo Upload
            if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0){
                require_once APPROOT . '/Core/UploadHandler.php';
                $upload = UploadHandler::processUpload($_FILES['logo'], 'front');
                if($upload['success']){
                    $cmsModel->updateLogo($upload['path']);
                }
            }

            // Handle Maintenance Background Upload
            if(isset($_FILES['maintenance_background']) && $_FILES['maintenance_background']['error'] == 0){
                require_once APPROOT . '/Core/UploadHandler.php';
                $upload = UploadHandler::processUpload($_FILES['maintenance_background'], 'front/maintenance');
                if($upload['success']){
                    $cmsModel->updateMaintenanceBackground($upload['path']);
                }
            }
            
            $_SESSION['flash_success'] = 'Public website & CMS settings updated.';
            header('Location: ' . URLROOT . '/frontcms/index');
            exit;
        }

        $settings = $cmsModel->getSettings();
        $this->view('frontcms/index', ['settings' => $settings]);
    }

    public function alumni($action = 'index', $id = 0){
        $alumniModel = $this->model('FrontAlumni');
        $cmsModel = $this->model('FrontCms');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // 1. Toggle feature on/off
            if(isset($_POST['toggle_feature_enable'])){
                $currentVal = $cmsModel->getSettings()->enable_alumni ?? 'yes';
                $newVal = ($currentVal === 'yes') ? 'no' : 'yes';
                $cmsModel->updateFeatureToggle('enable_alumni', $newVal);
                $_SESSION['flash_success'] = 'Alumni module visibility has been ' . ($newVal === 'yes' ? 'enabled' : 'disabled') . '.';
                header('Location: ' . URLROOT . '/frontcms/alumni');
                exit;
            }

            // 2. Delete Alumni Profile
            if(isset($_POST['delete_id'])){
                $alumniModel->deleteAlumni((int)$_POST['delete_id']);
                $_SESSION['flash_success'] = 'Alumni record deleted successfully.';
                header('Location: ' . URLROOT . '/frontcms/alumni');
                exit;
            }

            // 3. Quick Toggle Active Status
            if(isset($_POST['toggle_status_id'])){
                $alumniModel->toggleStatus((int)$_POST['toggle_status_id']);
                $_SESSION['flash_success'] = 'Alumni status updated successfully.';
                header('Location: ' . URLROOT . '/frontcms/alumni');
                exit;
            }

            // 4. Quick Toggle Featured Badge
            if(isset($_POST['toggle_featured_id'])){
                $alumniModel->toggleFeatured((int)$_POST['toggle_featured_id']);
                $_SESSION['flash_success'] = 'Featured spotlight updated.';
                header('Location: ' . URLROOT . '/frontcms/alumni');
                exit;
            }

            // 5. Add or Edit Alumni Profile
            $name = trim($_POST['name'] ?? '');
            if(empty($name)){
                $_SESSION['flash_error'] = 'Alumni name is required.';
                header('Location: ' . URLROOT . '/frontcms/alumni');
                exit;
            }

            $data = [
                'name' => $name,
                'batch_year' => trim($_POST['batch_year'] ?? date('Y')),
                'graduation_class' => trim($_POST['graduation_class'] ?? ''),
                'current_position' => trim($_POST['current_position'] ?? ''),
                'company_organization' => trim($_POST['company_organization'] ?? ''),
                'location' => trim($_POST['location'] ?? ''),
                'testimonial' => trim($_POST['testimonial'] ?? ''),
                'linkedin_url' => trim($_POST['linkedin_url'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'is_active' => isset($_POST['is_active']) ? 'yes' : 'no',
                'sort_order' => (int)($_POST['sort_order'] ?? 0),
                'image' => ''
            ];

            // Handle Profile Photo Upload
            if(isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK){
                require_once APPROOT . '/Core/UploadHandler.php';
                $upload = UploadHandler::processUpload($_FILES['image'], 'alumni');
                if($upload['success']){
                    $data['image'] = $upload['path'];
                }
            }

            if(!empty($_POST['id'])){
                $data['id'] = (int)$_POST['id'];
                $alumniModel->updateAlumni($data);
                $_SESSION['flash_success'] = 'Alumni profile updated successfully.';
            } else {
                $alumniModel->addAlumni($data);
                $_SESSION['flash_success'] = 'Alumni profile added successfully.';
            }
            header('Location: ' . URLROOT . '/frontcms/alumni');
            exit;
        }

        $alumni = $alumniModel->getAlumni(false);
        $batches = $alumniModel->getBatches();
        $cmsSettings = $cmsModel->getSettings();

        $this->view('frontcms/alumni/index', [
            'alumni' => $alumni,
            'batches' => $batches,
            'cms' => $cmsSettings
        ]);
    }

    public function requirements($action = 'index', $id = 0){
        $reqModel = $this->model('FrontRequirement');
        $cmsModel = $this->model('FrontCms');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // 1. Toggle feature on/off
            if(isset($_POST['toggle_feature_enable'])){
                $currentVal = $cmsModel->getSettings()->enable_requirements ?? 'yes';
                $newVal = ($currentVal === 'yes') ? 'no' : 'yes';
                $cmsModel->updateFeatureToggle('enable_requirements', $newVal);
                $_SESSION['flash_success'] = 'School Requirements portal visibility has been ' . ($newVal === 'yes' ? 'enabled' : 'disabled') . '.';
                header('Location: ' . URLROOT . '/frontcms/requirements');
                exit;
            }

            // 2. Delete Requirement
            if(isset($_POST['delete_id'])){
                $reqModel->deleteRequirement((int)$_POST['delete_id']);
                $_SESSION['flash_success'] = 'Requirement listing removed successfully.';
                header('Location: ' . URLROOT . '/frontcms/requirements');
                exit;
            }

            // 3. Quick Toggle Status (Active / Closed)
            if(isset($_POST['toggle_status_id'])){
                $reqModel->toggleStatus((int)$_POST['toggle_status_id']);
                $_SESSION['flash_success'] = 'Requirement status updated.';
                header('Location: ' . URLROOT . '/frontcms/requirements');
                exit;
            }

            // 4. Quick Toggle Top Menu Display
            if(isset($_POST['toggle_menu_id'])){
                $newStatus = $reqModel->toggleMenu((int)$_POST['toggle_menu_id']);
                $_SESSION['flash_success'] = $newStatus ? 'Notice linked to top website navigation menu.' : 'Notice unlinked from top menu.';
                header('Location: ' . URLROOT . '/frontcms/requirements');
                exit;
            }

            // 5. Add or Update Requirement
            $title = trim($_POST['title'] ?? '');
            if(empty($title)){
                $_SESSION['flash_error'] = 'Requirement title is required.';
                header('Location: ' . URLROOT . '/frontcms/requirements');
                exit;
            }

            $data = [
                'title' => $title,
                'category' => trim($_POST['category'] ?? 'Faculty Career'),
                'department' => trim($_POST['department'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'eligibility' => trim($_POST['eligibility'] ?? ''),
                'deadline' => !empty($_POST['deadline']) ? trim($_POST['deadline']) : null,
                'vacancies' => (int)($_POST['vacancies'] ?? 1),
                'status' => trim($_POST['status'] ?? 'active'),
                'show_in_menu' => isset($_POST['show_in_menu']) ? 1 : 0,
                'menu_title' => trim($_POST['menu_title'] ?? $title),
                'sort_order' => (int)($_POST['sort_order'] ?? 0),
                'attachment' => false
            ];

            // Handle PDF or Document Attachment Upload
            if(isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK){
                require_once APPROOT . '/Core/UploadHandler.php';
                $upload = UploadHandler::processUpload($_FILES['attachment'], 'requirements', null, null, 20971520);
                if($upload['success']){
                    $data['attachment'] = $upload['path'];
                }
            }

            if(!empty($_POST['id'])){
                $data['id'] = (int)$_POST['id'];
                $reqModel->updateRequirement($data);
                $_SESSION['flash_success'] = 'Requirement updated successfully.';
            } else {
                $reqModel->addRequirement($data);
                $_SESSION['flash_success'] = 'Requirement published successfully.';
            }
            header('Location: ' . URLROOT . '/frontcms/requirements');
            exit;
        }

        $requirements = $reqModel->getRequirements(false);
        $categories = $reqModel->getCategories();
        $cmsSettings = $cmsModel->getSettings();

        $this->view('frontcms/requirements/index', [
            'requirements' => $requirements,
            'categories' => $categories,
            'cms' => $cmsSettings
        ]);
    }
}


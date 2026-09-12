<?php
// app/Core/UploadHandler.php

class UploadHandler {
    
    // Default allowed extensions and mime types for basic uploads (images, docs, icons)
    private static $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'ico', 'svg', 'bmp',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt'
    ];
    private static $allowedMimeTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'image/x-icon', 'image/vnd.microsoft.icon', 'image/ico',
        'image/svg+xml', 'image/bmp', 'image/x-ms-bmp',
        'application/pdf', 
        'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain', 'text/csv'
    ];
    
    // Max size defaults to 10MB
    private static $maxSize = 10485760; 

    /**
     * Handles file uploads securely and segregates them by tenant (school_id)
     */
    public static function processUpload($fileArray, $subDirectory = 'misc', $customExtensions = null, $customMimeTypes = null, $customMaxSize = null) {
        
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        if (empty($schoolId)) {
            $schoolId = 1;
        }

        // 1. Basic PHP Upload Error Check
        if (!isset($fileArray['error']) || is_array($fileArray['error'])) {
            return ['success' => false, 'error' => 'Invalid upload parameters.'];
        }

        switch ($fileArray['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return ['success' => false, 'error' => 'No file sent.'];
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['success' => false, 'error' => 'Exceeded file size limit configured on server.'];
            case UPLOAD_ERR_PARTIAL:
                return ['success' => false, 'error' => 'File was only partially uploaded.'];
            case UPLOAD_ERR_NO_TMP_DIR:
                return ['success' => false, 'error' => 'Missing temporary folder on server.'];
            case UPLOAD_ERR_CANT_WRITE:
                return ['success' => false, 'error' => 'Failed to write file to disk.'];
            default:
                return ['success' => false, 'error' => 'Upload error (code: ' . $fileArray['error'] . ').'];
        }

        // 2. Validate File Size
        $maxBytes = $customMaxSize !== null ? $customMaxSize : self::$maxSize;
        if ($fileArray['size'] > $maxBytes) {
            return ['success' => false, 'error' => 'Exceeded maximum allowed file size (' . round($maxBytes / 1048576, 1) . 'MB).'];
        }

        // 3. Validate Extension
        $allowedExts = $customExtensions !== null ? $customExtensions : self::$allowedExtensions;
        $ext = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts)) {
            return ['success' => false, 'error' => 'Invalid file extension (.' . htmlspecialchars($ext) . ' is not allowed).'];
        }

        // 4. Validate Mime Type safely (with finfo, mime_content_type, or client type fallback)
        $mime = '';
        if (class_exists('finfo')) {
            try {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = @$finfo->file($fileArray['tmp_name']);
            } catch (Throwable $e) {
                $mime = '';
            }
        }
        if (empty($mime) && function_exists('mime_content_type')) {
            $mime = @mime_content_type($fileArray['tmp_name']);
        }
        if (empty($mime) && !empty($fileArray['type'])) {
            $mime = strtolower($fileArray['type']);
        }

        $allowedMimes = $customMimeTypes !== null ? $customMimeTypes : self::$allowedMimeTypes;
        // If mime was detected and not empty, check it against whitelist (skip check for ico if mime detection varies by OS)
        if (!empty($mime) && $ext !== 'ico') {
            if (!in_array($mime, $allowedMimes)) {
                return ['success' => false, 'error' => 'Invalid file format detected (' . htmlspecialchars($mime) . ').'];
            }
        }

        // 5. Build Safe Filename
        $hash = @sha1_file($fileArray['tmp_name']);
        if (!$hash) {
            $hash = md5(uniqid(mt_rand(), true));
        }
        $safeFileName = $hash . '_' . time() . '.' . $ext;

        // 6. Tenant Segregated Upload Directory
        $baseUploadDir = dirname(dirname(__DIR__)) . '/public/uploads';
        $tenantDir = $baseUploadDir . '/school_' . $schoolId;
        $targetDir = $tenantDir . '/' . trim($subDirectory, '/');

        // Ensure directories exist silently without warnings
        if (!is_dir($baseUploadDir)) {
            @mkdir($baseUploadDir, 0755, true);
        }
        if (!is_dir($tenantDir)) {
            @mkdir($tenantDir, 0755, true);
            @file_put_contents($tenantDir . '/index.html', '');
        }
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
            @file_put_contents($targetDir . '/index.html', '');
        }

        // 7. Move File securely
        $destination = $targetDir . '/' . $safeFileName;
        if (!@move_uploaded_file($fileArray['tmp_name'], $destination)) {
            return ['success' => false, 'error' => 'Failed to save file on server. Please check folder permissions.'];
        }

        // Return relative path to be stored in DB
        $relativePath = 'uploads/school_' . $schoolId . '/' . trim($subDirectory, '/') . '/' . $safeFileName;

        return ['success' => true, 'path' => $relativePath, 'error' => ''];
    }
}

<?php
// inc/functions.php
require_once __DIR__ . '/config.php';

/**
 * Check if logged in
 */
function is_logged_in(): bool {
    return !empty($_SESSION['admin_id']);
}

/**
 * Redirect to login if not logged in
 */
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Generate a CSRF token for forms
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token from form submission
 */
function verify_csrf(?string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

/**
 * Create a URL-friendly slug from a string
 */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text === '' ? 'n-a' : $text;
}

/**
 * Handle image upload and return the filename or null if no file uploaded
 */
function handle_image_upload(array $file): ?string {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; 
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload error code: ' . $file['error']);
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        throw new Exception('Image too large (max 2MB).');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $ext_map = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];

    if (!array_key_exists($mime, $ext_map)) {
        throw new Exception('Unsupported image type.');
    }

    $filename = bin2hex(random_bytes(8)) . '.' . $ext_map[$mime];
    $destDir = __DIR__ . '/../uploads';
    if (!is_dir($destDir) && !mkdir($destDir, 0755, true)) {
        throw new Exception('Failed to create uploads directory.');
    }

    $dest = $destDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new Exception('Failed to move uploaded file.');
    }

    return $filename;
}

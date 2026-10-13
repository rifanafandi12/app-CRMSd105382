<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper keamanan untuk sanitasi & validasi
 */

if (!function_exists('sanitize_input')) {
    /**
     * Sanitasi input dari XSS & karakter berbahaya
     */
    function sanitize_input($data, $allow_html = false) {
        if (is_array($data)) {
            return array_map(function($item) use ($allow_html) {
                return sanitize_input($item, $allow_html);
            }, $data);
        }

        $data = trim($data);
        
        if (!$allow_html) {
            $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } else {
            // Izinkan tag tertentu saja
            $allowed = '<p><br><strong><em><u><a><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><table><thead><tbody><tr><th><td>';
            $data = strip_tags($data, $allowed);
        }

        return $data;
    }
}

if (!function_exists('sanitize_filename')) {
    /**
     * Sanitasi nama file untuk mencegah path traversal
     */
    function sanitize_filename($filename) {
        // Hapus path traversal
        $filename = str_replace(['..', '/', '\\', "\0"], '', $filename);
        
        // Hapus karakter yang tidak diizinkan
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Batasi panjang
        if (strlen($filename) > 200) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $name = substr(pathinfo($filename, PATHINFO_FILENAME), 0, 180);
            $filename = $name . '.' . $ext;
        }
        
        return $filename;
    }
}

if (!function_exists('validate_upload')) {
    /**
     * Validasi file upload dengan MIME check & size
     */
    function validate_upload($file, $allowed_types = [], $max_size = 2048) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'File tidak ditemukan'];
        }

        // Cek size
        if ($file['size'] > ($max_size * 1024)) {
            return ['valid' => false, 'error' => "Ukuran file melebihi {$max_size}KB"];
        }

        // Cek MIME type sebenarnya (bukan hanya dari client)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowed_mimes = [
            'jpg'  => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png'  => ['image/png'],
            'gif'  => ['image/gif'],
            'webp' => ['image/webp'],
            'pdf'  => ['application/pdf'],
            'doc'  => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        ];

        $valid = false;
        foreach ($allowed_types as $ext) {
            if (isset($allowed_mimes[$ext]) && in_array($mime, $allowed_mimes[$ext])) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            return ['valid' => false, 'error' => 'Tipe file tidak diizinkan'];
        }

        return ['valid' => true, 'mime' => $mime];
    }
}

if (!function_exists('generate_csrf_token')) {
    /**
     * Generate CSRF token untuk form / AJAX
     */
    function generate_csrf_token() {
        $CI =& get_instance();
        return [
            'name'  => $CI->security->get_csrf_token_name(),
            'value' => $CI->security->get_csrf_hash(),
        ];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Output hidden input CSRF untuk form
     */
    function csrf_field() {
        $token = generate_csrf_token();
        return '<input type="hidden" name="' . $token['name'] . '" value="' . $token['value'] . '">';
    }
}

if (!function_exists('csrf_meta')) {
    /**
     * Output meta tag CSRF untuk AJAX
     */
    function csrf_meta() {
        $token = generate_csrf_token();
        return '<meta name="csrf-token" content="' . $token['value'] . '">';
    }
}

if (!function_exists('hash_password')) {
    /**
     * Hash password dengan algoritma yang kuat
     */
    function hash_password($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}

if (!function_exists('verify_password')) {
    /**
     * Verifikasi password
     */
    function verify_password($password, $hash) {
        return password_verify($password, $hash);
    }
}

if (!function_exists('generate_random_token')) {
    /**
     * Generate random token untuk remember-me / API key
     */
    function generate_random_token($length = 32) {
        return bin2hex(random_bytes($length));
    }
}

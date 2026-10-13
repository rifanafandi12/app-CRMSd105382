<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use WebPConvert\WebPConvert;

/**
 * Image Converter Library
 * 
 * Membungkus rosell-dk/webp-convert untuk memudahkan konversi
 * gambar (JPG/PNG) ke WebP setelah upload.
 * 
 * Usage:
 *   $this->load->library('Image_converter');
 *   $result = $this->image_converter->convert_to_webp($file_path);
 *   // $result['success'] = true/false
 *   // $result['webp_path'] = path ke file .webp
 *   // $result['message'] = pesan error/success
 */
class Image_converter {

    /**
     * Kualitas WebP (0-100)
     * 80 = kualitas bagus, ukuran wajar
     */
    protected $quality = 80;

    /**
     * Hapus file original setelah konversi sukses?
     */
    protected $delete_original = true;

    /**
     * Maksimal lebar/tinggi gambar (resize kalau lebih besar)
     * NULL = tidak resize
     */
    protected $max_width  = 1920;
    protected $max_height = 1920;

    public function __construct($config = []) {
        if (!empty($config['quality']))         $this->quality = (int)$config['quality'];
        if (isset($config['delete_original']))  $this->delete_original = (bool)$config['delete_original'];
        if (isset($config['max_width']))        $this->max_width = (int)$config['max_width'] ?: null;
        if (isset($config['max_height']))       $this->max_height = (int)$config['max_height'] ?: null;

        // Verifikasi library tersedia
        if (!class_exists('WebPConvert\WebPConvert')) {
            log_message('error', 'Image_converter: WebPConvert library tidak ditemukan. Jalankan: composer require rosell-dk/webp-convert');
        }
    }

    /**
     * Convert gambar ke WebP
     * 
     * @param string $source_path  Absolute path ke file sumber
     * @return array  ['success' => bool, 'webp_path' => string, 'message' => string, 'relative_path' => string]
     */
    public function convert_to_webp($source_path) {
        $result = [
            'success'       => false,
            'webp_path'     => null,
            'relative_path' => null,
            'message'       => '',
        ];

        // Cek file ada
        if (!file_exists($source_path)) {
            $result['message'] = 'File sumber tidak ditemukan: ' . $source_path;
            return $result;
        }

        // Cek MIME — hanya konversi JPG, JPEG, PNG
        $mime = $this->get_mime_type($source_path);
        $convertible = ['image/jpeg', 'image/jpg', 'image/png'];

        if (!in_array($mime, $convertible)) {
            // GIF, WebP, dll — skip konversi, return as-is
            $result['success']       = true;
            $result['message']       = 'File tidak perlu dikonversi (' . $mime . ')';
            $result['webp_path']     = $source_path;
            $result['relative_path'] = $this->get_relative_path($source_path);
            return $result;
        }

        // Cek library tersedia
        if (!class_exists('WebPConvert\WebPConvert')) {
            $result['message']       = 'WebPConvert library tidak tersedia';
            $result['success']       = false;
            return $result;
        }

        // Path tujuan (.webp)
        $destination = $this->get_webp_destination($source_path);

        try {
            // Opsi konversi
            $options = [
                'quality'         => $this->quality,
                'max-width'       => $this->max_width,
                'max-height'      => $this->max_height,
                'auto-limit'      => true,
                'sharp-yuv'       => true,
                'converters'      => [
                    'cwebp',
                    'vips',
                    'imagick',
                    'gmagick',
                    'imagemagick',
                    'graphicsmagick',
                    'ffmpeg',
                    'gd',
                ],
                'metadata'        => 'none',  // strip metadata untuk hemat
            ];

            // Convert
            WebPConvert::convert($source_path, $destination, $options);

            // Cek hasil
            if (!file_exists($destination)) {
                throw new \Exception('File WebP tidak dihasilkan');
            }

            // Cek ukuran — kalau WebP lebih besar dari original, revert
            $orig_size = filesize($source_path);
            $webp_size = filesize($destination);

            if ($webp_size >= $orig_size && $orig_size > 0) {
                // WebP lebih besar — hapus & pakai original
                @unlink($destination);

                $result['success']       = true;
                $result['message']       = 'WebP tidak menguntungkan (ukuran lebih besar), pakai original';
                $result['webp_path']     = $source_path;
                $result['relative_path'] = $this->get_relative_path($source_path);
                return $result;
            }

            // Sukses konversi
            $result['success']       = true;
            $result['webp_path']     = $destination;
            $result['relative_path'] = $this->get_relative_path($destination);
            $result['message']       = 'Konversi berhasil. Original: ' . $this->format_bytes($orig_size) 
                                     . ' → WebP: ' . $this->format_bytes($webp_size);

            // Hapus file original
            if ($this->delete_original) {
                @unlink($source_path);
            }

            return $result;

        } catch (\Exception $e) {
            $result['success'] = false;
            $result['message'] = 'Konversi gagal: ' . $e->getMessage();
            log_message('error', 'WebP Convert error: ' . $e->getMessage());
            return $result;
        }
    }

    /**
     * Konversi + return path untuk disimpan di database
     * 
     * @param string $relative_source  Relative path dari FCPATH (misal: 'assets/uploads/gallery/foto.jpg')
     * @return array  ['success' => bool, 'path' => string (relative), 'message' => string]
     */
    public function convert_and_get_path($relative_source) {
        $absolute = FCPATH . ltrim($relative_source, '/');
        $result = $this->convert_to_webp($absolute);

        return [
            'success' => $result['success'],
            'path'    => $result['relative_path'] ?? $relative_source,
            'message' => $result['message'],
        ];
    }

    /**
     * Cek MIME type file
     */
    protected function get_mime_type($file_path) {
        if (!file_exists($file_path)) return null;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file_path);
        finfo_close($finfo);

        return $mime;
    }

    /**
     * Generate path tujuan .webp
     */
    protected function get_webp_destination($source_path) {
        $info = pathinfo($source_path);
        $dir  = $info['dirname'];
        $name = $info['filename'];

        return $dir . '/' . $name . '.webp';
    }

    /**
     * Convert absolute path ke relative dari FCPATH
     */
    protected function get_relative_path($absolute_path) {
        $fcp = str_replace('\\', '/', FCPATH);
        $abs = str_replace('\\', '/', $absolute_path);

        if (strpos($abs, $fcp) === 0) {
            return substr($abs, strlen($fcp));
        }

        return $abs;
    }

    /**
     * Format bytes ke human-readable
     */
    protected function format_bytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Setter untuk kualitas
     */
    public function set_quality($quality) {
        $this->quality = max(0, min(100, (int)$quality));
        return $this;
    }

    /**
     * Setter untuk delete_original
     */
    public function set_delete_original($delete) {
        $this->delete_original = (bool)$delete;
        return $this;
    }

    /**
     * Setter untuk max_width
     */
    public function set_max_width($width) {
        $this->max_width = $width ? (int)$width : null;
        return $this;
    }

    /**
     * Setter untuk max_height
     */
    public function set_max_height($height) {
        $this->max_height = $height ? (int)$height : null;
        return $this;
    }
}

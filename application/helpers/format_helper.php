<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Format Helper — helper untuk formatting tampilan
 */

if (!function_exists('format_date')) {
    /**
     * Format tanggal ke format Indonesia
     */
    function format_date($date, $format = 'd F Y') {
        if (empty($date) || $date === '0000-00-00') return '-';
        
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        if ($timestamp === false) return '-';
        
        $formatted = date($format, $timestamp);
        
        // Ganti nama bulan Inggris ke Indonesia
        $english = ['January','February','March','April','May','June',
                    'July','August','September','October','November','December'];
        
        return str_replace($english, $bulan, $formatted);
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format tanggal & waktu Indonesia
     */
    function format_datetime($datetime, $with_day = false) {
        if (empty($datetime)) return '-';
        
        $hari = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa',
                 'Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
        
        $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
        if ($timestamp === false) return '-';
        
        $day_name = $hari[date('l', $timestamp)] ?? '';
        $date_str = date('d', $timestamp) . ' ' . $bulan[(int)date('n', $timestamp)] . ' ' . date('Y', $timestamp);
        $time_str = date('H:i', $timestamp);
        
        return ($with_day ? $day_name . ', ' : '') . $date_str . ' pukul ' . $time_str;
    }
}

if (!function_exists('format_time_ago')) {
    /**
     * Format waktu relatif (contoh: "5 menit yang lalu")
     */
    function format_time_ago($datetime) {
        if (empty($datetime)) return '-';
        
        $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
        if ($timestamp === false) return '-';
        
        $diff = time() - $timestamp;
        
        if ($diff < 60) return 'baru saja';
        if ($diff < 3600) return floor($diff / 60) . ' menit yang lalu';
        if ($diff < 86400) return floor($diff / 3600) . ' jam yang lalu';
        if ($diff < 604800) return floor($diff / 86400) . ' hari yang lalu';
        if ($diff < 2592000) return floor($diff / 604800) . ' minggu yang lalu';
        if ($diff < 31536000) return floor($diff / 2592000) . ' bulan yang lalu';
        return floor($diff / 31536000) . ' tahun yang lalu';
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format angka ke Rupiah
     */
    function format_rupiah($number, $with_prefix = true) {
        $formatted = number_format((float)$number, 0, ',', '.');
        return $with_prefix ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('format_number')) {
    /**
     * Format angka dengan pemisah ribuan
     */
    function format_number($number, $decimals = 0) {
        return number_format((float)$number, $decimals, ',', '.');
    }
}

if (!function_exists('format_phone')) {
    /**
     * Format nomor telepon Indonesia
     */
    function format_phone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 2) === '62') {
            $phone = '0' . substr($phone, 2);
        }
        
        if (strlen($phone) >= 10) {
            return substr($phone, 0, 4) . '-' . substr($phone, 4, 4) . '-' . substr($phone, 8);
        }
        
        return $phone;
    }
}

if (!function_exists('format_bytes')) {
    /**
     * Format ukuran file
     */
    function format_bytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('format_initial')) {
    /**
     * Ambil inisial dari nama (untuk avatar)
     */
    function format_initial($name, $max = 2) {
        if (empty($name)) return '?';
        
        $words = preg_split('/\s+/', trim($name));
        $initial = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initial .= strtoupper(substr($word, 0, 1));
                if (strlen($initial) >= $max) break;
            }
        }
        
        return $initial ?: '?';
    }
}

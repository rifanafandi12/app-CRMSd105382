<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Custom Helper
 * 
 * Berisi fungsi-fungsi utility umum untuk aplikasi.
 */

// ==============================================
// LOGGING
// ==============================================
if (!function_exists('log_activity')) {
    /**
     * Log aktivitas user ke tabel log_activities
     * 
     * @param int    $user_id
     * @param string $activity_type
     * @param string $description
     * @return bool
     */
    function log_activity($user_id, $activity_type, $description) {
        $CI =& get_instance();

        $data = [
            'id_user'       => $user_id,
            'activity_type' => $activity_type,
            'description'   => $description,
            'ip_address'    => $CI->input->ip_address(),
            'user_agent'    => substr($CI->input->user_agent() ?? '', 0, 255),
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        // Cek tabel log_activities ada
        if ($CI->db->table_exists('log_activities')) {
            return $CI->db->insert('log_activities', $data);
        }

        return false;
    }
}

// ==============================================
// CATEGORY COLORS
// ==============================================
if (!function_exists('get_category_color')) {
    /**
     * Dapatkan warna berdasarkan nama kategori
     * 
     * @param string $category
     * @param array  $map  Custom map (opsional)
     * @return string  primary|success|warning|danger|accent|neutral
     */
    function get_category_color($category, $map = []) {
        if (empty($map)) {
            $map = [
                // Announcements
                'akademik'  => 'primary',
                'kesiswaan' => 'success',
                'umum'      => 'neutral',
                'event'     => 'accent',
                'libur'     => 'warning',
                // Gallery
                'kegiatan'  => 'primary',
                'prestasi'  => 'accent',
                'fasilitas' => 'success',
                'acara'     => 'warning',
                'lainnya'   => 'neutral',
                // Aspirations
                'sarana'    => 'primary',
                'pembelajaran' => 'success',
                'kesiswaan_asp' => 'warning',
            ];
        }
        return $map[$category] ?? 'neutral';
    }
}

// ==============================================
// ONLINE STATUS
// ==============================================
if (!function_exists('get_online_status')) {
    /**
     * Cek apakah user sedang online
     * 
     * @param int $user_id
     * @return bool
     */
    function get_online_status($user_id) {
        $CI =& get_instance();
        $CI->db->where('id_user', $user_id);
        $query = $CI->db->get('user_status');

        if ($query->num_rows() > 0) {
            return $query->row()->is_online == 1;
        }
        return false;
    }
}

// ==============================================
// TIME ELAPSED
// ==============================================
if (!function_exists('time_elapsed_string')) {
    /**
     * Format waktu relatif dalam bahasa Indonesia
     * Contoh: "5 menit yang lalu"
     * 
     * @param string $datetime
     * @param bool   $full  Tampilkan detail lengkap
     * @return string
     */
    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'tahun',
            'm' => 'bulan',
            'w' => 'minggu',
            'd' => 'hari',
            'h' => 'jam',
            'i' => 'menit',
            's' => 'detik',
        ];

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v;
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' yang lalu' : 'baru saja';
    }
}

// ==============================================
// FORMAT RUPIAH
// ==============================================
if (!function_exists('format_idr')) {
    /**
     * Format angka jadi format Rupiah
     * 
     * @param float $number
     * @return string
     */
    function format_idr($number) {
        return 'Rp ' . number_format((float)$number, 0, ',', '.');
    }
}

// ==============================================
// INITIALS
// ==============================================
if (!function_exists('get_initials')) {
    /**
     * Ambil inisial dari nama (untuk avatar)
     * 
     * @param string $name
     * @param int    $max
     * @return string
     */
    function get_initials($name, $max = 2) {
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

// ==============================================
// ACTIVE LINK
// ==============================================
if (!function_exists('is_active_menu')) {
    /**
     * Cek apakah URI saat ini aktif untuk class menu
     * 
     * @param string|array $uri
     * @param string       $current_uri
     * @return bool
     */
    function is_active_menu($uri, $current_uri = null) {
        if ($current_uri === null) {
            $CI =& get_instance();
            $current_uri = $CI->uri->uri_string();
        }

        if (is_array($uri)) {
            foreach ($uri as $u) {
                if (is_active_menu($u, $current_uri)) return true;
            }
            return false;
        }

        if ($uri === $current_uri) return true;
        if (strpos($current_uri, $uri . '/') === 0) return true;
        return false;
    }
}

// ==============================================
// TRUNCATE
// ==============================================
if (!function_exists('truncate_text')) {
    /**
     * Potong teks dengan aman
     * 
     * @param string $text
     * @param int    $length
     * @param string $suffix
     * @return string
     */
    function truncate_text($text, $length = 100, $suffix = '...') {
        $text = strip_tags($text ?? '');
        if (strlen($text) <= $length) return $text;
        return substr($text, 0, $length) . $suffix;
    }
}

// ==============================================
// PERMISSION CHECK
// ==============================================
if (!function_exists('has_role')) {
    /**
     * Cek apakah user memiliki role tertentu
     * 
     * @param string|array $roles
     * @return bool
     */
    function has_role($roles) {
        $CI =& get_instance();
        $user_role = $CI->session->userdata('role');
        if (!$user_role) return false;

        if (is_array($roles)) {
            return in_array($user_role, $roles);
        }
        return $user_role === $roles;
    }
}

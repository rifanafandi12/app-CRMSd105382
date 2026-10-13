<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Rate Limiter sederhana berbasis file/session
 * 
 * Untuk production, gunakan Redis atau Memcached.
 */
class Rate_limiter {

    protected $CI;
    protected $storage_path;

    public function __construct() {
        $this->CI =& get_instance();
        $this->storage_path = APPPATH . 'cache/rate_limiter/';
        
        if (!is_dir($this->storage_path)) {
            @mkdir($this->storage_path, 0755, true);
        }
    }

    /**
     * Cek apakah aksi masih dalam batas
     * 
     * @param string $key   Identifier (misal: 'login_' . ip)
     * @param int    $limit Max attempts
     * @param int    $window Time window in seconds
     * @return bool  TRUE jika masih boleh, FALSE jika melebihi batas
     */
    public function attempt($key, $limit = 5, $window = 300) {
        $file = $this->storage_path . md5($key) . '.json';
        $now = time();
        $data = ['attempts' => [], 'blocked_until' => 0];

        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true) ?: $data;
        }

        // Cek apakah masih diblokir
        if ($data['blocked_until'] > $now) {
            return false;
        }

        // Filter attempts dalam window
        $data['attempts'] = array_filter($data['attempts'], function($time) use ($now, $window) {
            return ($now - $time) < $window;
        });

        // Tambah attempt baru
        $data['attempts'][] = $now;

        // Cek limit
        if (count($data['attempts']) > $limit) {
            $data['blocked_until'] = $now + $window;
            file_put_contents($file, json_encode($data));
            return false;
        }

        file_put_contents($file, json_encode($data));
        return true;
    }

    /**
     * Reset rate limit untuk key tertentu
     */
    public function reset($key) {
        $file = $this->storage_path . md5($key) . '.json';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Berapa detik tersisa sampai bisa dicoba lagi
     */
    public function retry_after($key) {
        $file = $this->storage_path . md5($key) . '.json';
        if (!file_exists($file)) return 0;

        $data = json_decode(file_get_contents($file), true);
        if (!$data || empty($data['blocked_until'])) return 0;

        $remaining = $data['blocked_until'] - time();
        return $remaining > 0 ? $remaining : 0;
    }
}

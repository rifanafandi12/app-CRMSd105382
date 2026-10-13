<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Security Hook — dijalankan setelah controller constructor
 */
class Security_hook {

    /**
     * @var CI_Controller|null
     */
    protected $CI;

    public function __construct() {
        // Jangan assign langsung — ambil saat method dipanggil
        $this->CI = null;
    }

    /**
     * Ambil instance CI dengan aman
     * @return CI_Controller|null
     */
    private function get_ci() {
        if ($this->CI === null) {
            // Cek apakah get_instance() sudah tersedia & valid
            if (function_exists('get_instance')) {
                $instance = get_instance();
                // Pastikan instance adalah objek, bukan null
                if (is_object($instance)) {
                    $this->CI = $instance;
                }
            }
        }
        return $this->CI;
    }

    /**
     * Cek request yang masuk untuk indikasi serangan
     */
    public function check_request() {
        $CI = $this->get_ci();

        // Jika CI belum siap, skip — jangan crash
        if ($CI === null) {
            return;
        }

        // Pastikan property yang dibutuhkan ada
        if (!isset($CI->input) || !is_object($CI->input)) {
            return;
        }

        try {
            // Cek User Agent — block bot / scanner umum
            $ua = $CI->input->user_agent();
            $blocked_agents = ['sqlmap', 'nikto', 'nmap', 'masscan', 'acunetix', 'netsparker'];

            if (!empty($ua)) {
                foreach ($blocked_agents as $agent) {
                    if (stripos($ua, $agent) !== false) {
                        log_message('error', "Blocked suspicious user agent: {$ua}");
                        // Jangan langsung show_error di hook, cukup log
                        // karena bisa mengganggu proses lain
                        break;
                    }
                }
            }

            // Cek path traversal di URL
            if (isset($CI->uri) && is_object($CI->uri)) {
                $uri = $CI->uri->uri_string();
                if (!empty($uri)) {
                    if (preg_match('/\.\.\//', $uri) || preg_match('/%2e%2e/i', $uri)) {
                        log_message('error', "Blocked path traversal attempt: {$uri}");
                        show_error('Permintaan tidak valid', 400);
                    }
                }
            }

            // Cek SQL injection pattern
            $this->detect_sql_injection($CI);

        } catch (Exception $e) {
            // Jangan biarkan hook crash aplikasi
            log_message('error', 'Security hook error: ' . $e->getMessage());
        }
    }

    /**
     * Deteksi pola SQL injection sederhana
     */
    private function detect_sql_injection($CI) {
        if (!isset($CI->input) || !is_object($CI->input)) {
            return;
        }

        $get  = method_exists($CI->input, 'get')  ? ($CI->input->get()  ?: []) : [];
        $post = method_exists($CI->input, 'post') ? ($CI->input->post() ?: []) : [];

        $inputs = array_merge($get, $post);

        if (empty($inputs)) {
            return;
        }

        $patterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bSELECT\b.*\bFROM\b.*\bWHERE\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bUPDATE\b.*\bSET\b)/i',
            '/(\-\-|\#|\/\*)/',
            '/(\bEXEC\b|\bEXECUTE\b)/i',
        ];

        foreach ($inputs as $key => $value) {
            if (!is_string($value)) continue;

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    log_message('error', "Possible SQL injection detected in field '{$key}'");
                    break;
                }
            }
        }
    }

    /**
     * Log response untuk audit
     */
    public function log_response() {
        $CI = $this->get_ci();

        if ($CI === null || !isset($CI->uri) || !is_object($CI->uri)) {
            return;
        }

        try {
            $uri    = $CI->uri->uri_string();
            $method = isset($CI->input) && is_object($CI->input)
                    ? $CI->input->method()
                    : 'GET';
            $ip     = isset($CI->input) && is_object($CI->input)
                    ? $CI->input->ip_address()
                    : '-';
            $user   = isset($CI->session) && is_object($CI->session)
                    ? ($CI->session->userdata('user_id') ?: 'guest')
                    : 'guest';

            $sensitive = ['auth/login', 'users', 'profile/update'];
            foreach ($sensitive as $path) {
                if (strpos($uri, $path) !== false) {
                    log_message('info', "[{$method}] {$uri} | user={$user} | ip={$ip}");
                    break;
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Security hook log_response error: ' . $e->getMessage());
        }
    }
}

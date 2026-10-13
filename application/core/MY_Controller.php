<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Base Controller dengan Security Layer
 * 
 * Semua controller harus extend class ini untuk mendapatkan:
 * - Authentication check
 * - Role-based access control
 * - CSRF protection
 * - Rate limiting
 * - Security headers
 */
class MY_Controller extends CI_Controller
{

	protected $user_id;
	protected $user_role;
	protected $user_name;
	protected $is_logged_in = false;

	public function __construct()
	{
		parent::__construct();

		// Load helper keamanan
		$this->load->helper(['security', 'custom', 'format']);
		$this->load->library(['session', 'form_validation', 'Rate_limiter']);

		// Set security headers
		$this->set_security_headers();

		// Cek autentikasi
		$this->check_authentication();

		// Set data user untuk view
		$this->set_user_data();
	}

	/**
	 * Set HTTP security headers
	 * 
	 * Semua asset di-serve dari domain sendiri, kecuali font dari Google Fonts.
	 */
	private function set_security_headers()
	{
		// ==============================================
		// Basic security headers
		// ==============================================
		$this->output->set_header('X-Frame-Options: SAMEORIGIN');
		$this->output->set_header('X-Content-Type-Options: nosniff');
		$this->output->set_header('X-XSS-Protection: 1; mode=block');
		$this->output->set_header('Referrer-Policy: strict-origin-when-cross-origin');
		$this->output->set_header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

		// ==============================================
		// Content Security Policy
		// ==============================================
		$is_production = (ENVIRONMENT === 'production');

		// Socket.io URL (untuk development: localhost:3000, production: domain)
		$socket_url = getenv('SOCKET_URL') ?: 'http://localhost:3000';

		// Parse socket URL untuk dapat host
		$socket_host_http  = $socket_url;
		$socket_host_ws    = str_replace('http://', 'ws://',  $socket_url);
		$socket_host_wss   = str_replace('http://', 'wss://', $socket_url);
		$socket_host_https = str_replace('http://', 'https://', $socket_url);

		// Build connect-src
		$connect_src = "'self' {$socket_host_http} {$socket_host_ws}";

		if ($is_production) {
			$connect_src .= " {$socket_host_wss} {$socket_host_https}";
		}

		// ==============================================
		// CSP Directive
		// ==============================================
		// - script-src: 'unsafe-inline' dibutuhkan karena kita inline script (Chart.js init, dsb)
		//              'unsafe-eval' dibutuhkan kalau ada library yang pakai eval (jarang)
		// - style-src:  Google Fonts CSS
		// - font-src:   Google Fonts file font
		// - img-src:    'self' + data: untuk base64 + blob: untuk preview upload
		// - connect-src: self + socket.io server (http & ws)
		// ==============================================
		$csp = "default-src 'self'; "
			. "script-src 'self' 'unsafe-inline'; "
			. "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
			. "font-src 'self' https://fonts.gstatic.com data:; "
			. "img-src 'self' data: blob:; "
			. "connect-src {$connect_src}; "
			. "media-src 'self' data: blob:; "
			. "object-src 'none'; "
			. "frame-ancestors 'self'; "
			. "base-uri 'self'; "
			. "form-action 'self';";

		$this->output->set_header('Content-Security-Policy: ' . $csp);

		// ==============================================
		// HSTS (hanya production + HTTPS)
		// ==============================================
		if ($is_production) {
			$this->output->set_header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
		}
	}

	/**
	 * Cek apakah user sudah login
	 */
	private function check_authentication()
	{
		$this->is_logged_in = (bool) $this->session->userdata('logged_in');

		if ($this->is_logged_in) {
			$this->user_id   = $this->session->userdata('user_id');
			$this->user_role = $this->session->userdata('role');
			$this->user_name = $this->session->userdata('name');

			// Regenerate session ID secara berkala (setiap 5 menit)
			$last_regen = $this->session->userdata('last_regenerate');
			if (!$last_regen || (time() - $last_regen) > 300) {
				$this->session->sess_regenerate(TRUE);
				$this->session->set_userdata('last_regenerate', time());
			}
		}
	}

	/**
	 * Set user data untuk dikirim ke view
	 */
	private function set_user_data()
	{
		$this->data = [
			'user_id'       => $this->user_id,
			'user_role'     => $this->user_role,
			'user_name'     => $this->user_name,
			'is_logged_in'  => $this->is_logged_in,
		];
	}

	/**
	 * Require login — redirect ke login jika belum
	 */
	protected function require_login()
	{
		if (!$this->is_logged_in) {
			if ($this->input->is_ajax_request()) {
				$this->json_error('Silakan login terlebih dahulu', 401);
			}
			$this->session->set_flashdata('redirect_after_login', current_url());
			redirect('auth/login');
		}
	}

	/**
	 * Require role tertentu
	 */
	protected function require_role($roles)
	{
		$this->require_login();

		if (is_string($roles)) {
			$roles = [$roles];
		}

		if (!in_array($this->user_role, $roles)) {
			if ($this->input->is_ajax_request()) {
				$this->json_error('Akses ditolak', 403);
			}
			show_error('Anda tidak memiliki akses ke halaman ini', 403, 'Akses Ditolak');
		}
	}

	/**
	 * Require role admin
	 */
	protected function require_admin()
	{
		$this->require_role('admin');
	}

	/**
	 * JSON response success
	 */
	protected function json_success($data = [], $message = 'Success')
	{
		$this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode([
				'status'  => 'success',
				'message' => $message,
				'data'    => $data,
			]));
	}

	/**
	 * JSON response error
	 */
	protected function json_error($message = 'Error', $code = 400, $data = [])
	{
		$this->output
			->set_content_type('application/json')
			->set_status_header($code)
			->set_output(json_encode([
				'status'  => 'error',
				'message' => $message,
				'data'    => $data,
			]));
	}

	/**
	 * Verify CSRF token untuk AJAX request
	 */
	protected function verify_csrf()
	{
		$token = $this->input->server('HTTP_X_CSRF_TOKEN')
			?: $this->input->post('csrf_token');

		if (!$token || $token !== $this->security->get_csrf_hash()) {
			$this->json_error('CSRF token tidak valid', 403);
		}
	}

	/**
	 * Render view dengan layout
	 */
	/**
	 * Render view dengan layout
	 * Otomatis inject user data global ke semua view
	 * 
	 * @param string $view  Nama view relatif dari folder views/
	 * @param array  $data  Data tambahan
	 */
	protected function render($view, $data = [])
	{
		// Data global yang selalu tersedia di semua view
		$global = [
			'user_id'      => $this->user_id,
			'user_name'    => $this->user_name,
			'user_role'    => $this->user_role,
			'user_email'   => $this->session->userdata('email'),
			'is_logged_in' => $this->is_logged_in,
		];

		// Gabungkan dengan $this->data dan $data yang di-pass
		$data = array_merge($global, $this->data ?? [], $data);

		$this->load->view('layouts/app', array_merge($data, [
			'content_view' => $view,
		]));
	}

	

}

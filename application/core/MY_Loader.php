<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MY_Loader — Custom Loader
 * 
 * Menambahkan method yang bisa diakses dari view via $this->method()
 */
class MY_Loader extends CI_Loader
{

	/**
	 * Dapatkan warna kategori — bisa dipanggil dari view
	 * 
	 * @param string $category
	 * @return string
	 */
	public function get_category_color($category)
	{
		$map = [
			'akademik'  => 'primary',
			'kesiswaan' => 'success',
			'umum'      => 'neutral',
			'event'     => 'accent',
			'libur'     => 'warning',
			'kegiatan'  => 'primary',
			'prestasi'  => 'accent',
			'fasilitas' => 'success',
			'acara'     => 'warning',
			'lainnya'   => 'neutral',
		];
		return $map[$category] ?? 'neutral';
	}

	/**
	 * Format Rupiah
	 */
	public function rupiah($number)
	{
		return 'Rp ' . number_format((float)$number, 0, ',', '.');
	}

	/**
	 * Ambil inisial nama
	 */
	public function initials($name, $max = 2)
	{
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

	/**
	 * Cek apakah menu aktif
	 */
	public function is_menu_active($uri)
	{
		$CI = &get_instance();
		$current = $CI->uri->uri_string();
		if (is_array($uri)) {
			foreach ($uri as $u) {
				if ($this->is_menu_active($u)) return true;
			}
			return false;
		}
		if ($uri === $current) return true;
		return strpos($current, $uri . '/') === 0;
	}

	/**
	 * Format tanggal — proxy ke helper
	 */
	public function format_date($date, $format = 'd F Y')
	{
		return format_date($date, $format);
	}

	/**
	 * Format waktu relatif
	 */
	public function format_time_ago($datetime)
	{
		return format_time_ago($datetime);
	}

	/**
	 * Cek apakah user punya role tertentu
	 */
	public function has_role($roles)
	{
		return has_role($roles);
	}
}

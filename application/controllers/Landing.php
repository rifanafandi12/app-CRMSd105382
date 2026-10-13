<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Landing extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();

		// Landing page TIDAK butuh login
		// Tapi kita tetap butuh session untuk cek status login
		$this->load->model('User_model');
		$this->load->model('Aspiration_model');
		$this->load->model('Gallery_model');
	}

	/**
	 * Halaman utama landing
	 */
	public function index()
	{
		$data['title'] = 'SD Negeri 105382 Galang — Sekolah Unggul dan Berkarakter';
		$data['page_type'] = 'landing';

		// Ambil pengumuman terbaru yang dipublish
		$this->db->where('is_published', 1);
		$this->db->where('expiry_date >=', date('Y-m-d'));
		$this->db->order_by('posted_date', 'DESC');
		$this->db->limit(5);
		$data['announcements'] = $this->db->get('announcements')->result();

		// Info sekolah
		$data['school_name']    = 'SD Negeri 105382 Galang';
		$data['school_address'] = 'Jalan Kesatuan Gang Inpres, Desa Galang Kota, Kecamatan Galang, Kabupaten Deli Serdang, Sumatera Utara';
		$data['school_email']   = 'sdn105382@gmail.com';
		$data['total_students'] = $this->db->count_all('students');
		$data['total_teachers'] = $this->db->where('role', 'teacher')->count_all_results('users');

		// Visi dan Misi
		$data['vision'] = 'Terwujudnya peserta didik yang beriman, bertaqwa, cerdas, terampil, dan berkarakter (profil Pelajar Pancasila).';
		$data['missions'] = [
			'Menyelenggarakan pembelajaran aktif, kreatif, dan inovatif',
			'Menumbuhkan budi pekerti luhur',
			'Menjalin kerjasama yang harmonis dengan orang tua dan masyarakat.',
		];

		$this->render_landing('landing/index', $data);
	}

	/**
	 * Halaman daftar pengumuman
	 */
	public function pengumuman($id = null)
	{
		if ($id) {
			$this->db->where('id_announcement', $id);
			$this->db->where('is_published', 1);
			$data['announcement'] = $this->db->get('announcements')->row();

			if (!$data['announcement']) show_404();

			// Increment view
			$this->db->set('view_count', 'view_count+1', false);
			$this->db->where('id_announcement', $id);
			$this->db->update('announcements');

			$data['title'] = $data['announcement']->title;
			$data['page_type'] = 'landing';
			$this->render_landing('landing/pengumuman_detail', $data);
		} else {
			$this->db->where('is_published', 1);
			$this->db->where('expiry_date >=', date('Y-m-d'));
			$this->db->order_by('posted_date', 'DESC');
			$data['announcements'] = $this->db->get('announcements')->result();
			$data['title'] = 'Pengumuman';
			$data['page_type'] = 'landing';
			$this->render_landing('landing/pengumuman', $data);
		}
	}

	/**
	 * Halaman aspirasi (form)
	 */
	public function aspirasi()
	{
		// Cek login
		if (!$this->session->userdata('logged_in')) {
			if ($this->input->is_ajax_request()) {
				$this->json_error('Silakan login terlebih dahulu', 401, [
					'redirect' => base_url('auth/login'),
				]);
				return;
			}
			redirect(base_url('auth/login'));
		}

		$this->form_validation->set_rules('subject', 'Subjek', 'required|trim|max_length[200]');
		$this->form_validation->set_rules('content', 'Pesan', 'required|trim|max_length[5000]');
		$this->form_validation->set_rules('category', 'Kategori', 'required|in_list[akademik,fasilitas,kesiswaan,umum]');

		if ($this->form_validation->run() === FALSE) {
			$this->json_error(strip_tags(validation_errors()), 422);
			return;
		}

		$data = [
			'id_parent'   => $this->session->userdata('user_id'),
			'subject'     => $this->input->post('subject', TRUE),
			'content'     => $this->input->post('content', TRUE),
			'category'    => $this->input->post('category', TRUE),
			'status'      => 'pending',
			'created_at'  => date('Y-m-d H:i:s'),
		];

		// Handle file upload
		if (!empty($_FILES['attachment']['name'])) {
			$config['upload_path']   = './assets/uploads/aspirations/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif|webp|pdf|doc|docx';
			$config['max_size']      = 2048;
			$config['encrypt_name']  = TRUE;
			$config['file_ext_tolower'] = TRUE;

			if (!is_dir($config['upload_path'])) {
				mkdir($config['upload_path'], 0755, true);
			}

			$this->load->library('upload', $config);

			if ($this->upload->do_upload('attachment')) {
				$upload_data = $this->upload->data();
				$original_relative = 'assets/uploads/aspirations/' . $upload_data['file_name'];

				// Konversi kalau gambar
				$is_image = in_array(strtolower($upload_data['file_ext']), ['.jpg', '.jpeg', '.png']);

				if ($is_image) {
					$this->load->library('image_converter', [
						'quality'         => 80,
						'delete_original' => true,
						'max_width'       => 1920,
						'max_height'      => 1920,
					]);

					$conversion = $this->image_converter->convert_and_get_path($original_relative);
					if ($conversion['success']) {
						$original_relative = $conversion['path'];
					}
				}

				$data['attachment'] = $original_relative;
			}
		}

		if ($this->db->insert('aspirations', $data)) {
			$this->json_success([], 'Aspirasi berhasil dikirim. Terima kasih atas masukannya.');
		} else {
			$this->json_error('Gagal mengirim aspirasi. Silakan coba lagi.', 500);
		}
	}

	public function gallery()
	{
		$data['title'] = 'Galeri Kegiatan — SD Negeri 105382 Galang';
		$data['page_type'] = 'landing';

		$page     = (int)($this->input->get('page') ?: 1);
		$category = $this->input->get('category') ?: 'all';
		$limit    = 12;
		$offset   = ($page - 1) * $limit;

		$data['gallery']      = $this->Gallery_model->get_all_gallery($limit, $offset, $category);
		$data['total']        = $this->Gallery_model->count_gallery($category);
		$data['current_page'] = $page;
		$data['total_pages']  = (int)ceil($data['total'] / $limit);
		$data['category']     = $category;
		$data['categories']   = $this->Gallery_model->get_categories();

		$this->render_landing('landing/gallery', $data);
	}

	public function gallery_detail($id)
	{
		$id = (int)$id;
		$data['gallery'] = $this->Gallery_model->get_gallery_by_id($id);

		if (!$data['gallery'] || !$data['gallery']->is_published) {
			show_404();
		}

		$this->Gallery_model->increment_view_count($id);

		// Related
		$this->db->select('gallery.*, users.name as uploader_name');
		$this->db->from('gallery');
		$this->db->join('users', 'gallery.uploaded_by = users.id_user', 'left');
		$this->db->where('gallery.category', $data['gallery']->category);
		$this->db->where('gallery.id_gallery !=', $id);
		$this->db->where('gallery.is_published', 1);
		$this->db->order_by('gallery.created_at', 'DESC');
		$this->db->limit(4);
		$data['related'] = $this->db->get()->result();

		$data['title'] = $data['gallery']->title . ' — Galeri SDN 105382 Galang';
		$data['page_type'] = 'landing';

		$this->render_landing('landing/gallery_detail', $data);
	}

	/**
	 * Helper: render landing dengan layout
	 */
	private function render_landing($view, $data = [])
	{
		$data = array_merge($this->data, $data);
		$this->load->view('layouts/landing', array_merge($data, [
			'content_view' => $view,
		]));
	}
}

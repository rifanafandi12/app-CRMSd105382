<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Announcements extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->require_login();
		$this->load->model('Announcement_model');
		$this->load->model('Class_model');
	}

	/**
	 * Map warna kategori — dikirim ke view sebagai array
	 * supaya bisa diakses tanpa $this->get_category_color()
	 */
	private function get_category_color_map()
	{
		return [
			'akademik'  => 'primary',
			'kesiswaan' => 'success',
			'umum'      => 'neutral',
			'event'     => 'accent',
			'libur'     => 'warning',
		];
	}

	// ==============================================
	// INDEX — daftar pengumuman
	// ==============================================
	public function index()
	{
		$data['title']     = 'Pengumuman Sekolah';
		$data['user_role'] = $this->user_role;  // ← TAMBAHKAN

		if ($this->user_role === 'admin') {
			$this->db->select('announcements.*, users.name as author_name');
			$this->db->from('announcements');
			$this->db->join('users', 'announcements.posted_by = users.id_user', 'left');
			$this->db->order_by('announcements.posted_date', 'DESC');
			$data['announcements'] = $this->db->get()->result();
		} else {
			$this->db->select('announcements.*, users.name as author_name');
			$this->db->from('announcements');
			$this->db->join('users', 'announcements.posted_by = users.id_user', 'left');
			$this->db->where('announcements.is_published', 1);
			$this->db->where('announcements.expiry_date >=', date('Y-m-d'));
			$this->db->order_by('announcements.posted_date', 'DESC');
			$data['announcements'] = $this->db->get()->result();
		}

		$data['categories'] = $this->get_categories();
		$data['classes']    = $this->Class_model->get_all_classes();
		$data['category_colors'] = $this->get_category_color_map();  // ← TAMBAHKAN

		$this->render('announcements/index', $data);
	}

	// ==============================================
	// ADD — buat pengumuman baru (POST)
	// ==============================================
	public function add()
	{
		if ($this->user_role !== 'admin') {
			redirect(base_url('announcements'));
		}

		// Rate limit
		$ip = $this->input->ip_address();
		if (!$this->rate_limiter->attempt("add_ann_{$ip}", 30, 300)) {
			$this->session->set_flashdata('error', 'Terlalu banyak permintaan.');
			redirect(base_url('announcements'));
		}

		$this->form_validation->set_rules('title',   'Judul',            'required|trim|max_length[200]');
		$this->form_validation->set_rules('content', 'Isi Pengumuman',   'required|trim');
		$this->form_validation->set_rules('category', 'Kategori',         'required|in_list[akademik,kesiswaan,umum,event,libur]');

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', strip_tags(validation_errors()));
			redirect(base_url('announcements'));
		}

		$data = [
			'title'           => trim($this->input->post('title')),
			'content'         => trim($this->input->post('content')),
			'category'        => $this->input->post('category'),
			'target_class_id' => $this->input->post('target_class_id') ?: null,
			'posted_by'       => $this->user_id,
			'posted_date'     => date('Y-m-d H:i:s'),
			'expiry_date'     => $this->input->post('expiry_date') ?: null,
			'is_published'    => $this->input->post('is_published') ? 1 : 0,
		];

		// Handle upload attachment
		if (!empty($_FILES['attachment']['name'])) {
			$upload = $this->handle_upload('attachment', 'announcements');
			if ($upload['status']) {
				$data['attachment'] = $upload['path'];
			} else {
				$this->session->set_flashdata('error', 'Upload gagal: ' . $upload['error']);
				redirect(base_url('announcements'));
			}
		}

		if ($this->db->insert('announcements', $data)) {
			$id = $this->db->insert_id();
			log_activity($this->user_id, 'announcement_add', "Menambah pengumuman: {$data['title']} (ID: {$id})");
			$this->session->set_flashdata('success', 'Pengumuman berhasil dibuat.');
		} else {
			$this->session->set_flashdata('error', 'Gagal membuat pengumuman.');
		}

		redirect(base_url('announcements'));
	}

	// ==============================================
	// EDIT — update pengumuman (POST)
	// ==============================================
	public function edit()
	{
		if ($this->user_role !== 'admin') {
			redirect(base_url('announcements'));
		}

		$id = (int)$this->input->post('id_announcement');
		if (!$id) {
			$this->session->set_flashdata('error', 'ID tidak valid.');
			redirect(base_url('announcements'));
		}

		$existing = $this->db->where('id_announcement', $id)->get('announcements')->row();
		if (!$existing) {
			$this->session->set_flashdata('error', 'Pengumuman tidak ditemukan.');
			redirect(base_url('announcements'));
		}

		$this->form_validation->set_rules('title',   'Judul',          'required|trim|max_length[200]');
		$this->form_validation->set_rules('content', 'Isi Pengumuman', 'required|trim');
		$this->form_validation->set_rules('category', 'Kategori',       'required');

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', strip_tags(validation_errors()));
			redirect(base_url('announcements'));
		}

		$update = [
			'title'           => trim($this->input->post('title')),
			'content'         => trim($this->input->post('content')),
			'category'        => $this->input->post('category'),
			'target_class_id' => $this->input->post('target_class_id') ?: null,
			'expiry_date'     => $this->input->post('expiry_date') ?: null,
			'is_published'    => $this->input->post('is_published') ? 1 : 0,
			'updated_at'      => date('Y-m-d H:i:s'),
		];

		// Handle upload baru
		if (!empty($_FILES['attachment']['name'])) {
			$upload = $this->handle_upload('attachment', 'announcements');
			if ($upload['status']) {
				// Hapus file lama
				if (!empty($existing->attachment) && file_exists(FCPATH . $existing->attachment)) {
					@unlink(FCPATH . $existing->attachment);
				}
				$update['attachment'] = $upload['path'];
			}
		}

		$this->db->where('id_announcement', $id);
		if ($this->db->update('announcements', $update)) {
			log_activity($this->user_id, 'announcement_edit', "Mengubah pengumuman: {$update['title']} (ID: {$id})");
			$this->session->set_flashdata('success', 'Pengumuman berhasil diperbarui.');
		} else {
			$this->session->set_flashdata('error', 'Gagal memperbarui pengumuman.');
		}

		redirect(base_url('announcements'));
	}

	// ==============================================
	// DELETE — hapus pengumuman (POST)
	// ==============================================
	public function delete()
	{
		if ($this->user_role !== 'admin') {
			redirect(base_url('announcements'));
		}

		$id = (int)$this->input->post('id_announcement');
		if (!$id) {
			$this->session->set_flashdata('error', 'ID tidak valid.');
			redirect(base_url('announcements'));
		}

		$ann = $this->db->where('id_announcement', $id)->get('announcements')->row();
		if (!$ann) {
			$this->session->set_flashdata('error', 'Pengumuman tidak ditemukan.');
			redirect(base_url('announcements'));
		}

		// Hapus file attachment kalau ada
		if (!empty($ann->attachment) && file_exists(FCPATH . $ann->attachment)) {
			@unlink(FCPATH . $ann->attachment);
		}

		$this->db->where('id_announcement', $id);
		if ($this->db->delete('announcements')) {
			log_activity($this->user_id, 'announcement_delete', "Menghapus pengumuman: {$ann->title}");
			$this->session->set_flashdata('success', 'Pengumuman berhasil dihapus.');
		} else {
			$this->session->set_flashdata('error', 'Gagal menghapus pengumuman.');
		}

		redirect(base_url('announcements'));
	}

	// ==============================================
	// DETAIL
	// ==============================================
	public function detail($id)
	{
		$id = (int)$id;

		$this->db->set('view_count', 'view_count+1', false);
		$this->db->where('id_announcement', $id);
		$this->db->update('announcements');

		$this->db->select('announcements.*, users.name as author_name');
		$this->db->from('announcements');
		$this->db->join('users', 'announcements.posted_by = users.id_user', 'left');
		$this->db->where('announcements.id_announcement', $id);
		$ann = $this->db->get()->row();

		if (!$ann) show_404();
		if ($this->user_role !== 'admin' && !$ann->is_published) show_404();

		$data['announcement']    = $ann;
		$data['title']           = $ann->title;
		$data['user_role']       = $this->user_role;              // ← TAMBAHKAN
		$data['category_colors'] = $this->get_category_color_map(); // ← TAMBAHKAN

		$this->render('announcements/detail', $data);
	}

	// ==============================================
	// TOGGLE PUBLISH (AJAX)
	// ==============================================
	public function toggle_publish()
	{
		if ($this->user_role !== 'admin') {
			$this->json_error('Akses ditolak', 403);
			return;
		}

		$id = (int)$this->input->post('id_announcement');
		if (!$id) {
			$this->json_error('ID tidak valid', 422);
			return;
		}

		$ann = $this->db->where('id_announcement', $id)->get('announcements')->row();
		if (!$ann) {
			$this->json_error('Pengumuman tidak ditemukan', 404);
			return;
		}

		$new_status = $ann->is_published ? 0 : 1;

		$this->db->where('id_announcement', $id);
		$this->db->update('announcements', [
			'is_published' => $new_status,
			'updated_at'   => date('Y-m-d H:i:s'),
		]);

		$this->json_success([
			'id'           => $id,
			'is_published' => $new_status,
		], $new_status ? 'Pengumuman dipublikasikan' : 'Pengumuman disembunyikan');
	}

	// ==============================================
	// HELPER: Upload file
	// ==============================================
	private function handle_upload($field_name, $subfolder)
	{
		$upload_path = FCPATH . 'assets/uploads/' . $subfolder . '/';

		if (!is_dir($upload_path)) {
			mkdir($upload_path, 0755, true);
		}

		$config = [
			'upload_path'      => $upload_path,
			'allowed_types'    => 'jpg|jpeg|png|gif|webp|pdf|doc|docx',
			'max_size'         => 5120,
			'encrypt_name'     => TRUE,
			'file_ext_tolower' => TRUE,
		];

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload($field_name)) {
			return ['status' => false, 'error' => strip_tags($this->upload->display_errors())];
		}

		$data = $this->upload->data();
		$original_relative = 'assets/uploads/' . $subfolder . '/' . $data['file_name'];

		// Cek tipe file — hanya konversi gambar
		$is_image = in_array(strtolower($data['file_ext']), ['.jpg', '.jpeg', '.png']);

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

		return [
			'status' => true,
			'path'   => $original_relative,
			'name'   => basename($original_relative),
		];
	}

	private function get_categories()
	{
		return [
			'akademik'  => 'Akademik',
			'kesiswaan' => 'Kesiswaan',
			'umum'      => 'Umum',
			'event'     => 'Event',
			'libur'     => 'Libur',
		];
	}
}

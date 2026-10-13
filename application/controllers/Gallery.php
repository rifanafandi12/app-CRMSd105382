<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gallery extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->require_login();
		$this->load->model('Gallery_model');
	}

	// ==============================================
	// INDEX — daftar galeri (admin)
	// ==============================================
	public function index()
	{
		if ($this->user_role !== 'admin') {
			redirect(base_url('dashboard'));
		}

		$data['title'] = 'Kelola Galeri';

		// Ambil semua galeri
		$this->db->select('gallery.*, users.name as uploader_name');
		$this->db->from('gallery');
		$this->db->join('users', 'gallery.uploaded_by = users.id_user', 'left');
		$this->db->order_by('gallery.created_at', 'DESC');
		$data['gallery'] = $this->db->get()->result();

		$data['categories'] = $this->get_categories();

		$this->render('gallery/index', $data);
	}

	// ==============================================
	// ADD — upload foto baru (POST)
	// ==============================================
	public function add()
	{
		if ($this->user_role !== 'admin') {
			redirect(base_url('dashboard'));
		}

		// Rate limit
		$ip = $this->input->ip_address();
		if (!$this->rate_limiter->attempt("add_gallery_{$ip}", 30, 300)) {
			$this->session->set_flashdata('error', 'Terlalu banyak permintaan. Coba lagi nanti.');
			redirect(base_url('gallery'));
		}

		$this->form_validation->set_rules('title',    'Judul',           'required|trim|max_length[200]');
		$this->form_validation->set_rules('category', 'Kategori',        'required|in_list[kegiatan,prestasi,fasilitas,acara,lainnya]');
		$this->form_validation->set_rules('description', 'Deskripsi',    'trim');

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', strip_tags(validation_errors()));
			redirect(base_url('gallery'));
		}

		// Upload file WAJIB
		if (empty($_FILES['image']['name'])) {
			$this->session->set_flashdata('error', 'Foto wajib diupload.');
			redirect(base_url('gallery'));
		}

		$upload = $this->handle_upload('image');
		if (!$upload['status']) {
			$this->session->set_flashdata('error', 'Upload gagal: ' . $upload['error']);
			redirect(base_url('gallery'));
		}

		$data = [
			'title'        => trim($this->input->post('title')),
			'description'  => trim($this->input->post('description') ?? ''),
			'image_path'   => $upload['path'],
			'category'     => $this->input->post('category'),
			'uploaded_by'  => $this->user_id,
			'is_published' => $this->input->post('is_published') ? 1 : 0,
			'created_at'   => date('Y-m-d H:i:s'),
		];

		if ($this->db->insert('gallery', $data)) {
			$id = $this->db->insert_id();
			log_activity($this->user_id, 'gallery_add', "Menambah galeri: {$data['title']} (ID: {$id})");
			$this->session->set_flashdata('success', 'Foto berhasil ditambahkan ke galeri.');
		} else {
			// Hapus file yang sudah diupload kalau insert gagal
			if (file_exists(FCPATH . $upload['path'])) {
				@unlink(FCPATH . $upload['path']);
			}
			$this->session->set_flashdata('error', 'Gagal menambahkan foto.');
		}

		redirect(base_url('gallery'));
	}

	// ==============================================
	// EDIT — update galeri (POST)
	// ==============================================
	public function edit()
	{
		if ($this->user_role !== 'admin') {
			redirect(base_url('dashboard'));
		}

		$id = (int)$this->input->post('id_gallery');
		if (!$id) {
			$this->session->set_flashdata('error', 'ID tidak valid.');
			redirect(base_url('gallery'));
		}

		$existing = $this->db->where('id_gallery', $id)->get('gallery')->row();
		if (!$existing) {
			$this->session->set_flashdata('error', 'Galeri tidak ditemukan.');
			redirect(base_url('gallery'));
		}

		$this->form_validation->set_rules('title',    'Judul',    'required|trim|max_length[200]');
		$this->form_validation->set_rules('category', 'Kategori', 'required|in_list[kegiatan,prestasi,fasilitas,acara,lainnya]');

		if ($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('error', strip_tags(validation_errors()));
			redirect(base_url('gallery'));
		}

		$update = [
			'title'        => trim($this->input->post('title')),
			'description'  => trim($this->input->post('description') ?? ''),
			'category'     => $this->input->post('category'),
			'is_published' => $this->input->post('is_published') ? 1 : 0,
			'updated_at'   => date('Y-m-d H:i:s'),
		];

		// Upload baru kalau ada
		if (!empty($_FILES['image']['name'])) {
			$upload = $this->handle_upload('image');
			if ($upload['status']) {
				// Hapus file lama
				$old_path = FCPATH . ltrim($existing->image_path, '/');
				if (!empty($existing->image_path) && file_exists($old_path)) {
					@unlink($old_path);
				}
				$update['image_path'] = $upload['path'];
			} else {
				$this->session->set_flashdata('error', 'Upload gagal: ' . $upload['error']);
				redirect(base_url('gallery'));
			}
		}

		$this->db->where('id_gallery', $id);
		if ($this->db->update('gallery', $update)) {
			log_activity($this->user_id, 'gallery_edit', "Mengubah galeri: {$update['title']} (ID: {$id})");
			$this->session->set_flashdata('success', 'Galeri berhasil diperbarui.');
		} else {
			$this->session->set_flashdata('error', 'Gagal memperbarui galeri.');
		}

		redirect(base_url('gallery'));
	}

	// ==============================================
	// DELETE — hapus galeri (POST)
	// ==============================================
	public function delete()
	{
		if ($this->user_role !== 'admin') {
			redirect(base_url('dashboard'));
		}

		$id = (int)$this->input->post('id_gallery');
		if (!$id) {
			$this->session->set_flashdata('error', 'ID tidak valid.');
			redirect(base_url('gallery'));
		}

		$item = $this->db->where('id_gallery', $id)->get('gallery')->row();
		if (!$item) {
			$this->session->set_flashdata('error', 'Galeri tidak ditemukan.');
			redirect(base_url('gallery'));
		}

		// Hapus file fisik
		$file_path = FCPATH . ltrim($item->image_path, '/');
		if (!empty($item->image_path) && file_exists($file_path)) {
			@unlink($file_path);
		}

		$this->db->where('id_gallery', $id);
		if ($this->db->delete('gallery')) {
			log_activity($this->user_id, 'gallery_delete', "Menghapus galeri: {$item->title}");
			$this->session->set_flashdata('success', 'Foto berhasil dihapus.');
		} else {
			$this->session->set_flashdata('error', 'Gagal menghapus foto.');
		}

		redirect(base_url('gallery'));
	}

	// ==============================================
	// DETAIL — detail galeri (admin)
	// ==============================================
	public function detail($id)
	{
		if ($this->user_role !== 'admin') {
			redirect(base_url('dashboard'));
		}

		$id = (int)$id;

		// Increment view
		$this->db->set('view_count', 'view_count+1', false);
		$this->db->where('id_gallery', $id);
		$this->db->update('gallery');

		$this->db->select('gallery.*, users.name as uploader_name');
		$this->db->from('gallery');
		$this->db->join('users', 'gallery.uploaded_by = users.id_user', 'left');
		$this->db->where('gallery.id_gallery', $id);
		$item = $this->db->get()->row();

		if (!$item) {
			show_404();
		}

		$data['item']  = $item;
		$data['title'] = $item->title;

		$this->render('gallery/detail', $data);
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

		$id = (int)$this->input->post('id_gallery');
		if (!$id) {
			$this->json_error('ID tidak valid', 422);
			return;
		}

		$item = $this->db->where('id_gallery', $id)->get('gallery')->row();
		if (!$item) {
			$this->json_error('Galeri tidak ditemukan', 404);
			return;
		}

		$new_status = $item->is_published ? 0 : 1;

		$this->db->where('id_gallery', $id);
		$this->db->update('gallery', [
			'is_published' => $new_status,
			'updated_at'   => date('Y-m-d H:i:s'),
		]);

		$this->json_success([
			'id'           => $id,
			'is_published' => $new_status,
		], $new_status ? 'Foto dipublikasikan' : 'Foto disembunyikan');
	}

	// ==============================================
	// HELPER: Upload file
	// ==============================================
	private function handle_upload($field_name)
	{
		$upload_path = FCPATH . 'assets/uploads/gallery/';

		if (!is_dir($upload_path)) {
			mkdir($upload_path, 0755, true);
		}

		$config = [
			'upload_path'      => $upload_path,
			'allowed_types'    => 'jpg|jpeg|png|gif|webp',
			'max_size'         => 5120, // 5MB
			'encrypt_name'     => TRUE,
			'file_ext_tolower' => TRUE,
			'max_width'        => 4000,
			'max_height'       => 4000,
		];

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload($field_name)) {
			return [
				'status' => false,
				'error'  => strip_tags($this->upload->display_errors()),
			];
		}

		$data = $this->upload->data();
		$original_relative = 'assets/uploads/gallery/' . $data['file_name'];

		// ==============================================
		// KONVERSI KE WEBP
		// ==============================================
		$this->load->library('image_converter', [
			'quality'         => 80,
			'delete_original' => true,
			'max_width'       => 1920,
			'max_height'      => 1920,
		]);

		$conversion = $this->image_converter->convert_and_get_path($original_relative);

		if (!$conversion['success']) {
			// Konversi gagal — log tapi tetap lanjut dengan file original
			log_message('error', 'WebP conversion failed for gallery: ' . $conversion['message']);
		}

		// Path final (bisa .webp atau tetap original)
		$final_path = $conversion['path'];

		return [
			'status' => true,
			'path'   => $final_path,
			'name'   => basename($final_path),
		];
	}

	// ==============================================
	// HELPER: Kategori
	// ==============================================
	private function get_categories()
	{
		return [
			'kegiatan'  => 'Kegiatan Sekolah',
			'prestasi'  => 'Prestasi',
			'fasilitas' => 'Fasilitas',
			'acara'     => 'Acara',
			'lainnya'   => 'Lainnya',
		];
	}
}

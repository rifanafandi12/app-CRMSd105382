<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->require_login();
        $this->load->model('User_model');
        $this->load->model('Student_model');
        $this->load->model('Chat_model');
    }

    // ==============================================
    // INDEX — Halaman profil
    // ==============================================
    public function index() {
        $data['title']     = 'Profil Saya';
        $data['user_role'] = $this->user_role;

        // Ambil user fresh dari DB
        $user = $this->db->where('id_user', $this->user_id)->get('users')->row();
        if (!$user) {
            show_error('User tidak ditemukan', 404);
        }

        $data['user'] = $user;

        // Statistik
        $data['total_messages_sent']     = $this->db->where('id_sender', $this->user_id)->count_all_results('chat_messages');
        $data['total_messages_received'] = $this->db->where('id_receiver', $this->user_id)->count_all_results('chat_messages');
        $data['total_aspirations']       = $this->db->where('id_parent', $this->user_id)->count_all_results('aspirations');

        // Info tambahan
        $data['last_login']   = $user->last_login;
        $data['member_since'] = $user->created_at;

        // Kalau parent — data anak
        if ($user->role === 'parent') {
            $data['children'] = $this->db
                ->select('students.*, classes.class_name')
                ->from('students')
                ->join('classes', 'students.id_class = classes.id_class', 'left')
                ->where('students.id_user_parent', $this->user_id)
                ->where('students.is_active', 1)
                ->get()->result();
        } else {
            $data['children'] = [];
        }

        // Kalau teacher — kelas yang diwalikan
        if ($user->role === 'teacher') {
            $data['my_classes'] = $this->db
                ->where('homeroom_teacher_id', $this->user_id)
                ->get('classes')->result();
        } else {
            $data['my_classes'] = [];
        }

        // Log aktivitas user ini (10 terakhir)
        $data['recent_activities'] = $this->db
            ->where('id_user', $this->user_id)
            ->order_by('created_at', 'DESC')
            ->limit(10)
            ->get('log_activities')->result();

        $this->render('profile/index', $data);
    }

    // ==============================================
    // UPDATE — Update profil (POST)
    // ==============================================
    public function update() {
        $this->form_validation->set_rules('name', 'Nama Lengkap', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|max_length[100]');
        $this->form_validation->set_rules('phone', 'No. Telepon', 'trim|max_length[20]');

        // Validasi unique email (kecuali milik sendiri)
        $newEmail = $this->input->post('email');
        $currentUser = $this->db->where('id_user', $this->user_id)->get('users')->row();

        if (!empty($newEmail) && $newEmail !== ($currentUser->email ?? '')) {
            $this->form_validation->set_rules('email', 'Email', 'is_unique[users.email]');
        }

        // Validasi password hanya kalau diisi
        $password = $this->input->post('password');
        if (!empty($password)) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]|max_length[255]');
            $this->form_validation->set_rules('passconf', 'Konfirmasi Password', 'matches[password]');
        }

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            redirect(base_url('profile'));
        }

        $update = [
            'name'  => trim($this->input->post('name')),
            'email' => $this->input->post('email') ? trim($this->input->post('email')) : null,
            'phone' => $this->input->post('phone') ? trim($this->input->post('phone')) : null,
        ];

        // Password baru
        if (!empty($password)) {
            $update['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->db->where('id_user', $this->user_id);
        if ($this->db->update('users', $update)) {
            // Update session
            $this->session->set_userdata('name', $update['name']);
            $this->session->set_userdata('email', $update['email']);

            log_activity($this->user_id, 'profile_update', 'Update profil');
            $this->session->set_flashdata('success', 'Profil berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui profil.');
        }

        redirect(base_url('profile'));
    }

    // ==============================================
    // UPLOAD AVATAR (POST)
    // ==============================================
    public function upload_avatar() {
        if (empty($_FILES['avatar']['name'])) {
            $this->session->set_flashdata('error', 'Pilih file gambar terlebih dahulu.');
            redirect(base_url('profile'));
        }

        $upload_path = FCPATH . 'assets/uploads/avatars/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config = [
            'upload_path'      => $upload_path,
            'allowed_types'    => 'jpg|jpeg|png|gif|webp',
            'max_size'         => 2048, // 2MB
            'encrypt_name'     => TRUE,
            'file_ext_tolower' => TRUE,
        ];

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('avatar')) {
            $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
            redirect(base_url('profile'));
        }

        $uploadData = $this->upload->data();
        $relativePath = 'assets/uploads/avatars/' . $uploadData['file_name'];

        // Konversi ke WebP kalau gambar JPG/PNG
        if (in_array(strtolower($uploadData['file_ext']), ['.jpg', '.jpeg', '.png'])) {
            $this->load->library('image_converter', [
                'quality'         => 85,
                'delete_original' => true,
                'max_width'       => 500,
                'max_height'      => 500,
            ]);

            $conv = $this->image_converter->convert_and_get_path($relativePath);
            if ($conv['success']) {
                $relativePath = $conv['path'];
            }
        }

        // Ambil avatar lama untuk dihapus
        $old = $this->db->select('profile_picture')->where('id_user', $this->user_id)->get('users')->row();

        // Update DB
        $this->db->where('id_user', $this->user_id);
        $this->db->update('users', ['profile_picture' => $relativePath]);

        // Hapus file lama
        if ($old && !empty($old->profile_picture)) {
            $oldFile = FCPATH . ltrim($old->profile_picture, '/');
            if (file_exists($oldFile) && $oldFile !== FCPATH . $relativePath) {
                @unlink($oldFile);
            }
        }

        log_activity($this->user_id, 'avatar_upload', 'Upload foto profil baru');
        $this->session->set_flashdata('success', 'Foto profil berhasil diperbarui.');
        redirect(base_url('profile'));
    }

    // ==============================================
    // REMOVE AVATAR (POST)
    // ==============================================
    public function remove_avatar() {
        $user = $this->db->select('profile_picture')->where('id_user', $this->user_id)->get('users')->row();

        if ($user && !empty($user->profile_picture)) {
            $file = FCPATH . ltrim($user->profile_picture, '/');
            if (file_exists($file)) @unlink($file);

            $this->db->where('id_user', $this->user_id);
            $this->db->update('users', ['profile_picture' => null]);

            log_activity($this->user_id, 'avatar_remove', 'Hapus foto profil');
            $this->session->set_flashdata('success', 'Foto profil berhasil dihapus.');
        }

        redirect(base_url('profile'));
    }

    // ==============================================
    // CHANGE PASSWORD (POST)
    // ==============================================
    public function change_password() {
        $this->form_validation->set_rules('current_password', 'Password Saat Ini', 'required');
        $this->form_validation->set_rules('new_password', 'Password Baru', 'required|min_length[6]|max_length[255]');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[new_password]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            redirect(base_url('profile'));
        }

        $user = $this->db->where('id_user', $this->user_id)->get('users')->row();
        if (!$user) {
            $this->session->set_flashdata('error', 'User tidak ditemukan.');
            redirect(base_url('profile'));
        }

        if (!password_verify($this->input->post('current_password'), $user->password)) {
            $this->session->set_flashdata('error', 'Password saat ini salah.');
            redirect(base_url('profile'));
        }

        $this->db->where('id_user', $this->user_id);
        $this->db->update('users', [
            'password' => password_hash($this->input->post('new_password'), PASSWORD_DEFAULT),
        ]);

        log_activity($this->user_id, 'password_change', 'Ubah password');
        $this->session->set_flashdata('success', 'Password berhasil diubah.');
        redirect(base_url('profile'));
    }
}	

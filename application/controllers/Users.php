<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->require_admin();
        $this->load->model('User_model');
    }

    public function index() {
        $data['title'] = 'Kelola Pengguna';

        $this->db->select('users.*, 
                          (SELECT is_online FROM user_status WHERE id_user = users.id_user LIMIT 1) as is_online');
        $this->db->from('users');
        $this->db->order_by('users.role', 'ASC');
        $this->db->order_by('users.name', 'ASC');
        $data['users'] = $this->db->get()->result();

        $this->render('users/index', $data);
    }

    public function add() {
        $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[3]|max_length[50]|is_unique[users.username]|alpha_numeric_punct');
        $this->form_validation->set_rules('name', 'Nama', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|max_length[100]|is_unique[users.email]');
        $this->form_validation->set_rules('phone', 'Telepon', 'trim|max_length[20]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[255]');
        $this->form_validation->set_rules('passconf', 'Konfirmasi Password', 'required|matches[password]');
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,teacher,parent]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            redirect(base_url('users'));
        }

        $data = [
            'username'   => trim($this->input->post('username')),
            'name'       => trim($this->input->post('name')),
            'email'      => $this->input->post('email') ? trim($this->input->post('email')) : null,
            'phone'      => $this->input->post('phone') ? trim($this->input->post('phone')) : null,
            'password'   => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'role'       => $this->input->post('role'),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->db->insert('users', $data)) {
            $id = $this->db->insert_id();
            log_activity($this->user_id, 'user_add', "Menambah user: {$data['username']} (ID: {$id})");
            $this->session->set_flashdata('success', 'User berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan user.');
        }

        redirect(base_url('users'));
    }

    public function edit() {
        $id = (int)$this->input->post('id_user');
        if (!$id) {
            $this->session->set_flashdata('error', 'ID tidak valid.');
            redirect(base_url('users'));
        }

        $user = $this->db->where('id_user', $id)->get('users')->row();
        if (!$user) {
            $this->session->set_flashdata('error', 'User tidak ditemukan.');
            redirect(base_url('users'));
        }

        // Validasi — untuk unique, kecualikan user ini sendiri
        $this->form_validation->set_rules('name', 'Nama', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|max_length[100]');
        $this->form_validation->set_rules('phone', 'Telepon', 'trim|max_length[20]');
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,teacher,parent]');

        // Cek unique email kalau diubah
        $newEmail = $this->input->post('email');
        if (!empty($newEmail) && $newEmail !== $user->email) {
            $this->form_validation->set_rules('email', 'Email', 'is_unique[users.email]');
        }

        $password = $this->input->post('password');
        if (!empty($password)) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]|max_length[255]');
            $this->form_validation->set_rules('passconf', 'Konfirmasi Password', 'matches[password]');
        }

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            redirect(base_url('users'));
        }

        $update = [
            'name'  => trim($this->input->post('name')),
            'email' => $this->input->post('email') ? trim($this->input->post('email')) : null,
            'phone' => $this->input->post('phone') ? trim($this->input->post('phone')) : null,
            'role'  => $this->input->post('role'),
        ];

        // Cegah admin hapus role sendiri
        if ($id === (int)$this->user_id && $update['role'] !== 'admin') {
            $this->session->set_flashdata('error', 'Tidak dapat mengubah role akun sendiri.');
            redirect(base_url('users'));
        }

        if (!empty($password)) {
            $update['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->db->where('id_user', $id);
        if ($this->db->update('users', $update)) {
            log_activity($this->user_id, 'user_edit', "Mengubah user: {$update['name']} (ID: {$id})");
            $this->session->set_flashdata('success', 'User berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui user.');
        }

        redirect(base_url('users'));
    }

    public function delete() {
        $id = (int)$this->input->post('id_user');
        if (!$id) {
            $this->session->set_flashdata('error', 'ID tidak valid.');
            redirect(base_url('users'));
        }

        if ($id === (int)$this->user_id) {
            $this->session->set_flashdata('error', 'Tidak dapat menghapus akun sendiri.');
            redirect(base_url('users'));
        }

        $user = $this->db->where('id_user', $id)->get('users')->row();
        if (!$user) {
            $this->session->set_flashdata('error', 'User tidak ditemukan.');
            redirect(base_url('users'));
        }

        // Cek relasi — kalau guru sebagai wali kelas
        if ($user->role === 'teacher') {
            $classCount = $this->db->where('homeroom_teacher_id', $id)->count_all_results('classes');
            if ($classCount > 0) {
                $this->session->set_flashdata('error', "Guru ini masih menjadi wali kelas untuk {$classCount} kelas. Ganti wali kelas dulu.");
                redirect(base_url('users'));
            }
        }

        // Soft delete
        $this->db->where('id_user', $id);
        if ($this->db->update('users', ['is_active' => 0])) {
            log_activity($this->user_id, 'user_delete', "Menghapus user: {$user->username} (ID: {$id})");
            $this->session->set_flashdata('success', 'User berhasil dinonaktifkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menonaktifkan user.');
        }

        redirect(base_url('users'));
    }

    public function toggle_status() {
        $id = (int)$this->input->post('id_user');
        if (!$id) {
            $this->json_error('ID tidak valid', 422);
            return;
        }

        if ($id === (int)$this->user_id) {
            $this->json_error('Tidak dapat menonaktifkan akun sendiri', 400);
            return;
        }

        $user = $this->db->where('id_user', $id)->get('users')->row();
        if (!$user) {
            $this->json_error('User tidak ditemukan', 404);
            return;
        }

        $new = $user->is_active ? 0 : 1;
        $this->db->where('id_user', $id);
        $this->db->update('users', ['is_active' => $new]);

        log_activity($this->user_id, 'user_toggle', ($new ? 'Mengaktifkan' : 'Menonaktifkan') . " user: {$user->username}");

        $this->json_success(['id' => $id, 'is_active' => $new], $new ? 'User diaktifkan' : 'User dinonaktifkan');
    }

    public function reset_password() {
        $id = (int)$this->input->post('id_user');
        $newPassword = $this->input->post('new_password');

        if (!$id || empty($newPassword)) {
            $this->json_error('Data tidak lengkap', 422);
            return;
        }

        if (strlen($newPassword) < 6) {
            $this->json_error('Password minimal 6 karakter', 422);
            return;
        }

        $user = $this->db->where('id_user', $id)->get('users')->row();
        if (!$user) {
            $this->json_error('User tidak ditemukan', 404);
            return;
        }

        $this->db->where('id_user', $id);
        $this->db->update('users', [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        log_activity($this->user_id, 'user_reset_password', "Reset password user: {$user->username}");
        $this->json_success([], 'Password berhasil direset');
    }
}

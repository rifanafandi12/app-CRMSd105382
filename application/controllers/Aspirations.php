<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aspirations extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->require_login();
        $this->load->model('Aspiration_model');
    }

    public function index() {
        $data['title'] = 'Aspirasi';
        $data['user_role'] = $this->user_role;

        if (in_array($this->user_role, ['admin', 'teacher'])) {
            // Admin/Teacher: lihat semua
            $this->db->select('aspirations.*, users.name as parent_name');
            $this->db->from('aspirations');
            $this->db->join('users', 'aspirations.id_parent = users.id_user');
            $this->db->order_by('aspirations.created_at', 'DESC');
            $data['aspirations'] = $this->db->get()->result();
            $data['is_admin_view'] = true;
        } else {
            // Parent: hanya aspirasi sendiri
            $this->db->where('id_parent', $this->user_id);
            $this->db->order_by('created_at', 'DESC');
            $data['aspirations'] = $this->db->get('aspirations')->result();
            $data['is_admin_view'] = false;
        }

        $this->render('aspirations/index', $data);
    }

    public function add() {
        if ($this->user_role !== 'parent') {
            redirect(base_url('aspirations'));
        }

        $this->form_validation->set_rules('subject', 'Subjek', 'required|trim|max_length[200]');
        $this->form_validation->set_rules('content', 'Isi', 'required|trim|max_length[5000]');
        $this->form_validation->set_rules('category', 'Kategori', 'required|in_list[saran,kritik,keluhan,pertanyaan,lainnya]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            redirect(base_url('aspirations'));
        }

        $data = [
            'id_parent'  => $this->user_id,
            'subject'    => trim($this->input->post('subject')),
            'content'    => trim($this->input->post('content')),
            'category'   => $this->input->post('category'),
            'status'     => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Upload attachment
        if (!empty($_FILES['attachment']['name'])) {
            $config = [
                'upload_path'      => FCPATH . 'assets/uploads/aspirations/',
                'allowed_types'    => 'jpg|jpeg|png|gif|webp|pdf|doc|docx',
                'max_size'         => 2048,
                'encrypt_name'     => TRUE,
                'file_ext_tolower' => TRUE,
            ];
            if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0755, true);

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('attachment')) {
                $up = $this->upload->data();
                $rel = 'assets/uploads/aspirations/' . $up['file_name'];

                // Konversi ke WebP kalau gambar
                if (in_array(strtolower($up['file_ext']), ['.jpg', '.jpeg', '.png'])) {
                    $this->load->library('image_converter', ['quality' => 80, 'delete_original' => true]);
                    $conv = $this->image_converter->convert_and_get_path($rel);
                    if ($conv['success']) $rel = $conv['path'];
                }
                $data['attachment'] = $rel;
            }
        }

        if ($this->db->insert('aspirations', $data)) {
            log_activity($this->user_id, 'aspiration_add', "Kirim aspirasi: {$data['subject']}");
            $this->session->set_flashdata('success', 'Aspirasi berhasil dikirim. Terima kasih atas masukannya.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengirim aspirasi.');
        }

        redirect(base_url('aspirations'));
    }

    public function detail($id) {
        $id = (int)$id;

        $this->db->select('aspirations.*, users.name as parent_name');
        $this->db->from('aspirations');
        $this->db->join('users', 'aspirations.id_parent = users.id_user');
        $this->db->where('aspirations.id_aspiration', $id);
        $asp = $this->db->get()->row();

        if (!$asp) show_404();

        // Validasi akses
        if ($this->user_role === 'parent' && (int)$asp->id_parent !== (int)$this->user_id) {
            show_404();
        }

        // Nama responder
        $asp->responder_name = null;
        if (!empty($asp->responded_by)) {
            $r = $this->db->select('name')->where('id_user', $asp->responded_by)->get('users')->row();
            $asp->responder_name = $r->name ?? null;
        }

        // Mark as read untuk admin
        if (in_array($this->user_role, ['admin', 'teacher']) && $asp->status === 'pending') {
            $this->db->where('id_aspiration', $id);
            $this->db->update('aspirations', ['status' => 'dibaca']);
        }

        $data['aspiration'] = $asp;
        $data['title'] = 'Detail Aspirasi';
        $data['user_role'] = $this->user_role;

        $this->render('aspirations/detail', $data);
    }

    public function respond() {
        if (!in_array($this->user_role, ['admin', 'teacher'])) {
            redirect(base_url('aspirations'));
        }

        $id = (int)$this->input->post('id_aspiration');
        $response = trim($this->input->post('response'));

        if (!$id || empty($response)) {
            $this->session->set_flashdata('error', 'Data tidak lengkap.');
            redirect(base_url('aspirations'));
        }

        $asp = $this->db->where('id_aspiration', $id)->get('aspirations')->row();
        if (!$asp) {
            $this->session->set_flashdata('error', 'Aspirasi tidak ditemukan.');
            redirect(base_url('aspirations'));
        }

        $this->db->where('id_aspiration', $id);
        $this->db->update('aspirations', [
            'response'     => $response,
            'responded_by' => $this->user_id,
            'responded_at' => date('Y-m-d H:i:s'),
            'status'       => 'selesai',
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        log_activity($this->user_id, 'aspiration_respond', "Respon aspirasi ID: {$id}");
        $this->session->set_flashdata('success', 'Respon berhasil dikirim.');
        redirect(base_url('aspirations/detail/' . $id));
    }

    public function delete() {
        if (!in_array($this->user_role, ['admin', 'teacher'])) {
            redirect(base_url('aspirations'));
        }

        $id = (int)$this->input->post('id_aspiration');
        if (!$id) {
            $this->session->set_flashdata('error', 'ID tidak valid.');
            redirect(base_url('aspirations'));
        }

        $asp = $this->db->where('id_aspiration', $id)->get('aspirations')->row();
        if (!$asp) {
            $this->session->set_flashdata('error', 'Aspirasi tidak ditemukan.');
            redirect(base_url('aspirations'));
        }

        // Hapus file
        if (!empty($asp->attachment)) {
            $file = FCPATH . ltrim($asp->attachment, '/');
            if (file_exists($file)) @unlink($file);
        }

        $this->db->where('id_aspiration', $id);
        $this->db->delete('aspirations');

        log_activity($this->user_id, 'aspiration_delete', "Hapus aspirasi ID: {$id}");
        $this->session->set_flashdata('success', 'Aspirasi berhasil dihapus.');
        redirect(base_url('aspirations'));
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Classes extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->require_admin();
        $this->load->model('Class_model');
        $this->load->model('User_model');
    }

    // ==============================================
    // INDEX
    // ==============================================
    public function index() {
        $data['title'] = 'Kelola Data Kelas';

        $this->db->select('classes.*, users.name as homeroom_teacher_name, 
                          (SELECT COUNT(*) FROM students WHERE students.id_class = classes.id_class AND students.is_active = 1) as student_count');
        $this->db->from('classes');
        $this->db->join('users', 'classes.homeroom_teacher_id = users.id_user', 'left');
        $this->db->order_by('classes.grade_level', 'ASC');
        $this->db->order_by('classes.class_name', 'ASC');
        $data['classes'] = $this->db->get()->result();

        // Teachers untuk dropdown
        $data['teachers'] = $this->db->where('role', 'teacher')
                                     ->where('is_active', 1)
                                     ->order_by('name', 'ASC')
                                     ->get('users')->result();

        $this->render('classes/index', $data);
    }

    // ==============================================
    // ADD (POST)
    // ==============================================
    public function add() {
        $this->form_validation->set_rules('class_name', 'Nama Kelas', 'required|trim|max_length[20]');
        $this->form_validation->set_rules('grade_level', 'Tingkat', 'required|numeric|greater_than[0]|less_than[7]');
        $this->form_validation->set_rules('academic_year', 'Tahun Ajaran', 'trim|max_length[20]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            redirect(base_url('classes'));
        }

        $data = [
            'class_name'          => trim($this->input->post('class_name')),
            'grade_level'         => (int)$this->input->post('grade_level'),
            'homeroom_teacher_id' => $this->input->post('homeroom_teacher_id') ?: null,
            'academic_year'       => trim($this->input->post('academic_year') ?? ''),
            'description'         => trim($this->input->post('description') ?? ''),
        ];

        // Cek duplikat nama + tahun ajaran
        $exists = $this->db->where('class_name', $data['class_name'])
                           ->where('academic_year', $data['academic_year'])
                           ->count_all_results('classes');

        if ($exists > 0) {
            $this->session->set_flashdata('error', 'Kelas dengan nama dan tahun ajaran tersebut sudah ada.');
            redirect(base_url('classes'));
        }

        if ($this->db->insert('classes', $data)) {
            $id = $this->db->insert_id();
            log_activity($this->user_id, 'class_add', "Menambah kelas: {$data['class_name']} (ID: {$id})");
            $this->session->set_flashdata('success', 'Kelas berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan kelas.');
        }

        redirect(base_url('classes'));
    }

    // ==============================================
    // EDIT (POST)
    // ==============================================
    public function edit() {
        $id = (int)$this->input->post('id_class');
        if (!$id) {
            $this->session->set_flashdata('error', 'ID tidak valid.');
            redirect(base_url('classes'));
        }

        $class = $this->db->where('id_class', $id)->get('classes')->row();
        if (!$class) {
            $this->session->set_flashdata('error', 'Kelas tidak ditemukan.');
            redirect(base_url('classes'));
        }

        $this->form_validation->set_rules('class_name', 'Nama Kelas', 'required|trim|max_length[20]');
        $this->form_validation->set_rules('grade_level', 'Tingkat', 'required|numeric|greater_than[0]|less_than[7]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            redirect(base_url('classes'));
        }

        $update = [
            'class_name'          => trim($this->input->post('class_name')),
            'grade_level'         => (int)$this->input->post('grade_level'),
            'homeroom_teacher_id' => $this->input->post('homeroom_teacher_id') ?: null,
            'description'         => trim($this->input->post('description') ?? ''),
        ];

        $this->db->where('id_class', $id);
        if ($this->db->update('classes', $update)) {
            log_activity($this->user_id, 'class_edit', "Mengubah kelas: {$update['class_name']} (ID: {$id})");
            $this->session->set_flashdata('success', 'Kelas berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui kelas.');
        }

        redirect(base_url('classes'));
    }

    // ==============================================
    // DELETE (POST)
    // ==============================================
    public function delete() {
        $id = (int)$this->input->post('id_class');
        if (!$id) {
            $this->session->set_flashdata('error', 'ID tidak valid.');
            redirect(base_url('classes'));
        }

        // Cek apakah ada siswa
        $student_count = $this->db->where('id_class', $id)->count_all_results('students');
        if ($student_count > 0) {
            $this->session->set_flashdata('error', "Tidak dapat menghapus kelas yang masih memiliki {$student_count} siswa.");
            redirect(base_url('classes'));
        }

        $class = $this->db->where('id_class', $id)->get('classes')->row();
        if (!$class) {
            $this->session->set_flashdata('error', 'Kelas tidak ditemukan.');
            redirect(base_url('classes'));
        }

        $this->db->where('id_class', $id);
        if ($this->db->delete('classes')) {
            log_activity($this->user_id, 'class_delete', "Menghapus kelas: {$class->class_name}");
            $this->session->set_flashdata('success', 'Kelas berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus kelas.');
        }

        redirect(base_url('classes'));
    }

    // ==============================================
    // DETAIL
    // ==============================================
    public function detail($id) {
        $id = (int)$id;

        $this->db->select('classes.*, users.name as homeroom_teacher_name');
        $this->db->from('classes');
        $this->db->join('users', 'classes.homeroom_teacher_id = users.id_user', 'left');
        $this->db->where('classes.id_class', $id);
        $data['class'] = $this->db->get()->row();

        if (!$data['class']) show_404();

        $this->db->select('students.*');
        $this->db->from('students');
        $this->db->where('id_class', $id);
        $this->db->where('is_active', 1);
        $this->db->order_by('student_name', 'ASC');
        $data['students'] = $this->db->get()->result();

        $data['title'] = 'Detail Kelas — ' . $data['class']->class_name;

        $this->render('classes/detail', $data);
    }
}

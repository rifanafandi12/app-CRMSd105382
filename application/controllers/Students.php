<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Students Controller
 * 
 * Mengelola data siswa:
 * - Admin: CRUD semua siswa
 * - Parent: lihat data anak sendiri
 * - Semua role: lihat detail siswa (dengan validasi akses)
 */
class Students extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->require_login();
        $this->load->model('Student_model');
        $this->load->model('Class_model');
        $this->load->model('User_model');
    }

    // ==============================================
    // ADMIN: Daftar semua siswa
    // ==============================================
    public function index() {
        if ($this->user_role !== 'admin') {
            redirect(base_url('dashboard'));
        }

        $data['title'] = 'Data Siswa';

        // Ambil semua siswa dengan info kelas & orang tua
        $this->db->select('students.*, classes.class_name, users.name as parent_name');
        $this->db->from('students');
        $this->db->join('classes', 'students.id_class = classes.id_class', 'left');
        $this->db->join('users', 'students.id_user_parent = users.id_user', 'left');
        $this->db->order_by('students.student_name', 'ASC');
        $data['students'] = $this->db->get()->result();

        // Untuk dropdown di modal
        $data['classes'] = $this->db->order_by('grade_level', 'ASC')->get('classes')->result();

        $data['parents'] = $this->db
            ->where('role', 'parent')
            ->where('is_active', 1)
            ->order_by('name', 'ASC')
            ->get('users')
            ->result();

        $this->render('students/index', $data);
    }

    // ==============================================
    // PARENT: Daftar anak sendiri
    // ==============================================
    public function my_children() {
        if ($this->user_role !== 'parent') {
            redirect(base_url('dashboard'));
        }

        $data['title'] = 'Data Anak Saya';

        $this->db->select('students.*, classes.class_name');
        $this->db->from('students');
        $this->db->join('classes', 'students.id_class = classes.id_class', 'left');
        $this->db->where('students.id_user_parent', $this->user_id);
        $this->db->where('students.is_active', 1);
        $this->db->order_by('students.student_name', 'ASC');
        $data['children'] = $this->db->get()->result();

        $this->render('students/my_children', $data);
    }

    // ==============================================
    // DETAIL: Bisa diakses admin, teacher, atau parent (anaknya)
    // ==============================================
    public function detail($id) {
        $id = (int)$id;

        if (!$id) {
            show_404();
        }

        // Ambil data siswa
        $this->db->select('students.*, classes.class_name, classes.grade_level, users.name as parent_account_name');
        $this->db->from('students');
        $this->db->join('classes', 'students.id_class = classes.id_class', 'left');
        $this->db->join('users', 'students.id_user_parent = users.id_user', 'left');
        $this->db->where('students.id_student', $id);
        $student = $this->db->get()->row();

        if (!$student) {
            show_404();
        }

        // Validasi akses
        if ($this->user_role === 'parent') {
            // Parent cek apakah siswa ini anaknya
            if ((int)$student->id_user_parent !== (int)$this->user_id) {
                show_404();
            }
        } elseif ($this->user_role === 'teacher') {
            // Teacher hanya bisa lihat siswa di kelas yang diwalikan
            $is_homeroom = $this->db
                ->where('id_class', $student->id_class)
                ->where('homeroom_teacher_id', $this->user_id)
                ->count_all_results('classes') > 0;

            if (!$is_homeroom) {
                // Cek juga apakah guru mengajar kelas ini (opsional — kalau ada tabel relasi guru-kelas)
                // Untuk sekarang: block kalau bukan wali kelas
                show_error('Anda tidak memiliki akses ke data siswa ini', 403, 'Akses Ditolak');
            }
        }
        // Admin: full access

        $data['student'] = $student;
        $data['title']   = 'Detail Siswa — ' . $student->student_name;

        // Ambil nilai siswa
        $this->db->select('scores.*, academic_subjects.subject_name');
        $this->db->from('scores');
        $this->db->join('academic_subjects', 'scores.id_subject = academic_subjects.id_subject');
        $this->db->where('scores.id_student', $id);
        $this->db->order_by('academic_subjects.subject_name', 'ASC');
        $data['scores'] = $this->db->get()->result();

        $this->render('students/detail', $data);
    }

    // ==============================================
    // ADMIN: Tambah siswa (POST)
    // ==============================================
    public function add() {
        if ($this->user_role !== 'admin') {
            redirect(base_url('dashboard'));
        }

        // Rate limit untuk cegah spam
        $ip = $this->input->ip_address();
        if (!$this->rate_limiter->attempt("add_student_{$ip}", 20, 60)) {
            $this->session->set_flashdata('error', 'Terlalu banyak permintaan. Coba lagi nanti.');
            redirect(base_url('students'));
        }

        // Validasi
        $this->form_validation->set_rules('nisn',           'NISN',              'required|trim|max_length[20]|is_unique[students.nisn]');
        $this->form_validation->set_rules('student_name',   'Nama Siswa',        'required|trim|max_length[100]');
        $this->form_validation->set_rules('gender',         'Jenis Kelamin',     'required|in_list[Laki-laki,Perempuan]');
        $this->form_validation->set_rules('id_class',       'Kelas',             'required|numeric');
        $this->form_validation->set_rules('parent_name',    'Nama Orang Tua',    'required|trim|max_length[100]');
        $this->form_validation->set_rules('parent_phone',   'No. Telepon',       'required|trim|max_length[20]');
        $this->form_validation->set_rules('parent_email',   'Email Orang Tua',   'valid_email|max_length[100]');
        $this->form_validation->set_rules('birth_place',    'Tempat Lahir',      'trim|max_length[100]');
        $this->form_validation->set_rules('address',        'Alamat',            'trim|max_length[255]');
        $this->form_validation->set_rules('enrollment_year','Tahun Masuk',       'numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            redirect(base_url('students'));
        }

        // Cek kelas valid
        $class_id = (int)$this->input->post('id_class');
        $class_exists = $this->db->where('id_class', $class_id)->count_all_results('classes') > 0;
        if (!$class_exists) {
            $this->session->set_flashdata('error', 'Kelas tidak valid.');
            redirect(base_url('students'));
        }

        // Cek parent account kalau diisi
        $parent_user_id = $this->input->post('id_user_parent');
        if (!empty($parent_user_id)) {
            $parent_user_id = (int)$parent_user_id;
            $is_valid_parent = $this->db
                ->where('id_user', $parent_user_id)
                ->where('role', 'parent')
                ->where('is_active', 1)
                ->count_all_results('users') > 0;

            if (!$is_valid_parent) {
                $this->session->set_flashdata('error', 'Akun orang tua tidak valid.');
                redirect(base_url('students'));
            }
        } else {
            $parent_user_id = null;
        }

        // Siapkan data
        $data = [
            'nisn'            => trim($this->input->post('nisn')),
            'student_name'    => trim($this->input->post('student_name')),
            'gender'          => $this->input->post('gender'),
            'birth_place'     => $this->input->post('birth_place') ?: null,
            'birth_date'      => $this->input->post('birth_date') ?: null,
            'address'         => $this->input->post('address') ?: null,
            'parent_name'     => trim($this->input->post('parent_name')),
            'parent_phone'    => trim($this->input->post('parent_phone')),
            'parent_email'    => $this->input->post('parent_email') ?: null,
            'id_class'        => $class_id,
            'id_user_parent'  => $parent_user_id,
            'enrollment_year' => (int)($this->input->post('enrollment_year') ?: date('Y')),
            'is_active'       => 1,
            'created_at'      => date('Y-m-d H:i:s'),
        ];

        if ($this->db->insert('students', $data)) {
            $insert_id = $this->db->insert_id();
            log_activity($this->user_id, 'student_add', "Menambah siswa: {$data['student_name']} (ID: {$insert_id})");
            $this->session->set_flashdata('success', 'Data siswa berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan data siswa.');
        }

        redirect(base_url('students'));
    }

    // ==============================================
    // ADMIN: Edit siswa (POST — pakai id_student dari POST)
    // ==============================================
    public function edit() {
        if ($this->user_role !== 'admin') {
            redirect(base_url('dashboard'));
        }

        $id = (int)$this->input->post('id_student');
        if (!$id) {
            $this->session->set_flashdata('error', 'ID siswa tidak valid.');
            redirect(base_url('students'));
        }

        // Cek siswa ada
        $student = $this->db->where('id_student', $id)->get('students')->row();
        if (!$student) {
            $this->session->set_flashdata('error', 'Data siswa tidak ditemukan.');
            redirect(base_url('students'));
        }

        // Validasi (NISN tidak bisa diubah, jadi tidak divalidasi unique)
        $this->form_validation->set_rules('student_name',   'Nama Siswa',        'required|trim|max_length[100]');
        $this->form_validation->set_rules('gender',         'Jenis Kelamin',     'required|in_list[Laki-laki,Perempuan]');
        $this->form_validation->set_rules('id_class',       'Kelas',             'required|numeric');
        $this->form_validation->set_rules('parent_name',    'Nama Orang Tua',    'required|trim|max_length[100]');
        $this->form_validation->set_rules('parent_phone',   'No. Telepon',       'required|trim|max_length[20]');
        $this->form_validation->set_rules('parent_email',   'Email Orang Tua',   'valid_email|max_length[100]');
        $this->form_validation->set_rules('birth_place',    'Tempat Lahir',      'trim|max_length[100]');
        $this->form_validation->set_rules('address',        'Alamat',            'trim|max_length[255]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            redirect(base_url('students'));
        }

        // Cek kelas valid
        $class_id = (int)$this->input->post('id_class');
        $class_exists = $this->db->where('id_class', $class_id)->count_all_results('classes') > 0;
        if (!$class_exists) {
            $this->session->set_flashdata('error', 'Kelas tidak valid.');
            redirect(base_url('students'));
        }

        // Cek parent account
        $parent_user_id = $this->input->post('id_user_parent');
        if (!empty($parent_user_id)) {
            $parent_user_id = (int)$parent_user_id;
            $is_valid_parent = $this->db
                ->where('id_user', $parent_user_id)
                ->where('role', 'parent')
                ->where('is_active', 1)
                ->count_all_results('users') > 0;

            if (!$is_valid_parent) {
                $this->session->set_flashdata('error', 'Akun orang tua tidak valid.');
                redirect(base_url('students'));
            }
        } else {
            $parent_user_id = null;
        }

        // Data update
        $data = [
            'student_name'    => trim($this->input->post('student_name')),
            'gender'          => $this->input->post('gender'),
            'birth_place'     => $this->input->post('birth_place') ?: null,
            'birth_date'      => $this->input->post('birth_date') ?: null,
            'address'         => $this->input->post('address') ?: null,
            'parent_name'     => trim($this->input->post('parent_name')),
            'parent_phone'    => trim($this->input->post('parent_phone')),
            'parent_email'    => $this->input->post('parent_email') ?: null,
            'id_class'        => $class_id,
            'id_user_parent'  => $parent_user_id,
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $this->db->where('id_student', $id);
        if ($this->db->update('students', $data)) {
            log_activity($this->user_id, 'student_edit', "Mengubah data siswa: {$data['student_name']} (ID: {$id})");
            $this->session->set_flashdata('success', 'Data siswa berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data siswa.');
        }

        redirect(base_url('students'));
    }

    // ==============================================
    // ADMIN: Hapus siswa (POST)
    // Soft delete — set is_active = 0
    // ==============================================
    public function delete() {
        if ($this->user_role !== 'admin') {
            redirect(base_url('dashboard'));
        }

        $id = (int)$this->input->post('id_student');
        if (!$id) {
            $this->session->set_flashdata('error', 'ID siswa tidak valid.');
            redirect(base_url('students'));
        }

        // Cek siswa ada
        $student = $this->db->where('id_student', $id)->get('students')->row();
        if (!$student) {
            $this->session->set_flashdata('error', 'Data siswa tidak ditemukan.');
            redirect(base_url('students'));
        }

        // Soft delete
        $this->db->where('id_student', $id);
        $result = $this->db->update('students', [
            'is_active'  => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($result) {
            log_activity($this->user_id, 'student_delete', "Menghapus siswa: {$student->student_name} (ID: {$id})");
            $this->session->set_flashdata('success', 'Data siswa berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data siswa.');
        }

        redirect(base_url('students'));
    }

    // ==============================================
    // AJAX: Search siswa (untuk autocomplete)
    // ==============================================
    public function search() {
        if ($this->user_role !== 'admin' && $this->user_role !== 'teacher') {
            $this->json_error('Akses ditolak', 403);
            return;
        }

        $q = trim($this->input->get('q') ?? '');
        if (strlen($q) < 2) {
            $this->json_success([]);
            return;
        }

        $this->db->select('students.id_student, students.student_name, students.nisn, classes.class_name');
        $this->db->from('students');
        $this->db->join('classes', 'students.id_class = classes.id_class', 'left');
        $this->db->where('students.is_active', 1);
        $this->db->group_start();
        $this->db->like('students.student_name', $q);
        $this->db->or_like('students.nisn', $q);
        $this->db->group_end();
        $this->db->order_by('students.student_name', 'ASC');
        $this->db->limit(10);
        $result = $this->db->get()->result();

        $students = [];
        foreach ($result as $s) {
            $students[] = [
                'id_student'   => (int)$s->id_student,
                'student_name' => $s->student_name,
                'nisn'         => $s->nisn,
                'class_name'   => $s->class_name,
            ];
        }

        $this->json_success($students);
    }

    // ==============================================
    // ADMIN: Restore siswa (aktifkan kembali)
    // ==============================================
    public function restore() {
        if ($this->user_role !== 'admin') {
            redirect(base_url('dashboard'));
        }

        $id = (int)$this->input->post('id_student');
        if (!$id) {
            $this->session->set_flashdata('error', 'ID siswa tidak valid.');
            redirect(base_url('students'));
        }

        $this->db->where('id_student', $id);
        if ($this->db->update('students', ['is_active' => 1, 'updated_at' => date('Y-m-d H:i:s')])) {
            log_activity($this->user_id, 'student_restore', "Mengaktifkan siswa ID: {$id}");
            $this->session->set_flashdata('success', 'Data siswa berhasil diaktifkan kembali.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengaktifkan siswa.');
        }

        redirect(base_url('students'));
    }

    // ==============================================
    // ADMIN: Export CSV data siswa
    // ==============================================
    public function export() {
        if ($this->user_role !== 'admin') {
            redirect(base_url('dashboard'));
        }

        $this->db->select('students.nisn, students.student_name, students.gender, 
                          students.birth_place, students.birth_date, students.address,
                          students.parent_name, students.parent_phone, students.parent_email,
                          students.enrollment_year, classes.class_name');
        $this->db->from('students');
        $this->db->join('classes', 'students.id_class = classes.id_class', 'left');
        $this->db->where('students.is_active', 1);
        $this->db->order_by('classes.grade_level', 'ASC');
        $this->db->order_by('students.student_name', 'ASC');
        $students = $this->db->get()->result();

        $this->load->helper('download');

        $csv = "\xEF\xBB\xBF"; // UTF-8 BOM untuk Excel
        $csv .= "NISN,Nama Siswa,Jenis Kelamin,Tempat Lahir,Tanggal Lahir,Alamat,Kelas,Nama Orang Tua,No. Telepon,Email,Tahun Masuk\n";

        foreach ($students as $s) {
            $row = [
                $s->nisn,
                $s->student_name,
                $s->gender,
                $s->birth_place,
                $s->birth_date,
                $s->address,
                $s->class_name,
                $s->parent_name,
                $s->parent_phone,
                $s->parent_email,
                $s->enrollment_year,
            ];
            $row = array_map(function ($v) {
                $v = (string)($v ?? '');
                $v = str_replace('"', '""', $v);
                return '"' . $v . '"';
            }, $row);
            $csv .= implode(',', $row) . "\n";
        }

        $filename = 'data-siswa-' . date('Y-m-d') . '.csv';
        force_download($filename, $csv);

        log_activity($this->user_id, 'student_export', "Export data siswa ({$filename})");
    }

    // ==============================================
    // ADMIN: Bulk delete (hapus beberapa sekaligus)
    // ==============================================
    public function bulk_delete() {
        if ($this->user_role !== 'admin') {
            $this->json_error('Akses ditolak', 403);
            return;
        }

        $ids = $this->input->post('ids');
        if (empty($ids) || !is_array($ids)) {
            $this->json_error('Tidak ada siswa yang dipilih', 422);
            return;
        }

        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, fn($id) => $id > 0);

        if (empty($ids)) {
            $this->json_error('ID tidak valid', 422);
            return;
        }

        $this->db->where_in('id_student', $ids);
        $result = $this->db->update('students', [
            'is_active'  => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($result) {
            $count = count($ids);
            log_activity($this->user_id, 'student_bulk_delete', "Menghapus {$count} siswa sekaligus");
            $this->json_success(['deleted' => $count], "{$count} siswa berhasil dihapus");
        } else {
            $this->json_error('Gagal menghapus data siswa', 500);
        }
    }

    // ==============================================
    // ADMIN: Statistik siswa
    // ==============================================
    public function stats() {
        if ($this->user_role !== 'admin') {
            $this->json_error('Akses ditolak', 403);
            return;
        }

        $total = $this->db->where('is_active', 1)->count_all_results('students');
        $male  = $this->db->where('is_active', 1)->where('gender', 'Laki-laki')->count_all_results('students');
        $female = $this->db->where('is_active', 1)->where('gender', 'Perempuan')->count_all_results('students');

        // Distribusi per kelas
        $this->db->select('classes.class_name, classes.grade_level, COUNT(students.id_student) as total');
        $this->db->from('classes');
        $this->db->join('students', 'classes.id_class = students.id_class AND students.is_active = 1', 'left');
        $this->db->group_by('classes.id_class');
        $this->db->order_by('classes.grade_level', 'ASC');
        $distribution = $this->db->get()->result();

        $this->json_success([
            'total'        => $total,
            'male'         => $male,
            'female'       => $female,
            'distribution' => $distribution,
        ]);
    }
}

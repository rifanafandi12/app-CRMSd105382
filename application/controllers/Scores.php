<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scores extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->require_login();
        $this->load->model('Score_model');
        $this->load->model('Student_model');
        $this->load->model('Class_model');
    }

    // ==============================================
    // INDEX — Dispatch berdasarkan role
    // ==============================================
    public function index() {
        switch ($this->user_role) {
            case 'admin':
            case 'teacher':
                $this->input_form();
                break;
            case 'parent':
            default:
                $this->parent_view();
                break;
        }
    }

    // ==============================================
    // VIEW: Input Nilai (Guru & Admin)
    // ==============================================
    private function input_form() {
        $data['title']     = 'Input Nilai Siswa';
        $data['user_role'] = $this->user_role;

        if ($this->user_role === 'admin') {
            // Admin: akses semua kelas
            $data['my_classes'] = $this->Class_model->get_all_classes();
        } else {
            // Guru: hanya kelas yang diwalikan
            $this->db->select('classes.*');
            $this->db->from('classes');
            $this->db->where('homeroom_teacher_id', $this->user_id);
            $data['my_classes'] = $this->db->get()->result();
        }

        $data['subjects']       = $this->Score_model->get_all_subjects();
        $data['academic_years'] = $this->get_academic_years();

        $this->render('scores/teacher', $data);
    }

    // ==============================================
    // VIEW: Nilai Anak (Parent)
    // ==============================================
    private function parent_view() {
        $data['title']     = 'Nilai Anak';
        $data['user_role'] = $this->user_role;

        $data['children'] = $this->db
            ->select('students.*, classes.class_name')
            ->from('students')
            ->join('classes', 'students.id_class = classes.id_class', 'left')
            ->where('students.id_user_parent', $this->user_id)
            ->where('students.is_active', 1)
            ->order_by('students.student_name', 'ASC')
            ->get()->result();

        $data['academic_years'] = $this->get_academic_years();

        $this->render('scores/parent', $data);
    }

    // ==============================================
    // AJAX: Get students by class
    // ==============================================
    public function get_students_by_class() {
        $class_id = (int)$this->input->get('class_id');

        if (!$class_id) {
            $this->json_success([]);
            return;
        }

        $students = $this->db
            ->select('id_student, student_name, nisn')
            ->where('id_class', $class_id)
            ->where('is_active', 1)
            ->order_by('student_name', 'ASC')
            ->get('students')->result();

        $result = [];
        foreach ($students as $s) {
            $result[] = [
                'id_student'   => (int)$s->id_student,
                'student_name' => $s->student_name,
                'nisn'         => $s->nisn,
            ];
        }

        $this->json_success($result);
    }

    // ==============================================
    // AJAX: Get scores for student
    // ==============================================
    public function get_scores() {
        $student_id    = (int)$this->input->get('student_id');
        $semester      = $this->input->get('semester');
        $academic_year = $this->input->get('academic_year');

        if (!$student_id) {
            $this->json_success([]);
            return;
        }

        // Parent: cek apakah siswa adalah anaknya
        if ($this->user_role === 'parent') {
            $check = $this->db
                ->where('id_student', $student_id)
                ->where('id_user_parent', $this->user_id)
                ->count_all_results('students');
            if ($check === 0) {
                $this->json_error('Akses ditolak', 403);
                return;
            }
        }

        $this->db->select('scores.*, academic_subjects.subject_name, academic_subjects.subject_code');
        $this->db->from('scores');
        $this->db->join('academic_subjects', 'scores.id_subject = academic_subjects.id_subject');
        $this->db->where('scores.id_student', $student_id);

        if ($semester)      $this->db->where('scores.semester', $semester);
        if ($academic_year) $this->db->where('scores.academic_year', $academic_year);

        $scores = $this->db->get()->result();

        $result = [];
        foreach ($scores as $s) {
            $result[] = [
                'id_score'         => (int)$s->id_score,
                'id_student'       => (int)$s->id_student,
                'id_subject'       => (int)$s->id_subject,
                'subject_name'     => $s->subject_name,
                'semester'         => (int)$s->semester,
                'academic_year'    => $s->academic_year,
                'daily_score_1'    => $s->daily_score_1 !== null ? (float)$s->daily_score_1 : null,
                'daily_score_2'    => $s->daily_score_2 !== null ? (float)$s->daily_score_2 : null,
                'mid_exam_score'   => $s->mid_exam_score !== null ? (float)$s->mid_exam_score : null,
                'final_exam_score' => $s->final_exam_score !== null ? (float)$s->final_exam_score : null,
                'final_score'      => $s->final_score !== null ? (float)$s->final_score : null,
                'grade'            => $s->grade,
                'notes'            => $s->notes,
            ];
        }

        $this->json_success($result);
    }

    // ==============================================
    // AJAX: Save scores
    // ==============================================
    public function save() {
        if (!in_array($this->user_role, ['admin', 'teacher'])) {
            $this->json_error('Unauthorized', 403);
            return;
        }

        $student_id    = (int)$this->input->post('student_id');
        $semester      = (int)$this->input->post('semester');
        $academic_year = $this->input->post('academic_year');
        $subject_ids   = $this->input->post('subject_id');
        $daily1_arr    = $this->input->post('daily_score_1');
        $daily2_arr    = $this->input->post('daily_score_2');
        $mid_arr       = $this->input->post('mid_exam_score');
        $final_arr     = $this->input->post('final_exam_score');
        $notes_arr     = $this->input->post('notes');

        if (!$student_id || !$semester || !$academic_year || empty($subject_ids)) {
            $this->json_error('Data tidak lengkap', 422);
            return;
        }

        // Validasi student ada & guru punya akses
        if ($this->user_role === 'teacher') {
            $student = $this->db->select('id_class')->where('id_student', $student_id)->get('students')->row();
            if (!$student) {
                $this->json_error('Siswa tidak ditemukan', 404);
                return;
            }

            $is_homeroom = $this->db
                ->where('id_class', $student->id_class)
                ->where('homeroom_teacher_id', $this->user_id)
                ->count_all_results('classes') > 0;

            if (!$is_homeroom) {
                $this->json_error('Anda tidak mengajar kelas ini', 403);
                return;
            }
        }

        $success = 0;
        $errors  = 0;

        foreach ($subject_ids as $i => $subject_id) {
            $subject_id = (int)$subject_id;
            if (!$subject_id) continue;

            $daily1 = isset($daily1_arr[$i]) && $daily1_arr[$i] !== '' ? (float)$daily1_arr[$i] : null;
            $daily2 = isset($daily2_arr[$i]) && $daily2_arr[$i] !== '' ? (float)$daily2_arr[$i] : null;
            $mid    = isset($mid_arr[$i])    && $mid_arr[$i] !== ''    ? (float)$mid_arr[$i]    : null;
            $final  = isset($final_arr[$i])  && $final_arr[$i] !== ''  ? (float)$final_arr[$i]  : null;
            $notes  = $notes_arr[$i] ?? null;

            // Validasi range 0-100
            foreach ([$daily1, $daily2, $mid, $final] as $v) {
                if ($v !== null && ($v < 0 || $v > 100)) {
                    $errors++;
                    continue 2;
                }
            }

            $final_score = $this->calculate_final_score($daily1, $daily2, $mid, $final);
            $grade       = $this->calculate_grade($final_score);

            $row = [
                'id_student'       => $student_id,
                'id_subject'       => $subject_id,
                'semester'         => $semester,
                'academic_year'    => $academic_year,
                'daily_score_1'    => $daily1,
                'daily_score_2'    => $daily2,
                'mid_exam_score'   => $mid,
                'final_exam_score' => $final,
                'final_score'      => $final_score,
                'grade'            => $grade,
                'notes'            => $notes ? trim($notes) : null,
                'created_by'       => $this->user_id,
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            // Cek existing
            $exists = $this->db
                ->where('id_student', $student_id)
                ->where('id_subject', $subject_id)
                ->where('semester', $semester)
                ->where('academic_year', $academic_year)
                ->get('scores')->row();

            if ($exists) {
                $this->db->where('id_score', $exists->id_score);
                if ($this->db->update('scores', $row)) $success++;
                else $errors++;
            } else {
                $row['created_at'] = date('Y-m-d H:i:s');
                if ($this->db->insert('scores', $row)) $success++;
                else $errors++;
            }
        }

        if ($success > 0) {
            log_activity($this->user_id, 'score_save', "Menyimpan {$success} nilai untuk siswa ID {$student_id}");
            $this->json_success(
                ['success' => $success, 'errors' => $errors],
                "{$success} nilai berhasil disimpan" . ($errors > 0 ? " ({$errors} gagal)" : '')
            );
        } else {
            $this->json_error('Gagal menyimpan nilai', 500);
        }
    }

    // ==============================================
    // VIEW: Rapor
    // ==============================================
    public function raport($student_id) {
        $student_id = (int)$student_id;
        $this->check_raport_access($student_id);

        $data['student'] = $this->db
            ->select('students.*, classes.class_name')
            ->from('students')
            ->join('classes', 'students.id_class = classes.id_class', 'left')
            ->where('students.id_student', $student_id)
            ->get()->row();

        if (!$data['student']) show_404();

        $semester      = $this->input->get('semester') ?: 1;
        $academic_year = $this->input->get('academic_year') ?: date('Y') . '/' . (date('Y') + 1);

        $data['scores'] = $this->db
            ->select('scores.*, academic_subjects.subject_name')
            ->from('scores')
            ->join('academic_subjects', 'scores.id_subject = academic_subjects.id_subject')
            ->where('scores.id_student', $student_id)
            ->where('scores.semester', $semester)
            ->where('scores.academic_year', $academic_year)
            ->order_by('academic_subjects.subject_name', 'ASC')
            ->get()->result();

        $data['semester']       = $semester;
        $data['academic_year']  = $academic_year;
        $data['semester_text']  = $semester == 1 ? 'Ganjil' : 'Genap';
        $data['user_role']      = $this->user_role;
        $data['title']          = 'Rapor — ' . $data['student']->student_name;

        // Hitung rata-rata
        $total = 0; $count = 0;
        foreach ($data['scores'] as $s) {
            if ($s->final_score) { $total += $s->final_score; $count++; }
        }
        $data['average']       = $count > 0 ? round($total / $count, 2) : 0;
        $data['average_grade'] = $this->calculate_grade($data['average']);

        $this->render('scores/raport', $data);
    }

    // ==============================================
    // VIEW: Print Rapor
    // ==============================================
    public function print_raport($student_id) {
        $student_id = (int)$student_id;
        $this->check_raport_access($student_id);

        $data['student'] = $this->db
            ->select('students.*, classes.class_name')
            ->from('students')
            ->join('classes', 'students.id_class = classes.id_class', 'left')
            ->where('students.id_student', $student_id)
            ->get()->row();

        if (!$data['student']) show_404();

        $semester      = $this->input->get('semester') ?: 1;
        $academic_year = $this->input->get('academic_year') ?: date('Y') . '/' . (date('Y') + 1);

        $data['scores'] = $this->db
            ->select('scores.*, academic_subjects.subject_name')
            ->from('scores')
            ->join('academic_subjects', 'scores.id_subject = academic_subjects.id_subject')
            ->where('scores.id_student', $student_id)
            ->where('scores.semester', $semester)
            ->where('scores.academic_year', $academic_year)
            ->order_by('academic_subjects.subject_name', 'ASC')
            ->get()->result();

        $data['semester']      = $semester;
        $data['semester_text'] = $semester == 1 ? 'Ganjil' : 'Genap';
        $data['academic_year'] = $academic_year;

        $total = 0; $count = 0;
        foreach ($data['scores'] as $s) {
            if ($s->final_score) { $total += $s->final_score; $count++; }
        }
        $data['average'] = $count > 0 ? round($total / $count, 2) : 0;

        $this->load->view('scores/print_raport', $data);
    }

    // ==============================================
    // Helper: Access check untuk raport
    // ==============================================
    private function check_raport_access($student_id) {
        if ($this->user_role === 'parent') {
            $check = $this->db
                ->where('id_student', $student_id)
                ->where('id_user_parent', $this->user_id)
                ->count_all_results('students');
            if ($check === 0) show_404();
        }
    }

    // ==============================================
    // Helper: Calculate final score
    // Formula: 30% harian + 30% UTS + 40% UAS
    // ==============================================
    private function calculate_final_score($daily1, $daily2, $mid, $final) {
        $dailyValues = [];
        if ($daily1 !== null) $dailyValues[] = $daily1;
        if ($daily2 !== null) $dailyValues[] = $daily2;
        $dailyAvg = !empty($dailyValues) ? array_sum($dailyValues) / count($dailyValues) : 0;

        $nilaiHarian = $dailyAvg * 0.3;
        $nilaiUTS    = ($mid ?? 0) * 0.3;
        $nilaiUAS    = ($final ?? 0) * 0.4;

        return round($nilaiHarian + $nilaiUTS + $nilaiUAS, 2);
    }

    // ==============================================
    // Helper: Calculate grade
    // ==============================================
    private function calculate_grade($score) {
        if ($score >= 90) return 'A+';
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'A-';
        if ($score >= 75) return 'B+';
        if ($score >= 70) return 'B';
        if ($score >= 65) return 'B-';
        if ($score >= 60) return 'C+';
        if ($score >= 55) return 'C';
        if ($score >= 50) return 'D';
        return 'E';
    }

    // ==============================================
    // Helper: Academic years list
    // ==============================================
    private function get_academic_years() {
        $current = (int)date('Y');
        $years = [];
        for ($i = -2; $i <= 2; $i++) {
            $years[] = ($current + $i) . '/' . ($current + $i + 1);
        }
        return $years;
    }
}

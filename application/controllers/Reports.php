<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->require_admin();
		$this->load->model('Report_model');
		$this->load->model('Class_model');
	}

	// ==============================================
	// INDEX — Dashboard Laporan
	// ==============================================
	public function index()
	{
		$data['title']     = 'Laporan Sistem';
		$data['user_role'] = $this->user_role;

		// Filter periode (default 6 bulan terakhir)
		$period = $this->input->get('period') ?: '6months';
		$data['period'] = $period;

		// ==========================================
		// STATISTIK UMUM
		// ==========================================
		$data['total_users']       = $this->db->where('is_active', 1)->count_all_results('users');
		$data['total_students']    = $this->db->where('is_active', 1)->count_all_results('students');
		$data['total_classes']     = $this->db->count_all('classes');
		$data['total_messages']    = $this->db->count_all('chat_messages');
		$data['total_aspirations'] = $this->db->count_all('aspirations');
		$data['total_announcements'] = $this->db->count_all('announcements');
		$data['total_gallery']     = $this->db->count_all('gallery');

		// ==========================================
		// STATISTIK PER ROLE
		// ==========================================
		$data['total_admins']   = $this->db->where('role', 'admin')->where('is_active', 1)->count_all_results('users');
		$data['total_teachers'] = $this->db->where('role', 'teacher')->where('is_active', 1)->count_all_results('users');
		$data['total_parents']  = $this->db->where('role', 'parent')->where('is_active', 1)->count_all_results('users');

		// ==========================================
		// STATISTIK HARI INI
		// ==========================================
		$today = date('Y-m-d');
		$data['today_messages']     = $this->db->where('DATE(created_at)', $today)->count_all_results('chat_messages');
		$data['today_aspirations']  = $this->db->where('DATE(created_at)', $today)->count_all_results('aspirations');
		$data['online_users']       = $this->db->where('is_online', 1)->count_all_results('user_status');

		// ==========================================
		// DATA CHART — CHAT & ASPIRASI per Bulan
		// ==========================================
		$months = $this->get_month_range($period);
		$data['chart_months']    = $months['labels'];
		$data['chart_messages']  = $this->get_monthly_count('chat_messages', $months['dates']);
		$data['chart_aspirations'] = $this->get_monthly_count('aspirations', $months['dates']);
		$data['chart_users']     = $this->get_monthly_count('users', $months['dates']);

		// ==========================================
		// DATA CHART — Chat 7 Hari Terakhir
		// ==========================================
		$data['weekly_messages']    = $this->get_weekly_data('chat_messages');
		$data['weekly_aspirations'] = $this->get_weekly_data('aspirations');

		// ==========================================
		// STATISTIK SISWA
		// ==========================================
		$data['students_by_gender'] = $this->get_students_by_gender();
		$data['students_by_class']  = $this->get_students_by_class();

		// ==========================================
		// STATISTIK ASPIRASI
		// ==========================================
		$data['aspirations_by_status'] = $this->get_aspirations_by_status();

		// ==========================================
		// AKTIVITAS TERBARU
		// ==========================================
		$data['recent_activities'] = $this->db
			->select('log_activities.*, users.name as user_name, users.role as user_role')
			->from('log_activities')
			->join('users', 'log_activities.id_user = users.id_user', 'left')
			->order_by('log_activities.created_at', 'DESC')
			->limit(20)
			->get()->result();

		// ==========================================
		// TOP USERS (paling aktif)
		// ==========================================
		$data['top_senders'] = $this->db
			->select('users.id_user, users.name, users.role, COUNT(chat_messages.id_message) as total')
			->from('chat_messages')
			->join('users', 'chat_messages.id_sender = users.id_user')
			->group_by('users.id_user')
			->order_by('total', 'DESC')
			->limit(5)
			->get()->result();

		// ==========================================
		// DATA KELAS (untuk filter)
		// ==========================================
		$data['classes'] = $this->Class_model->get_all_classes();

		// ==========================================
		// RENDER
		// ==========================================
		$this->render('reports/index', $data);
	}

	// ==============================================
	// AJAX: Get chart data per periode
	// ==============================================
	public function get_chart_data()
	{
		$period = $this->input->get('period') ?: '6months';
		$months = $this->get_month_range($period);

		$result = [
			'labels'      => $months['labels'],
			'messages'    => $this->get_monthly_count('chat_messages', $months['dates']),
			'aspirations' => $this->get_monthly_count('aspirations', $months['dates']),
			'users'       => $this->get_monthly_count('users', $months['dates']),
		];

		$this->json_success($result);
	}

	// ==============================================
	// EXPORT: Pesan Chat
	// ==============================================
	public function export_messages()
	{
		$start_date = $this->input->get('start_date');
		$end_date   = $this->input->get('end_date');

		$this->db->select('chat_messages.*, s.name as sender_name, r.name as receiver_name');
		$this->db->from('chat_messages');
		$this->db->join('users as s', 'chat_messages.id_sender = s.id_user');
		$this->db->join('users as r', 'chat_messages.id_receiver = r.id_user');

		if ($start_date) $this->db->where('DATE(chat_messages.created_at) >=', $start_date);
		if ($end_date)   $this->db->where('DATE(chat_messages.created_at) <=', $end_date);

		$this->db->order_by('chat_messages.created_at', 'DESC');
		$messages = $this->db->get()->result();

		// Generate CSV
		$this->load->helper('download');

		$csv = "\xEF\xBB\xBF"; // UTF-8 BOM
		$csv .= "ID,Pengirim,Penerima,Pesan,Tanggal,Dibaca\n";

		foreach ($messages as $msg) {
			$row = [
				$msg->id_message,
				$msg->sender_name,
				$msg->receiver_name,
				$msg->message,
				$msg->created_at,
				$msg->is_read ? 'Ya' : 'Tidak',
			];
			$csv .= implode(',', array_map([$this, 'csv_escape'], $row)) . "\n";
		}

		log_activity($this->user_id, 'report_export', 'Export pesan chat');
		force_download('laporan_pesan_' . date('Y-m-d') . '.csv', $csv);
	}

	// ==============================================
	// EXPORT: Aspirasi
	// ==============================================
	public function export_aspirations()
	{
		$this->db->select('aspirations.*, users.name as parent_name');
		$this->db->from('aspirations');
		$this->db->join('users', 'aspirations.id_parent = users.id_user');
		$this->db->order_by('aspirations.created_at', 'DESC');
		$aspirations = $this->db->get()->result();

		$this->load->helper('download');

		$csv = "\xEF\xBB\xBF";
		$csv .= "ID,Orang Tua,Subjek,Kategori,Status,Tanggal,Respon\n";

		foreach ($aspirations as $asp) {
			$row = [
				$asp->id_aspiration,
				$asp->parent_name,
				$asp->subject,
				$asp->category,
				$asp->status,
				$asp->created_at,
				$asp->response ?? '',
			];
			$csv .= implode(',', array_map([$this, 'csv_escape'], $row)) . "\n";
		}

		log_activity($this->user_id, 'report_export', 'Export aspirasi');
		force_download('laporan_aspirasi_' . date('Y-m-d') . '.csv', $csv);
	}

	// ==============================================
	// EXPORT: Siswa
	// ==============================================
	public function export_students()
	{
		$this->db->select('students.*, classes.class_name');
		$this->db->from('students');
		$this->db->join('classes', 'students.id_class = classes.id_class', 'left');
		$this->db->where('students.is_active', 1);
		$this->db->order_by('classes.grade_level', 'ASC');
		$this->db->order_by('students.student_name', 'ASC');
		$students = $this->db->get()->result();

		$this->load->helper('download');

		$csv = "\xEF\xBB\xBF";
		$csv .= "NISN,Nama Siswa,Jenis Kelamin,Tempat Lahir,Tanggal Lahir,Kelas,Nama Orang Tua,No. Telepon,Email\n";

		foreach ($students as $s) {
			$row = [
				$s->nisn,
				$s->student_name,
				$s->gender,
				$s->birth_place ?? '',
				$s->birth_date ?? '',
				$s->class_name ?? '',
				$s->parent_name,
				$s->parent_phone ?? '',
				$s->parent_email ?? '',
			];
			$csv .= implode(',', array_map([$this, 'csv_escape'], $row)) . "\n";
		}

		log_activity($this->user_id, 'report_export', 'Export data siswa');
		force_download('laporan_siswa_' . date('Y-m-d') . '.csv', $csv);
	}

	// ==============================================
	// EXPORT: Nilai per Kelas
	// ==============================================
	public function export_scores()
	{
		$class_id      = $this->input->get('class_id');
		$semester      = $this->input->get('semester') ?: 1;
		$academic_year = $this->input->get('academic_year') ?: date('Y') . '/' . (date('Y') + 1);

		$this->db->select('students.student_name, students.nisn, classes.class_name, 
                          academic_subjects.subject_name, scores.daily_score_1, scores.daily_score_2,
                          scores.mid_exam_score, scores.final_exam_score, scores.final_score, scores.grade');
		$this->db->from('scores');
		$this->db->join('students', 'scores.id_student = students.id_student');
		$this->db->join('classes', 'students.id_class = classes.id_class', 'left');
		$this->db->join('academic_subjects', 'scores.id_subject = academic_subjects.id_subject');
		$this->db->where('scores.semester', $semester);
		$this->db->where('scores.academic_year', $academic_year);

		if ($class_id) {
			$this->db->where('students.id_class', $class_id);
		}

		$this->db->order_by('students.student_name', 'ASC');
		$this->db->order_by('academic_subjects.subject_name', 'ASC');
		$scores = $this->db->get()->result();

		$this->load->helper('download');

		$csv = "\xEF\xBB\xBF";
		$csv .= "Nama Siswa,NISN,Kelas,Mata Pelajaran,NH1,NH2,UTS,UAS,Nilai Akhir,Grade\n";

		foreach ($scores as $sc) {
			$row = [
				$sc->student_name,
				$sc->nisn,
				$sc->class_name ?? '',
				$sc->subject_name,
				$sc->daily_score_1 ?? '',
				$sc->daily_score_2 ?? '',
				$sc->mid_exam_score ?? '',
				$sc->final_exam_score ?? '',
				$sc->final_score ?? '',
				$sc->grade ?? '',
			];
			$csv .= implode(',', array_map([$this, 'csv_escape'], $row)) . "\n";
		}

		log_activity($this->user_id, 'report_export', 'Export nilai');
		force_download('laporan_nilai_' . date('Y-m-d') . '.csv', $csv);
	}

	// ==============================================
	// HELPER: Range bulan
	// ==============================================
	private function get_month_range($period = '6months')
	{
		$labels = [];
		$dates  = [];
		$count  = 6;

		switch ($period) {
			case '3months':
				$count = 3;
				break;
			case '12months':
				$count = 12;
				break;
			case '6months':
			default:
				$count = 6;
				break;
		}

		for ($i = $count - 1; $i >= 0; $i--) {
			$ts = strtotime("-$i months");
			$labels[] = date('M Y', $ts);
			$dates[]  = date('Y-m', $ts);
		}

		return ['labels' => $labels, 'dates' => $dates];
	}

	// ==============================================
	// HELPER: Count per bulan
	// ==============================================
	private function get_monthly_count($table, $months)
	{
		$result = [];
		foreach ($months as $month) {
			// Cara 1: Pakai where() dengan 3 parameter — set FALSE di param ke-3
			// untuk mencegah CI escape otomatis
			$this->db->where("DATE_FORMAT(created_at, '%Y-%m') = ", $month, FALSE);
			$count = $this->db->count_all_results($table);
			$result[] = $count;

			// Reset query builder
			$this->db->reset_query();
		}
		return $result;
	}

	// ==============================================
	// HELPER: Data 7 hari terakhir
	// ==============================================
	private function get_weekly_data($table)
	{
		$result = ['labels' => [], 'data' => []];
		for ($i = 6; $i >= 0; $i--) {
			$date = date('Y-m-d', strtotime("-$i days"));
			$count = $this->db->where('DATE(created_at)', $date)->count_all_results($table);
			$result['labels'][] = date('D', strtotime($date));
			$result['data'][]   = $count;
		}
		return $result;
	}

	// ==============================================
	// HELPER: Statistik gender
	// ==============================================
	private function get_students_by_gender()
	{
		$male   = $this->db->where('is_active', 1)->where('gender', 'Laki-laki')->count_all_results('students');
		$female = $this->db->where('is_active', 1)->where('gender', 'Perempuan')->count_all_results('students');
		return ['male' => $male, 'female' => $female];
	}

	// ==============================================
	// HELPER: Statistik per kelas
	// ==============================================
	private function get_students_by_class()
	{
		$this->db->select('classes.class_name, classes.grade_level, COUNT(students.id_student) as total');
		$this->db->from('classes');
		$this->db->join('students', 'classes.id_class = students.id_class AND students.is_active = 1', 'left');
		$this->db->group_by('classes.id_class');
		$this->db->order_by('classes.grade_level', 'ASC');
		return $this->db->get()->result();
	}

	// ==============================================
	// HELPER: Statistik aspirasi
	// ==============================================
	private function get_aspirations_by_status()
	{
		$this->db->select('status, COUNT(*) as total');
		$this->db->from('aspirations');
		$this->db->group_by('status');
		$result = $this->db->get()->result();

		$statuses = ['pending' => 0, 'dibaca' => 0, 'diproses' => 0, 'selesai' => 0];
		foreach ($result as $r) {
			if (isset($statuses[$r->status])) {
				$statuses[$r->status] = (int)$r->total;
			}
		}
		return $statuses;
	}

	// ==============================================
	// HELPER: CSV escape
	// ==============================================
	private function csv_escape($value)
	{
		$value = (string)($value ?? '');
		$value = str_replace('"', '""', $value);
		return '"' . $value . '"';
	}
}

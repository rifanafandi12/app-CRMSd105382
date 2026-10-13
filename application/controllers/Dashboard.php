<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->require_login();
        $this->load->model('Chat_model');
        $this->load->model('User_model');
    }

    public function index() {
        $data['title']     = 'Dashboard';
        $data['user_id']   = $this->user_id;
        $data['user_name'] = $this->user_name;
        $data['user_role'] = $this->user_role;

        // Statistik umum
        $data['unread_count'] = $this->Chat_model->get_unread_count($this->user_id);

        if ($this->user_role === 'admin') {
            $this->load_admin_data($data);
        } elseif ($this->user_role === 'teacher') {
            $this->load_teacher_data($data);
        } else {
            $this->load_parent_data($data);
        }

        $this->render('dashboard/index', $data);
    }

    private function load_admin_data(&$data) {
        $data['total_parents']       = $this->db->where('role', 'parent')->where('is_active', 1)->count_all_results('users');
        $data['total_teachers']      = $this->db->where('role', 'teacher')->where('is_active', 1)->count_all_results('users');
        $data['total_students']      = $this->db->count_all('students');
        $data['total_classes']       = $this->db->count_all('classes');
        $data['today_messages']      = $this->db->where('DATE(created_at)', date('Y-m-d'))->count_all_results('chat_messages');
        $data['total_aspirations']   = $this->db->count_all('aspirations');
        $data['total_announcements'] = $this->db->count_all('announcements');
        $data['online_users']        = $this->db->where('is_online', 1)->count_all_results('user_status');

        // Recent activities
        $this->db->select('*');
        $this->db->from('log_activities');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(10);
        $data['recent_activities'] = $this->db->get()->result();

        // Weekly messages
        $data['weekly_messages'] = $this->get_weekly_messages();
    }

    private function load_teacher_data(&$data) {
        $teacher_id = $this->user_id;

        // Kelas yang diwalikan
        $this->db->where('homeroom_teacher_id', $teacher_id);
        $data['my_classes'] = $this->db->get('classes')->result();

        // Siswa di kelas tersebut
        $class_ids = array_column($data['my_classes'], 'id_class');
        if (!empty($class_ids)) {
            $this->db->select('students.*, classes.class_name');
            $this->db->from('students');
            $this->db->join('classes', 'students.id_class = classes.id_class', 'left');
            $this->db->where_in('students.id_class', $class_ids);
            $this->db->where('students.is_active', 1);
            $data['my_students'] = $this->db->get()->result();
        } else {
            $data['my_students'] = [];
        }

        $data['pending_aspirations'] = $this->db->where('status', 'pending')->count_all_results('aspirations');
    }

    private function load_parent_data(&$data) {
        $this->db->select('students.*, classes.class_name');
        $this->db->from('students');
        $this->db->join('classes', 'students.id_class = classes.id_class', 'left');
        $this->db->where('students.id_user_parent', $this->user_id);
        $this->db->where('students.is_active', 1);
        $data['my_children'] = $this->db->get()->result();

        $this->db->order_by('posted_date', 'DESC');
        $this->db->where('is_published', 1);
        $this->db->limit(5);
        $data['recent_announcements'] = $this->db->get('announcements')->result();
    }

    private function get_weekly_messages() {
        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $count = $this->db->where('DATE(created_at)', $date)->count_all_results('chat_messages');
            $result[] = [
                'date'  => date('D', strtotime($date)),
                'count' => $count,
            ];
        }
        return $result;
    }
}

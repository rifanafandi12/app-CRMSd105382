<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_daily_report($date = null) {
        if(!$date) $date = date('Y-m-d');
        
        $login_count = $this->db->where('DATE(created_at)', $date)
                                 ->where('activity_type', 'login')
                                 ->count_all_results('log_activities');
        
        $message_count = $this->db->where('DATE(created_at)', $date)
                                  ->count_all_results('chat_messages');
        
        $aspiration_count = $this->db->where('DATE(created_at)', $date)
                                     ->count_all_results('aspirations');
        
        return (object)[
            'date' => $date,
            'logins' => $login_count,
            'messages' => $message_count,
            'aspirations' => $aspiration_count
        ];
    }

    public function get_weekly_report() {
        $start_date = date('Y-m-d', strtotime('-6 days'));
        $end_date = date('Y-m-d');
        
        $daily_reports = [];
        for($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $daily_reports[] = $this->get_daily_report($date);
        }
        
        $total_messages = $this->db->where('created_at >=', $start_date)
                                   ->count_all_results('chat_messages');
        
        $total_aspirations = $this->db->where('created_at >=', $start_date)
                                      ->count_all_results('aspirations');
        
        return (object)[
            'start_date' => $start_date,
            'end_date' => $end_date,
            'daily_reports' => $daily_reports,
            'total_messages' => $total_messages,
            'total_aspirations' => $total_aspirations
        ];
    }

    public function get_monthly_report($month = null) {
        if(!$month) $month = date('Y-m');
        
        $start_date = $month . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));
        
        $messages = $this->db->where('created_at >=', $start_date)
                             ->where('created_at <=', $end_date)
                             ->count_all_results('chat_messages');
        
        $aspirations = $this->db->where('created_at >=', $start_date)
                                ->where('created_at <=', $end_date)
                                ->count_all_results('aspirations');
        
        $new_users = $this->db->where('created_at >=', $start_date)
                              ->where('created_at <=', $end_date)
                              ->count_all_results('users');
        
        return (object)[
            'month' => $month,
            'messages' => $messages,
            'aspirations' => $aspirations,
            'new_users' => $new_users
        ];
    }

    public function get_yearly_report($year = null) {
        if(!$year) $year = date('Y');
        
        $monthly_reports = [];
        for($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $monthly_reports[] = $this->get_monthly_report($year . '-' . $month);
        }
        
        return $monthly_reports;
    }

    public function get_system_summary() {
        $total_users = $this->db->count_all('users');
        $active_users = $this->db->where('is_active', 1)->count_all_results('users');
        $online_users = $this->db->where('is_online', 1)->count_all_results('user_status');
        
        $total_students = $this->db->count_all('students');
        $total_classes = $this->db->count_all('classes');
        
        $total_messages = $this->db->count_all('chat_messages');
        $total_aspirations = $this->db->count_all('aspirations');
        $total_announcements = $this->db->count_all('announcements');
        
        return (object)[
            'users' => (object)[
                'total' => $total_users,
                'active' => $active_users,
                'online' => $online_users
            ],
            'students' => $total_students,
            'classes' => $total_classes,
            'messages' => $total_messages,
            'aspirations' => $total_aspirations,
            'announcements' => $total_announcements
        ];
    }
}
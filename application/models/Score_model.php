<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Score_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_subjects() {
        $this->db->order_by('subject_name', 'ASC');
        return $this->db->get('academic_subjects')->result();
    }

    public function get_students_by_class($class_id) {
        $this->db->select('id_student, student_name, nisn');
        $this->db->where('id_class', $class_id);
        $this->db->where('is_active', 1);
        $this->db->order_by('student_name', 'ASC');
        return $this->db->get('students')->result();
    }

    public function get_scores_by_student($student_id, $semester = null, $academic_year = null) {
        $this->db->select('scores.*, academic_subjects.subject_name');
        $this->db->from('scores');
        $this->db->join('academic_subjects', 'scores.id_subject = academic_subjects.id_subject');
        $this->db->where('scores.id_student', $student_id);
        
        if($semester) {
            $this->db->where('scores.semester', $semester);
        }
        if($academic_year) {
            $this->db->where('scores.academic_year', $academic_year);
        }
        
        $this->db->order_by('academic_subjects.subject_name', 'ASC');
        return $this->db->get()->result();
    }

    public function save_score($data) {
        $exists = $this->db->where('id_student', $data['id_student'])
                           ->where('id_subject', $data['id_subject'])
                           ->where('semester', $data['semester'])
                           ->where('academic_year', $data['academic_year'])
                           ->get('scores')
                           ->row();
        
        if($exists) {
            $this->db->where('id_score', $exists->id_score);
            return $this->db->update('scores', $data);
        } else {
            return $this->db->insert('scores', $data);
        }
    }
    
    public function get_raport($student_id, $semester, $academic_year) {
        $scores = $this->get_scores_by_student($student_id, $semester, $academic_year);
        
        $total = 0;
        $count = 0;
        foreach($scores as $score) {
            if($score->final_score) {
                $total += $score->final_score;
                $count++;
            }
        }
        
        return (object)[
            'scores' => $scores,
            'average' => $count > 0 ? round($total / $count, 2) : 0,
            'total_subjects' => $count
        ];
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Class_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all classes with homeroom teacher info
     * @return array
     */
    public function get_all_classes() {
        $this->db->select('classes.*, users.name as homeroom_teacher_name');
        $this->db->from('classes');
        $this->db->join('users', 'classes.homeroom_teacher_id = users.id_user', 'left');
        $this->db->order_by('classes.grade_level', 'ASC');
        $this->db->order_by('classes.class_name', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get class by ID
     * @param int $id
     * @return object|null
     */
    public function get_class_by_id($id) {
        $this->db->select('classes.*, users.name as homeroom_teacher_name');
        $this->db->from('classes');
        $this->db->join('users', 'classes.homeroom_teacher_id = users.id_user', 'left');
        $this->db->where('classes.id_class', $id);
        return $this->db->get()->row();
    }

    /**
     * Get classes by grade level
     * @param int $grade_level
     * @return array
     */
    public function get_classes_by_grade($grade_level) {
        $this->db->where('grade_level', $grade_level);
        $this->db->order_by('class_name', 'ASC');
        return $this->db->get('classes')->result();
    }

    /**
     * Add new class
     * @param array $data
     * @return int|bool
     */
    public function add_class($data) {
        $this->db->insert('classes', $data);
        return $this->db->insert_id();
    }

    /**
     * Update class data
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_class($id, $data) {
        $this->db->where('id_class', $id);
        return $this->db->update('classes', $data);
    }

    /**
     * Delete class
     * @param int $id
     * @return bool
     */
    public function delete_class($id) {
        $this->db->where('id_class', $id);
        return $this->db->delete('classes');
    }

    /**
     * Check if class has students
     * @param int $id
     * @return bool
     */
    public function has_students($id) {
        $this->db->where('id_class', $id);
        $count = $this->db->count_all_results('students');
        return $count > 0;
    }

    /**
     * Get class statistics
     * @param int $id
     * @return object
     */
    public function get_class_stats($id) {
        // Total students
        $student_count = $this->db->where('id_class', $id)->where('is_active', 1)->count_all_results('students');
        
        // Male students
        $male_count = $this->db->where('id_class', $id)->where('gender', 'Laki-laki')->where('is_active', 1)->count_all_results('students');
        
        // Female students
        $female_count = $this->db->where('id_class', $id)->where('gender', 'Perempuan')->where('is_active', 1)->count_all_results('students');
        
        return (object)[
            'total_students' => $student_count,
            'male_count' => $male_count,
            'female_count' => $female_count
        ];
    }

    /**
     * Get classes by academic year
     * @param string $academic_year
     * @return array
     */
    public function get_classes_by_academic_year($academic_year) {
        $this->db->like('academic_year', $academic_year);
        $this->db->order_by('grade_level', 'ASC');
        return $this->db->get('classes')->result();
    }

    /**
     * Get available grade levels
     * @return array
     */
    public function get_grade_levels() {
        $this->db->distinct();
        $this->db->select('grade_level');
        $this->db->order_by('grade_level', 'ASC');
        $result = $this->db->get('classes')->result();
        
        $levels = [];
        foreach($result as $row) {
            $levels[] = $row->grade_level;
        }
        return $levels;
    }

    /**
     * Get class options for dropdown
     * @param bool $include_empty
     * @return array
     */
    public function get_class_options($include_empty = false) {
        $options = [];
        if($include_empty) {
            $options[''] = '-- Pilih Kelas --';
        }
        
        $classes = $this->get_all_classes();
        foreach($classes as $class) {
            $options[$class->id_class] = $class->class_name . ' (Kelas ' . $class->grade_level . ')';
        }
        return $options;
    }

    /**
     * Get class with teacher and student count
     * @return array
     */
    public function get_classes_with_stats() {
        $this->db->select('classes.*, users.name as homeroom_teacher, COUNT(students.id_student) as student_count');
        $this->db->from('classes');
        $this->db->join('users', 'classes.homeroom_teacher_id = users.id_user', 'left');
        $this->db->join('students', 'classes.id_class = students.id_class AND students.is_active = 1', 'left');
        $this->db->group_by('classes.id_class');
        $this->db->order_by('classes.grade_level', 'ASC');
        return $this->db->get()->result();
    }
}
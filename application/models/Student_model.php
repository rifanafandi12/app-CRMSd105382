<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all students with class and parent info
     * @return array
     */
    public function get_all_students() {
        $this->db->select('students.*, classes.class_name, classes.grade_level, users.name as parent_name, users.email as parent_email');
        $this->db->from('students');
        $this->db->join('classes', 'students.id_class = classes.id_class', 'left');
        $this->db->join('users', 'students.id_user_parent = users.id_user', 'left');
        $this->db->order_by('classes.grade_level', 'ASC');
        $this->db->order_by('students.student_name', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get student by ID
     * @param int $id
     * @return object|null
     */
    public function get_student_by_id($id) {
        $this->db->select('students.*, classes.class_name, classes.grade_level, users.name as parent_account_name');
        $this->db->from('students');
        $this->db->join('classes', 'students.id_class = classes.id_class', 'left');
        $this->db->join('users', 'students.id_user_parent = users.id_user', 'left');
        $this->db->where('students.id_student', $id);
        return $this->db->get()->row();
    }

    /**
     * Get students by class
     * @param int $class_id
     * @return array
     */
    public function get_students_by_class($class_id) {
        $this->db->where('id_class', $class_id);
        $this->db->where('is_active', 1);
        $this->db->order_by('student_name', 'ASC');
        return $this->db->get('students')->result();
    }

    /**
     * Get students by parent
     * @param int $parent_id
     * @return array
     */
    public function get_students_by_parent($parent_id) {
        $this->db->select('students.*, classes.class_name');
        $this->db->from('students');
        $this->db->join('classes', 'students.id_class = classes.id_class', 'left');
        $this->db->where('students.id_user_parent', $parent_id);
        $this->db->where('students.is_active', 1);
        $this->db->order_by('students.student_name', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Add new student
     * @param array $data
     * @return int|bool
     */
    public function add_student($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('students', $data);
        return $this->db->insert_id();
    }

    /**
     * Update student data
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_student($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id_student', $id);
        return $this->db->update('students', $data);
    }

    /**
     * Delete student (soft delete)
     * @param int $id
     * @return bool
     */
    public function delete_student($id) {
        $this->db->where('id_student', $id);
        return $this->db->update('students', ['is_active' => 0]);
    }

    /**
     * Hard delete student
     * @param int $id
     * @return bool
     */
    public function hard_delete_student($id) {
        $this->db->where('id_student', $id);
        return $this->db->delete('students');
    }

    /**
     * Count students by class
     * @param int $class_id
     * @return int
     */
    public function count_students_by_class($class_id) {
        $this->db->where('id_class', $class_id);
        $this->db->where('is_active', 1);
        return $this->db->count_all_results('students');
    }

    /**
     * Get total students count
     * @return int
     */
    public function count_all_students() {
        $this->db->where('is_active', 1);
        return $this->db->count_all_results('students');
    }

    /**
     * Search students by keyword
     * @param string $keyword
     * @return array
     */
    public function search_students($keyword) {
        $this->db->select('students.*, classes.class_name');
        $this->db->from('students');
        $this->db->join('classes', 'students.id_class = classes.id_class', 'left');
        $this->db->group_start();
        $this->db->like('students.student_name', $keyword);
        $this->db->or_like('students.nisn', $keyword);
        $this->db->or_like('students.parent_name', $keyword);
        $this->db->or_like('students.parent_phone', $keyword);
        $this->db->group_end();
        $this->db->where('students.is_active', 1);
        $this->db->order_by('students.student_name', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get student by NISN
     * @param string $nisn
     * @return object|null
     */
    public function get_student_by_nisn($nisn) {
        $this->db->where('nisn', $nisn);
        return $this->db->get('students')->row();
    }

    /**
     * Get students with no parent account linked
     * @return array
     */
    public function get_students_without_parent_account() {
        $this->db->where('id_user_parent IS NULL', null, false);
        $this->db->where('is_active', 1);
        $this->db->order_by('student_name', 'ASC');
        return $this->db->get('students')->result();
    }

    /**
     * Link parent account to student
     * @param int $student_id
     * @param int $parent_id
     * @return bool
     */
    public function link_parent($student_id, $parent_id) {
        $this->db->where('id_student', $student_id);
        return $this->db->update('students', ['id_user_parent' => $parent_id]);
    }

    /**
     * Get students count by gender
     * @return object
     */
    public function get_gender_stats() {
        $this->db->select("
            SUM(CASE WHEN gender = 'Laki-laki' THEN 1 ELSE 0 END) as male_count,
            SUM(CASE WHEN gender = 'Perempuan' THEN 1 ELSE 0 END) as female_count
        ");
        $this->db->where('is_active', 1);
        return $this->db->get('students')->row();
    }

    /**
     * Get students distribution by class
     * @return array
     */
    public function get_class_distribution() {
        $this->db->select('classes.class_name, COUNT(students.id_student) as student_count');
        $this->db->from('classes');
        $this->db->join('students', 'classes.id_class = students.id_class AND students.is_active = 1', 'left');
        $this->db->group_by('classes.id_class');
        $this->db->order_by('classes.grade_level', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Generate NISN automatically
     * @param int $enrollment_year
     * @return string
     */
    public function generate_nisn($enrollment_year) {
        $this->db->select_max('nisn');
        $last = $this->db->get('students')->row();
        
        if($last && $last->nisn) {
            $last_number = (int)substr($last->nisn, -4);
            $new_number = str_pad($last_number + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $new_number = '0001';
        }
        
        return $enrollment_year . $new_number;
    }
}
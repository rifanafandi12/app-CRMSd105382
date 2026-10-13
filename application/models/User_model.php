<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Login user
     * @param string $username
     * @param string $password
     * @return object|bool
     */
    public function login($username, $password) {
        $this->db->where('username', $username);
        $this->db->where('is_active', 1);
        $query = $this->db->get('users');
        
        if($query->num_rows() == 1) {
            $user = $query->row();
            if(password_verify($password, $user->password)) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Register new user
     * @param array $data
     * @return bool
     */
    public function register($data) {
        return $this->db->insert('users', $data);
    }

    /**
     * Get user by ID
     * @param int $id
     * @return object|null
     */
    public function get_user_by_id($id) {
        $this->db->where('id_user', $id);
        return $this->db->get('users')->row();
    }

    /**
     * Get user by username
     * @param string $username
     * @return object|null
     */
    public function get_user_by_username($username) {
        $this->db->where('username', $username);
        return $this->db->get('users')->row();
    }

    /**
     * Get user by email
     * @param string $email
     * @return object|null
     */
    public function get_user_by_email($email) {
        $this->db->where('email', $email);
        return $this->db->get('users')->row();
    }

    /**
     * Update user data
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_user($id, $data) {
        $this->db->where('id_user', $id);
        return $this->db->update('users', $data);
    }

    /**
     * Update last login timestamp
     * @param int $id
     * @return bool
     */
    public function update_last_login($id) {
        $this->db->where('id_user', $id);
        return $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get all users with optional role filter
     * @param string|null $role
     * @return array
     */
    public function get_all_users($role = null) {
        if($role) {
            $this->db->where('role', $role);
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get('users')->result();
    }

    /**
     * Get active users only
     * @param string|null $role
     * @return array
     */
    public function get_active_users($role = null) {
        $this->db->where('is_active', 1);
        if($role) {
            $this->db->where('role', $role);
        }
        $this->db->order_by('name', 'ASC');
        return $this->db->get('users')->result();
    }

    /**
     * Delete user (soft delete)
     * @param int $id
     * @return bool
     */
    public function delete_user($id) {
        $this->db->where('id_user', $id);
        return $this->db->update('users', ['is_active' => 0]);
    }

    /**
     * Hard delete user (permanent)
     * @param int $id
     * @return bool
     */
    public function hard_delete_user($id) {
        $this->db->where('id_user', $id);
        return $this->db->delete('users');
    }

    /**
     * Count users by role
     * @param string $role
     * @return int
     */
    public function count_users_by_role($role) {
        $this->db->where('role', $role);
        $this->db->where('is_active', 1);
        return $this->db->count_all_results('users');
    }

    /**
     * Get total users count
     * @return int
     */
    public function count_all_users() {
        return $this->db->count_all('users');
    }

    /**
     * Change user password
     * @param int $id
     * @param string $new_password
     * @return bool
     */
    public function change_password($id, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $this->db->where('id_user', $id);
        return $this->db->update('users', ['password' => $hashed_password]);
    }

    /**
     * Verify user password
     * @param int $id
     * @param string $password
     * @return bool
     */
    public function verify_password($id, $password) {
        $user = $this->get_user_by_id($id);
        if($user) {
            return password_verify($password, $user->password);
        }
        return false;
    }

    /**
     * Search users by keyword
     * @param string $keyword
     * @param string|null $role
     * @return array
     */
    public function search_users($keyword, $role = null) {
        $this->db->group_start();
        $this->db->like('name', $keyword);
        $this->db->or_like('username', $keyword);
        $this->db->or_like('email', $keyword);
        $this->db->group_end();
        
        if($role) {
            $this->db->where('role', $role);
        }
        
        $this->db->where('is_active', 1);
        $this->db->order_by('name', 'ASC');
        return $this->db->get('users')->result();
    }

    /**
     * Get parents who don't have linked students yet
     * @return array
     */
    public function get_unlinked_parents() {
        $this->db->select('users.*');
        $this->db->from('users');
        $this->db->where('users.role', 'parent');
        $this->db->where('users.is_active', 1);
        $this->db->where('users.id_user NOT IN (SELECT COALESCE(id_user_parent, 0) FROM students)', NULL, FALSE);
        return $this->db->get()->result();
    }
}
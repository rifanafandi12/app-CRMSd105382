<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aspiration_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all aspirations with parent name
     * @param string|null $status
     * @return array
     */
    public function get_all_aspirations($status = null) {
        $this->db->select('aspirations.*, users.name as parent_name');
        $this->db->from('aspirations');
        $this->db->join('users', 'aspirations.id_parent = users.id_user');
        
        if($status) {
            $this->db->where('aspirations.status', $status);
        }
        
        $this->db->order_by('aspirations.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get aspirations by parent
     * @param int $parent_id
     * @return array
     */
    public function get_aspirations_by_parent($parent_id) {
        $this->db->where('id_parent', $parent_id);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('aspirations')->result();
    }

    /**
     * Get aspiration by ID with parent name
     * @param int $id
     * @return object|null
     */
    public function get_aspiration_by_id($id) {
        $this->db->select('aspirations.*, users.name as parent_name');
        $this->db->from('aspirations');
        $this->db->join('users', 'aspirations.id_parent = users.id_user');
        $this->db->where('aspirations.id_aspiration', $id);
        return $this->db->get()->row();
    }

    /**
     * Add new aspiration
     * @param array $data
     * @return int|bool
     */
    public function add_aspiration($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = 'pending';
        $this->db->insert('aspirations', $data);
        return $this->db->insert_id();
    }

    /**
     * Respond to aspiration
     * @param int $id
     * @param string $response
     * @param int $responded_by
     * @return bool
     */
    public function respond_aspiration($id, $response, $responded_by) {
        $data = [
            'response' => $response,
            'responded_by' => $responded_by,
            'responded_at' => date('Y-m-d H:i:s'),
            'status' => 'selesai',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('id_aspiration', $id);
        return $this->db->update('aspirations', $data);
    }

    /**
     * Delete aspiration
     * @param int $id
     * @return bool
     */
    public function delete_aspiration($id) {
        $aspiration = $this->get_aspiration_by_id($id);
        
        $this->db->where('id_aspiration', $id);
        if($this->db->delete('aspirations')) {
            // Delete attachment if exists
            if($aspiration && !empty($aspiration->attachment) && file_exists($aspiration->attachment)) {
                unlink($aspiration->attachment);
            }
            return true;
        }
        return false;
    }

    /**
     * Get aspiration statistics
     * @return object
     */
    public function get_stats() {
        $total = $this->db->count_all('aspirations');
        $pending = $this->db->where('status', 'pending')->count_all_results('aspirations');
        $processed = $this->db->where('status', 'diproses')->count_all_results('aspirations');
        $completed = $this->db->where('status', 'selesai')->count_all_results('aspirations');
        
        return (object)[
            'total' => $total,
            'pending' => $pending,
            'processed' => $processed,
            'completed' => $completed
        ];
    }
}
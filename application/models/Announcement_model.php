<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Announcement_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all announcements
     * @param bool $only_published
     * @return array
     */
    public function get_all_announcements($only_published = false) {
        if($only_published) {
            $this->db->where('is_published', 1);
            $this->db->where('expiry_date >=', date('Y-m-d'));
        }
        
        $this->db->select('announcements.*, users.name as author_name');
        $this->db->from('announcements');
        $this->db->join('users', 'announcements.posted_by = users.id_user');
        $this->db->order_by('announcements.posted_date', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get announcement by ID
     * @param int $id
     * @return object|null
     */
    public function get_announcement_by_id($id) {
        $this->db->select('announcements.*, users.name as author_name');
        $this->db->from('announcements');
        $this->db->join('users', 'announcements.posted_by = users.id_user');
        $this->db->where('announcements.id_announcement', $id);
        return $this->db->get()->row();
    }

    /**
     * Get announcements by category
     * @param string $category
     * @param bool $only_published
     * @return array
     */
    public function get_announcements_by_category($category, $only_published = true) {
        $this->db->where('category', $category);
        
        if($only_published) {
            $this->db->where('is_published', 1);
            $this->db->where('expiry_date >=', date('Y-m-d'));
        }
        
        $this->db->order_by('posted_date', 'DESC');
        return $this->db->get('announcements')->result();
    }

    /**
     * Get announcements for a specific class
     * @param int $class_id
     * @return array
     */
    public function get_announcements_for_class($class_id) {
        $this->db->where('is_published', 1);
        $this->db->where('expiry_date >=', date('Y-m-d'));
        $this->db->group_start();
        $this->db->where('target_class_id', $class_id);
        $this->db->or_where('target_class_id IS NULL');
        $this->db->group_end();
        $this->db->order_by('posted_date', 'DESC');
        return $this->db->get('announcements')->result();
    }

    /**
     * Add new announcement
     * @param array $data
     * @return int|bool
     */
    public function add_announcement($data) {
        $data['posted_date'] = date('Y-m-d H:i:s');
        $this->db->insert('announcements', $data);
        return $this->db->insert_id();
    }

    /**
     * Update announcement
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_announcement($id, $data) {
        $this->db->where('id_announcement', $id);
        return $this->db->update('announcements', $data);
    }

    /**
     * Delete announcement
     * @param int $id
     * @return bool
     */
    public function delete_announcement($id) {
        // Get attachment path first
        $announcement = $this->get_announcement_by_id($id);
        
        $this->db->where('id_announcement', $id);
        if($this->db->delete('announcements')) {
            // Delete attachment file if exists
            if($announcement && $announcement->attachment && file_exists($announcement->attachment)) {
                unlink($announcement->attachment);
            }
            return true;
        }
        return false;
    }

    /**
     * Increment view count
     * @param int $id
     * @return bool
     */
    public function increment_view_count($id) {
        $this->db->set('view_count', 'view_count+1', false);
        $this->db->where('id_announcement', $id);
        return $this->db->update('announcements');
    }

    /**
     * Get recent announcements
     * @param int $limit
     * @return array
     */
    public function get_recent_announcements($limit = 5) {
        $this->db->where('is_published', 1);
        $this->db->where('expiry_date >=', date('Y-m-d'));
        $this->db->order_by('posted_date', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('announcements')->result();
    }

    /**
     * Get announcement statistics
     * @return object
     */
    public function get_stats() {
        $total = $this->db->count_all('announcements');
        $published = $this->db->where('is_published', 1)->count_all_results('announcements');
        
        return (object)[
            'total' => $total,
            'published' => $published,
            'draft' => $total - $published
        ];
    }
}
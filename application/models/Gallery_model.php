<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gallery_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_gallery($limit = null, $offset = 0, $category = null) {
        $this->db->select('gallery.*, users.name as uploader_name');
        $this->db->from('gallery');
        $this->db->join('users', 'gallery.uploaded_by = users.id_user', 'left');
        $this->db->where('gallery.is_published', 1);
        
        if($category && $category != 'all') {
            $this->db->where('gallery.category', $category);
        }
        
        $this->db->order_by('gallery.created_at', 'DESC');
        
        if($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result();
    }

    public function get_all_gallery_admin($limit = null, $offset = 0) {
        $this->db->select('gallery.*, users.name as uploader_name');
        $this->db->from('gallery');
        $this->db->join('users', 'gallery.uploaded_by = users.id_user', 'left');
        $this->db->order_by('gallery.created_at', 'DESC');
        
        if($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result();
    }

    public function get_gallery_by_id($id) {
        $this->db->select('gallery.*, users.name as uploader_name');
        $this->db->from('gallery');
        $this->db->join('users', 'gallery.uploaded_by = users.id_user', 'left');
        $this->db->where('gallery.id_gallery', $id);
        return $this->db->get()->row();
    }

    public function add_gallery($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('gallery', $data);
    }

    public function update_gallery($id, $data) {
        $this->db->where('id_gallery', $id);
        return $this->db->update('gallery', $data);
    }

    public function delete_gallery($id) {
        // Get image path first
        $gallery = $this->get_gallery_by_id($id);
        if($gallery && !empty($gallery->image_path) && file_exists('./assets/uploads/' . $gallery->image_path)) {
            unlink('./assets/uploads/' . $gallery->image_path);
        }
        
        $this->db->where('id_gallery', $id);
        return $this->db->delete('gallery');
    }

    public function increment_view_count($id) {
        $this->db->set('view_count', 'view_count+1', false);
        $this->db->where('id_gallery', $id);
        return $this->db->update('gallery');
    }

    public function count_gallery($category = null) {
        $this->db->where('is_published', 1);
        if($category && $category != 'all') {
            $this->db->where('category', $category);
        }
        return $this->db->count_all_results('gallery');
    }

    public function get_featured_gallery($limit = 6) {
        $this->db->select('gallery.*, users.name as uploader_name');
        $this->db->from('gallery');
        $this->db->join('users', 'gallery.uploaded_by = users.id_user', 'left');
        $this->db->where('gallery.is_published', 1);
        $this->db->order_by('gallery.view_count', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }
    
    public function get_categories() {
        return [
            'kegiatan' => 'Kegiatan Sekolah',
            'prestasi' => 'Prestasi',
            'fasilitas' => 'Fasilitas',
            'acara' => 'Acara',
            'lainnya' => 'Lainnya'
        ];
    }
}
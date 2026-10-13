<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Model dengan fitur umum
 */
class MY_Model extends CI_Model {

    protected $table;
    protected $primary_key = 'id';
    protected $fillable = [];
    protected $soft_delete = false;
    protected $timestamps = true;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all records dengan filter
     */
    public function all($conditions = [], $order_by = null, $limit = null, $offset = 0) {
        if ($this->soft_delete) {
            $conditions['deleted_at'] = null;
        }
        
        if (!empty($conditions)) {
            $this->db->where($conditions);
        }
        
        if ($order_by) {
            if (is_array($order_by)) {
                foreach ($order_by as $col => $dir) {
                    $this->db->order_by($col, $dir);
                }
            } else {
                $this->db->order_by($order_by);
            }
        }
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get($this->table)->result();
    }

    /**
     * Find by primary key
     */
    public function find($id) {
        $this->db->where($this->primary_key, $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Find by kondisi tertentu
     */
    public function find_by($conditions = []) {
        if ($this->soft_delete) {
            $conditions['deleted_at'] = null;
        }
        $this->db->where($conditions);
        return $this->db->get($this->table)->row();
    }

    /**
     * Create record
     */
    public function create($data) {
        // Filter hanya field yang fillable
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update record
     */
    public function update($id, $data) {
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete record (soft atau hard)
     */
    public function delete($id) {
        $this->db->where($this->primary_key, $id);
        
        if ($this->soft_delete) {
            return $this->db->update($this->table, ['deleted_at' => date('Y-m-d H:i:s')]);
        }
        
        return $this->db->delete($this->table);
    }

    /**
     * Count records
     */
    public function count($conditions = []) {
        if ($this->soft_delete) {
            $conditions['deleted_at'] = null;
        }
        
        if (!empty($conditions)) {
            $this->db->where($conditions);
        }
        
        return $this->db->count_all_results($this->table);
    }

    /**
     * Check existence
     */
    public function exists($conditions = []) {
        return $this->count($conditions) > 0;
    }
}

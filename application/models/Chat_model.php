<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chat_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Get all users that current user can chat with (based on role)
	 * @param int $user_id
	 * @param string $user_role
	 * @return array
	 */
	public function get_available_contacts($user_id, $user_role)
	{
		if ($user_role == 'admin') {
			// Admin bisa chat dengan semua parent dan teacher
			$this->db->select('users.id_user, users.name, users.role, COALESCE(user_status.is_online, 0) as is_online');
			$this->db->from('users');
			$this->db->join('user_status', 'users.id_user = user_status.id_user', 'left');
			$this->db->where_in('users.role', ['parent', 'teacher']);
			$this->db->where('users.is_active', 1);
			$this->db->where('users.id_user !=', $user_id);
			$this->db->order_by('users.name', 'ASC');
			return $this->db->get()->result();
		} elseif ($user_role == 'teacher') {
			// Guru bisa chat dengan admin DAN parent (orang tua)
			$this->db->select('users.id_user, users.name, users.role, COALESCE(user_status.is_online, 0) as is_online');
			$this->db->from('users');
			$this->db->join('user_status', 'users.id_user = user_status.id_user', 'left');
			$this->db->where_in('users.role', ['admin', 'parent']);  // PERBAIKAN: tambah parent
			$this->db->where('users.is_active', 1);
			$this->db->where('users.id_user !=', $user_id);
			$this->db->order_by('users.name', 'ASC');
			return $this->db->get()->result();
		} else {
			// Parent bisa chat dengan admin dan teacher
			$this->db->select('users.id_user, users.name, users.role, COALESCE(user_status.is_online, 0) as is_online');
			$this->db->from('users');
			$this->db->join('user_status', 'users.id_user = user_status.id_user', 'left');
			$this->db->where_in('users.role', ['admin', 'teacher']);
			$this->db->where('users.is_active', 1);
			$this->db->where('users.id_user !=', $user_id);
			$this->db->order_by('users.name', 'ASC');
			return $this->db->get()->result();
		}
	}

	/**
	 * Get conversation between two users
	 * @param int $user1
	 * @param int $user2
	 * @return array
	 */
	public function get_conversation($user1, $user2)
	{
		// Cast ke integer untuk safety
		$user1 = (int)$user1;
		$user2 = (int)$user2;

		if (!$user1 || !$user2) {
			return [];
		}

		$this->db->select('
        chat_messages.*,
        sender.name as sender_name,
        receiver.name as receiver_name
    ');
		$this->db->from('chat_messages');
		$this->db->join('users as sender', 'chat_messages.id_sender = sender.id_user', 'left');
		$this->db->join('users as receiver', 'chat_messages.id_receiver = receiver.id_user', 'left');
		$this->db->group_start();
		$this->db->where('chat_messages.id_sender', $user1);
		$this->db->where('chat_messages.id_receiver', $user2);
		$this->db->group_end();
		$this->db->or_group_start();
		$this->db->where('chat_messages.id_sender', $user2);
		$this->db->where('chat_messages.id_receiver', $user1);
		$this->db->group_end();
		$this->db->order_by('chat_messages.created_at', 'ASC');

		return $this->db->get()->result();
	}

	/**
	 * Get recent chats with last message (sorted by latest message DESC)
	 * @param int $user_id
	 * @return array
	 */
	public function get_recent_chats($user_id)
	{
		// Query yang lebih sederhana dan reliable
		$sql = "SELECT 
                    m.id_message,
                    m.id_sender,
                    m.id_receiver,
                    m.message,
                    m.is_read,
                    m.created_at,
                    CASE 
                        WHEN m.id_sender = ? THEN u2.name
                        ELSE u1.name
                    END as contact_name,
                    CASE 
                        WHEN m.id_sender = ? THEN u2.id_user
                        ELSE u1.id_user
                    END as contact_id,
                    CASE 
                        WHEN m.id_sender = ? THEN u2.role
                        ELSE u1.role
                    END as contact_role
                FROM chat_messages m
                INNER JOIN users u1 ON m.id_sender = u1.id_user
                INNER JOIN users u2 ON m.id_receiver = u2.id_user
                WHERE m.id_message IN (
                    SELECT MAX(sub.id_message) 
                    FROM chat_messages sub
                    WHERE sub.id_sender = ? OR sub.id_receiver = ?
                    GROUP BY CASE 
                        WHEN sub.id_sender = ? THEN sub.id_receiver
                        ELSE sub.id_sender
                    END
                )
                ORDER BY m.created_at DESC";

		$result = $this->db->query($sql, [
			$user_id,
			$user_id,
			$user_id,
			$user_id,
			$user_id,
			$user_id
		]);

		return $result->result();
	}

	/**
	 * Get unread count per contact
	 * @param int $user_id
	 * @return array
	 */
	public function get_unread_count_per_contact($user_id)
	{
		$this->db->select('id_sender, COUNT(*) as count');
		$this->db->from('chat_messages');
		$this->db->where('id_receiver', $user_id);
		$this->db->where('is_read', 0);
		$this->db->group_by('id_sender');
		$query = $this->db->get();

		$result = [];
		foreach ($query->result() as $row) {
			$result[$row->id_sender] = $row->count;
		}
		return $result;
	}

	/**
	 * Get last message between two users
	 * @param int $user1
	 * @param int $user2
	 * @return object|null
	 */
	public function get_last_message($user1, $user2)
	{
		$this->db->select('chat_messages.*, sender.name as sender_name');
		$this->db->from('chat_messages');
		$this->db->join('users as sender', 'chat_messages.id_sender = sender.id_user');
		$this->db->where("(chat_messages.id_sender = $user1 AND chat_messages.id_receiver = $user2)");
		$this->db->or_where("(chat_messages.id_sender = $user2 AND chat_messages.id_receiver = $user1)");
		$this->db->order_by('chat_messages.created_at', 'DESC');
		$this->db->limit(1);
		return $this->db->get()->row();
	}

	/**
	 * Save new message
	 * @param array $data
	 * @return int|bool
	 */
	public function save_message($data)
	{
		$data['created_at'] = date('Y-m-d H:i:s');
		$this->db->insert('chat_messages', $data);
		return $this->db->insert_id();
	}

	/**
	 * Mark messages as read
	 * @param int $sender_id
	 * @param int $receiver_id
	 * @return bool
	 */
	public function mark_messages_as_read($sender_id, $receiver_id)
	{
		$this->db->set('is_read', 1);
		$this->db->set('read_at', 'NOW()', false);
		$this->db->where('id_sender', $sender_id);
		$this->db->where('id_receiver', $receiver_id);
		$this->db->where('is_read', 0);
		return $this->db->update('chat_messages');
	}

	/**
	 * Get unread messages count for a user
	 * @param int $user_id
	 * @return int
	 */
	public function get_unread_count($user_id)
	{
		$this->db->select('COUNT(*) as count');
		$this->db->from('chat_messages');
		$this->db->where('id_receiver', $user_id);
		$this->db->where('is_read', 0);
		$result = $this->db->get()->row();
		return (int)$result->count;
	}

	/**
	 * Update user online status
	 * @param int $user_id
	 * @param bool $is_online
	 * @param string|null $socket_id
	 * @return bool
	 */
	public function update_user_status($user_id, $is_online, $socket_id = null)
	{
		$data = [
			'is_online' => $is_online ? 1 : 0,
			'last_activity' => date('Y-m-d H:i:s')
		];

		if ($socket_id) {
			$data['socket_id'] = $socket_id;
		}

		$exists = $this->db->where('id_user', $user_id)->get('user_status')->num_rows();

		if ($exists) {
			$this->db->where('id_user', $user_id);
			return $this->db->update('user_status', $data);
		} else {
			$data['id_user'] = $user_id;
			return $this->db->insert('user_status', $data);
		}
	}

	/**
	 * Get user online status
	 * @param int $user_id
	 * @return bool
	 */
	public function get_user_online_status($user_id)
	{
		$this->db->select('is_online');
		$this->db->where('id_user', $user_id);
		$result = $this->db->get('user_status')->row();

		if ($result) {
			return $result->is_online == 1;
		}
		return false;
	}

	/**
	 * Update last activity for a user (keep alive)
	 * @param int $user_id
	 * @return bool
	 */
	public function update_last_activity($user_id)
	{
		$this->db->where('id_user', $user_id);
		return $this->db->update('user_status', ['last_activity' => date('Y-m-d H:i:s')]);
	}
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chat extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->require_login();
		$this->load->model('Chat_model');
	}

	/**
	 * Halaman utama chat
	 */
	public function index()
	{
		$data['title']       = 'Pusat Komunikasi';
		$data['user_id']     = $this->user_id;
		$data['user_name']   = $this->user_name;
		$data['user_role']   = $this->user_role;
		$data['user_email']  = $this->session->userdata('email');

		// Data kontak & percakapan
		$data['contact_list']  = $this->Chat_model->get_available_contacts($this->user_id, $this->user_role);
		$data['recent_chats']  = $this->Chat_model->get_recent_chats($this->user_id);
		$data['unread_counts'] = $this->Chat_model->get_unread_count_per_contact($this->user_id);
		$data['total_unread']  = $this->Chat_model->get_unread_count($this->user_id);

		$this->render('chat/index', $data);
	}

	/**
	 * API: Ambil riwayat percakapan dengan user tertentu
	 * GET /chat/get_messages?with_user=<id>
	 */
	public function get_messages()
	{
		$user_id   = (int)$this->session->userdata('user_id');
		$with_user = (int)$this->input->get('with_user');

		if (!$user_id || !$with_user) {
			return $this->json_success([]);
		}

		$messages = $this->Chat_model->get_conversation($user_id, $with_user);

		// Mark as read
		$this->Chat_model->mark_messages_as_read($with_user, $user_id);

		$result = [];
		foreach ($messages as $msg) {
			$result[] = [
				'id_message'    => (int)$msg->id_message,
				'id_sender'     => (int)$msg->id_sender,
				'id_receiver'   => (int)$msg->id_receiver,
				'message'       => $msg->message,
				'is_read'       => (int)$msg->is_read,
				'created_at'    => $msg->created_at,
				'sender_name'   => $msg->sender_name ?? '',
				'receiver_name' => $msg->receiver_name ?? '',
			];
		}

		$this->json_success($result);
	}

	/**
	 * API: Kirim pesan (fallback — biasanya via Socket.io)
	 * POST /chat/send_message
	 */
	public function send_message()
	{
		$receiver_id = (int) $this->input->post('receiver_id');
		$message     = trim($this->input->post('message', TRUE));

		if (!$receiver_id || !$message) {
			return $this->json_error('Data tidak lengkap', 422);
		}

		if (mb_strlen($message) > 5000) {
			return $this->json_error('Pesan terlalu panjang (max 5000 karakter)', 422);
		}

		if (!$this->is_valid_contact($receiver_id, $this->user_id, $this->user_role)) {
			return $this->json_error('Penerima tidak valid', 403);
		}

		$data = [
			'id_sender'   => $this->user_id,
			'id_receiver' => $receiver_id,
			'message'     => $message,
		];

		$id = $this->Chat_model->save_message($data);
		return $this->json_success(['id_message' => $id]);
	}

	/**
	 * API: Jumlah pesan belum dibaca
	 */
	public function get_unread_count()
	{
		$count = $this->Chat_model->get_unread_count($this->user_id);
		return $this->json_success(['unread_count' => $count]);
	}

	/**
	 * API: Status online satu user
	 * GET /chat/get_online_status?user_id=<id>
	 */
	public function get_online_status()
	{
		$user_id = (int) $this->input->get('user_id');
		if (!$user_id) {
			return $this->json_error('User ID wajib diisi', 422);
		}

		$is_online = $this->Chat_model->get_user_online_status($user_id);
		return $this->json_success(['user_id' => $user_id, 'is_online' => $is_online]);
	}

	/**
	 * API: Daftar user online
	 */
	public function get_online_users()
	{
		$this->db->select('id_user');
		$this->db->where('is_online', 1);
		$rows = $this->db->get('user_status')->result();

		$online = array_map(fn($r) => (int) $r->id_user, $rows);
		return $this->json_success(['online_users' => $online]);
	}

	/**
	 * API: Heartbeat — keep user online
	 */
	public function heartbeat()
	{
		$this->Chat_model->update_last_activity($this->user_id);
		return $this->json_success();
	}

	/**
	 * Cek apakah user X boleh chat dengan user Y berdasarkan role
	 */
	private function is_valid_contact($contact_id, $my_id, $my_role)
	{
		if ($contact_id === $my_id) return false;

		$contact = $this->db->select('id_user, role, is_active')
			->where('id_user', $contact_id)
			->where('is_active', 1)
			->get('users')
			->row();

		if (!$contact) return false;

		// Aturan: admin ↔ semua, teacher ↔ admin+parent, parent ↔ admin+teacher
		$rules = [
			'admin'   => ['teacher', 'parent', 'admin'],
			'teacher' => ['admin', 'parent'],
			'parent'  => ['admin', 'teacher'],
		];

		return in_array($contact->role, $rules[$my_role] ?? []);
	}
}

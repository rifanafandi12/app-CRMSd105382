<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function login() {
        // Jika sudah login, redirect ke dashboard
        if ($this->is_logged_in) {
            redirect(base_url('dashboard'));
        }

        $data['title'] = 'Masuk ke Sistem';

        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('layouts/auth', array_merge($data, [
                'content_view' => 'auth/login',
            ]));
            return;
        }

        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);

        $user = $this->User_model->login($username, $password);

        if (!$user) {
            $this->session->set_flashdata('error', 'Username atau password salah.');
            redirect(base_url('auth/login'));
        }

        // Regenerate session ID
        $this->session->sess_regenerate(TRUE);

        $session_data = [
            'user_id'         => $user->id_user,
            'username'        => $user->username,
            'name'            => $user->name,
            'role'            => $user->role,
            'email'           => $user->email,
            'logged_in'       => TRUE,
            'login_time'      => time(),
            'last_regenerate' => time(),
        ];
        $this->session->set_userdata($session_data);

        // Update last login
        $this->User_model->update_last_login($user->id_user);

        // Update status online
        $this->db->where('id_user', $user->id_user);
        $this->db->update('user_status', [
            'is_online'     => 1,
            'last_activity' => date('Y-m-d H:i:s'),
        ]);

        log_activity($user->id_user, 'login', 'Login dari IP ' . $this->input->ip_address());

        // Redirect ke dashboard
        redirect(base_url('dashboard'));
    }

    public function logout() {
        $user_id = $this->session->userdata('user_id');
        if ($user_id) {
            log_activity($user_id, 'logout', 'User logout');
            $this->db->where('id_user', $user_id);
            $this->db->update('user_status', [
                'is_online'     => 0,
                'last_activity' => date('Y-m-d H:i:s'),
            ]);
        }
        $this->session->sess_destroy();
        redirect(base_url('login'));
    }

   
}

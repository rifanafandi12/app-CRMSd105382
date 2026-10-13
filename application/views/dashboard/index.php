<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
// Dispatch berdasarkan role
if ($user_role === 'admin') {
    $this->load->view('dashboard/admin', get_defined_vars());
} elseif ($user_role === 'teacher') {
    $this->load->view('dashboard/teacher', get_defined_vars());
} else {
    $this->load->view('dashboard/parent', get_defined_vars());
}
?>

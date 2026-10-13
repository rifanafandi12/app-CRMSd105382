<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// ==============================================
// HOOK: Security headers on every request
// ==============================================
$hook['post_controller_constructor'][] = [
    'class'    => 'Security_hook',
    'function' => 'check_request',
    'filename' => 'Security_hook.php',
    'filepath' => 'hooks',
    'params'   => [],
];

// ==============================================
// HOOK: Log slow queries / errors
// ==============================================
$hook['post_controller'][] = [
    'class'    => 'Security_hook',
    'function' => 'log_response',
    'filename' => 'Security_hook.php',
    'filepath' => 'hooks',
    'params'   => [],
];

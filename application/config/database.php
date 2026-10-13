<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// ==============================================
// DATABASE CONFIGURATION (dari .env)
// ==============================================

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = [
    'dsn'      => '',
    'hostname' => getenv('DB_HOST') ?: 'localhost',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'database' => getenv('DB_NAME') ?: 'db_crm_sd_negeri_105382',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_general_ci',
    'swap_pre' => '',
    'encrypt'  => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => [],
    'save_queries' => TRUE,
];

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| AUTO-LOADER
| -------------------------------------------------------------------
| Autoload libraries, helpers, dll saat CodeIgniter bootstrap.
| PENTING: Jangan load 'security' di libraries — itu core class.
*/

// ==============================================
// AUTOLOAD PACKAGES
// ==============================================
$autoload['packages'] = [];

// ==============================================
// AUTOLOAD LIBRARIES
// ==============================================
$autoload['libraries'] = [
    'database',
    'session',
    'form_validation',
];

// ==============================================
// AUTOLOAD DRIVERS
// ==============================================
$autoload['drivers'] = [];

// ==============================================
// AUTOLOAD HELPER FILES
// PENTING: 'custom' dan 'format' HARUS ada
// ==============================================
$autoload['helper'] = [
    'url',
    'file',
    'form',
    'html',
    'security',    // security_helper.php
    'custom',      // custom_helper.php  ← WAJIB (log_activity, get_category_color)
    'format',      // format_helper.php  ← WAJIB (format_date, dll)
];

// ==============================================
// AUTOLOAD CONFIG FILES
// ==============================================
$autoload['config'] = [];

// ==============================================
// AUTOLOAD LANGUAGE FILES
// ==============================================
$autoload['language'] = [];

// ==============================================
// AUTOLOAD MODELS
// ==============================================
$autoload['model'] = [];

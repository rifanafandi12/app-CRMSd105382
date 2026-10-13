<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// ==============================================
// BASE URL
// ==============================================
$config['base_url']    = getenv('APP_URL') ?: 'http://localhost:8080/';
$config['index_page'] = '';
$config['uri_protocol'] = 'REQUEST_URI';
$config['url_suffix']  = '';

// ==============================================
// LANGUAGE & CHARSET
// ==============================================
$config['language'] = 'english';
$config['charset']  = 'UTF-8';

// ==============================================
// SECURITY — CSRF PROTECTION
// ==============================================
$config['csrf_protection']   = TRUE;
$config['csrf_token_name']   = 'csrf_token';
$config['csrf_cookie_name']  = 'csrf_cookie';
$config['csrf_expire']       = 7200;
$config['csrf_regenerate']   = FALSE;
$config['csrf_exclude_uris'] = [
    'chat/send_message',
    'chat/get_messages',
    'chat/mark_read',
    'chat/get_unread_count',
    'chat/heartbeat',
    'api/.*',
];

// ==============================================
// SECURITY — COOKIES
// ==============================================
$config['cookie_prefix']   = '';
$config['cookie_domain']   = '';
$config['cookie_path']     = '/';
$config['cookie_secure']   = FALSE;
$config['cookie_httponly'] = TRUE;
$config['cookie_samesite'] = 'Lax';

// ==============================================
// SESSION
// ==============================================
$config['sess_driver']             = 'files';
$config['sess_cookie_name']        = 'ci_session';
$config['sess_samesite']           = 'Lax';
$config['sess_expiration']         = 7200;
$config['sess_save_path']          = sys_get_temp_dir();
$config['sess_match_ip']           = FALSE;
$config['sess_time_to_update']     = 300;
$config['sess_regenerate_destroy'] = FALSE;

// ==============================================
// ENCRYPTION
// ==============================================
$config['encryption_key'] = getenv('ENCRYPTION_KEY') ?: 'default-32-char-key-change-in-env';

// ==============================================
// XSS FILTERING
// ==============================================
$config['global_xss_filtering'] = FALSE;

// ==============================================
// LOGGING
// ==============================================
$config['log_threshold']        = 4;
$config['log_path']             = '';
$config['log_date_format']      = 'Y-m-d H:i:s';
$config['log_file_extension']   = '';
$config['log_file_permissions'] = 0644;

// ==============================================
// OUTPUT COMPRESSION
// ==============================================
$config['compress_output'] = FALSE;

// ==============================================
// SUB-CLASS PREFIX
// ==============================================
$config['subclass_prefix'] = 'MY_';

// ==============================================
// HOOKS
// ==============================================
$config['enable_hooks'] = FALSE;

// ==============================================
// COMPOSER AUTOLOAD
// ==============================================
$config['composer_autoload'] = FCPATH . 'vendor/autoload.php';

// ==============================================
// PERMITTED URI CHARS
// ==============================================
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';

// ==============================================
// ALLOW GET ARRAY
// ==============================================
$config['allow_get_array'] = TRUE;

// ==============================================
// STANDARDIZE NEWLINES
// ==============================================
$config['standardize_newlines'] = FALSE;

// ==============================================
// ERROR VIEWS PATH
// ==============================================
$config['error_views_path'] = '';

// ==============================================
// CACHE
// ==============================================
$config['cache_path']         = '';
$config['cache_query_string'] = FALSE;

// ==============================================
// REWRITE SHORT TAGS
// ==============================================
$config['rewrite_short_tags'] = FALSE;

// ==============================================
// PROXY IPs
// ==============================================
$config['proxy_ips'] = '';

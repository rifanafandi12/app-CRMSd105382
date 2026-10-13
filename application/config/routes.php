<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// ==============================================
// BASE CONFIGURATION
// ==============================================
$route['default_controller']   = 'landing/index';
$route['404_override']         = '';
$route['translate_uri_dashes'] = FALSE;

// ==============================================
// LANDING PAGE ROUTES (Publik)
// ==============================================
$route['']                              = 'landing/index';
$route['landing']                       = 'landing/index';
$route['landing/index']                 = 'landing/index';
$route['landing/pengumuman']            = 'landing/pengumuman';
$route['landing/pengumuman/(:num)']     = 'landing/pengumuman/$1';
$route['landing/aspirasi']              = 'landing/aspirasi';
$route['landing/gallery']               = 'landing/gallery';
$route['landing/gallery_detail/(:num)'] = 'landing/gallery_detail/$1';

// Alias Bahasa Indonesia
$route['pengumuman']                    = 'landing/pengumuman';
$route['pengumuman/(:num)']             = 'landing/pengumuman/$1';
$route['galeri']                        = 'landing/gallery';
$route['galeri/detail/(:num)']          = 'landing/gallery_detail/$1';

// ==============================================
// AUTHENTICATION ROUTES
// ==============================================
$route['login']                  = 'auth/login';
$route['signin']                 = 'auth/login';
$route['masuk']                  = 'auth/login';
$route['auth/login']             = 'auth/login';

$route['logout']                 = 'auth/logout';
$route['keluar']                 = 'auth/logout';
$route['auth/logout']            = 'auth/logout';

$route['register']               = 'auth/register';
$route['auth/register']          = 'auth/register';

// ==============================================
// DASHBOARD ROUTES
// ==============================================
$route['dashboard']              = 'dashboard/index';
$route['dashboard/index']        = 'dashboard/index';
$route['home']                   = 'dashboard/index';
$route['beranda']                = 'dashboard/index';

// ==============================================
// PROFILE ROUTES
// ==============================================
$route['profile']                = 'profile/index';
$route['profile/index']          = 'profile/index';
$route['profile/update']         = 'profile/update';
$route['profile/upload_avatar']  = 'profile/upload_avatar';
$route['profile/remove_avatar']  = 'profile/remove_avatar';
$route['profile/change_password']= 'profile/change_password';
$route['profil']                 = 'profile/index';

// ==============================================
// CHAT ROUTES
// ==============================================
$route['chat']                   = 'chat/index';
$route['chat/index']             = 'chat/index';
$route['chat/get_messages']      = 'chat/get_messages';
$route['chat/send_message']      = 'chat/send_message';
$route['chat/get_unread_count']  = 'chat/get_unread_count';
$route['chat/get_online_users']  = 'chat/get_online_users';
$route['chat/get_online_status'] = 'chat/get_online_status';
$route['chat/heartbeat']         = 'chat/heartbeat';
$route['chat/mark_read']         = 'chat/mark_read';
$route['chat/delete/(:num)']     = 'chat/delete/$1';

// Alias Bahasa Indonesia
$route['pesan']                  = 'chat/index';
$route['komunikasi']             = 'chat/index';
$route['inbox']                  = 'chat/index';

// ==============================================
// USERS ROUTES (Admin Only)
// ==============================================
$route['users']                  = 'users/index';
$route['users/index']            = 'users/index';
$route['users/add']              = 'users/add';
$route['users/edit']             = 'users/edit';
$route['users/delete']           = 'users/delete';
$route['users/toggle_status']    = 'users/toggle_status';
$route['users/reset_password']   = 'users/reset_password';
$route['users/detail/(:num)']    = 'users/detail/$1';

// Alias Bahasa Indonesia
$route['kelola-user']            = 'users/index';
$route['manajemen-user']         = 'users/index';
$route['pengguna']               = 'users/index';

// ==============================================
// STUDENTS ROUTES
// ==============================================
$route['students']               = 'students/index';
$route['students/index']         = 'students/index';
$route['students/add']           = 'students/add';
$route['students/edit']          = 'students/edit';
$route['students/delete']        = 'students/delete';
$route['students/restore']       = 'students/restore';
$route['students/bulk_delete']   = 'students/bulk_delete';
$route['students/search']        = 'students/search';
$route['students/export']        = 'students/export';
$route['students/stats']         = 'students/stats';
$route['students/my_children']   = 'students/my_children';
$route['students/detail/(:num)'] = 'students/detail/$1';

// Alias Bahasa Indonesia
$route['siswa']                  = 'students/index';
$route['data-siswa']             = 'students/index';
$route['siswa-saya']             = 'students/my_children';

// ==============================================
// CLASSES ROUTES (Admin Only)
// ==============================================
$route['classes']                = 'classes/index';
$route['classes/index']          = 'classes/index';
$route['classes/add']            = 'classes/add';
$route['classes/edit']           = 'classes/edit';
$route['classes/delete']         = 'classes/delete';
$route['classes/detail/(:num)']  = 'classes/detail/$1';

// Alias Bahasa Indonesia
$route['kelas']                  = 'classes/index';
$route['data-kelas']             = 'classes/index';

// ==============================================
// SCORES ROUTES
// PENTING: Semua URL mengarah ke scores/index
// yang auto-dispatch berdasarkan role user.
// ==============================================
$route['scores']                         = 'scores/index';
$route['scores/index']                   = 'scores/index';
$route['scores/admin']                   = 'scores/index';
$route['scores/teacher']                 = 'scores/index';
$route['scores/parent']                  = 'scores/index';
$route['scores/get_students_by_class']   = 'scores/get_students_by_class';
$route['scores/get_scores']              = 'scores/get_scores';
$route['scores/save']                    = 'scores/save';
$route['scores/raport/(:num)']           = 'scores/raport/$1';
$route['scores/print_raport/(:num)']     = 'scores/print_raport/$1';

// Alias Bahasa Indonesia
$route['nilai']                  = 'scores/index';
$route['input-nilai']            = 'scores/index';
$route['nilai-anak']             = 'scores/index';
$route['rapor/(:num)']           = 'scores/raport/$1';

// ==============================================
// ANNOUNCEMENTS ROUTES
// ==============================================
$route['announcements']                  = 'announcements/index';
$route['announcements/index']            = 'announcements/index';
$route['announcements/add']              = 'announcements/add';
$route['announcements/edit']             = 'announcements/edit';
$route['announcements/delete']           = 'announcements/delete';
$route['announcements/toggle_publish']   = 'announcements/toggle_publish';
$route['announcements/detail/(:num)']    = 'announcements/detail/$1';

// Alias Bahasa Indonesia
$route['pengumuman-internal']            = 'announcements/index';
$route['pengumuman-internal/detail/(:num)'] = 'announcements/detail/$1';

// ==============================================
// ASPIRATIONS ROUTES
// ==============================================
$route['aspirations']                    = 'aspirations/index';
$route['aspirations/index']              = 'aspirations/index';
$route['aspirations/add']                = 'aspirations/add';
$route['aspirations/respond']            = 'aspirations/respond';
$route['aspirations/delete']             = 'aspirations/delete';
$route['aspirations/detail/(:num)']      = 'aspirations/detail/$1';

// Alias Bahasa Indonesia
$route['aspirasi']                       = 'aspirations/index';
$route['aspirasi/detail/(:num)']         = 'aspirations/detail/$1';
$route['masukan']                        = 'aspirations/index';
$route['saran']                          = 'aspirations/index';

// ==============================================
// GALLERY ROUTES (Admin)
// ==============================================
$route['gallery']                        = 'gallery/index';
$route['gallery/index']                  = 'gallery/index';
$route['gallery/add']                    = 'gallery/add';
$route['gallery/edit']                   = 'gallery/edit';
$route['gallery/delete']                 = 'gallery/delete';
$route['gallery/toggle_publish']         = 'gallery/toggle_publish';
$route['gallery/detail/(:num)']          = 'gallery/detail/$1';

// ==============================================
// REPORTS ROUTES (Admin Only)
// ==============================================
$route['reports']                        = 'reports/index';
$route['reports/index']                  = 'reports/index';
$route['reports/get_chart_data']         = 'reports/get_chart_data';
$route['reports/export_messages']        = 'reports/export_messages';
$route['reports/export_aspirations']     = 'reports/export_aspirations';
$route['reports/export_students']        = 'reports/export_students';
$route['reports/export_scores']          = 'reports/export_scores';

// Alias Bahasa Indonesia
$route['laporan']                        = 'reports/index';
$route['statistik']                      = 'reports/index';
$route['laporan/export-pesan']           = 'reports/export_messages';
$route['laporan/export-aspirasi']        = 'reports/export_aspirations';

// ==============================================
// API ROUTES (Untuk AJAX)
// ==============================================
$route['api/get_user_info']              = 'api/user_info';
$route['api/get_recent_activities']      = 'api/recent_activities';
$route['api/get_dashboard_stats']        = 'api/dashboard_stats';
$route['api/search_users']               = 'api/search_users';
$route['api/search_students']            = 'api/search_students';
$route['api/get_notifications']          = 'api/notifications';
$route['api/chat/last-message/(:num)']   = 'api/last_message/$1';

// ==============================================
// ERROR ROUTES
// ==============================================
$route['forbidden']                      = 'errors/forbidden';
$route['access-denied']                  = 'errors/forbidden';
$route['unauthorized']                   = 'errors/forbidden';
$route['error-404']                      = 'errors/not_found';

// ==============================================
// AKHIR DARI FILE ROUTES
// ==============================================

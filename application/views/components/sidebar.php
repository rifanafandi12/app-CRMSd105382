<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Mobile sidebar backdrop -->
<div id="sidebar-backdrop" class="fixed inset-0 bg-secondary-900/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-secondary-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">

    <!-- Logo -->
    <div class="h-16 flex items-center px-5 border-b border-secondary-200">
        <a href="<?= base_url('dashboard') ?>" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="font-display font-bold text-secondary-900 text-sm leading-tight truncate">SDN 105382</div>
                <div class="text-secondary-500 text-[10px] leading-tight">Galang, Deli Serdang</div>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 scrollbar-thin">
        <?php
        $current_uri = $this->uri->uri_string();
        $role = $this->session->userdata('role');

        function is_active($uri, $current) {
            if ($uri === $current) return true;
            if (strpos($current, $uri . '/') === 0) return true;
            return false;
        }

        // Menu berdasarkan role
        $menus = [];

        // Dashboard — semua role
        $menus[] = [
            'section' => 'Utama',
            'items' => [
                ['uri' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                ['uri' => 'chat', 'label' => 'Pesan', 'icon' => 'chat', 'badge' => true],
            ],
        ];

        // Menu Admin
        if ($role === 'admin') {
            $menus[] = [
                'section' => 'Manajemen',
                'items' => [
                    ['uri' => 'users', 'label' => 'Pengguna', 'icon' => 'users'],
                    ['uri' => 'students', 'label' => 'Data Siswa', 'icon' => 'students'],
                    ['uri' => 'classes', 'label' => 'Data Kelas', 'icon' => 'classes'],
                ],
            ];
            $menus[] = [
                'section' => 'Akademik',
                'items' => [
                    ['uri' => 'scores', 'label' => 'Nilai Siswa', 'icon' => 'scores'],
                ],
            ];
            $menus[] = [
                'section' => 'Konten',
                'items' => [
                    ['uri' => 'announcements', 'label' => 'Pengumuman', 'icon' => 'announcement'],
                    ['uri' => 'gallery', 'label' => 'Galeri', 'icon' => 'gallery'],
                    ['uri' => 'aspirations', 'label' => 'Aspirasi', 'icon' => 'aspiration'],
                ],
            ];
            $menus[] = [
                'section' => 'Lainnya',
                'items' => [
                    ['uri' => 'reports', 'label' => 'Laporan', 'icon' => 'reports'],
                ],
            ];
        }

        // Menu Teacher
        if ($role === 'teacher') {
            $menus[] = [
                'section' => 'Akademik',
                'items' => [
                    ['uri' => 'scores/teacher', 'label' => 'Input Nilai', 'icon' => 'scores'],
                ],
            ];
            $menus[] = [
                'section' => 'Konten',
                'items' => [
                    ['uri' => 'announcements', 'label' => 'Pengumuman', 'icon' => 'announcement'],
                    ['uri' => 'aspirations', 'label' => 'Aspirasi', 'icon' => 'aspiration'],
                ],
            ];
        }

        // Menu Parent
        if ($role === 'parent') {
            $menus[] = [
                'section' => 'Anak Saya',
                'items' => [
                    ['uri' => 'students/my_children', 'label' => 'Data Anak', 'icon' => 'students'],
                    ['uri' => 'scores/parent', 'label' => 'Nilai Anak', 'icon' => 'scores'],
                ],
            ];
            $menus[] = [
                'section' => 'Lainnya',
                'items' => [
                    ['uri' => 'announcements', 'label' => 'Pengumuman', 'icon' => 'announcement'],
                    ['uri' => 'aspirations', 'label' => 'Aspirasi Saya', 'icon' => 'aspiration'],
                ],
            ];
        }

        // Render menu
        foreach ($menus as $menu): ?>
            <div class="pt-3 first:pt-0">
                <div class="px-3 mb-2 text-[10px] font-semibold text-secondary-400 uppercase tracking-wider">
                    <?= $menu['section'] ?>
                </div>
                <?php foreach ($menu['items'] as $item):
                    $active = is_active($item['uri'], $current_uri);
                ?>
                    <a href="<?= base_url($item['uri']) ?>"
                       class="sidebar-link <?= $active ? 'active' : '' ?>">
                        <?= sidebar_icon($item['icon']) ?>
                        <span class="flex-1"><?= $item['label'] ?></span>
                        <?php if (!empty($item['badge'])): ?>
                            <span id="sidebar-unread"
                                  class="hidden min-w-[20px] h-5 px-1.5 text-[10px] font-semibold bg-danger-500 text-white rounded-full flex items-center justify-center"></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <!-- Profile section -->
        <div class="pt-3">
            <div class="px-3 mb-2 text-[10px] font-semibold text-secondary-400 uppercase tracking-wider">Akun</div>
            <a href="<?= base_url('profile') ?>" class="sidebar-link <?= is_active('profile', $current_uri) ? 'active' : '' ?>">
                <?= sidebar_icon('user') ?>
                <span>Profil Saya</span>
            </a>
            <a href="<?= base_url('auth/logout') ?>"
               onclick="return confirm('Yakin ingin keluar dari sistem?')"
               class="sidebar-link text-danger-600 hover:bg-danger-50 hover:text-danger-700">
                <?= sidebar_icon('logout') ?>
                <span>Keluar</span>
            </a>
        </div>
    </nav>

    <!-- User info at bottom -->
    <div class="p-3 border-t border-secondary-200">
        <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-secondary-50">
            <div class="w-9 h-9 rounded-full bg-primary-600 text-white flex items-center justify-center text-sm font-semibold flex-shrink-0">
                <?= strtoupper(substr($this->session->userdata('name') ?? 'U', 0, 1)) ?>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-sm font-medium text-secondary-900 truncate">
                    <?= html_escape($this->session->userdata('name') ?? 'User') ?>
                </div>
                <div class="text-[10px] text-secondary-500 uppercase tracking-wide">
                    <?= html_escape($this->session->userdata('role') ?? '') ?>
                </div>
            </div>
        </div>
    </div>
</aside>

<?php
/**
 * Render SVG icon berdasarkan nama
 */
function sidebar_icon($name) {
    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        'chat' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        'students' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>',
        'classes' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        'scores' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'announcement' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>',
        'gallery' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        'aspiration' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>',
        'reports' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'logout' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>',
    ];
    $path = $icons[$name] ?? $icons['home'];
    return '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">' . $path . '</svg>';
}
?>

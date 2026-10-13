<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-secondary-200">
    <div class="h-16 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">

        <!-- Left: Menu toggle + Page title -->
        <div class="flex items-center gap-3 min-w-0">
            <button type="button"
                    onclick="toggleSidebar()"
                    class="lg:hidden p-2 -ml-2 text-secondary-600 hover:bg-secondary-100 rounded-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="min-w-0">
                <h1 class="font-display font-bold text-lg text-secondary-900 truncate">
                    <?= html_escape($title ?? 'Dashboard') ?>
                </h1>
                <div class="hidden sm:flex items-center gap-1.5 text-xs text-secondary-500 mt-0.5">
                    <a href="<?= base_url('dashboard') ?>" class="hover:text-primary-600 transition-colors">Beranda</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="truncate"><?= html_escape($title ?? 'Dashboard') ?></span>
                </div>
            </div>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-2">

            <!-- Chat notification -->
            <a href="<?= base_url('chat') ?>"
               class="relative p-2 text-secondary-600 hover:bg-secondary-100 rounded-lg transition-colors"
               title="Pesan">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span id="unread-badge"
                      class="hidden absolute top-1 right-1 min-w-[18px] h-[18px] px-1 text-[10px] font-semibold bg-danger-500 text-white rounded-full flex items-center justify-center"></span>
            </a>

            <!-- User dropdown -->
            <div class="relative" id="user-menu-wrapper">
                <button type="button"
                        onclick="toggleUserMenu()"
                        class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-secondary-100 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center text-xs font-semibold">
                        <?= strtoupper(substr($this->session->userdata('name') ?? 'U', 0, 1)) ?>
                    </div>
                    <svg class="hidden sm:block w-4 h-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="user-menu"
                     class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-soft border border-secondary-200 py-1 z-50">
                    <div class="px-4 py-3 border-b border-secondary-100">
                        <div class="text-sm font-semibold text-secondary-900 truncate">
                            <?= html_escape($this->session->userdata('name') ?? 'User') ?>
                        </div>
                        <div class="text-xs text-secondary-500 truncate">
                            <?= html_escape($this->session->userdata('email') ?? '') ?>
                        </div>
                    </div>
                    <a href="<?= base_url('profile') ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-secondary-700 hover:bg-secondary-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil Saya
                    </a>
                    <a href="<?= base_url('landing') ?>" target="_blank" class="flex items-center gap-3 px-4 py-2.5 text-sm text-secondary-700 hover:bg-secondary-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Lihat Website
                    </a>
                    <hr class="my-1 border-secondary-100">
                    <a href="<?= base_url('auth/logout') ?>"
                       onclick="return confirm('Yakin ingin keluar dari sistem?')"
                       class="flex items-center gap-3 px-4 py-2.5 text-sm text-danger-600 hover:bg-danger-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
function toggleUserMenu() {
    const menu = document.getElementById('user-menu');
    menu.classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('user-menu-wrapper');
    const menu = document.getElementById('user-menu');
    if (wrapper && !wrapper.contains(e.target)) {
        menu.classList.add('hidden');
    }
});
</script>

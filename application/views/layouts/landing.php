<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Website Resmi SD Negeri 105382 Galang — Sekolah Dasar Negeri di Kecamatan Galang, Kabupaten Deli Serdang, Sumatera Utara.">
    <title><?= html_escape($title ?? 'SD Negeri 105382 Galang') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <link rel="icon" type="image/webp" href="<?= base_url('assets/images/logo-tutwuri-handayani.webp') ?>">
</head>
<body class="h-full bg-white font-sans antialiased flex flex-col min-h-screen">

<!-- ==============================================
     NAVBAR
     ============================================== -->
<header class="sticky top-0 z-40 bg-white border-b border-neutral-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Logo & Brand -->
            <a href="<?= base_url() ?>" class="flex items-center gap-3">
                <img src="<?= base_url('assets/images/logo-tutwuri-handayani.webp') ?>"
                     alt="Logo Tut Wuri Handayani"
                     class="w-10 h-10 object-contain">
                <div class="hidden sm:block leading-tight">
                    <div class="font-display font-bold text-neutral-900 text-sm">
                        SD Negeri 105382
                    </div>
                    <div class="text-[10px] text-neutral-500 uppercase tracking-wide">
                        Galang, Deli Serdang
                    </div>
                </div>
            </a>

            <!-- Nav Links -->
            <nav class="hidden md:flex items-center gap-1">
                <a href="<?= base_url() ?>"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-colors <?= ($this->uri->uri_string() === '') ? 'text-primary-600 bg-primary-50' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50' ?>">
                    Beranda
                </a>
                <a href="<?= base_url('pengumuman') ?>"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-colors <?= (strpos($this->uri->uri_string(), 'pengumuman') !== false) ? 'text-primary-600 bg-primary-50' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50' ?>">
                    Pengumuman
                </a>
                <a href="<?= base_url('galeri') ?>"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-colors <?= (strpos($this->uri->uri_string(), 'galeri') !== false) ? 'text-primary-600 bg-primary-50' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50' ?>">
                    Galeri
                </a>
                <a href="<?= base_url('#profil') ?>"
                   class="px-4 py-2 text-sm font-medium text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50 rounded-lg transition-colors">
                    Profil
                </a>
            </nav>

            <!-- Auth -->
            <div class="flex items-center gap-2">
                <?php if (!empty($is_logged_in)): ?>
                    <a href="<?= base_url('dashboard') ?>" class="btn-primary btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>" class="btn-primary btn-sm">
                        Masuk
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile menu button -->
            <button type="button"
                    onclick="toggleMobileMenu()"
                    class="md:hidden p-2 -mr-2 text-neutral-600 hover:bg-neutral-100 rounded-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-neutral-200 bg-white">
        <nav class="px-4 py-3 space-y-1">
            <a href="<?= base_url() ?>" class="block px-4 py-2.5 text-sm font-medium text-neutral-700 hover:bg-neutral-50 rounded-lg">
                Beranda
            </a>
            <a href="<?= base_url('pengumuman') ?>" class="block px-4 py-2.5 text-sm font-medium text-neutral-700 hover:bg-neutral-50 rounded-lg">
                Pengumuman
            </a>
            <a href="<?= base_url('galeri') ?>" class="block px-4 py-2.5 text-sm font-medium text-neutral-700 hover:bg-neutral-50 rounded-lg">
                Galeri
            </a>
            <a href="<?= base_url('#profil') ?>" class="block px-4 py-2.5 text-sm font-medium text-neutral-700 hover:bg-neutral-50 rounded-lg">
                Profil
            </a>
        </nav>
    </div>
</header>

<!-- ==============================================
     MAIN CONTENT
     ============================================== -->
<main class="flex-1">
    <?php $this->load->view($content_view); ?>
</main>

<!-- ==============================================
     FOOTER
     ============================================== -->
<footer class="bg-neutral-900 text-neutral-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

        <div class="grid grid-cols-1 md:grid-cols-12 gap-10">

            <!-- Brand -->
            <div class="md:col-span-5">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-lg bg-white p-1.5 flex items-center justify-center flex-shrink-0">
                        <img src="<?= base_url('assets/images/logo-tutwuri-handayani.webp') ?>"
                             alt="Logo"
                             class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="font-display font-bold text-white text-sm">
                            SD Negeri 105382
                        </div>
                        <div class="text-xs text-neutral-500">
                            Galang, Deli Serdang
                        </div>
                    </div>
                </div>
                <p class="text-sm leading-relaxed max-w-sm">
                    Sekolah dasar negeri yang berkomitmen menyelenggarakan pendidikan berkualitas
                    untuk mencetak generasi yang beriman, cerdas, terampil, dan berkarakter.
                </p>
            </div>

            <!-- Navigasi -->
            <div class="md:col-span-3">
                <h4 class="font-display font-semibold text-white text-sm uppercase tracking-wider mb-5">
                    Navigasi
                </h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?= base_url() ?>" class="hover:text-white transition-colors">Beranda</a></li>
                    <li><a href="<?= base_url('pengumuman') ?>" class="hover:text-white transition-colors">Pengumuman</a></li>
                    <li><a href="<?= base_url('galeri') ?>" class="hover:text-white transition-colors">Galeri</a></li>
                    <li><a href="<?= base_url('login') ?>" class="hover:text-white transition-colors">Portal Login</a></li>
                </ul>
            </div>

            <!-- Kontak -->
            <div class="md:col-span-4">
                <h4 class="font-display font-semibold text-white text-sm uppercase tracking-wider mb-5">
                    Kontak
                </h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-primary-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Jalan Kesatuan Gang Inpres, Desa Galang Kota, Kec. Galang, Kab. Deli Serdang</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:sdn105382@gmail.com" class="hover:text-white transition-colors">
                            sdn105382@gmail.com
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom -->
        <div class="mt-12 pt-6 border-t border-neutral-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-neutral-500">
            <div>
                &copy; <?= date('Y') ?> SD Negeri 105382 Galang. Seluruh hak cipta dilindungi.
            </div>
            <div>
                Sistem Informasi Sekolah v1.0
            </div>
        </div>
    </div>
</footer>

<script src="<?= base_url('assets/js/app.js') ?>"></script>
<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    if (menu) menu.classList.toggle('hidden');
}

// Smooth scroll untuk anchor link
document.querySelectorAll('a[href^="<?= base_url() ?>#"]').forEach(link => {
    link.addEventListener('click', function (e) {
        const target = this.getAttribute('href').split('#')[1];
        const el = document.getElementById(target);
        if (el) {
            e.preventDefault();
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>
</body>
</html>

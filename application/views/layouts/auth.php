<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem CRM SD Negeri 105382 Galang">
    <?= csrf_meta() ?>
    
    <title><?= html_escape($title ?? 'Login') ?> &mdash; CRM SDN 105382 Galang</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <link rel="icon" type="image/webp" href="<?= base_url('assets/images/logo-tutwuri-handayani.webp') ?>">
</head>
<body class="h-full bg-neutral-50 font-sans antialiased">

<div class="min-h-full flex">

    <!-- ==============================================
         LEFT PANEL: Branding
         ============================================== -->
    <div class="hidden lg:flex lg:w-[55%] xl:w-1/2 relative bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 overflow-hidden">
        <!-- Decorative grid -->
        <svg class="absolute inset-0 w-full h-full opacity-10" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)"/>
        </svg>

        <!-- Accent circle -->
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-accent-500/20 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-primary-400/20 blur-3xl"></div>

        <div class="relative z-10 flex flex-col justify-between p-12 w-full">

            <!-- Header: Logo + Nama Sekolah -->
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center shadow-lg p-2">
                    <img src="<?= base_url('assets/images/logo-tutwuri-handayani.webp') ?>"
                         alt="Logo Tut Wuri Handayani"
                         class="w-full h-full object-contain">
                </div>
                <div>
                    <div class="text-white font-display font-bold text-xl leading-tight">
                        SD Negeri 105382
                    </div>
                    <div class="text-primary-100 text-sm mt-0.5">
                        Kec. Galang, Kab. Deli Serdang
                    </div>
                </div>
            </div>

            <!-- Middle: Headline -->
            <div class="max-w-lg">
                <h1 class="text-white font-display font-bold text-4xl xl:text-5xl leading-tight mb-5 text-balance">
                    Sistem Informasi Terpadu Sekolah
                </h1>
                <p class="text-primary-100 text-base leading-relaxed">
                    Platform komunikasi dan manajemen data yang menghubungkan sekolah,
                    guru, dan orang tua dalam satu ekosistem digital.
                </p>

                <!-- Feature highlights -->
                <div class="mt-10 space-y-4">
                    <div class="flex items-center gap-3 text-white">
                        <div class="w-9 h-9 rounded-lg bg-white/15 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-sm">Keamanan Terjamin</div>
                            <div class="text-primary-200 text-xs">Data terenkripsi dan terlindungi</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-white">
                        <div class="w-9 h-9 rounded-lg bg-white/15 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-sm">Komunikasi Real-time</div>
                            <div class="text-primary-200 text-xs">Chat langsung dengan guru &amp; staf</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-white">
                        <div class="w-9 h-9 rounded-lg bg-white/15 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-sm">Monitoring Akademik</div>
                            <div class="text-primary-200 text-xs">Pantau nilai dan perkembangan siswa</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-primary-200 text-xs">
                &copy; <?= date('Y') ?> SD Negeri 105382 Galang. Hak cipta dilindungi.
            </div>
        </div>
    </div>

    <!-- ==============================================
         RIGHT PANEL: Form
         ============================================== -->
    <div class="flex-1 flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8 bg-neutral-50">
        <div class="w-full max-w-md">

            <!-- Mobile logo -->
            <div class="lg:hidden flex flex-col items-center gap-3 mb-8">
                <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center shadow-md p-2 border border-neutral-100">
                    <img src="<?= base_url('assets/images/logo-tutwuri-handayani.webp') ?>"
                         alt="Logo Tut Wuri Handayani"
                         class="w-full h-full object-contain">
                </div>
                <div class="text-center">
                    <div class="font-display font-bold text-neutral-900">SD Negeri 105382</div>
                    <div class="text-neutral-500 text-xs mt-0.5">Kec. Galang, Kab. Deli Serdang</div>
                </div>
            </div>

            <!-- Flash messages -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert-danger mb-5 animate-slide-down">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><?= $this->session->flashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert-success mb-5 animate-slide-down">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><?= $this->session->flashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <!-- Content -->
            <?php $this->load->view($content_view); ?>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>

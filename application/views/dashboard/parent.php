<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Welcome Banner -->
<div class="mb-6 bg-gradient-to-br from-accent-500 via-accent-500 to-accent-600 rounded-2xl p-6 lg:p-8 text-white relative overflow-hidden">
    <div class="absolute -top-20 -right-20 w-72 h-72 bg-white/20 rounded-full blur-3xl"></div>

    <div class="relative">
        <div class="text-white/80 text-sm mb-1"><?= format_datetime(date('Y-m-d H:i:s'), true) ?></div>
        <h2 class="font-display font-bold text-2xl lg:text-3xl mb-2">
            Selamat Datang, <?= html_escape($user_name) ?>
        </h2>
        <p class="text-white/90 text-sm lg:text-base max-w-2xl">
            Pantau perkembangan akademik anak Anda dan berkomunikasi langsung dengan pihak sekolah.
        </p>
    </div>
</div>

<!-- Kartu Anak -->
<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-display font-bold text-lg text-neutral-900">Anak Saya</h3>
        <a href="<?= base_url('students/my_children') ?>" class="text-sm font-medium text-primary-600 hover:text-primary-700">
            Lihat Semua
        </a>
    </div>

    <?php if (!empty($my_children)): ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($my_children as $child): ?>
                <div class="card p-5 hover:shadow-elevated transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-lg font-bold flex-shrink-0">
                            <?= strtoupper(substr($child->student_name ?? 'S', 0, 1)) ?>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-display font-semibold text-neutral-900 truncate">
                                <?= html_escape($child->student_name ?? '-') ?>
                            </div>
                            <div class="text-xs text-neutral-500 mt-0.5">
                                NISN: <?= html_escape($child->nisn ?? '-') ?>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <span class="badge-primary"><?= html_escape($child->class_name ?? 'Belum ada kelas') ?></span>
                        <span class="text-xs text-neutral-500">
                            <?= html_escape($child->gender ?? '-') ?>
                        </span>
                    </div>

                    <div class="flex gap-2">
                        <a href="<?= base_url('scores/raport/' . ($child->id_student ?? 0)) ?>"
                           class="btn-primary btn-sm flex-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Rapor
                        </a>
                        <a href="<?= base_url('students/detail/' . ($child->id_student ?? 0)) ?>"
                           class="btn-outline btn-sm flex-1">
                            Detail
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <?php $this->load->view('components/empty_state', [
            'icon'    => 'inbox',
            'title'   => 'Belum Ada Data Anak',
            'message' => 'Data anak Anda belum terdaftar. Hubungi pihak sekolah.',
        ]); ?>
    <?php endif; ?>
</div>

<!-- Pengumuman + Quick Actions -->
<div class="grid lg:grid-cols-3 gap-4">

    <!-- Pengumuman Terbaru -->
    <div class="card lg:col-span-2">
        <div class="card-header">
            <div>
                <h3 class="font-display font-semibold text-neutral-900">Pengumuman Terbaru</h3>
                <p class="text-xs text-neutral-500 mt-0.5">Informasi dari sekolah</p>
            </div>
            <a href="<?= base_url('announcements') ?>" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                Lihat Semua
            </a>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($recent_announcements)): ?>
                <ul class="divide-y divide-neutral-100">
                    <?php foreach ($recent_announcements as $ann): ?>
                        <li>
                            <a href="<?= base_url('announcements/detail/' . $ann->id_announcement) ?>"
                               class="block px-5 py-4 hover:bg-neutral-50 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-accent-100 text-accent-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-medium text-neutral-900 text-sm line-clamp-2">
                                            <?= html_escape($ann->title ?? '-') ?>
                                        </div>
                                        <div class="text-xs text-neutral-500 mt-1 flex items-center gap-2">
                                            <span class="badge-neutral"><?= html_escape(ucfirst($ann->category ?? 'umum')) ?></span>
                                            <span><?= format_date($ann->posted_date ?? '') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <?php $this->load->view('components/empty_state', [
                    'icon'    => 'announcement',
                    'title'   => 'Belum Ada Pengumuman',
                    'message' => 'Pengumuman terbaru dari sekolah akan muncul di sini.',
                ]); ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="font-display font-semibold text-neutral-900">Aksi Cepat</h3>
        </div>
        <div class="card-body space-y-2">
            <a href="<?= base_url('chat') ?>" class="sidebar-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Chat dengan Guru
            </a>
            <a href="<?= base_url('aspirations') ?>" class="sidebar-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                Kirim Aspirasi
            </a>
            <a href="<?= base_url('scores/parent') ?>" class="sidebar-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Lihat Nilai Anak
            </a>
        </div>
    </div>
</div>

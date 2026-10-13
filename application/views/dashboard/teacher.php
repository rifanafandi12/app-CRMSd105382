<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Welcome Banner -->
<div class="mb-6 bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 rounded-2xl p-6 lg:p-8 text-white relative overflow-hidden">
    <div class="absolute -top-20 -right-20 w-72 h-72 bg-accent-500/20 rounded-full blur-3xl"></div>

    <div class="relative">
        <div class="text-primary-100 text-sm mb-1"><?= format_datetime(date('Y-m-d H:i:s'), true) ?></div>
        <h2 class="font-display font-bold text-2xl lg:text-3xl mb-2">
            Selamat Datang, <?= html_escape($user_name) ?>
        </h2>
        <p class="text-primary-100 text-sm lg:text-base max-w-2xl">
            Berikut ringkasan aktivitas kelas dan aspirasi dari orang tua murid.
        </p>
    </div>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php
    $this->load->view('components/stat_card', [
        'icon'  => 'student',
        'label' => 'Siswa di Kelas',
        'value' => number_format(count($my_students ?? [])),
        'color' => 'primary',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'aspiration',
        'label' => 'Aspirasi Pending',
        'value' => number_format($pending_aspirations ?? 0),
        'color' => 'warning',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'chat',
        'label' => 'Pesan Belum Dibaca',
        'value' => number_format($unread_count ?? 0),
        'color' => 'accent',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'classroom',
        'label' => 'Kelas Aktif',
        'value' => count($my_classes ?? []),
        'color' => 'success',
    ]);
    ?>
</div>

<!-- Daftar Siswa + Aspirasi -->
<div class="grid lg:grid-cols-3 gap-4">

    <!-- Daftar Siswa -->
    <div class="card lg:col-span-2">
        <div class="card-header">
            <div>
                <h3 class="font-display font-semibold text-neutral-900">Daftar Siswa di Kelas Saya</h3>
                <p class="text-xs text-neutral-500 mt-0.5">Total <?= count($my_students ?? []) ?> siswa</p>
            </div>
            <a href="<?= base_url('students') ?>" class="btn-ghost btn-sm">
                Lihat Semua
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($my_students)): ?>
                <div class="table-wrapper border-0 rounded-none">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>
                                <th>NISN</th>
                                <th>Kelas</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($my_students, 0, 8) as $student): ?>
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-xs font-semibold flex-shrink-0">
                                                <?= strtoupper(substr($student->student_name ?? 'S', 0, 1)) ?>
                                            </div>
                                            <span class="font-medium text-neutral-800"><?= html_escape($student->student_name ?? '-') ?></span>
                                        </div>
                                    </td>
                                    <td class="text-neutral-600"><?= html_escape($student->nisn ?? '-') ?></td>
                                    <td><span class="badge-primary"><?= html_escape($student->class_name ?? '-') ?></span></td>
                                    <td class="text-right">
                                        <a href="<?= base_url('students/detail/' . ($student->id_student ?? 0)) ?>"
                                           class="btn-ghost btn-sm">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <?php $this->load->view('components/empty_state', [
                    'icon'    => 'inbox',
                    'title'   => 'Belum Ada Siswa',
                    'message' => 'Anda belum ditugaskan sebagai wali kelas.',
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
            <a href="<?= base_url('scores/teacher') ?>" class="sidebar-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Input Nilai Siswa
            </a>
            <a href="<?= base_url('aspirations') ?>" class="sidebar-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                Lihat Aspirasi
            </a>
            <a href="<?= base_url('chat') ?>" class="sidebar-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Buka Pesan
            </a>
            <a href="<?= base_url('announcements') ?>" class="sidebar-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                Pengumuman
            </a>
        </div>
    </div>
</div>

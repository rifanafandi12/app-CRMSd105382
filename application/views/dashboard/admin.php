<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Welcome Banner -->
<div class="mb-6 bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 rounded-2xl p-6 lg:p-8 text-white relative overflow-hidden">
    <div class="absolute -top-20 -right-20 w-72 h-72 bg-accent-500/20 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-primary-400/20 rounded-full blur-3xl"></div>

    <div class="relative">
        <div class="text-primary-100 text-sm mb-1"><?= format_datetime(date('Y-m-d H:i:s'), true) ?></div>
        <h2 class="font-display font-bold text-2xl lg:text-3xl mb-2">
            Selamat Datang, <?= html_escape($user_name) ?>
        </h2>
        <p class="text-primary-100 text-sm lg:text-base max-w-2xl">
            Berikut ringkasan aktivitas sistem CRM SD Negeri 105382 Galang hari ini.
        </p>
    </div>
</div>

<!-- Stat Cards Row 1 -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php
    $this->load->view('components/stat_card', [
        'icon'  => 'users',
        'label' => 'Total Orang Tua',
        'value' => number_format($total_parents ?? 0),
        'color' => 'primary',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'teacher',
        'label' => 'Total Guru',
        'value' => number_format($total_teachers ?? 0),
        'color' => 'success',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'student',
        'label' => 'Total Siswa',
        'value' => number_format($total_students ?? 0),
        'color' => 'accent',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'classroom',
        'label' => 'Total Kelas',
        'value' => number_format($total_classes ?? 0),
        'color' => 'warning',
    ]);
    ?>
</div>

<!-- Stat Cards Row 2 -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php
    $this->load->view('components/stat_card', [
        'icon'  => 'chat',
        'label' => 'Pesan Hari Ini',
        'value' => number_format($today_messages ?? 0),
        'color' => 'primary',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'online',
        'label' => 'User Online',
        'value' => number_format($online_users ?? 0),
        'color' => 'success',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'aspiration',
        'label' => 'Aspirasi',
        'value' => number_format($total_aspirations ?? 0),
        'color' => 'warning',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'announcement',
        'label' => 'Pengumuman',
        'value' => number_format($total_announcements ?? 0),
        'color' => 'accent',
    ]);
    ?>
</div>

<!-- Chart + Recent Activities -->
<div class="grid lg:grid-cols-3 gap-4 mb-6">

    <!-- Chart Pesan 7 Hari -->
    <div class="card lg:col-span-2">
        <div class="card-header">
            <div>
                <h3 class="font-display font-semibold text-neutral-900">Aktivitas Pesan</h3>
                <p class="text-xs text-neutral-500 mt-0.5">7 hari terakhir</p>
            </div>
            <span class="badge-primary">Realtime</span>
        </div>
        <div class="card-body">
            <div class="h-64">
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="font-display font-semibold text-neutral-900">Aktivitas Terbaru</h3>
                <p class="text-xs text-neutral-500 mt-0.5">Log sistem</p>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($recent_activities)): ?>
                <ul class="divide-y divide-neutral-100 max-h-80 overflow-y-auto">
                    <?php foreach (array_slice($recent_activities, 0, 8) as $log): ?>
                        <li class="px-5 py-3 hover:bg-neutral-50 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0 text-xs font-semibold mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-medium text-neutral-800 line-clamp-2">
                                        <?= html_escape($log->description ?? '-') ?>
                                    </div>
                                    <div class="text-[10px] text-neutral-400 mt-0.5">
                                        <?= format_time_ago($log->created_at ?? date('Y-m-d H:i:s')) ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <?php $this->load->view('components/empty_state', [
                    'icon'    => 'inbox',
                    'title'   => 'Belum ada aktivitas',
                    'message' => 'Aktivitas pengguna akan muncul di sini.',
                ]); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="<?= base_url('users') ?>" class="card p-5 hover:shadow-elevated transition-all group">
        <div class="w-10 h-10 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div class="font-display font-semibold text-sm text-neutral-900">Kelola User</div>
        <div class="text-xs text-neutral-500 mt-0.5">Tambah, edit, hapus user</div>
    </a>

    <a href="<?= base_url('announcements/add') ?>" class="card p-5 hover:shadow-elevated transition-all group">
        <div class="w-10 h-10 rounded-lg bg-accent-100 text-accent-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
        </div>
        <div class="font-display font-semibold text-sm text-neutral-900">Buat Pengumuman</div>
        <div class="text-xs text-neutral-500 mt-0.5">Publikasi info ke wali murid</div>
    </a>

    <a href="<?= base_url('gallery/add') ?>" class="card p-5 hover:shadow-elevated transition-all group">
        <div class="w-10 h-10 rounded-lg bg-success-100 text-success-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="font-display font-semibold text-sm text-neutral-900">Upload Galeri</div>
        <div class="text-xs text-neutral-500 mt-0.5">Tambah foto kegiatan</div>
    </a>

    <a href="<?= base_url('reports') ?>" class="card p-5 hover:shadow-elevated transition-all group">
        <div class="w-10 h-10 rounded-lg bg-danger-100 text-danger-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div class="font-display font-semibold text-sm text-neutral-900">Lihat Laporan</div>
        <div class="text-xs text-neutral-500 mt-0.5">Export data &amp; statistik</div>
    </a>
</div>

<!-- Chart.js -->
<script src="<?= base_url('assets/js/vendor/chart.umd.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('weeklyChart');
    if (!ctx) return;

    const weeklyData = <?= json_encode($weekly_messages ?? []) ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: weeklyData.map(d => d.date),
            datasets: [{
                label: 'Jumlah Pesan',
                data: weeklyData.map(d => d.count),
                borderColor: '#1E65B8',
                backgroundColor: 'rgba(30, 101, 184, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#1E65B8',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1F2937',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: '600' },
                    bodyFont: { family: 'Inter', size: 12 },
                    displayColors: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#E2E8F0', drawBorder: false },
                    ticks: { font: { family: 'Inter', size: 11 }, color: '#94A3B8', precision: 0 },
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Inter', size: 11 }, color: '#94A3B8' },
                }
            }
        }
    });
});
</script>

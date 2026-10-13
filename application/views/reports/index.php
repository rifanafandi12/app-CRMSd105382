<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Laporan Sistem',
    'subtitle'   => 'Statistik dan analitik aktivitas sistem',
    'breadcrumb' => [['label' => 'Laporan']],
]);
?>

<!-- ==============================================
     FILTER PERIODE
     ============================================== -->
<div class="card mb-6">
    <div class="card-body flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-neutral-700">Periode:</label>
            <select id="period-select" class="form-select text-sm w-auto">
                <option value="3months"  <?= ($period ?? '') === '3months'  ? 'selected' : '' ?>>3 Bulan Terakhir</option>
                <option value="6months"  <?= ($period ?? '') === '6months'  ? 'selected' : '' ?>>6 Bulan Terakhir</option>
                <option value="12months" <?= ($period ?? '') === '12months' ? 'selected' : '' ?>>12 Bulan Terakhir</option>
            </select>
        </div>
        <div class="flex items-center gap-2 text-xs text-neutral-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Terakhir diperbarui: <?= date('d M Y H:i') ?></span>
        </div>
    </div>
</div>

<!-- ==============================================
     STAT CARDS — UTAMA
     ============================================== -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php
    $this->load->view('components/stat_card', [
        'icon'  => 'users',
        'label' => 'Total Pengguna',
        'value' => number_format($total_users ?? 0),
        'color' => 'primary',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'student',
        'label' => 'Total Siswa',
        'value' => number_format($total_students ?? 0),
        'color' => 'success',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'classroom',
        'label' => 'Total Kelas',
        'value' => number_format($total_classes ?? 0),
        'color' => 'accent',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'online',
        'label' => 'User Online',
        'value' => number_format($online_users ?? 0),
        'color' => 'warning',
    ]);
    ?>
</div>

<!-- ==============================================
     STAT CARDS — AKTIVITAS
     ============================================== -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php
    $this->load->view('components/stat_card', [
        'icon'  => 'chat',
        'label' => 'Total Pesan',
        'value' => number_format($total_messages ?? 0),
        'color' => 'primary',
        'trend' => ($today_messages ?? 0) > 0 ? "+{$today_messages} hari ini" : null,
        'trend_up' => true,
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'aspiration',
        'label' => 'Total Aspirasi',
        'value' => number_format($total_aspirations ?? 0),
        'color' => 'accent',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'announcement',
        'label' => 'Pengumuman',
        'value' => number_format($total_announcements ?? 0),
        'color' => 'success',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'gallery',
        'label' => 'Foto Galeri',
        'value' => number_format($total_gallery ?? 0),
        'color' => 'warning',
    ]);
    ?>
</div>

<!-- ==============================================
     CHART — Aktivitas Bulanan
     ============================================== -->
<div class="grid lg:grid-cols-3 gap-4 mb-6">

    <!-- Chart 1: Aktivitas Bulanan -->
    <div class="card lg:col-span-2">
        <div class="card-header">
            <div>
                <h3 class="font-display font-semibold text-neutral-900">Aktivitas Bulanan</h3>
                <p class="text-xs text-neutral-500 mt-0.5">Jumlah pesan, aspirasi, dan pengguna baru</p>
            </div>
        </div>
        <div class="card-body">
            <div class="h-72">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart 2: Distribusi Siswa per Gender -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="font-display font-semibold text-neutral-900">Distribusi Siswa</h3>
                <p class="text-xs text-neutral-500 mt-0.5">Berdasarkan jenis kelamin</p>
            </div>
        </div>
        <div class="card-body">
            <div class="h-72 flex items-center justify-center">
                <canvas id="genderChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     CHART — Chat 7 Hari + Status Aspirasi
     ============================================== -->
<div class="grid lg:grid-cols-3 gap-4 mb-6">

    <!-- Chart 3: Chat 7 Hari -->
    <div class="card lg:col-span-2">
        <div class="card-header">
            <div>
                <h3 class="font-display font-semibold text-neutral-900">Aktivitas Pesan</h3>
                <p class="text-xs text-neutral-500 mt-0.5">7 hari terakhir</p>
            </div>
        </div>
        <div class="card-body">
            <div class="h-64">
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart 4: Status Aspirasi -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="font-display font-semibold text-neutral-900">Status Aspirasi</h3>
                <p class="text-xs text-neutral-500 mt-0.5">Berdasarkan status penanganan</p>
            </div>
        </div>
        <div class="card-body">
            <div class="h-64 flex items-center justify-center">
                <canvas id="aspirationChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     STATISTIK SISWA PER KELAS
     ============================================== -->
<div class="card mb-6">
    <div class="card-header">
        <div>
            <h3 class="font-display font-semibold text-neutral-900">Distribusi Siswa per Kelas</h3>
            <p class="text-xs text-neutral-500 mt-0.5">Jumlah siswa aktif per kelas</p>
        </div>
    </div>
    <div class="card-body">
        <div class="h-72">
            <canvas id="classChart"></canvas>
        </div>
    </div>
</div>

<!-- ==============================================
     TOP USERS + RECENT ACTIVITIES
     ============================================== -->
<div class="grid lg:grid-cols-2 gap-4 mb-6">

    <!-- Top Users -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="font-display font-semibold text-neutral-900">Pengguna Teraktif</h3>
                <p class="text-xs text-neutral-500 mt-0.5">Berdasarkan jumlah pesan terkirim</p>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($top_senders)): ?>
                <ul class="divide-y divide-neutral-100">
                    <?php foreach ($top_senders as $i => $u): ?>
                        <li class="px-5 py-3 hover:bg-neutral-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-xs font-semibold flex-shrink-0">
                                    <?= $i + 1 ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-neutral-900 text-sm truncate">
                                        <?= html_escape($u->name ?? '-') ?>
                                    </div>
                                    <div class="text-xs text-neutral-500">
                                        <span class="uppercase tracking-wide"><?= html_escape($u->role ?? '') ?></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-primary-600"><?= number_format($u->total) ?></div>
                                    <div class="text-[10px] text-neutral-400">pesan</div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <?php $this->load->view('components/empty_state', [
                    'icon'    => 'inbox',
                    'title'   => 'Belum Ada Data',
                    'message' => 'Belum ada aktivitas pesan.',
                ]); ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="font-display font-semibold text-neutral-900">Aktivitas Terbaru</h3>
                <p class="text-xs text-neutral-500 mt-0.5">Log aktivitas pengguna</p>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($recent_activities)): ?>
                <ul class="divide-y divide-neutral-100 max-h-96 overflow-y-auto">
                    <?php foreach (array_slice($recent_activities, 0, 15) as $log): ?>
                        <li class="px-5 py-3 hover:bg-neutral-50 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-medium text-neutral-800 line-clamp-2">
                                        <?= html_escape($log->description ?? '-') ?>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1 text-[10px] text-neutral-400">
                                        <span class="font-medium text-neutral-500">
                                            <?= html_escape($log->user_name ?? 'System') ?>
                                        </span>
                                        <span>•</span>
                                        <span><?= format_time_ago($log->created_at ?? '') ?></span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <?php $this->load->view('components/empty_state', [
                    'icon'    => 'inbox',
                    'title'   => 'Belum Ada Aktivitas',
                    'message' => 'Aktivitas pengguna akan muncul di sini.',
                ]); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ==============================================
     EXPORT SECTION
     ============================================== -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="font-display font-semibold text-neutral-900">Export Data</h3>
            <p class="text-xs text-neutral-500 mt-0.5">Unduh data dalam format CSV (dapat dibuka di Excel)</p>
        </div>
    </div>
    <div class="card-body">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <a href="<?= base_url('reports/export_messages') ?>"
               class="card p-4 hover:shadow-elevated transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-display font-semibold text-sm text-neutral-900">Export Pesan</div>
                        <div class="text-xs text-neutral-500">Data chat</div>
                    </div>
                    <svg class="w-4 h-4 text-neutral-400 group-hover:text-primary-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
            </a>

            <a href="<?= base_url('reports/export_aspirations') ?>"
               class="card p-4 hover:shadow-elevated transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-accent-100 text-accent-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-display font-semibold text-sm text-neutral-900">Export Aspirasi</div>
                        <div class="text-xs text-neutral-500">Data aspirasi</div>
                    </div>
                    <svg class="w-4 h-4 text-neutral-400 group-hover:text-accent-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
            </a>

            <a href="<?= base_url('reports/export_students') ?>"
               class="card p-4 hover:shadow-elevated transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-success-100 text-success-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-display font-semibold text-sm text-neutral-900">Export Siswa</div>
                        <div class="text-xs text-neutral-500">Data siswa</div>
                    </div>
                    <svg class="w-4 h-4 text-neutral-400 group-hover:text-success-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
            </a>

            <a href="<?= base_url('reports/export_scores') ?>"
               class="card p-4 hover:shadow-elevated transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-warning-100 text-warning-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-display font-semibold text-sm text-neutral-900">Export Nilai</div>
                        <div class="text-xs text-neutral-500">Data nilai siswa</div>
                    </div>
                    <svg class="w-4 h-4 text-neutral-400 group-hover:text-warning-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- ==============================================
     CHART.JS + DATA
     ============================================== -->
<script src="<?= base_url('assets/js/vendor/chart.umd.min.js') ?>"></script>
<script>
    window.BASE_URL = "<?= base_url() ?>";
    window.REPORT_DATA = {
        monthly: {
            labels:      <?= json_encode($chart_months ?? []) ?>,
            messages:    <?= json_encode($chart_messages ?? []) ?>,
            aspirations: <?= json_encode($chart_aspirations ?? []) ?>,
            users:       <?= json_encode($chart_users ?? []) ?>,
        },
        weekly: {
            labels:      <?= json_encode($weekly_messages['labels'] ?? []) ?>,
            messages:    <?= json_encode($weekly_messages['data'] ?? []) ?>,
            aspirations: <?= json_encode($weekly_aspirations['data'] ?? []) ?>,
        },
        gender: {
            male:   <?= (int)($students_by_gender['male'] ?? 0) ?>,
            female: <?= (int)($students_by_gender['female'] ?? 0) ?>,
        },
        classes: {
            labels: <?= json_encode(array_map(fn($c) => $c->class_name ?? '', $students_by_class ?? [])) ?>,
            data:   <?= json_encode(array_map(fn($c) => (int)($c->total ?? 0), $students_by_class ?? [])) ?>,
        },
        aspirations: {
            pending:  <?= (int)($aspirations_by_status['pending'] ?? 0) ?>,
            dibaca:   <?= (int)($aspirations_by_status['dibaca'] ?? 0) ?>,
            diproses: <?= (int)($aspirations_by_status['diproses'] ?? 0) ?>,
            selesai:  <?= (int)($aspirations_by_status['selesai'] ?? 0) ?>,
        },
    };
</script>
<script src="<?= base_url('assets/js/reports.js') ?>"></script>

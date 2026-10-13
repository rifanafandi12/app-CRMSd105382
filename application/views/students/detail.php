<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Detail Siswa',
    'breadcrumb' => [
        ['label' => 'Data Siswa', 'url' => 'students'],
        ['label' => $student->student_name ?? 'Detail'],
    ],
]);
?>

<div class="grid lg:grid-cols-3 gap-4">

    <!-- Kartu Profil -->
    <div class="card overflow-hidden lg:col-span-1">
        <div class="h-24 bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700"></div>

        <div class="px-5 pb-5">
            <div class="-mt-12 mb-3">
                <div class="w-24 h-24 rounded-full bg-white p-1 shadow-xl">
                    <div class="w-full h-full rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-3xl font-bold">
                        <?= strtoupper(substr($student->student_name ?? 'S', 0, 1)) ?>
                    </div>
                </div>
            </div>

            <h2 class="font-display font-bold text-xl text-neutral-900 mb-1">
                <?= html_escape($student->student_name ?? '-') ?>
            </h2>
            <div class="flex items-center gap-2 mb-5">
                <span class="badge-primary"><?= html_escape($student->class_name ?? 'Belum ada kelas') ?></span>
                <?php if (!empty($student->is_active)): ?>
                    <span class="badge-success">Aktif</span>
                <?php else: ?>
                    <span class="badge-neutral">Tidak Aktif</span>
                <?php endif; ?>
            </div>

            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-0.5">NISN</dt>
                    <dd class="font-mono text-neutral-900"><?= html_escape($student->nisn ?? '-') ?></dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-0.5">Jenis Kelamin</dt>
                    <dd class="text-neutral-900"><?= html_escape($student->gender ?? '-') ?></dd>
                </div>

                <?php if (!empty($student->birth_place) || !empty($student->birth_date)): ?>
                    <div>
                        <dt class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-0.5">Tempat, Tanggal Lahir</dt>
                        <dd class="text-neutral-900">
                            <?= html_escape(($student->birth_place ?? '') . ', ' . format_date($student->birth_date ?? '')) ?>
                        </dd>
                    </div>
                <?php endif; ?>

                <?php if (!empty($student->address)): ?>
                    <div>
                        <dt class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-0.5">Alamat</dt>
                        <dd class="text-neutral-900"><?= nl2br(html_escape($student->address)) ?></dd>
                    </div>
                <?php endif; ?>

                <?php if (!empty($student->enrollment_year)): ?>
                    <div>
                        <dt class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-0.5">Tahun Masuk</dt>
                        <dd class="text-neutral-900"><?= html_escape($student->enrollment_year) ?></dd>
                    </div>
                <?php endif; ?>
            </dl>

            <div class="mt-5 pt-5 border-t border-neutral-100">
                <div class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-3">Data Orang Tua</div>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-neutral-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-neutral-900"><?= html_escape($student->parent_name ?? '-') ?></span>
                    </div>

                    <?php if (!empty($student->parent_phone)): ?>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-neutral-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="tel:<?= html_escape($student->parent_phone) ?>" class="text-primary-600 hover:underline">
                                <?= html_escape($student->parent_phone) ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($student->parent_email)): ?>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-neutral-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:<?= html_escape($student->parent_email) ?>" class="text-primary-600 hover:underline truncate">
                                <?= html_escape($student->parent_email) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex gap-2 mt-5 pt-5 border-t border-neutral-100">
                <a href="<?= base_url('scores/raport/' . ($student->id_student ?? 0)) ?>"
                   class="btn-primary btn-sm flex-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Lihat Rapor
                </a>
            </div>
        </div>
    </div>

    <!-- Nilai & Informasi -->
    <div class="lg:col-span-2 space-y-4">
        <!-- Nilai Siswa -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="font-display font-semibold text-neutral-900">Nilai Akademik</h3>
                    <p class="text-xs text-neutral-500 mt-0.5">Ringkasan nilai siswa</p>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($scores)): ?>
                    <div class="table-wrapper border-0 rounded-none">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center">NH1</th>
                                    <th class="text-center">NH2</th>
                                    <th class="text-center">UTS</th>
                                    <th class="text-center">UAS</th>
                                    <th class="text-center">Nilai Akhir</th>
                                    <th class="text-center">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($scores, 0, 10) as $score): ?>
                                    <tr>
                                        <td class="font-medium text-neutral-800">
                                            <?= html_escape($score->subject_name ?? '-') ?>
                                        </td>
                                        <td class="text-center text-neutral-600"><?= html_escape($score->daily_score_1 ?? '-') ?></td>
                                        <td class="text-center text-neutral-600"><?= html_escape($score->daily_score_2 ?? '-') ?></td>
                                        <td class="text-center text-neutral-600"><?= html_escape($score->mid_exam_score ?? '-') ?></td>
                                        <td class="text-center text-neutral-600"><?= html_escape($score->final_exam_score ?? '-') ?></td>
                                        <td class="text-center font-semibold text-neutral-900">
                                            <?= $score->final_score !== null ? number_format($score->final_score, 1) : '-' ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($score->grade)): ?>
                                                <span class="badge-primary"><?= html_escape($score->grade) ?></span>
                                            <?php else: ?>
                                                <span class="text-neutral-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <?php $this->load->view('components/empty_state', [
                        'icon'    => 'chart',
                        'title'   => 'Belum Ada Nilai',
                        'message' => 'Nilai siswa belum diinput oleh guru.',
                    ]); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Rapor Siswa',
    'subtitle'   => 'Semester ' . html_escape($semester_text ?? '-') . ' — ' . html_escape($academic_year ?? '-'),
    'breadcrumb' => [
        ['label' => 'Nilai', 'url' => 'scores'],
        ['label' => 'Rapor'],
    ],
]);
?>

<!-- Actions -->
<div class="flex flex-wrap gap-2 mb-4 no-print">
    <a href="<?= base_url('scores/print_raport/' . ($student->id_student ?? 0)) ?>?semester=<?= $semester ?>&academic_year=<?= urlencode($academic_year) ?>"
       target="_blank" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak Rapor
    </a>
    <a href="<?= base_url('students/detail/' . ($student->id_student ?? 0)) ?>" class="btn-outline">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>

<div class="card" id="raport-content">
    <div class="card-body">

        <!-- Header -->
        <div class="text-center border-b-2 border-primary-600 pb-6 mb-6">
            <div class="flex justify-center items-center gap-4 mb-4">
                <img src="<?= base_url('assets/images/logo-tutwuri-handayani.webp') ?>"
                     alt="Logo" class="w-16 h-16 object-contain">
                <div class="text-center">
                    <div class="text-xs font-semibold text-neutral-600 uppercase tracking-wide">Dinas Pendidikan</div>
                    <div class="text-xs text-neutral-500">Kabupaten Deli Serdang</div>
                </div>
            </div>
            <h1 class="font-display font-bold text-2xl text-neutral-900 mb-1">SD NEGERI 105382 GALANG</h1>
            <p class="text-xs text-neutral-500 mb-4">Jalan Kesatuan Gang Inpres, Desa Galang Kota, Kec. Galang, Kab. Deli Serdang</p>
            <div class="inline-block px-6 py-2 bg-primary-600 text-white font-display font-bold text-lg rounded-lg">
                RAPOR SISWA
            </div>
        </div>

        <!-- Info Siswa -->
        <div class="grid sm:grid-cols-2 gap-4 mb-6 text-sm">
            <dl class="space-y-2">
                <div class="flex"><dt class="w-32 text-neutral-500">Nama Siswa</dt><dd class="font-semibold text-neutral-900">: <?= html_escape($student->student_name ?? '-') ?></dd></div>
                <div class="flex"><dt class="w-32 text-neutral-500">NISN</dt><dd class="font-mono text-neutral-900">: <?= html_escape($student->nisn ?? '-') ?></dd></div>
                <div class="flex"><dt class="w-32 text-neutral-500">Kelas</dt><dd class="text-neutral-900">: <?= html_escape($student->class_name ?? '-') ?></dd></div>
            </dl>
            <dl class="space-y-2">
                <div class="flex"><dt class="w-32 text-neutral-500">Semester</dt><dd class="text-neutral-900">: <?= html_escape($semester_text ?? '-') ?></dd></div>
                <div class="flex"><dt class="w-32 text-neutral-500">Tahun Ajaran</dt><dd class="text-neutral-900">: <?= html_escape($academic_year ?? '-') ?></dd></div>
                <div class="flex"><dt class="w-32 text-neutral-500">Tanggal Cetak</dt><dd class="text-neutral-900">: <?= format_date(date('Y-m-d')) ?></dd></div>
            </dl>
        </div>

        <!-- Nilai -->
        <div class="table-wrapper mb-6">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>Mata Pelajaran</th>
                        <th class="text-center">NH 1</th>
                        <th class="text-center">NH 2</th>
                        <th class="text-center">UTS</th>
                        <th class="text-center">UAS</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th class="text-center">Grade</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($scores)): ?>
                        <?php foreach ($scores as $i => $score): ?>
                            <tr>
                                <td class="text-center text-neutral-500"><?= $i + 1 ?></td>
                                <td class="font-medium text-neutral-800"><?= html_escape($score->subject_name ?? '-') ?></td>
                                <td class="text-center text-neutral-600"><?= $score->daily_score_1 ?? '-' ?></td>
                                <td class="text-center text-neutral-600"><?= $score->daily_score_2 ?? '-' ?></td>
                                <td class="text-center text-neutral-600"><?= $score->mid_exam_score ?? '-' ?></td>
                                <td class="text-center text-neutral-600"><?= $score->final_exam_score ?? '-' ?></td>
                                <td class="text-center font-semibold text-neutral-900">
                                    <?= $score->final_score ? number_format($score->final_score, 2) : '-' ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge-primary"><?= html_escape($score->grade ?? '-') ?></span>
                                </td>
                                <td class="text-xs text-neutral-500"><?= html_escape($score->notes ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-primary-50 font-semibold">
                            <td colspan="6" class="text-right text-primary-700">Rata-rata:</td>
                            <td class="text-center text-primary-700"><?= number_format($average ?? 0, 2) ?></td>
                            <td class="text-center"><span class="badge-primary"><?= html_escape($average_grade ?? '-') ?></span></td>
                            <td></td>
                        </tr>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center py-8 text-neutral-500">Belum ada nilai</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tanda tangan -->
        <div class="grid sm:grid-cols-2 gap-8 mt-10 pt-6">
            <div class="text-center">
                <p class="text-sm text-neutral-600 mb-16">Mengetahui,<br>Kepala Sekolah</p>
                <div class="border-t border-neutral-400 inline-block px-8 pt-1">
                    <p class="text-sm font-semibold text-neutral-900">Kepala SDN 105382</p>
                </div>
            </div>
            <div class="text-center">
                <p class="text-sm text-neutral-600 mb-16">Galang, <?= format_date(date('Y-m-d')) ?><br>Wali Kelas</p>
                <div class="border-t border-neutral-400 inline-block px-8 pt-1">
                    <p class="text-sm font-semibold text-neutral-900">Wali Kelas</p>
                </div>
            </div>
        </div>
    </div>
</div>

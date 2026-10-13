<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor — <?= html_escape($student->student_name ?? '-') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <style>
        @media print {
            @page { size: A4; margin: 15mm; }
            body { background: white !important; }
            .no-print { display: none !important; }
        }
        body { padding: 20px; font-family: Inter, sans-serif; }
        table { border-collapse: collapse; width: 100%; font-size: 12px; }
        table th, table td { border: 1px solid #d1d5db; padding: 6px 8px; }
        table th { background: #f3f4f6; font-weight: 600; }
    </style>
</head>
<body>

<div class="max-w-4xl mx-auto">

    <!-- Header -->
    <div class="text-center border-b-2 border-neutral-800 pb-4 mb-5">
        <div class="flex justify-center items-center gap-4 mb-3">
            <img src="<?= base_url('assets/images/logo-tutwuri-handayani.webp') ?>"
                 alt="Logo" class="w-14 h-14 object-contain">
            <div class="text-center text-xs">
                <div class="font-semibold">DINAS PENDIDIKAN</div>
                <div>KABUPATEN DELI SERDANG</div>
            </div>
        </div>
        <h1 class="font-bold text-lg">SD NEGERI 105382 GALANG</h1>
        <p class="text-xs">Jalan Kesatuan Gang Inpres, Desa Galang Kota, Kec. Galang, Kab. Deli Serdang</p>
        <div class="mt-3 font-bold text-base">LAPORAN HASIL BELAJAR (RAPOR)</div>
    </div>

    <!-- Info Siswa -->
    <table class="mb-4" style="border: none;">
        <tr style="border: none;">
            <td style="border: none; width: 50%; padding: 2px 0; font-size: 12px;">
                <div style="display: flex; margin-bottom: 4px;"><span style="width: 100px;">Nama Siswa</span>: <b><?= html_escape($student->student_name ?? '-') ?></b></div>
                <div style="display: flex; margin-bottom: 4px;"><span style="width: 100px;">NISN</span>: <?= html_escape($student->nisn ?? '-') ?></div>
                <div style="display: flex;"><span style="width: 100px;">Kelas</span>: <?= html_escape($student->class_name ?? '-') ?></div>
            </td>
            <td style="border: none; width: 50%; padding: 2px 0; font-size: 12px;">
                <div style="display: flex; margin-bottom: 4px;"><span style="width: 100px;">Semester</span>: <?= html_escape($semester_text ?? '-') ?></div>
                <div style="display: flex; margin-bottom: 4px;"><span style="width: 100px;">Tahun Ajaran</span>: <?= html_escape($academic_year ?? '-') ?></div>
                <div style="display: flex;"><span style="width: 100px;">Tanggal</span>: <?= format_date(date('Y-m-d')) ?></div>
            </td>
        </tr>
    </table>

    <!-- Nilai -->
    <table>
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">No</th>
                <th>Mata Pelajaran</th>
                <th style="width: 50px; text-align: center;">NH 1</th>
                <th style="width: 50px; text-align: center;">NH 2</th>
                <th style="width: 50px; text-align: center;">UTS</th>
                <th style="width: 50px; text-align: center;">UAS</th>
                <th style="width: 70px; text-align: center;">Nilai Akhir</th>
                <th style="width: 50px; text-align: center;">Grade</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($scores)): ?>
                <?php foreach ($scores as $i => $score): ?>
                    <tr>
                        <td style="text-align: center;"><?= $i + 1 ?></td>
                        <td><?= html_escape($score->subject_name ?? '-') ?></td>
                        <td style="text-align: center;"><?= $score->daily_score_1 ?? '-' ?></td>
                        <td style="text-align: center;"><?= $score->daily_score_2 ?? '-' ?></td>
                        <td style="text-align: center;"><?= $score->mid_exam_score ?? '-' ?></td>
                        <td style="text-align: center;"><?= $score->final_exam_score ?? '-' ?></td>
                        <td style="text-align: center; font-weight: 600;"><?= $score->final_score ? number_format($score->final_score, 2) : '-' ?></td>
                        <td style="text-align: center;"><?= html_escape($score->grade ?? '-') ?></td>
                        <td style="font-size: 11px;"><?= html_escape($score->notes ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="background: #f3f4f6; font-weight: 600;">
                    <td colspan="6" style="text-align: right;">Rata-rata:</td>
                    <td style="text-align: center;"><?= number_format($average ?? 0, 2) ?></td>
                    <td style="text-align: center;"><?= html_escape($average_grade ?? '-') ?></td>
                    <td></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">Belum ada nilai</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <table style="border: none; margin-top: 30px;">
        <tr style="border: none;">
            <td style="border: none; width: 50%; text-align: center; padding: 5px; font-size: 12px;">
                <div>Mengetahui,</div>
                <div>Kepala Sekolah</div>
                <div style="height: 60px;"></div>
                <div style="border-top: 1px solid #374151; display: inline-block; padding: 0 30px; padding-top: 3px; font-weight: 600;">
                    Kepala SDN 105382
                </div>
            </td>
            <td style="border: none; width: 50%; text-align: center; padding: 5px; font-size: 12px;">
                <div>Galang, <?= format_date(date('Y-m-d')) ?></div>
                <div>Wali Kelas</div>
                <div style="height: 60px;"></div>
                <div style="border-top: 1px solid #374151; display: inline-block; padding: 0 30px; padding-top: 3px; font-weight: 600;">
                    Wali Kelas
                </div>
            </td>
        </tr>
    </table>

</div>

<!-- Auto print button (hidden saat cetak) -->
<div class="no-print text-center mt-6">
    <button onclick="window.print()" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak Sekarang
    </button>
</div>

<script>
    // Auto trigger print setelah 500ms (opsional)
    // window.addEventListener('load', () => setTimeout(() => window.print(), 500));
</script>

</body>
</html>

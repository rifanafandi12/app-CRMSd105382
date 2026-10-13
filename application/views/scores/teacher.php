<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Input Nilai Siswa',
    'subtitle'   => 'Masukkan nilai harian, UTS, dan UAS siswa',
    'breadcrumb' => [['label' => 'Nilai']],
]);
?>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="form-label">Kelas</label>
                <select id="filter-class" class="form-select">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach (($my_classes ?? []) as $class): ?>
                        <option value="<?= (int)$class->id_class ?>">
                            <?= html_escape($class->class_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Siswa</label>
                <select id="filter-student" class="form-select" disabled>
                    <option value="">-- Pilih Kelas Dulu --</option>
                </select>
            </div>

            <div>
                <label class="form-label">Semester</label>
                <select id="filter-semester" class="form-select">
                    <option value="1">Ganjil</option>
                    <option value="2">Genap</option>
                </select>
            </div>

            <div>
                <label class="form-label">Tahun Ajaran</label>
                <select id="filter-year" class="form-select">
                    <?php
                    $currentYear = date('Y') . '/' . (date('Y') + 1);
                    foreach (($academic_years ?? []) as $year): ?>
                        <option value="<?= html_escape($year) ?>" <?= $year === $currentYear ? 'selected' : '' ?>>
                            <?= html_escape($year) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Score Input Card -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="font-display font-semibold text-neutral-900">Daftar Nilai</h3>
            <p class="text-xs text-neutral-500 mt-0.5">Isi nilai per mata pelajaran</p>
        </div>
    </div>

    <div class="card-body">
        <!-- Alert -->
        <div id="score-alert-container"></div>

        <!-- Placeholder -->
        <div id="score-placeholder">
            <?php $this->load->view('components/empty_state', [
                'icon'    => 'chart',
                'title'   => 'Pilih Siswa Dulu',
                'message' => 'Pilih kelas dan siswa untuk mulai input nilai.',
            ]); ?>
        </div>

        <!-- Form -->
        <div id="score-input-wrapper" class="hidden">
            <form id="score-form">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="min-w-[180px]">Mata Pelajaran</th>
                                <th class="w-24 text-center">NH 1</th>
                                <th class="w-24 text-center">NH 2</th>
                                <th class="w-24 text-center">UTS</th>
                                <th class="w-24 text-center">UAS</th>
                                <th class="w-28 text-center">Nilai Akhir</th>
                                <th class="w-20 text-center">Grade</th>
                                <th class="min-w-[150px]">Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="score-tbody">
                            <!-- Diisi oleh JS -->
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-5">
                    <p class="text-xs text-neutral-500">
                        <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Nilai akhir = 30% harian + 30% UTS + 40% UAS
                    </p>
                    <button type="submit" id="save-scores-btn" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Subjects data untuk JS -->
<script>
    window.BASE_URL = "<?= base_url() ?>";
    window.SUBJECTS = <?= json_encode(array_map(function($s) {
        return [
            'id'    => (int)$s->id_subject,
            'name'  => $s->subject_name,
            'code'  => $s->subject_code ?? '',
        ];
    }, $subjects ?? [])) ?>;
</script>
<script src="<?= base_url('assets/js/scores.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect    = document.getElementById('filter-class');
    const studentSelect  = document.getElementById('filter-student');
    const semesterSelect = document.getElementById('filter-semester');
    const yearSelect     = document.getElementById('filter-year');

    // Kelas berubah → load siswa
    classSelect?.addEventListener('change', function () {
        if (this.value) {
            Scores.loadStudentsByClass(this.value, 'filter-student');
            document.getElementById('score-input-wrapper').classList.add('hidden');
            document.getElementById('score-placeholder').classList.remove('hidden');
        } else {
            studentSelect.innerHTML = '<option value="">-- Pilih Kelas Dulu --</option>';
            studentSelect.disabled = true;
        }
    });

    // Siswa berubah → load nilai
    studentSelect?.addEventListener('change', function () {
        if (this.value) {
            Scores.state.currentStudent = this.value;
            Scores.state.currentSemester = semesterSelect.value;
            Scores.state.currentAcademicYear = yearSelect.value;
            Scores.loadScores(this.value, semesterSelect.value, yearSelect.value);
        }
    });

    // Semester / tahun berubah → reload
    [semesterSelect, yearSelect].forEach(el => {
        el?.addEventListener('change', function () {
            if (Scores.state.currentStudent) {
                Scores.state.currentSemester = semesterSelect.value;
                Scores.state.currentAcademicYear = yearSelect.value;
                Scores.loadScores(
                    Scores.state.currentStudent,
                    semesterSelect.value,
                    yearSelect.value
                );
            }
        });
    });
});
</script>

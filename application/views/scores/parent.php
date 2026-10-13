<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Nilai Anak',
    'subtitle'   => 'Pantau perkembangan akademik anak Anda',
    'breadcrumb' => [['label' => 'Nilai']],
]);
?>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <div class="grid sm:grid-cols-3 gap-4">
            <div>
                <label class="form-label">Pilih Anak</label>
                <select id="filter-student" class="form-select">
                    <option value="">-- Pilih Anak --</option>
                    <?php foreach (($children ?? []) as $child): ?>
                        <option value="<?= (int)$child->id_student ?>">
                            <?= html_escape($child->student_name) ?> — <?= html_escape($child->class_name ?? '') ?>
                        </option>
                    <?php endforeach; ?>
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

<!-- Score Display -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="font-display font-semibold text-neutral-900">Rincian Nilai</h3>
            <p class="text-xs text-neutral-500 mt-0.5">Nilai per mata pelajaran</p>
        </div>
        <a id="raport-link" href="#" class="btn-outline btn-sm hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Lihat Rapor
        </a>
    </div>

    <div class="card-body">
        <div id="score-placeholder">
            <?php $this->load->view('components/empty_state', [
                'icon'    => 'chart',
                'title'   => 'Pilih Anak Dulu',
                'message' => 'Pilih anak Anda untuk melihat nilai.',
            ]); ?>
        </div>

        <div id="score-display" class="hidden">
            <!-- Summary -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="card bg-gradient-to-br from-primary-50 to-white p-4">
                    <div class="text-xs font-semibold text-primary-600 uppercase tracking-wide mb-1">Rata-rata</div>
                    <div class="font-display font-bold text-2xl text-primary-700" id="avg-score">-</div>
                </div>
                <div class="card bg-gradient-to-br from-success-50 to-white p-4">
                    <div class="text-xs font-semibold text-success-600 uppercase tracking-wide mb-1">Grade</div>
                    <div class="font-display font-bold text-2xl text-success-700" id="avg-grade">-</div>
                </div>
                <div class="card bg-gradient-to-br from-accent-50 to-white p-4">
                    <div class="text-xs font-semibold text-accent-600 uppercase tracking-wide mb-1">Total Mapel</div>
                    <div class="font-display font-bold text-2xl text-accent-700" id="total-subjects">0</div>
                </div>
                <div class="card bg-gradient-to-br from-warning-50 to-white p-4">
                    <div class="text-xs font-semibold text-warning-600 uppercase tracking-wide mb-1">Nilai Tertinggi</div>
                    <div class="font-display font-bold text-2xl text-warning-700" id="highest-score">-</div>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
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
                    <tbody id="parent-score-tbody">
                        <!-- Diisi oleh JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    window.BASE_URL = "<?= base_url() ?>";
</script>
<script src="<?= base_url('assets/js/scores.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const studentSelect  = document.getElementById('filter-student');
    const semesterSelect = document.getElementById('filter-semester');
    const yearSelect     = document.getElementById('filter-year');
    const raportLink     = document.getElementById('raport-link');

    function loadParentScores() {
        const studentId = studentSelect.value;
        if (!studentId) return;

        const semester = semesterSelect.value;
        const year     = yearSelect.value;

        // Raport link
        if (raportLink) {
            raportLink.href = window.BASE_URL + 'scores/raport/' + studentId
                            + '?semester=' + semester
                            + '&academic_year=' + encodeURIComponent(year);
            raportLink.classList.remove('hidden');
        }

        document.getElementById('score-placeholder').classList.add('hidden');
        document.getElementById('score-display').classList.remove('hidden');

        fetch(window.BASE_URL + 'scores/get_scores?student_id=' + studentId
              + '&semester=' + semester + '&academic_year=' + encodeURIComponent(year), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(r => r.json())
        .then(res => {
            const scores = (res.data && Array.isArray(res.data)) ? res.data
                         : (Array.isArray(res) ? res : []);
            renderParentScores(scores);
        })
        .catch(err => console.error(err));
    }

    function renderParentScores(scores) {
        const tbody = document.getElementById('parent-score-tbody');
        tbody.innerHTML = '';

        if (!scores || scores.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-neutral-500 text-sm">
                Belum ada nilai untuk periode ini</td></tr>`;
            ['avg-score','avg-grade','highest-score'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = '-';
            });
            document.getElementById('total-subjects').textContent = '0';
            return;
        }

        let total = 0, count = 0, highest = 0;

        scores.forEach(s => {
            const fs = parseFloat(s.final_score) || 0;
            if (fs > 0) {
                total += fs;
                count++;
                if (fs > highest) highest = fs;
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="font-medium text-neutral-800">${escapeHtml(s.subject_name || '-')}</td>
                <td class="text-center text-neutral-600">${s.daily_score_1 ?? '-'}</td>
                <td class="text-center text-neutral-600">${s.daily_score_2 ?? '-'}</td>
                <td class="text-center text-neutral-600">${s.mid_exam_score ?? '-'}</td>
                <td class="text-center text-neutral-600">${s.final_exam_score ?? '-'}</td>
                <td class="text-center font-semibold text-neutral-900">${s.final_score ? parseFloat(s.final_score).toFixed(2) : '-'}</td>
                <td class="text-center"><span class="badge-${getGradeColor(s.grade)}">${s.grade || '-'}</span></td>
                <td class="text-xs text-neutral-500">${escapeHtml(s.notes || '-')}</td>
            `;
            tbody.appendChild(tr);
        });

        const avg = count > 0 ? total / count : 0;
        document.getElementById('avg-score').textContent = avg.toFixed(2);
        document.getElementById('avg-grade').textContent = calculateGrade(avg);
        document.getElementById('total-subjects').textContent = count;
        document.getElementById('highest-score').textContent = highest.toFixed(2);
    }

    function calculateGrade(score) {
        if (score >= 90) return 'A+';
        if (score >= 85) return 'A';
        if (score >= 80) return 'A-';
        if (score >= 75) return 'B+';
        if (score >= 70) return 'B';
        if (score >= 65) return 'B-';
        if (score >= 60) return 'C+';
        if (score >= 55) return 'C';
        if (score >= 50) return 'D';
        return 'E';
    }

    function getGradeColor(grade) {
        if (!grade) return 'neutral';
        if (grade.startsWith('A')) return 'success';
        if (grade.startsWith('B')) return 'primary';
        if (grade.startsWith('C')) return 'warning';
        return 'danger';
    }

    function escapeHtml(t) {
        if (!t) return '';
        const d = document.createElement('div');
        d.textContent = String(t);
        return d.innerHTML;
    }

    studentSelect?.addEventListener('change', loadParentScores);
    semesterSelect?.addEventListener('change', loadParentScores);
    yearSelect?.addEventListener('change', loadParentScores);
});
</script>

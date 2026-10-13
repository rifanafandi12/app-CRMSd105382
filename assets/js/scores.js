/**
 * Scores.js — Logic input & kelola nilai siswa
 */
(function () {
    'use strict';

    const state = {
        baseUrl: window.BASE_URL || '/',
        currentStudent: null,
        currentSemester: 1,
        currentAcademicYear: '',
        subjects: window.SUBJECTS || [],
    };

    // ==============================================
    // AJAX helpers
    // ==============================================
    async function ajaxGet(url, params = {}) {
        const qs = new URLSearchParams(params).toString();
        const res = await fetch(url + (qs ? '?' + qs : ''), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    }

    async function ajaxPost(url, data) {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfName = 'csrf_token';
        const csrfVal  = csrfMeta ? csrfMeta.getAttribute('content') : '';

        const body = new URLSearchParams();
        body.append(csrfName, csrfVal);

        Object.keys(data).forEach(key => {
            const v = data[key];
            if (Array.isArray(v)) {
                v.forEach(item => body.append(key + '[]', item));
            } else if (v !== null && v !== undefined) {
                body.append(key, v);
            }
        });

        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: body.toString(),
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    }

    // ==============================================
    // Load students by class
    // ==============================================
    async function loadStudentsByClass(classId, targetSelectId) {
        const select = document.getElementById(targetSelectId);
        if (!select) return;

        select.innerHTML = '<option value="">Memuat...</option>';
        select.disabled = true;

        try {
            const res = await ajaxGet(state.baseUrl + 'scores/get_students_by_class', { class_id: classId });
            const students = (res.data && Array.isArray(res.data)) ? res.data : (Array.isArray(res) ? res : []);

            select.innerHTML = '<option value="">-- Pilih Siswa --</option>';
            if (students.length > 0) {
                students.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id_student;
                    opt.textContent = s.student_name + ' (' + s.nisn + ')';
                    select.appendChild(opt);
                });
            } else {
                select.innerHTML = '<option value="">Tidak ada siswa</option>';
            }
            select.disabled = false;
        } catch (err) {
            console.error('Load students error:', err);
            select.innerHTML = '<option value="">Gagal memuat</option>';
            select.disabled = false;
        }
    }

    // ==============================================
    // Load scores for a student
    // ==============================================
    async function loadScores(studentId, semester, academicYear) {
        try {
            const res = await ajaxGet(state.baseUrl + 'scores/get_scores', {
                student_id: studentId,
                semester: semester,
                academic_year: academicYear,
            });
            const scores = (res.data && Array.isArray(res.data)) ? res.data : (Array.isArray(res) ? res : []);
            renderScoreTable(scores);
        } catch (err) {
            console.error('Load scores error:', err);
            showAlert('Gagal memuat nilai', 'danger');
        }
    }

    // ==============================================
    // Render table
    // ==============================================
    function renderScoreTable(scores) {
        const tbody = document.getElementById('score-tbody');
        const wrapper = document.getElementById('score-input-wrapper');
        const placeholder = document.getElementById('score-placeholder');
        if (!tbody) return;

        placeholder?.classList.add('hidden');
        wrapper?.classList.remove('hidden');

        // Build map scores by subject
        const scoresBySubject = {};
        scores.forEach(s => { scoresBySubject[s.id_subject] = s; });

        tbody.innerHTML = '';

        // Iterasi berdasarkan SUBJECTS (semua mapel) — supaya semua muncul
        const subjects = state.subjects.length > 0 ? state.subjects : [];
        if (subjects.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-6 text-neutral-500">Belum ada mata pelajaran terdaftar</td></tr>';
            return;
        }

        subjects.forEach(subj => {
            const score = scoresBySubject[subj.id] || {};
            const tr = document.createElement('tr');
            tr.dataset.subjectId = subj.id;

            tr.innerHTML = `
                <td class="font-medium text-neutral-800">
                    ${escapeHtml(subj.name)}
                    <input type="hidden" name="subject_id[]" value="${subj.id}">
                </td>
                <td><input type="number" name="daily_score_1[]" min="0" max="100" step="0.01"
                        class="form-input text-center text-sm score-input"
                        value="${score.daily_score_1 ?? ''}" data-subject="${subj.id}"></td>
                <td><input type="number" name="daily_score_2[]" min="0" max="100" step="0.01"
                        class="form-input text-center text-sm score-input"
                        value="${score.daily_score_2 ?? ''}" data-subject="${subj.id}"></td>
                <td><input type="number" name="mid_exam_score[]" min="0" max="100" step="0.01"
                        class="form-input text-center text-sm score-input"
                        value="${score.mid_exam_score ?? ''}" data-subject="${subj.id}"></td>
                <td><input type="number" name="final_exam_score[]" min="0" max="100" step="0.01"
                        class="form-input text-center text-sm score-input"
                        value="${score.final_exam_score ?? ''}" data-subject="${subj.id}"></td>
                <td class="text-center"><span class="final-score-display font-semibold text-neutral-900">${score.final_score ?? '-'}</span></td>
                <td class="text-center"><span class="grade-display badge-primary">${score.grade ?? '-'}</span></td>
                <td><input type="text" name="notes[]" maxlength="100"
                        class="form-input text-sm" value="${escapeHtml(score.notes ?? '')}"
                        placeholder="Catatan..."></td>
            `;

            tbody.appendChild(tr);
        });

        // Auto-calculate on input
        tbody.querySelectorAll('.score-input').forEach(inp => {
            inp.addEventListener('input', function () {
                autoCalculateRow(this.closest('tr'));
            });
        });
    }

    // ==============================================
    // Auto calculate row
    // ==============================================
    function autoCalculateRow(row) {
        if (!row) return;

        const d1 = parseFloat(row.querySelector('input[name="daily_score_1[]"]')?.value) || 0;
        const d2 = parseFloat(row.querySelector('input[name="daily_score_2[]"]')?.value) || 0;
        const mid = parseFloat(row.querySelector('input[name="mid_exam_score[]"]')?.value) || 0;
        const fin = parseFloat(row.querySelector('input[name="final_exam_score[]"]')?.value) || 0;

        const dailyValues = [d1, d2].filter(v => v > 0);
        const dailyAvg = dailyValues.length > 0 ? dailyValues.reduce((a,b) => a+b, 0) / dailyValues.length : 0;

        const finalScore = (dailyAvg * 0.3) + (mid * 0.3) + (fin * 0.4);
        const rounded = Math.round(finalScore * 100) / 100;
        const grade = calculateGrade(rounded);

        const scoreEl = row.querySelector('.final-score-display');
        const gradeEl = row.querySelector('.grade-display');
        if (scoreEl) scoreEl.textContent = rounded > 0 ? rounded.toFixed(2) : '-';
        if (gradeEl) {
            gradeEl.textContent = grade || '-';
            gradeEl.className = 'grade-display badge-' + getGradeColor(grade);
        }
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

    // ==============================================
    // Save scores
    // ==============================================
    async function saveScores() {
        if (!state.currentStudent) {
            showAlert('Pilih siswa terlebih dahulu', 'warning');
            return;
        }

        const form = document.getElementById('score-form');
        if (!form) return;

        const btn = document.getElementById('save-scores-btn');
        const orig = btn?.innerHTML;

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...';
        }

        const rows = form.querySelectorAll('#score-tbody tr');
        const data = {
            student_id:    state.currentStudent,
            semester:      state.currentSemester,
            academic_year: state.currentAcademicYear,
            subject_id:    [],
            daily_score_1: [],
            daily_score_2: [],
            mid_exam_score: [],
            final_exam_score: [],
            notes: [],
        };

        rows.forEach(row => {
            if (!row.dataset.subjectId) return;
            data.subject_id.push(row.dataset.subjectId);
            data.daily_score_1.push(row.querySelector('input[name="daily_score_1[]"]')?.value || '');
            data.daily_score_2.push(row.querySelector('input[name="daily_score_2[]"]')?.value || '');
            data.mid_exam_score.push(row.querySelector('input[name="mid_exam_score[]"]')?.value || '');
            data.final_exam_score.push(row.querySelector('input[name="final_exam_score[]"]')?.value || '');
            data.notes.push(row.querySelector('input[name="notes[]"]')?.value || '');
        });

        try {
            const result = await ajaxPost(state.baseUrl + 'scores/save', data);
            if (result.status === 'success') {
                showAlert(result.message || 'Nilai berhasil disimpan', 'success');
                if (window.Sound) window.Sound.play('success');
            } else {
                showAlert(result.message || 'Gagal menyimpan', 'danger');
                if (window.Sound) window.Sound.play('error');
            }
        } catch (err) {
            console.error('Save scores error:', err);
            showAlert('Terjadi kesalahan: ' + err.message, 'danger');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = orig;
            }
        }
    }

    // ==============================================
    // Alert
    // ==============================================
    function showAlert(message, type = 'info') {
        const container = document.getElementById('score-alert-container');
        if (!container) return;

        const div = document.createElement('div');
        div.className = 'alert-' + type + ' mb-4';
        div.innerHTML = '<span>' + escapeHtml(message) + '</span>';
        container.innerHTML = '';
        container.appendChild(div);

        setTimeout(() => {
            div.style.transition = 'opacity 0.5s';
            div.style.opacity = '0';
            setTimeout(() => div.remove(), 500);
        }, 4000);
    }

    function escapeHtml(t) {
        if (t === null || t === undefined) return '';
        const d = document.createElement('div');
        d.textContent = String(t);
        return d.innerHTML;
    }

    // ==============================================
    // Public API
    // ==============================================
    window.Scores = {
        loadStudentsByClass,
        loadScores,
        saveScores,
        state,
    };

    // ==============================================
    // Event listeners
    // ==============================================
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('score-form');
        form?.addEventListener('submit', function (e) {
            e.preventDefault();
            saveScores();
        });
    });
})();

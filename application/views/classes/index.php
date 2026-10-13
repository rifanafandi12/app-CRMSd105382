<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Kelola Data Kelas',
    'subtitle'   => 'Kelola kelas dan wali kelas',
    'breadcrumb' => [['label' => 'Kelas']],
]);
?>

<!-- Stat -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php
    $total = count($classes ?? []);
    $withHomeroom = 0;
    $totalStudents = 0;
    foreach (($classes ?? []) as $c) {
        if (!empty($c->homeroom_teacher_id)) $withHomeroom++;
        $totalStudents += (int)($c->student_count ?? 0);
    }
    $this->load->view('components/stat_card', [
        'icon' => 'classroom', 'label' => 'Total Kelas',
        'value' => number_format($total), 'color' => 'primary',
    ]);
    $this->load->view('components/stat_card', [
        'icon' => 'teacher', 'label' => 'Ada Wali Kelas',
        'value' => number_format($withHomeroom), 'color' => 'success',
    ]);
    $this->load->view('components/stat_card', [
        'icon' => 'student', 'label' => 'Total Siswa',
        'value' => number_format($totalStudents), 'color' => 'accent',
    ]);
    $this->load->view('components/stat_card', [
        'icon' => 'users', 'label' => 'Rata-rata/Kelas',
        'value' => $total > 0 ? round($totalStudents / $total, 1) : 0, 'color' => 'warning',
    ]);
    ?>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="font-display font-semibold text-neutral-900">Daftar Kelas</h3>
            <p class="text-xs text-neutral-500 mt-0.5"><?= count($classes) ?> kelas terdaftar</p>
        </div>
        <button type="button" onclick="openAddModal()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kelas
        </button>
    </div>

    <div class="card-body">
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <div class="relative flex-1 sm:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="search" id="class-search" class="form-input pl-9 text-sm" placeholder="Cari kelas...">
            </div>
            <select id="filter-grade" class="form-select text-sm sm:max-w-[180px]">
                <option value="">Semua Tingkat</option>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <option value="<?= $i ?>">Kelas <?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Tingkat</th>
                        <th>Wali Kelas</th>
                        <th class="text-center">Jumlah Siswa</th>
                        <th>Tahun Ajaran</th>
                        <th class="w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $c): ?>
                            <tr class="class-row"
                                data-name="<?= html_escape(strtolower($c->class_name ?? '')) ?>"
                                data-grade="<?= (int)($c->grade_level ?? 0) ?>">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                            <?= html_escape($c->class_name ?? '-') ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-neutral-900"><?= html_escape($c->class_name ?? '-') ?></div>
                                            <div class="text-xs text-neutral-500">Tingkat <?= (int)($c->grade_level ?? 0) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-primary">Kelas <?= (int)($c->grade_level ?? 0) ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($c->homeroom_teacher_name)): ?>
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-neutral-100 text-neutral-600 flex items-center justify-center text-[10px] font-semibold">
                                                <?= strtoupper(substr($c->homeroom_teacher_name, 0, 1)) ?>
                                            </div>
                                            <span class="text-sm text-neutral-800"><?= html_escape($c->homeroom_teacher_name) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-neutral-400 italic">Belum ada wali kelas</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge-<?= ($c->student_count ?? 0) > 0 ? 'success' : 'neutral' ?>">
                                        <?= (int)($c->student_count ?? 0) ?> siswa
                                    </span>
                                </td>
                                <td class="text-sm text-neutral-600">
                                    <?= html_escape($c->academic_year ?? '-') ?>
                                </td>
                                <td>
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="<?= base_url('classes/detail/' . $c->id_class) ?>"
                                           class="p-2 rounded-lg text-neutral-500 hover:text-primary-600 hover:bg-primary-50 transition-colors"
                                           title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <button type="button"
                                                onclick='openEditModal(<?= json_encode($c) ?>)'
                                                class="p-2 rounded-lg text-neutral-500 hover:text-primary-600 hover:bg-primary-50 transition-colors"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button type="button"
                                                onclick="confirmDelete(<?= (int)$c->id_class ?>, '<?= html_escape($c->class_name) ?>', <?= (int)($c->student_count ?? 0) ?>)"
                                                class="p-2 rounded-lg text-neutral-500 hover:text-danger-600 hover:bg-danger-50 transition-colors"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <?php $this->load->view('components/empty_state', [
                                    'icon'       => 'classroom',
                                    'title'      => 'Belum Ada Kelas',
                                    'message'    => 'Tambahkan kelas pertama Anda.',
                                    'action_url' => 'javascript:openAddModal()',
                                    'action_txt' => 'Tambah Kelas',
                                ]); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="class-no-result" class="hidden text-center py-12">
            <p class="text-sm text-neutral-500">Tidak ada kelas yang cocok</p>
        </div>
    </div>
</div>

<!-- MODAL: ADD -->
<?php ob_start(); ?>
<form id="form-add-class" action="<?= base_url('classes/add') ?>" method="post">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <div class="space-y-4">
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Nama Kelas <span class="text-danger-500">*</span></label>
                <input type="text" name="class_name" class="form-input" required maxlength="20" placeholder="Contoh: I-A">
            </div>
            <div>
                <label class="form-label">Tingkat <span class="text-danger-500">*</span></label>
                <select name="grade_level" class="form-select" required>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <option value="<?= $i ?>">Kelas <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div>
            <label class="form-label">Wali Kelas</label>
            <select name="homeroom_teacher_id" class="form-select">
                <option value="">-- Tidak ada --</option>
                <?php foreach (($teachers ?? []) as $t): ?>
                    <option value="<?= (int)$t->id_user ?>"><?= html_escape($t->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label">Tahun Ajaran</label>
            <input type="text" name="academic_year" class="form-input" value="<?= date('Y') . '/' . (date('Y')+1) ?>" maxlength="20">
        </div>
        <div>
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-textarea" rows="2" maxlength="255"></textarea>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('modal-add-class')" class="btn-outline">Batal</button>
        <button type="submit" class="btn-primary">Simpan Kelas</button>
    </div>
</form>
<?php $modal_add = ob_get_clean(); ?>
<?php $this->load->view('components/modal', [
    'id' => 'modal-add-class', 'title' => 'Tambah Kelas', 'size' => 'md', 'content' => $modal_add,
]); ?>

<!-- MODAL: EDIT -->
<?php ob_start(); ?>
<form id="form-edit-class" action="<?= base_url('classes/edit') ?>" method="post">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <input type="hidden" name="id_class" id="edit-id-class">
    <div class="space-y-4">
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Nama Kelas <span class="text-danger-500">*</span></label>
                <input type="text" name="class_name" id="edit-class-name" class="form-input" required maxlength="20">
            </div>
            <div>
                <label class="form-label">Tingkat <span class="text-danger-500">*</span></label>
                <select name="grade_level" id="edit-grade-level" class="form-select" required>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <option value="<?= $i ?>">Kelas <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div>
            <label class="form-label">Wali Kelas</label>
            <select name="homeroom_teacher_id" id="edit-homeroom" class="form-select">
                <option value="">-- Tidak ada --</option>
                <?php foreach (($teachers ?? []) as $t): ?>
                    <option value="<?= (int)$t->id_user ?>"><?= html_escape($t->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label">Deskripsi</label>
            <textarea name="description" id="edit-description" class="form-textarea" rows="2" maxlength="255"></textarea>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('modal-edit-class')" class="btn-outline">Batal</button>
        <button type="submit" class="btn-primary">Simpan Perubahan</button>
    </div>
</form>
<?php $modal_edit = ob_get_clean(); ?>
<?php $this->load->view('components/modal', [
    'id' => 'modal-edit-class', 'title' => 'Edit Kelas', 'size' => 'md', 'content' => $modal_edit,
]); ?>

<!-- MODAL: DELETE -->
<?php ob_start(); ?>
<form id="form-delete-class" action="<?= base_url('classes/delete') ?>" method="post">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <input type="hidden" name="id_class" id="delete-id-class">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-full bg-danger-100 text-danger-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="font-display font-semibold text-neutral-900 mb-1">Hapus Kelas?</h4>
            <p class="text-sm text-neutral-600">
                Kelas <span class="font-medium text-neutral-900" id="delete-class-name">-</span> akan dihapus permanen.
            </p>
            <p id="delete-class-warning" class="text-xs text-warning-600 mt-2 hidden"></p>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('modal-delete-class')" class="btn-outline">Batal</button>
        <button type="submit" class="btn-danger">Ya, Hapus</button>
    </div>
</form>
<?php $modal_del = ob_get_clean(); ?>
<?php $this->load->view('components/modal', [
    'id' => 'modal-delete-class', 'title' => 'Konfirmasi Hapus', 'size' => 'sm', 'content' => $modal_del,
]); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchEl = document.getElementById('class-search');
    const gradeEl  = document.getElementById('filter-grade');
    const noResult = document.getElementById('class-no-result');
    const table    = document.querySelector('#class-search')?.closest('.card-body');

    function filter() {
        const q = (searchEl?.value || '').toLowerCase().trim();
        const g = gradeEl?.value || '';
        const rows = document.querySelectorAll('.class-row');
        let visible = 0;

        rows.forEach(row => {
            const name = row.dataset.name || '';
            const grade = row.dataset.grade || '';
            const match = (!q || name.includes(q)) && (!g || grade === g);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        noResult?.classList.toggle('hidden', visible > 0 || rows.length === 0);
    }

    searchEl?.addEventListener('input', filter);
    gradeEl?.addEventListener('change', filter);
});

function openAddModal() {
    document.getElementById('form-add-class')?.reset();
    openModal('modal-add-class');
}

function openEditModal(data) {
    document.getElementById('edit-id-class').value = data.id_class || '';
    document.getElementById('edit-class-name').value = data.class_name || '';
    document.getElementById('edit-grade-level').value = data.grade_level || 1;
    document.getElementById('edit-homeroom').value = data.homeroom_teacher_id || '';
    document.getElementById('edit-description').value = data.description || '';
    openModal('modal-edit-class');
}

function confirmDelete(id, name, studentCount) {
    document.getElementById('delete-id-class').value = id;
    document.getElementById('delete-class-name').textContent = name;
    const warn = document.getElementById('delete-class-warning');
    if (studentCount > 0) {
        warn.textContent = `Kelas ini masih memiliki ${studentCount} siswa aktif. Tidak dapat dihapus.`;
        warn.classList.remove('hidden');
    } else {
        warn.classList.add('hidden');
    }
    openModal('modal-delete-class');
}
</script>

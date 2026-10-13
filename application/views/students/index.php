<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Header -->
<?php
$this->load->view('components/page_header', [
    'title'      => 'Data Siswa',
    'subtitle'   => 'Kelola seluruh data siswa sekolah',
    'breadcrumb' => [['label' => 'Data Siswa']],
]);
?>

<!-- Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php
    $total     = count($students ?? []);
    $male      = 0;
    $female    = 0;
    $withClass = 0;

    foreach (($students ?? []) as $s) {
        if (($s->gender ?? '') === 'Laki-laki')  $male++;
        if (($s->gender ?? '') === 'Perempuan')  $female++;
        if (!empty($s->class_name))              $withClass++;
    }

    $this->load->view('components/stat_card', [
        'icon'  => 'student',
        'label' => 'Total Siswa',
        'value' => number_format($total),
        'color' => 'primary',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'users',
        'label' => 'Siswa Laki-laki',
        'value' => number_format($male),
        'color' => 'accent',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'users',
        'label' => 'Siswa Perempuan',
        'value' => number_format($female),
        'color' => 'success',
    ]);
    $this->load->view('components/stat_card', [
        'icon'  => 'classroom',
        'label' => 'Sudah di Kelas',
        'value' => number_format($withClass),
        'color' => 'warning',
    ]);
    ?>
</div>

<!-- Main Card -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="font-display font-semibold text-neutral-900">Daftar Siswa</h3>
            <p class="text-xs text-neutral-500 mt-0.5">Kelola informasi siswa</p>
        </div>
        <button type="button"
                onclick="openAddModal()"
                class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Siswa
        </button>
    </div>

    <div class="card-body">
        <!-- Toolbar -->
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <div class="relative flex-1 sm:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="search"
                       id="student-search"
                       class="form-input pl-9 text-sm"
                       placeholder="Cari nama, NISN, atau orang tua...">
            </div>

            <select id="filter-class" class="form-select text-sm sm:max-w-[200px]">
                <option value="">Semua Kelas</option>
                <?php foreach (($classes ?? []) as $class): ?>
                    <option value="<?= html_escape($class->class_name) ?>">
                        <?= html_escape($class->class_name) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select id="filter-gender" class="form-select text-sm sm:max-w-[160px]">
                <option value="">Semua Gender</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
            <table class="table" id="students-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Gender</th>
                        <th>Orang Tua</th>
                        <th class="w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="students-tbody">
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $i => $student): ?>
                            <tr class="student-row"
                                data-name="<?= html_escape(strtolower($student->student_name ?? '')) ?>"
                                data-nisn="<?= html_escape($student->nisn ?? '') ?>"
                                data-parent="<?= html_escape(strtolower($student->parent_name ?? '')) ?>"
                                data-class="<?= html_escape($student->class_name ?? '') ?>"
                                data-gender="<?= html_escape($student->gender ?? '') ?>">
                                <td class="text-center text-neutral-500"><?= $i + 1 ?></td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-xs font-semibold flex-shrink-0">
                                            <?= strtoupper(substr($student->student_name ?? 'S', 0, 1)) ?>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-neutral-900 truncate">
                                                <?= html_escape($student->student_name ?? '-') ?>
                                            </div>
                                            <?php if (!empty($student->birth_place)): ?>
                                                <div class="text-xs text-neutral-500 truncate">
                                                    <?= html_escape($student->birth_place) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-neutral-600 font-mono text-xs"><?= html_escape($student->nisn ?? '-') ?></td>
                                <td>
                                    <?php if (!empty($student->class_name)): ?>
                                        <span class="badge-primary"><?= html_escape($student->class_name) ?></span>
                                    <?php else: ?>
                                        <span class="badge-neutral">Belum ada kelas</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (($student->gender ?? '') === 'Laki-laki'): ?>
                                        <span class="badge-primary">Laki-laki</span>
                                    <?php elseif (($student->gender ?? '') === 'Perempuan'): ?>
                                        <span class="badge-accent">Perempuan</span>
                                    <?php else: ?>
                                        <span class="text-neutral-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-neutral-600 text-sm">
                                    <?= html_escape($student->parent_name ?? '-') ?>
                                </td>
                                <td>
                                    <div class="flex items-center justify-center gap-1">
                                        <button type="button"
                                                onclick='openEditModal(<?= json_encode($student) ?>)'
                                                class="p-2 rounded-lg text-neutral-500 hover:text-primary-600 hover:bg-primary-50 transition-colors"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <a href="<?= base_url('students/detail/' . ($student->id_student ?? 0)) ?>"
                                           class="p-2 rounded-lg text-neutral-500 hover:text-success-600 hover:bg-success-50 transition-colors"
                                           title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <button type="button"
                                                onclick="confirmDelete(<?= (int)($student->id_student ?? 0) ?>, '<?= html_escape($student->student_name ?? '') ?>')"
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
                            <td colspan="7">
                                <?php $this->load->view('components/empty_state', [
                                    'icon'       => 'inbox',
                                    'title'      => 'Belum Ada Siswa',
                                    'message'    => 'Mulai dengan menambahkan data siswa pertama.',
                                    'action_url' => 'javascript:openAddModal()',
                                    'action_txt' => 'Tambah Siswa',
                                ]); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- No result message -->
        <div id="no-result" class="hidden text-center py-12">
            <svg class="w-16 h-16 text-neutral-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-sm text-neutral-500">Tidak ada siswa yang cocok dengan pencarian</p>
        </div>
    </div>
</div>

<!-- ==============================================
     MODAL: TAMBAH SISWA
     ============================================== -->
<?php ob_start(); ?>
<form id="form-add-student" action="<?= base_url('students/add') ?>" method="post">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

    <div class="grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <label class="form-label">Nama Lengkap <span class="text-danger-500">*</span></label>
            <input type="text" name="student_name" class="form-input" required maxlength="100" placeholder="Contoh: Ahmad Fauzan">
        </div>

        <div>
            <label class="form-label">NISN <span class="text-danger-500">*</span></label>
            <input type="text" name="nisn" class="form-input" required maxlength="20" placeholder="10 digit angka">
        </div>

        <div>
            <label class="form-label">Jenis Kelamin <span class="text-danger-500">*</span></label>
            <select name="gender" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </div>

        <div>
            <label class="form-label">Tempat Lahir</label>
            <input type="text" name="birth_place" class="form-input" maxlength="100" placeholder="Kota kelahiran">
        </div>

        <div>
            <label class="form-label">Tanggal Lahir</label>
            <input type="date" name="birth_date" class="form-input">
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-textarea" rows="2" maxlength="255" placeholder="Alamat lengkap"></textarea>
        </div>

        <div>
            <label class="form-label">Kelas <span class="text-danger-500">*</span></label>
            <select name="id_class" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                <?php foreach (($classes ?? []) as $class): ?>
                    <option value="<?= (int)$class->id_class ?>">
                        <?= html_escape($class->class_name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label">Tahun Masuk</label>
            <input type="number" name="enrollment_year" class="form-input" value="<?= date('Y') ?>" min="2000" max="2100">
        </div>

        <div class="sm:col-span-2 pt-2 mt-2 border-t border-neutral-100">
            <div class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-3">Data Orang Tua</div>
        </div>

        <div>
            <label class="form-label">Nama Orang Tua <span class="text-danger-500">*</span></label>
            <input type="text" name="parent_name" class="form-input" required maxlength="100" placeholder="Nama lengkap orang tua">
        </div>

        <div>
            <label class="form-label">No. Telepon <span class="text-danger-500">*</span></label>
            <input type="text" name="parent_phone" class="form-input" required maxlength="20" placeholder="08xxxxxxxxxx">
        </div>

        <div>
            <label class="form-label">Email Orang Tua</label>
            <input type="email" name="parent_email" class="form-input" maxlength="100" placeholder="email@contoh.com">
        </div>

        <div>
            <label class="form-label">Akun Orang Tua</label>
            <select name="id_user_parent" class="form-select">
                <option value="">-- Tidak ditautkan --</option>
                <?php foreach (($parents ?? []) as $parent): ?>
                    <option value="<?= (int)$parent->id_user ?>">
                        <?= html_escape($parent->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="form-help">Hubungkan siswa ke akun orang tua yang sudah terdaftar.</p>
        </div>
    </div>

    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('modal-add-student')" class="btn-outline">Batal</button>
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan Siswa
        </button>
    </div>
</form>
<?php $modal_add_content = ob_get_clean(); ?>

<?php $this->load->view('components/modal', [
    'id'      => 'modal-add-student',
    'title'   => 'Tambah Data Siswa',
    'size'    => 'lg',
    'content' => $modal_add_content,
]); ?>

<!-- ==============================================
     MODAL: EDIT SISWA
     ============================================== -->
<?php ob_start(); ?>
<form id="form-edit-student" action="<?= base_url('students/edit') ?>" method="post">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <input type="hidden" name="id_student" id="edit-id-student">

    <div class="grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <label class="form-label">Nama Lengkap <span class="text-danger-500">*</span></label>
            <input type="text" name="student_name" id="edit-student-name" class="form-input" required maxlength="100">
        </div>

        <div>
            <label class="form-label">NISN</label>
            <input type="text" id="edit-nisn" class="form-input bg-neutral-50" readonly>
            <p class="form-help">NISN tidak dapat diubah</p>
        </div>

        <div>
            <label class="form-label">Jenis Kelamin <span class="text-danger-500">*</span></label>
            <select name="gender" id="edit-gender" class="form-select" required>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </div>

        <div>
            <label class="form-label">Tempat Lahir</label>
            <input type="text" name="birth_place" id="edit-birth-place" class="form-input" maxlength="100">
        </div>

        <div>
            <label class="form-label">Tanggal Lahir</label>
            <input type="date" name="birth_date" id="edit-birth-date" class="form-input">
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">Alamat</label>
            <textarea name="address" id="edit-address" class="form-textarea" rows="2" maxlength="255"></textarea>
        </div>

        <div>
            <label class="form-label">Kelas <span class="text-danger-500">*</span></label>
            <select name="id_class" id="edit-id-class" class="form-select" required>
                <?php foreach (($classes ?? []) as $class): ?>
                    <option value="<?= (int)$class->id_class ?>">
                        <?= html_escape($class->class_name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="sm:col-span-2 pt-2 mt-2 border-t border-neutral-100">
            <div class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-3">Data Orang Tua</div>
        </div>

        <div>
            <label class="form-label">Nama Orang Tua <span class="text-danger-500">*</span></label>
            <input type="text" name="parent_name" id="edit-parent-name" class="form-input" required maxlength="100">
        </div>

        <div>
            <label class="form-label">No. Telepon</label>
            <input type="text" name="parent_phone" id="edit-parent-phone" class="form-input" maxlength="20">
        </div>

        <div>
            <label class="form-label">Email Orang Tua</label>
            <input type="email" name="parent_email" id="edit-parent-email" class="form-input" maxlength="100">
        </div>

        <div>
            <label class="form-label">Akun Orang Tua</label>
            <select name="id_user_parent" id="edit-id-user-parent" class="form-select">
                <option value="">-- Tidak ditautkan --</option>
                <?php foreach (($parents ?? []) as $parent): ?>
                    <option value="<?= (int)$parent->id_user ?>">
                        <?= html_escape($parent->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('modal-edit-student')" class="btn-outline">Batal</button>
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Update Siswa        </button>
    </div>
</form>
<?php $modal_edit_content = ob_get_clean(); ?>

<?php $this->load->view('components/modal', [
    'id'      => 'modal-edit-student',
    'title'   => 'Edit Data Siswa',
    'size'    => 'lg',
    'content' => $modal_edit_content,
]); ?>

<!-- ==============================================
     MODAL: KONFIRMASI HAPUS
     ============================================== -->
<?php ob_start(); ?>
<form id="form-delete-student" action="<?= base_url('students/delete') ?>" method="post">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <input type="hidden" name="id_student" id="delete-id-student">

    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-full bg-danger-100 text-danger-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="font-display font-semibold text-neutral-900 mb-1">Hapus Data Siswa?</h4>
            <p class="text-sm text-neutral-600 mb-1">
                Anda akan menghapus data siswa:
            </p>
            <p class="font-medium text-neutral-900" id="delete-student-name">-</p>
            <p class="text-xs text-neutral-500 mt-3">
                Tindakan ini tidak dapat dibatalkan. Data nilai dan riwayat terkait juga akan terpengaruh.
            </p>
        </div>
    </div>

    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('modal-delete-student')" class="btn-outline">Batal</button>
        <button type="submit" class="btn-danger">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Ya, Hapus
        </button>
    </div>
</form>
<?php $modal_delete_content = ob_get_clean(); ?>

<?php $this->load->view('components/modal', [
    'id'      => 'modal-delete-student',
    'title'   => 'Konfirmasi Hapus',
    'size'    => 'sm',
    'content' => $modal_delete_content,
]); ?>

<!-- ==============================================
     JAVASCRIPT
     ============================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==============================================
    // SEARCH & FILTER
    // ==============================================
    const searchEl  = document.getElementById('student-search');
    const classEl   = document.getElementById('filter-class');
    const genderEl  = document.getElementById('filter-gender');
    const tbody     = document.getElementById('students-tbody');
    const noResult  = document.getElementById('no-result');

    function filterTable() {
        const q      = (searchEl?.value || '').toLowerCase().trim();
        const cls    = classEl?.value || '';
        const gender = genderEl?.value || '';

        const rows = tbody?.querySelectorAll('.student-row') || [];
        let visible = 0;

        rows.forEach(row => {
            const name   = row.dataset.name || '';
            const nisn   = row.dataset.nisn || '';
            const parent = row.dataset.parent || '';
            const rowCls = row.dataset.class || '';
            const rowGen = row.dataset.gender || '';

            const matchSearch = !q || name.includes(q) || nisn.includes(q) || parent.includes(q);
            const matchClass  = !cls || rowCls === cls;
            const matchGender = !gender || rowGen === gender;

            if (matchSearch && matchClass && matchGender) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noResult) {
            noResult.classList.toggle('hidden', visible > 0 || rows.length === 0);
        }

        // Hide table kalau tidak ada hasil
        const table = document.getElementById('students-table');
        if (table) {
            table.style.display = (visible === 0 && rows.length > 0) ? 'none' : '';
        }
    }

    if (searchEl) searchEl.addEventListener('input', filterTable);
    if (classEl)  classEl.addEventListener('change', filterTable);
    if (genderEl) genderEl.addEventListener('change', filterTable);
});

// ==============================================
// OPEN ADD MODAL
// ==============================================
function openAddModal() {
    const form = document.getElementById('form-add-student');
    if (form) form.reset();
    openModal('modal-add-student');
}

// ==============================================
// OPEN EDIT MODAL
// ==============================================
function openEditModal(student) {
    document.getElementById('edit-id-student').value       = student.id_student || '';
    document.getElementById('edit-student-name').value     = student.student_name || '';
    document.getElementById('edit-nisn').value             = student.nisn || '';
    document.getElementById('edit-gender').value           = student.gender || 'Laki-laki';
    document.getElementById('edit-birth-place').value      = student.birth_place || '';
    document.getElementById('edit-birth-date').value       = student.birth_date || '';
    document.getElementById('edit-address').value          = student.address || '';
    document.getElementById('edit-id-class').value         = student.id_class || '';
    document.getElementById('edit-parent-name').value      = student.parent_name || '';
    document.getElementById('edit-parent-phone').value     = student.parent_phone || '';
    document.getElementById('edit-parent-email').value     = student.parent_email || '';
    document.getElementById('edit-id-user-parent').value   = student.id_user_parent || '';

    openModal('modal-edit-student');
}

// ==============================================
// CONFIRM DELETE
// ==============================================
function confirmDelete(id, name) {
    document.getElementById('delete-id-student').value = id;
    document.getElementById('delete-student-name').textContent = name || '-';
    openModal('modal-delete-student');
}
</script>

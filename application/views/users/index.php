<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$this->load->view('components/page_header', [
    'title'      => 'Kelola Pengguna',
    'subtitle'   => 'Kelola akun admin, guru, dan orang tua',
    'breadcrumb' => [['label' => 'Pengguna']],
]);
?>

<!-- Stat -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php
    $total = count($users ?? []);
    $adminCount = $teacherCount = $parentCount = 0;
    foreach (($users ?? []) as $u) {
        if ($u->role === 'admin')   $adminCount++;
        if ($u->role === 'teacher') $teacherCount++;
        if ($u->role === 'parent')  $parentCount++;
    }
    $this->load->view('components/stat_card', ['icon' => 'users',     'label' => 'Total Pengguna', 'value' => number_format($total),        'color' => 'primary']);
    $this->load->view('components/stat_card', ['icon' => 'user',      'label' => 'Admin',          'value' => number_format($adminCount),   'color' => 'danger']);
    $this->load->view('components/stat_card', ['icon' => 'teacher',   'label' => 'Guru',           'value' => number_format($teacherCount), 'color' => 'success']);
    $this->load->view('components/stat_card', ['icon' => 'users',     'label' => 'Orang Tua',      'value' => number_format($parentCount),  'color' => 'accent']);
    ?>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="font-display font-semibold text-neutral-900">Daftar Pengguna</h3>
            <p class="text-xs text-neutral-500 mt-0.5"><?= count($users) ?> pengguna terdaftar</p>
        </div>
        <button type="button" onclick="openAddModal()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pengguna
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
                <input type="search" id="user-search" class="form-input pl-9 text-sm" placeholder="Cari nama, username, atau email...">
            </div>
            <select id="filter-role" class="form-select text-sm sm:max-w-[180px]">
                <option value="">Semua Role</option>
                <option value="admin">Admin</option>
                <option value="teacher">Guru</option>
                <option value="parent">Orang Tua</option>
            </select>
            <select id="filter-status" class="form-select text-sm sm:max-w-[180px]">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Kontak</th>
                        <th>Status</th>
                        <th class="w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <tr class="user-row"
                                data-name="<?= html_escape(strtolower($u->name ?? '')) ?>"
                                data-username="<?= html_escape(strtolower($u->username ?? '')) ?>"
                                data-email="<?= html_escape(strtolower($u->email ?? '')) ?>"
                                data-role="<?= html_escape($u->role) ?>"
                                data-status="<?= $u->is_active ? 'active' : 'inactive' ?>">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex-shrink-0">
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-xs font-semibold">
                                                <?= strtoupper(substr($u->name ?? 'U', 0, 1)) ?>
                                            </div>
                                            <?php if (!empty($u->is_online)): ?>
                                                <span class="w-2.5 h-2.5 rounded-full bg-success-500 border-2 border-white absolute -bottom-0.5 -right-0.5"></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-neutral-900 text-sm truncate">
                                                <?= html_escape($u->name ?? '-') ?>
                                                <?php if ($u->id_user == $this->session->userdata('user_id')): ?>
                                                    <span class="text-[10px] text-primary-600 ml-1">(Anda)</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-xs text-neutral-500 truncate"><?= html_escape($u->email ?? '-') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-mono text-xs text-neutral-600"><?= html_escape($u->username) ?></td>
                                <td>
                                    <?php
                                    $roleColors = ['admin' => 'danger', 'teacher' => 'success', 'parent' => 'accent'];
                                    $roleLabels = ['admin' => 'Admin', 'teacher' => 'Guru', 'parent' => 'Orang Tua'];
                                    $color = $roleColors[$u->role] ?? 'neutral';
                                    ?>
                                    <span class="badge-<?= $color ?>"><?= $roleLabels[$u->role] ?? $u->role ?></span>
                                </td>
                                <td class="text-xs text-neutral-600">
                                    <?= html_escape($u->phone ?? '-') ?>
                                </td>
                                <td>
                                    <?php if ($u->is_active): ?>
                                        <span class="badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge-neutral">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="flex items-center justify-center gap-1">
                                        <button type="button"
                                                onclick='openEditModal(<?= json_encode($u) ?>)'
                                                class="p-2 rounded-lg text-neutral-500 hover:text-primary-600 hover:bg-primary-50 transition-colors"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button type="button"
                                                onclick="openResetPassword(<?= (int)$u->id_user ?>, '<?= html_escape($u->name) ?>')"
                                                class="p-2 rounded-lg text-neutral-500 hover:text-warning-600 hover:bg-warning-50 transition-colors"
                                                title="Reset Password">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                        </button>
                                        <?php if ($u->id_user != $this->session->userdata('user_id')): ?>
                                            <button type="button"
                                                    onclick="toggleStatus(<?= (int)$u->id_user ?>, <?= $u->is_active ?>)"
                                                    class="p-2 rounded-lg text-neutral-500 hover:text-<?= $u->is_active ? 'danger' : 'success' ?>-600 hover:bg-<?= $u->is_active ? 'danger' : 'success' ?>-50 transition-colors"
                                                    title="<?= $u->is_active ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                <?php if ($u->is_active): ?>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                <?php else: ?>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                <?php endif; ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: ADD -->
<?php ob_start(); ?>
<form id="form-add-user" action="<?= base_url('users/add') ?>" method="post">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <div class="space-y-4">
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Username <span class="text-danger-500">*</span></label>
                <input type="text" name="username" class="form-input" required maxlength="50" placeholder="username">
            </div>
            <div>
                <label class="form-label">Nama Lengkap <span class="text-danger-500">*</span></label>
                <input type="text" name="name" class="form-input" required maxlength="100">
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" maxlength="100">
            </div>
            <div>
                <label class="form-label">No. Telepon</label>
                <input type="text" name="phone" class="form-input" maxlength="20">
            </div>
            <div>
                <label class="form-label">Password <span class="text-danger-500">*</span></label>
                <input type="password" name="password" class="form-input" required minlength="6">
            </div>
            <div>
                <label class="form-label">Konfirmasi Password <span class="text-danger-500">*</span></label>
                <input type="password" name="passconf" class="form-input" required>
            </div>
        </div>
        <div>
            <label class="form-label">Role <span class="text-danger-500">*</span></label>
            <select name="role" class="form-select" required>
                <option value="teacher">Guru</option>
                <option value="parent">Orang Tua</option>
                <option value="admin">Admin</option>
            </select>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('modal-add-user')" class="btn-outline">Batal</button>
        <button type="submit" class="btn-primary">Simpan</button>
    </div>
</form>
<?php $modal_add = ob_get_clean(); ?>
<?php $this->load->view('components/modal', ['id' => 'modal-add-user', 'title' => 'Tambah Pengguna', 'size' => 'lg', 'content' => $modal_add]); ?>

<!-- MODAL: EDIT -->
<?php ob_start(); ?>
<form id="form-edit-user" action="<?= base_url('users/edit') ?>" method="post">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <input type="hidden" name="id_user" id="edit-user-id">
    <div class="space-y-4">
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Username</label>
                <input type="text" id="edit-username" class="form-input bg-neutral-50" readonly>
            </div>
            <div>
                <label class="form-label">Nama Lengkap <span class="text-danger-500">*</span></label>
                <input type="text" name="name" id="edit-user-name" class="form-input" required maxlength="100">
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" id="edit-user-email" class="form-input" maxlength="100">
            </div>
            <div>
                <label class="form-label">No. Telepon</label>
                <input type="text" name="phone" id="edit-user-phone" class="form-input" maxlength="20">
            </div>
        </div>
        <div>
            <label class="form-label">Role <span class="text-danger-500">*</span></label>
            <select name="role" id="edit-user-role" class="form-select" required>
                <option value="admin">Admin</option>
                <option value="teacher">Guru</option>
                <option value="parent">Orang Tua</option>
            </select>
        </div>
        <div class="pt-4 border-t border-neutral-100">
            <div class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-3">Ganti Password (Opsional)</div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-input" minlength="6">
                </div>
                <div>
                    <label class="form-label">Konfirmasi</label>
                    <input type="password" name="passconf" class="form-input">
                </div>
            </div>
            <p class="form-help mt-2">Kosongkan jika tidak ingin ganti password.</p>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('modal-edit-user')" class="btn-outline">Batal</button>
        <button type="submit" class="btn-primary">Simpan Perubahan</button>
    </div>
</form>
<?php $modal_edit = ob_get_clean(); ?>
<?php $this->load->view('components/modal', ['id' => 'modal-edit-user', 'title' => 'Edit Pengguna', 'size' => 'lg', 'content' => $modal_edit]); ?>

<!-- MODAL: RESET PASSWORD -->
<?php ob_start(); ?>
<form id="form-reset-password">
    <input type="hidden" id="reset-user-id">
    <div class="space-y-4">
        <p class="text-sm text-neutral-600">
            Reset password untuk <strong id="reset-user-name">-</strong>:
        </p>
        <div>
            <label class="form-label">Password Baru <span class="text-danger-500">*</span></label>
            <input type="text" id="reset-new-password" class="form-input" required minlength="6" placeholder="Minimal 6 karakter">
            <p class="form-help">Password akan ditampilkan plaintext — catat dan berikan ke user.</p>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('modal-reset-password')" class="btn-outline">Batal</button>
        <button type="button" onclick="submitResetPassword()" class="btn-warning">Reset Password</button>
    </div>
</form>
<?php $modal_reset = ob_get_clean(); ?>
<?php $this->load->view('components/modal', ['id' => 'modal-reset-password', 'title' => 'Reset Password', 'size' => 'sm', 'content' => $modal_reset]); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchEl = document.getElementById('user-search');
    const roleEl   = document.getElementById('filter-role');
    const statusEl = document.getElementById('filter-status');

    function filter() {
        const q = (searchEl?.value || '').toLowerCase().trim();
        const r = roleEl?.value || '';
        const s = statusEl?.value || '';
        const rows = document.querySelectorAll('.user-row');
        rows.forEach(row => {
            const name = row.dataset.name || '';
            const uname = row.dataset.username || '';
            const email = row.dataset.email || '';
            const role = row.dataset.role || '';
            const status = row.dataset.status || '';
            const match = (!q || name.includes(q) || uname.includes(q) || email.includes(q))
                       && (!r || role === r)
                       && (!s || status === s);
            row.style.display = match ? '' : 'none';
        });
    }

    searchEl?.addEventListener('input', filter);
    roleEl?.addEventListener('change', filter);
    statusEl?.addEventListener('change', filter);
});

function openAddModal() {
    document.getElementById('form-add-user')?.reset();
    openModal('modal-add-user');
}

function openEditModal(u) {
    document.getElementById('edit-user-id').value = u.id_user || '';
    document.getElementById('edit-username').value = u.username || '';
    document.getElementById('edit-user-name').value = u.name || '';
    document.getElementById('edit-user-email').value = u.email || '';
    document.getElementById('edit-user-phone').value = u.phone || '';
    document.getElementById('edit-user-role').value = u.role || 'parent';
    document.querySelectorAll('#form-edit-user input[type="password"]').forEach(el => el.value = '');
    openModal('modal-edit-user');
}

function openResetPassword(id, name) {
    document.getElementById('reset-user-id').value = id;
    document.getElementById('reset-user-name').textContent = name;
    document.getElementById('reset-new-password').value = '';
    openModal('modal-reset-password');
}

function submitResetPassword() {
    const id = document.getElementById('reset-user-id').value;
    const pwd = document.getElementById('reset-new-password').value;

    if (!pwd || pwd.length < 6) {
        alert('Password minimal 6 karakter');
        return;
    }

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

    const body = new URLSearchParams();
    body.append('csrf_token', csrf);
    body.append('id_user', id);
    body.append('new_password', pwd);

    fetch(window.BASE_URL + 'users/reset_password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: body.toString(),
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Password berhasil direset: ' + pwd);
            closeModal('modal-reset-password');
        } else {
            alert(data.message || 'Gagal reset password');
        }
    })
    .catch(() => alert('Terjadi kesalahan'));
}

function toggleStatus(id, isActive) {
    const action = isActive ? 'nonaktifkan' : 'aktifkan';
    if (!confirm(`Yakin ingin ${action} user ini?`)) return;

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

    const body = new URLSearchParams();
    body.append('csrf_token', csrf);
    body.append('id_user', id);

    fetch(window.BASE_URL + 'users/toggle_status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: body.toString(),
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') location.reload();
        else alert(data.message || 'Gagal');
    })
    .catch(() => alert('Terjadi kesalahan'));
}
</script>
